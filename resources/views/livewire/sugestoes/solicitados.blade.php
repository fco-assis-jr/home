<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Solicitações  </h1>
            <p>Listagem das solicitações de sugestão</p>
        </div>
    </div>

    <div class="container mt-4" wire:ignore>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Tabela de Solicitações</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="sampleTable">
                            <thead>
                            <tr class="text-uppercase text-center">
                                <th class="text-center">CODSUG</th>
                                <th class="text-center">FUNCIONÁRIO</th>
                                <th class="text-center">CODFILIAL</th>
                                <th class="text-center">QT ITENS</th>
                                <th class="text-center">DATA CRIAÇÃO</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($itensc as $index => $item)
                                <tr class="text-uppercase text-center align-middle cursor-pointer" wire:click="modalOpen({{$item->codsug}})">
                                    <td class="text-center">{{ $item->codsug }}</td>
                                    <td class="text-center">{{ $item->nome }}</td>
                                    <td class="text-center">{{ $item->codfilial }}</td>
                                    <td class="text-center">{{ $item->qtd_aguardando }}</td>
                                    <td class="text-center">{{ $item->data }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
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
                                <td class="truncate" title="{{ $item->descricao }} | {{ $item->unid }}">
                                    <div style="width: 100%; overflow: auto;">
                                        {{ $item->descricao }} | {{ $item->unid }}
                                    </div>
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
                <div class="modal-footer">
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
