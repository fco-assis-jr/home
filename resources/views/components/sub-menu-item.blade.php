<!-- resources/views/components/sub-menu-item.blade.php -->
@props(['route', 'icon', 'label'])

<li>
    <a class="app-menu__item some_no_mobile" href="{{ $route }}">
        <i class="app-menu__icon {{ $icon }}"></i>
        <span class="app-menu__label">{{ $label }}</span>
    </a>
</li>
