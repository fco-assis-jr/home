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
                    <th style="width: 170px;">STATUS</th>
                    <th>OBSERVACAO</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($cabecario_227_agrupado as $codfornec => $dados)
                    <tr class="text-uppercase text-center align-middle cursor-pointer {{ $dados['ITENS_STATUS'] == 'COMPLETO' ? 'bg-blue-400 text-white' : 'bg-red-400 text-white' }}"
                        wire:click.prevent="modalOpenOptions({{ $dados['CODFORNEC'] }})">
                        <td>{{ $dados['CODFORNEC'] }}</td>
                        <td class="truncate text-left" title="{{ $dados['FORNECEDOR'] }}">
                            <div style="width: 100%;">
                                {{ $dados['FORNECEDOR'] }}
                            </div>
                        </td>
                        <td>{{ $dados['QUANTIDADE'] }}</td>
                        <td class="text-center" >
                            <div class="relative w-full h-5 bg-gray-500 rounded overflow-hidden shadow-md">
                                <!-- Porcentagem fixa no centro -->
                                <span class="absolute inset-0 flex items-center justify-center text-sm font-bold"
                                      style="
                                      color: #fff;
                                      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
                                      z-index: 10;">{{ $dados['PERC_ACEITE'] }}%</span>
                                <!-- Barra de progresso -->
                                <div
                                    class="absolute top-0 left-0 h-full rounded transition-all"
                                    style="
                                    width: {{ $dados['PERC_ACEITE'] == 0 ? '10%' : $dados['PERC_ACEITE'] . '%' }};
                                    background-color: {{ $dados['PERC_ACEITE'] == 0 ? '#a3a3a3' : '#60a5fa' }};
                                    z-index: 5;
                                    ">
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
