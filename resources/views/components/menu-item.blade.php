<!-- resources/views/components/menu-item.blade.php -->
@props(['contro'])

@if($contro->codrotina == 1444)
    <li class="treeview menu-open">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon bi bi-basket2-fill"></i><span class="app-menu__label"> Sugestoes | {{$contro->codrotina}}</span>
            <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
        <ul class="treeview-menu">
            @foreach($contro->pccontroi as $Pccontroi)
                @if($Pccontroi->codcontrole == 1 && $Pccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('sugestoes.home')}}"><i class="icon bi bi-card-text"></i>Cadastrar Sugestão</a></li>
                    <li><a class="treeview-item" href="{{route('sugestoes.solicitados')}}"><i class="icon bi bi-bar-chart-line-fill"></i> solicitados</a></li>
                @elseif($Pccontroi->codcontrole == 2 && $Pccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('sugestoes.avaliar')}}"><i class="icon bi bi-graph-up-arrow"></i> Avaliar</a></li>
                @endif
            @endforeach
        </ul>
    </li>
@endif

@if($contro->codrotina == 8177)
    <li class="treeview menu-open">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon bi bi-exclamation-triangle-fill"></i><span class="app-menu__label">Ocorrências | {{$contro->codrotina}}</span>
            <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
         <ul class="treeview-menu">
             @foreach($contro->pccontroi as $Pccontroi)
                @if($Pccontroi->codcontrole == 1 && $Pccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('ocorrencias.home')}}"><i class="icon bi bi-amd"></i>Cadastro Ocorrências</a></li>
                @elseif($Pccontroi->codcontrole == 2 && $Pccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('ocorrencias.ocorrencia')}}"><i class="icon bi bi-card-text"></i>Listar Ocorrências</a></li>
                @elseif($Pccontroi->codcontrole == 3 && $Pccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('tipos.ocorrencia')}}"><i class="icon bi-bar-chart-steps"></i>Tipos Ocorrências</a></li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
