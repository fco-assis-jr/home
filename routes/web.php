<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\sugestoes\PDF\PDFController;


Route::middleware(['auth'])->group(function () {
    Route::get('/index', App\Livewire\Index::class)->name('index');
    Route::get('/home', App\Livewire\Index::class)->name('home');

    Route::prefix('sugestoes')->namespace('App\Livewire\sugestoes')->group(function () {

        Route::get('/home', App\Livewire\sugestoes\Home::class)->name('sugestoes.home');
        Route::get('/avaliar', App\Livewire\sugestoes\Avaliar::class)->name('sugestoes.avaliar');
        Route::get('/solicitados', App\Livewire\sugestoes\Solicitados::class)->name('sugestoes.solicitados');
        Route::get('/visualizar-pdf', [PDFController::class, 'visualizarPDF'])->name('sugestoes.visualizar-pdf');

    });
    Route::prefix('ocorrencias')->namespace('App\Livewire\ocorrencias')->group(function () {

        Route::get('/home', App\Livewire\ocorrencias\Home::class)->name('ocorrencias.home');
        Route::get('/ocorrencia', App\Livewire\ocorrencias\ocorrencias::class)->name('ocorrencias.ocorrencia');
        Route::get('/tipos', App\Livewire\ocorrencias\Tipos::class)->name('tipos.ocorrencia');

    });
});

Route::get('/', App\Livewire\login\Login::class)->name('login');

Route::get('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');
