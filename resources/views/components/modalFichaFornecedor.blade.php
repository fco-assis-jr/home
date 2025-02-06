<div class="modal-content">
    <div class="modal-header">
        <div class="w-full flex justify-between pr-10">
            <h5 class="modal-title" id="staticBackdropLabel" style="font-size: 20px">
                227 - FICHA TÉCNICA POR FORNECEDOR
            </h5>
            <h5 style="font-size: 20px">
                {{$data_inicial}} á {{$data_final}}
            </h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div>
            <h6 class="modal-title" id="exampleModalLabel"><i class="bi bi-person-circle"></i> {{ $nome }}
            </h6>
            <h6 class="modal-title" id="exampleModalLabel"><i class="bi bi-house-gear-fill"></i>
                FILIAL: {{ $filial }}</h6>
            <h6 class="modal-title" id="exampleModalLabel"><i
                    class="bi bi-calendar4-event"></i> {{ $this->cabecario_227_agrupado[$primeiroFornecedor]['DATACRIACAO'] ?? '' }}
            </h6>
        </div>
        <div style="overflow: scroll; height: 500px">
            <table class="table table-bordered table-hover mt-3">
                <thead>
                <tr class="text-uppercase text-center">
                    <th>CODFORNEC</th>
                    <th>FORNECEDOR</th>
                    <th>QUANTIDADE</th>
                    <th>STATUS</th>
                    <th>OBSERVACAO</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($cabecario_227_agrupado as $codfornec => $dados)
                    <tr class="text-uppercase text-center align-middle cursor-pointer {{ $dados['ITENS_STATUS'] == 'COMPLETO' ? 'bg-blue-400 text-white' : 'bg-red-400 text-white' }}"
                        wire:click.prevent="modalOpenOptions({{ $dados['CODFORNEC'] }})">
                        <td>{{ $dados['CODFORNEC'] }} {{ $dados['ITENS_STATUS'] }}</td>
                        <td class="truncate text-left" title="{{ $dados['FORNECEDOR'] }}">
                            <div style="width: 100%;">
                                {{ $dados['FORNECEDOR'] }}
                            </div>
                        </td>
                        <td>{{ $dados['QUANTIDADE'] }}</td>
                        <td class="text-center">
                            <div class="relative w-full h-6 bg-gray-200 rounded">
                                <div
                                    class="absolute top-0 left-0 h-6 rounded flex items-center justify-center"
                                    style="width: {{ $dados['PERC_ACEITE'] == 0 ? '30px' : $dados['PERC_ACEITE'] . '%' }};
                                            background-color:  {{ $dados['PERC_ACEITE'] == 0 ? '#d1d5db' : '#60a5fa' }};">
                                    <span class="text-sm"
                                          style="color: {{ $dados['PERC_ACEITE'] == 0 ? '#000' : '#fff' }};">{{ $dados['PERC_ACEITE'] }}%</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-left">{{ $dados['OBSERVACAO'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
    </div>
</div>
