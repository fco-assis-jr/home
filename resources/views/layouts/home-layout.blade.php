<!-- resources/views/layouts/login-layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('NOME_EMPRESA', 'Nome da Empresa') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
            integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app sidebar-mini" x-data="{ open: true }" :class="open ? '' : 'sidenav-toggled'">
<!-- Navbar -->
<header class="app-header">
    <a class="app-header__logo" href="/home" style="font-family: 'Arial Black', serif; font-size: 18px;">
        <img src="{{ asset('images/logo.png') }}" style="width: 100px; height: 40px;">
    </a>
    <!-- Sidebar toggle button -->
    <span x-on:click="open = !open" style="cursor: pointer" class="app-sidebar__toggle"
          aria-label="Hide Sidebar"></span>
    <!-- Navbar Right Menu -->
    <ul class="app-nav">
        <li class="dropdown">
            <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i
                    class="bi bi-person fs-4"></i></a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"><i
                            class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a>
                </li>
            </ul>
        </li>
    </ul>
</header>

<!-- Sidebar menu -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar"
             src="{{ Session::has('foto_usuario') ? Session::get('foto_usuario') : asset('images/user.png') }}"
             alt="User Image">
        <div>
            <p class="app-sidebar__user-name">{{ auth()->user()->usuariobd }}</p>
        </div>
    </div>

    <ul class="app-menu">
        @foreach(session('pccontro') as $contro)
            @if(in_array($contro->codrotina, [1444, 8177]))
                <x-menu-item :contro="$contro"/>
            @endif
        @endforeach
    </ul>
</aside>

<main class="app-content">
    <x-livewire-alert::scripts/>
    <div class="cover">
        {{ $slot }}
    </div>
</main>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script>
    document.querySelector("html").classList.add('js');
    document.addEventListener('livewire:init', () => {
        Livewire.on('nome-preenchido', () => document.getElementById('quantidade').focus());
        Livewire.on('NovoItem', () => document.getElementById('codigo').focus());
        Livewire.on('ModalTableAvaliar', () => $('#ModalTableAvaliar').modal('show'));
        Livewire.on('ModalOptions', () => $('#ModalTableAvaliarOptions').modal('show'));
        Livewire.on('ModalTableAvaliar227', () => $('#ModalTableAvaliar227').modal('show'));
        Livewire.on('ModalEditItem', () => $('#ModalEditItem').modal('show'));
        Livewire.on('closeModalEditItem', () => $('#ModalEditItem').modal('hide'));
        Livewire.on('abrir-nova-aba', data => window.open(data[0].url, '_blank'));
        Livewire.on('AbrirModalEditar', () => {$('#exampleModalEditar').modal('show');});
        Livewire.on('FecharModalEditar', () => {$('#exampleModalEditar').modal('hide');});
        Livewire.on('FecharModalCadastro', () => {$('#exampleModal').modal('hide');});
        Livewire.on('abrirModalOcorrencia', () => {$('#ModalOcorrencia').modal('show');});
        Livewire.on('OpenDuplicarModal', () => {$('#ModalDuplicarOcorrencia').modal('show');});
        Livewire.on('FecharDuplicarModal', () => {$('#ModalOcorrencia').hide('show');});
        Livewire.on('FecharModalOcorrencia', () => {$('#ModalDuplicarOcorrencia').hide('show');});
    });

    function formatarMoeda(input) {
        let valor = input.value.replace(/\D/g, '');
        valor = (valor / 100).toFixed(2).replace('.', ',');
        input.value = 'R$ ' + valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function spanLoading() {
        var spanLoading = document.querySelectorAll("#span-loading");
        var buttonLoading = document.querySelectorAll("#button-loading");

        spanLoading.forEach(function (item) {
            item.style.display = "none";
        });

        buttonLoading.forEach(function (item) {
            item.style.display = "block";
            item.style.width = "71px";
        });
    }

    function spanLoadingHome() {
        var spanLoading = document.querySelectorAll("#span-loading");
        var buttonLoading = document.querySelectorAll("#button-loading");

        spanLoading.forEach(function (item) {
            item.style.display = "none";
        });

        buttonLoading.forEach(function (item) {
            item.style.display = "block";
        });
    }

    $('#sampleTable').DataTable({
        order: [[0, 'desc']],
        language: {
            "sEmptyTable": "Nenhum dado disponível na tabela",
            "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ entradas",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 entradas",
            "sInfoFiltered": "(filtrado de _MAX_ entradas no total)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "Mostrar _MENU_ entradas",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sSearch": "Buscar:",
            "sZeroRecords": "Nenhum registro encontrado",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior"
            }
        }
    });

</script>
</body>
</html>
