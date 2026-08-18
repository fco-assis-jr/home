<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Solicitações  </h1>
            <p>Listagem das solicitações de sugestão</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="container mt-4">
            <div class="tile">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h3 class="tile-title mb-0">Tabela de Solicitações</h3>
                    <div class="d-flex gap-3 small text-muted text-uppercase">
                        <span><i class="bi bi-circle-fill text-secondary"></i> Pendente</span>
                        <span><i class="bi bi-circle-fill text-warning"></i> Em Andamento</span>
                        <span><i class="bi bi-circle-fill text-success"></i> Concluído</span>
                    </div>
                </div>

                <form wire:submit.prevent="buscar" class="row gy-2 gx-3 align-items-end mb-3">
                    <div class="col-auto">
                        <label class="form-label small mb-0">Código Sugestão</label>
                        <input
                            type="number"
                            class="form-control form-control-sm"
                            wire:model="filtroCodsug"
                            placeholder="Ex: 1234"
                            style="width: 140px;"
                        >
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-0">Período - De</label>
                        <input type="date" class="form-control form-control-sm" wire:model="filtroDataInicio">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-0">Até</label>
                        <input type="date" class="form-control form-control-sm" wire:model="filtroDataFim">
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <span wire:loading.remove wire:target="buscar"><i class="bi bi-search"></i> Filtrar</span>
                            <span wire:loading wire:target="buscar">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Filtrando...
                            </span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="limparFiltros">
                            <i class="bi bi-x-circle"></i> Limpar
                        </button>
                    </div>
                </form>

                <div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="text-uppercase text-center">
                            <th class="text-center">CODSUG</th>
                            <th class="text-center">FUNCIONÁRIO</th>
                            <th class="text-center">CODFILIAL</th>
                            <th class="text-center">DATA CRIAÇÃO</th>
                            <th class="text-center">ANDAMENTO</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($itensc as $index => $item)
                            <tr class="text-uppercase text-center align-middle cursor-pointer" wire:key="{{ $item->codsug }}" wire:click="modalOpen({{$item->codsug}})">
                                <td class="text-center">{{ $item->codsug }}</td>
                                <td class="text-center">{{ $item->nome }}</td>
                                <td class="text-center">{{ $item->codfilial }}</td>
                                <td class="text-center">{{ $item->data }}</td>
                                <td class="text-center" style="min-width: 170px;">
                                    <span class="badge bg-{{ $this->statusClass($item->perc_concluido) }} mb-1">
                                        {{ $this->statusLabel($item->perc_concluido) }}
                                    </span>
                                    <div class="progress" style="height: 6px;">
                                        <div
                                            class="progress-bar bg-{{ $this->statusClass($item->perc_concluido) }}"
                                            role="progressbar"
                                            style="width: {{ $item->perc_concluido }}%"
                                            aria-valuenow="{{ $item->perc_concluido }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->qtd_avaliados }}/{{ $item->qtd_aguardando }} itens avaliados ({{ $item->perc_concluido }}%)
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a
                                        href="{{ route('sugestoes.comprovante', $item->codsug) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Imprimir comprovante"
                                        onclick="event.stopPropagation()"
                                    >
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox"></i> Nenhuma requisição encontrada com os filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Itens -->
    <div class="modal fade fade backdrop-blur-lg" id="ModalTableAvaliar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-table"></i> Detalhes da Sugestão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <h6 class="modal-title" id="exampleModalLabel"><i class="bi bi-person-circle"></i> {{ $nome }}</h6>
                        <h6 class="modal-title" id="exampleModalLabel"><i class="bi bi-house-gear-fill"></i> FILIAL: {{ $filial }}</h6>
                        <h6 class="modal-title" id="exampleModalLabel"><i class="bi bi-calendar4-event"></i> {{ $data_criacao }} </h6>
                    </div>
                    <div style="height: 500px; overflow: scroll">
                        <table class="table table-bordered table-hover table-dark mt-3">
                            <thead>
                            <tr class="text-uppercase text-center">
                                <th>CODSUGITEM</th>
                                <th>NOME</th>
                                <th>CODPROD</th>
                                <th>CODAUXILIAR</th>
                                <th>VALOR PRODUTO</th>
                                <th>QUANTIDADE</th>
                                <th>DATA VENCIMENTO</th>
                                <th>STATUS</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($itensi as $index => $item)
                                <tr class="text-uppercase text-center align-middle cursor-pointer {{ $item->status == '0' ? 'table-primary' : 'table-danger' }}"
                                    @if($item->status == '0') wire:click="editItem({{ $item->codsug }}, {{ $item->codsugitem }}, {{ $item->quantidade }}, '{{ $item->data_vencimento }}' )" @endif
                                >
                                    <td>{{ $item->codsugitem }}</td>
                                    <td class="truncate text-left" title="{{ $item->descricao }} | {{ $item->unid }}">
                                        {{ Str::limit($item->descricao.' '.$item->unid, 40, '...') }}
                                    </td>
                                    <td>{{ $item->codprod }}</td>
                                    <td>{{ $item->codauxiliar }}</td>
                                    <td>{{ $item->valor_produto }}</td>
                                    <td>{{ $item->quantidade }}</td>
                                    <td>{{ $item->data_vencimento }}</td>
                                    <td>
                                    <span class="{{ $item->status == '0' ? 'badge bg-primary' : 'badge bg-danger' }}">
                                         {{ $item->status == '0' ? 'ATIVO' : 'LANÇADO' }}
                                    </span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a
                        href="{{ $codsug ? route('sugestoes.comprovante', $codsug) : '#' }}"
                        target="_blank"
                        class="btn btn-outline-primary"
                    >
                        <i class="bi bi-printer"></i> Imprimir Comprovante
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Item -->
    <div class="modal fade" id="ModalEditItem" tabindex="-1" aria-labelledby="ModalEditItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalEditItemLabel"><i class="bi bi-pencil"></i> Editar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4 flex justify-content-center">
                                <div class="col-md-4">
                                    <label for="quantidade">Quantidade</label>
                                    <input type="number" class="form-control" id="quantidade" wire:model="quantidade" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="data_vencimento">Data Vencimento</label>
                                    <input type="date" class="form-control" id="data_vencimento" wire:model="data_vencimento" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($codsug && $codsugitem)
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" wire:click="updateItem">Salvar</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
