# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 11 + Livewire 3 intranet application ("home") that sits on top of a **WinThor ERP** Oracle
database. It is not a standalone app with its own domain model — almost all business data (employees,
suppliers, products, permissions, "ocorrências", "sugestões") lives in existing WinThor Oracle tables
(`PCEMPR`, `PCPRODUT`, `PCFORNEC`, `BDC_*`, ...) and this app is a custom UI/workflow layer over them.
There is no repository/service layer: Livewire components talk to the database directly via `DB::connection('oracle')`.

## Commands

```bash
# Install deps
composer install
npm install

# Run everything (server + queue listener + log tail + vite), like `php artisan serve` but all-in-one
composer run dev

# Or individually
php artisan serve
npm run dev        # vite dev server
npm run build       # vite production build

# Tests (PHPUnit, not Pest)
php artisan test
vendor/bin/phpunit
vendor/bin/phpunit --filter=testMethodName
vendor/bin/phpunit tests/Feature/SomeTest.php

# Code style (Laravel Pint, installed as a dev dependency)
vendor/bin/pint
vendor/bin/pint --test    # check only, no changes

# Artisan
php artisan migrate
php artisan tinker
```

Note: `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` are the untouched Laravel
skeleton tests — this app currently has no real test coverage of its Livewire components or Oracle
queries. Don't assume existing tests exercise app behavior.

## Two databases, two auth systems

The app runs against **two separate DB connections** defined in [config/database.php](config/database.php):

- `oracle` — the WinThor ERP database (real business data, no migrations, schema owned outside this repo).
  Credentials come from `ORACLE_DB_*` env vars. Requires the `oci8` PHP extension
  (`yajra/laravel-oci8` provides the Laravel driver).
- `mysql`/`sqlite` — this app's own Laravel-native tables (sessions, cache, jobs, queue, `users`).
  Migrations under [database/migrations](database/migrations) only cover this connection.

Authentication is **not** the standard Laravel `users` table flow. The default auth guard is `pcempr`
(see [config/auth.php](config/auth.php)), backed by the [App\Models\PCempr](app/Models/PCempr.php) model,
which maps to the Oracle `PCEMPR` table (WinThor employee/user registry, primary key `matricula`).
[App\Livewire\login\Login](app/Livewire/login/Login.php) authenticates by running a raw Oracle query that
calls the database's own `decrypt(senhabd, usuariobd)` function — password checking happens in Oracle, not PHP.
On successful login it also:
- loads the user's permission tree from `BDC_CONTROC`/`BDC_CONTROI` (Oracle tables) into
  `Session::put('bdc_controc', ...)`
- fetches a profile photo from a **remote Oracle DB link** (`R034FOT@DBLSENIOR`) and stores it as base64 in session

The unused `App\Models\User` (mysql-backed) exists for Laravel's password-reset scaffolding but is not
the login path used by the app.

## Permissions model

There's no Laravel policy/gate system here. Access control is a WinThor concept:
- `BDC_MODULO` = feature modules (e.g. `codmod` 800 = Permissões, 1444 = Sugestões, 8177 = Ocorrências)
- `BDC_MODULOCONTRO` = sub-controls/actions within a module
- `BDC_CONTROC` / `BDC_CONTROI` = which users have access to which module/control (granted via the
  Permissões screen, [App\Livewire\permissoes\Home](app/Livewire/permissoes/Home.php))

The logged-in user's permission set is loaded into the `bdc_controc` session key at login and rendered
by [resources/views/components/menu-item.blade.php](resources/views/components/menu-item.blade.php), which
branches on hardcoded `codmod`/`controle` numbers to decide which sidebar links to show. Route access
itself is only gated by the `auth` middleware in [routes/web.php](routes/web.php) — the module/control
numbers are a UI convention, not enforced per-route, so a new screen needs both a route and a
corresponding `codmod`/`controle` check added to the menu (and, if it should actually be locked down,
inside the component itself).

## Application structure

Routes → single-action Livewire full-page components (no traditional controllers except the two PDF
controllers). Feature areas live under `app/Livewire/<area>/` with matching Blade views under
`resources/views/livewire/<area>/`:

- `App\Livewire\Index` — home dashboard (counts + chart data queried straight from Oracle)
- `App\Livewire\login\Login` — the `pcempr` login flow described above
- `App\Livewire\sugestoes\*` — "sugestões" (product suggestion) workflow: `Home` (create), `Solicitados`,
  `Avaliar` (evaluate), `Relatorios`, plus `PDF\PDFController` / `PDFControllerRelatorio` which render
  DomPDF views fed from data staged in `Cache` (a cache key is passed via query string, not the raw data)
- `App\Livewire\ocorrencias\*` — "ocorrências" (incident log) workflow: `Home`, `Ocorrencias` (list/detail/
  duplicate), `Tipos` (incident type CRUD)
- `App\Livewire\permissoes\Home` — grants/revokes `BDC_CONTROC`/`BDC_CONTROI` rows for a selected `matricula`
- `App\Livewire\UtilityFunctions\UtilityFunctions` — trait mixed into components for `toast()` (wraps
  `jantinnerezo/livewire-alert`) and `formatMoeda()` (BRL currency formatting); several components
  duplicate these methods locally instead of using the trait — prefer the trait in new code
- `App\View\Components\Tabela227` / `Modal227` / `ModalFichaFornecedor` — generic Blade table/modal
  components used to render ad-hoc result sets (`$dados_cursor`) from raw Oracle queries

Layouts: `layouts.login-layout` (public login page) and `layouts.home-layout` (authenticated shell with
the permission-driven sidebar). Almost every authenticated Livewire component renders with
`->layout('layouts.home-layout')`.

## Working conventions specific to this codebase

- New data access almost always means a raw SQL string against `DB::connection('oracle')`, not Eloquent —
  there are no Eloquent models for WinThor tables besides `PCempr`, and the schema is external/read-mostly
  from this app's perspective. Match the existing style: parameterized `?`/named bindings, `TO_DATE`/`TO_CHAR`
  for Oracle date handling, `NVL` for null defaults, sequences (`*_SEQ.NEXTVAL`) for new PKs.
  Follow the codebase's existing use of parameterized queries — never interpolate user input into SQL.
- Money is formatted as Brazilian currency (`R$ 1.234,56`) via `formatMoeda`/inline `number_format($v, 2, ',', '.')`;
  dates in the UI are `d/m/Y`. Keep new UI consistent with this.
- User feedback goes through `livewire-alert` toasts (`$this->alert(...)` / the `toast()` helper), not
  Laravel session flash messages or Blade `@error` banners.
- The frontend stack is Bootstrap-based (see `bootstrap.bundle.min.js`, `app-menu` classes, jQuery plugins
  under `resources/js/plugins/`) plus Tailwind utility classes coexisting in the same views — this predates
  a full migration to Tailwind, so match whichever pattern the file you're editing already uses.
