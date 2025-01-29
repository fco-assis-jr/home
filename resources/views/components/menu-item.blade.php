@props(['contro'])
@if($contro->codmod == 800)
    <li>
        <a class="app-menu__item " href="{{route('permissoes.home')}}">
            <i class="app-menu__icon bi bi-key"></i>
            <span class="app-menu__label">Permissões</span>
        </a>
    </li>
@endif
@if($contro->codmod == 1444)
    <li class="treeview menu-open">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon bi bi-basket2-fill"></i><span class="app-menu__label"> Sugestoes | {{$contro->codmod}}</span>
            <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
        <ul class="treeview-menu">
            @foreach($contro->bdc_controi as $bdccontroi)
                @if($bdccontroi->controle == 1 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('sugestoes.home')}}"><i class="icon bi bi-card-text"></i>Cadastrar Sugestões</a></li>
                @elseif($bdccontroi->controle == 2 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('sugestoes.solicitados')}}"><i class="icon bi bi-bar-chart-line-fill"></i> Solicitados</a></li>
                @elseif($bdccontroi->controle == 4 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('sugestoes.relatorios')}}"><i class="icon bi bi-filetype-pdf"></i> Relatorio</a></li>
                @elseif($bdccontroi->controle == 3 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('sugestoes.avaliar')}}"><i class="icon bi bi-graph-up-arrow"></i> Avaliar</a></li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
@if($contro->codmod == 8177)
    <li class="treeview menu-open">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon bi bi-exclamation-triangle-fill"></i><span
                class="app-menu__label">Ocorrências | {{$contro->codmod}}</span>
            <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
        <ul class="treeview-menu">
            @foreach($contro->bdc_controi as $bdccontroi)
                @if($bdccontroi->controle == 1 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('ocorrencias.home')}}"><i class="icon bi bi-amd"></i>Cadastro
                            Ocorrências</a></li>
                @elseif($bdccontroi->controle == 2 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('ocorrencias.ocorrencia')}}"><i
                                class="icon bi bi-card-text"></i>Listar Ocorrências</a></li>
                @elseif($bdccontroi->controle == 3 && $bdccontroi->acesso == 'S')
                    <li><a class="treeview-item" href="{{route('tipos.ocorrencia')}}"><i
                                class="icon bi-bar-chart-steps"></i>Tipos Ocorrências</a></li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
