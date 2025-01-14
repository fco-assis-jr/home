<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Avaliação de Sugestões</h1>
            <p>Aliviando as sugestões cadastradas</p>
        </div>
    </div>

    <div wire:loading wire:target="modalOpen, modalOpenOptions">
        <div style="display: flex; justify-content: center; align-items: center; background-color: black; position: fixed; top: 0; left: 0; z-index: 9999; width: 100%; height: 100%; opacity: 0.75;">
            <p class="loader"></p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="container mt-4" wire:ignore>
            <div class="tile">
                <h3 class="tile-title">Tabela de Sugestões</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="sampleTable">
                        <thead>
                        <tr class="text-uppercase text-center">
                            <th class="text-center">CODSUG</th>
                            <th class="text-center">NOME</th>
                            <th class="text-center">CODFILIAL</th>
                            <th class="text-center">QT ITENS</th>
                            <th class="text-center">DATA CRIAÇÃO</th>
                            <th class="text-center">DATA CRIAÇÃO</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($itensc as $index => $item)
                            <tr class="text-uppercase text-center align-middle cursor-pointer"
                                wire:click="modalOpen({{$item->codsug}})">
                                <td class="text-center">{{ $item->codsug }}</td>
                                <td class="text-center">{{ $item->nome }}</td>
                                <td class="text-center">{{ $item->codfilial }}</td>
                                <td class="text-center">{{ $item->qtd_aguardando }}</td>
                                <td class="text-center">{{ $item->data }}</td>
                                <td class="text-center">{{ $item->data }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Itens -->
    <div class="modal fade backdrop-blur-lg" id="ModalTableAvaliar" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl">
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
                                class="bi bi-calendar4-event"></i> {{ $data_criacao }} </h6>
                    </div>

                    <table class="table table-bordered table-hover  mt-3">
                        <thead>
                        <tr class="text-uppercase text-center">
                            <th>CODFORNEC</th>
                            <th>FORNECEDOR</th>
                            <th>QUANTIDADE</th>
                            <th>PRAZOENTREGA</th>
                            <th>OBSERVACAO</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($cabecario_227_agrupado as $codfornec => $dados)
                            <tr class="text-uppercase text-center align-middle cursor-pointer"
                                wire:click.prevent="modalOpenOptions({{ $dados['CODFORNEC'] }})">
                                <td>{{ $dados['CODFORNEC'] }}</td>
                                <td class="truncate" title="{{ $dados['FORNECEDOR'] }}">
                                    <div style="width: 100%; overflow: auto;">
                                        {{ $dados['FORNECEDOR'] }}
                                    </div>
                                </td>
                                <td>{{ $dados['QUANTIDADE'] }}</td>
                                <td>{{ $dados['PRAZOENTREGA'] }}</td>
                                <td>{{ $dados['OBSERVACAO'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 227 -->
    <div class="modal fade" id="ModalTableAvaliar227" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="w-full flex justify-between pr-10">
                        <h5 class="modal-title" id="staticBackdropLabel" style="font-size: 20px">
                            227 - FICHA TÉCNICA POR FORNECEDOR MASTER
                        </h5>
                        <h5 style="font-size: 20px">
                            {{$data_inicial}} á {{$data_final}}
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        @if(isset($dados_cursor))
                            @php $mostrarFornecedor = true; @endphp

                            @foreach ($dados_cursor as $index => $item)
                                @if ($mostrarFornecedor)
                                    <div class="row font-bold mb-2" style="font-size: 17px">
                                        <div class="col-md-4">
                                            <p class="modal-title" id="exampleModalLabel">
                                                FORNECEDOR: {{ $item['CODFORNEC'] }} - {{ $item['FORNECEDOR'] }}</p>
                                            <p class="modal-title" id="exampleModalLabel">
                                                CONTATO: {{ $item['TELFAB'] }}</p>
                                        </div>
                                        <div class="col-md">
                                            <p class="modal-title" id="exampleModalLabel">PRAZO DE
                                                ENTREGA: {{ $item['PRAZOENTREGA'] }} DIAS</p>
                                            <p class="modal-title" id="exampleModalLabel">
                                                ULT.RFENTE: {{ $item['FRETE'] }}</p>
                                        </div>
                                        <div class="col-md">
                                            <p class="modal-title" id="exampleModalLabel">% DESP
                                                FIN: {{ $item['PERCDESPFIN'] }}</p>
                                            <p class="modal-title" id="exampleModalLabel">PRAZO
                                                PAGAMENTO: {{ $item['DESCPARCELA'] }}</p>
                                        </div>
                                        <div class="col-md">
                                            <p class="modal-title" id="exampleModalLabel">% DESP
                                                FIN: {{ $item['PERCDESCFIN'] }}</p>
                                        </div>
                                    </div>
                                    <div class="row font-bold mb-2" style="font-size: 17px">
                                        <div class="col-md">
                                            <p class="modal-title text-red-600" id="exampleModalLabel">
                                                FILIAL: {{ $item['CODFILIAL'] }}</p>
                                        </div>
                                    </div>
                                    @php $mostrarFornecedor = false; @endphp <!-- Garante que não será exibido novamente -->
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <div class="flex justify-between gap-3">
                        <table class="table table-bordered table-hover  mt-3 border-1 border-black ">
                            <thead>
                            <tr class="text-uppercase text-center">
                                <th>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 flex gap-3 pb-2" style="font-size: 15px;">
                                                <div>
                                                    <span>COD</span>
                                                </div>
                                                <div class="w-full text-left">
                                                    <span>DESCRIÇÃO</span>
                                                </div>
                                                <div>
                                                    <span>VENCIMENTO</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                                                <span>ÚLTIMA ENTRADA</span>
                                            </div>
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span>Dt.Ult.Ent</span>
                                                <span>Valor</span>
                                                <span>Qtde</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-2" style="font-size: 15px;">
                                                <span>QUANTIDADE VENDA MÊS</span>
                                            </div>
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span>Atual</span>
                                                <span>Ant1</span>
                                                <span>Ant2</span>
                                                <span>Ant3</span>
                                            </div>
                                            <div class="col-md-12 text-center" style="font-size: 14px;">
                                                <span>MÉDIA GIRO</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                                                <span>ESTOQUE</span>
                                            </div>
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span>Disp</span>
                                                <span>Fat CD</span>
                                                <span>Ped CD</span>
                                                <span>Dias</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                                                <span>MARGEM PREÇO</span>
                                            </div>
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span>P.Venda</span>
                                                <span>Mg-Atual</span>
                                                <span>Mg-Winthor</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                                                <span>SUGESTÃO</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th style="width: 7%">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                                                <span>VL REEMBOLSO</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th style="width: 7%">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                                                <span>VL OFERTA</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dados_cursor as $index => $item)
                                <tr style="{{ ($item['VL_REEMBOLSO'] <= 0 || $item['VL_OFERTA'] <= 0) ? 'background-color: #f87171;' : '' }}">
                                    <td class="text-uppercase text-center">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 flex gap-3 pb-2" style="font-size: 14px;">
                                                    <div>
                                                        <span>{{ $item['CODPROD'] }}</span>
                                                    </div>
                                                    <div class="w-full text-left">
                                                        <span
                                                            title="{{ $item['DESCRICAO'].' '.$item['EMBALAGEMMASTER'] }}">
                                                            {{ Str::limit($item['DESCRICAO'].' '.$item['EMBALAGEMMASTER'], 30, '...') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 flex justify-between gap-3"
                                                     style="font-size: 14px;">
                                                    <span>{{ $item['CODAUXILIAR'] }}</span>
                                                    <span>{{ $item['ICMS'] }}</span>
                                                    <span class="text-red-600">{{ $item['DATA_VENCIMENTO'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-uppercase text-center ">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 flex justify-between gap-3 pb-2"
                                                     style="font-size: 14px;">
                                                    <span>{{ date('d/m/Y', strtotime($item['DTULTENT'])) }}</span>
                                                    <span>{{ $this->formatMoeda($item['VALORENT']) }}</span>
                                                    <span>{{ $item['QTULTENT'] }}</span>
                                                </div>
                                                <div class="col-md-12 flex justify-between gap-3"
                                                     style="font-size: 14px;">
                                                    <span>Unidade ></span>
                                                    <span>{{ $this->formatMoeda($item['CD_UNIDADE']) }}</span>
                                                    <span class="text-transparent">{{ $item['QTULTENT'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-uppercase text-center">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 flex justify-between gap-3 pb-2"
                                                     style="font-size: 14px;">
                                                    <span>{{ $item['QTVENDMES'] }}</span>
                                                    <span>{{ $item['QTVENDMES1'] }}</span>
                                                    <span>{{ $item['QTVENDMES2'] }}</span>
                                                    <span>{{ $item['QTVENDMES3'] }}</span>
                                                </div>
                                                <div class="col-md-12 flex justify-between gap-3"
                                                     style="font-size: 14px;">
                                                    <span>Dia {{ $item['QTGIRODIA'] }}</span>
                                                    <span>Sem {{ $item['QTGIROSEMANA'] }}</span>
                                                    <span>Mês {{ $item['QTGIROMES'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-uppercase text-center">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 flex justify-between gap-3 pb-2"
                                                     style="font-size: 14px;">
                                                    <span>{{ $item['QTESTGER'] }}</span>
                                                    <span>Fat CD</span>
                                                    <span>Ped CD</span>
                                                    <span>{{ $item['ESTDIAS'] }}</span>
                                                </div>
                                                <div class="col-md-12 flex justify-between gap-3"
                                                     style="font-size: 14px;">
                                                    <span class="text-transparent">CD CX ></span>
                                                    <span class="text-red-600">QT {{ $item['QUANTIDADE'] }}</span>
                                                    <span>CD CX></span>
                                                    <span>{{ $item['CD_CX'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 flex justify-between gap-3"
                                                     style="font-size: 14px;">
                                                    <span>{{ $item['MARGEM_PVENDA'] }}</span>
                                                    <span>{{ $item['MARGEM_ATUAL'] }}</span>
                                                    <span>{{ $item['MARGEM_WINTHOR'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 text-center pb-2" style="font-size: 14px;">
                                                    <span>{{ $item['SUGCOMPRA'] }}</span>
                                                </div>
                                                <div class="col-md-12 flex justify-between gap-3"
                                                     style="font-size: 14px;">
                                                    <span>Pendente</span>
                                                    <span>{{ $item['QTPEDIDA'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $this->formatMoeda($item['VL_REEMBOLSO']) }}"
                                               onkeyup="k(this);"
                                               wire:change="updateValue('{{ $index }}', 'VL_REEMBOLSO', $event.target.value)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $this->formatMoeda($item['VL_OFERTA']) }}"
                                               onkeyup="k(this);"
                                               wire:change="updateValue('{{ $index }}', 'VL_OFERTA', $event.target.value)">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer gap-3">
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"
                            wire:click.prevent="salvar_dados()">Salvar
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function k(i) {
            let value = i.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            value = (parseInt(value || '0') / 100).toFixed(2); // Divide por 100 para incluir as casas decimais
            i.value = formatMoeda(value); // Formata como moeda com R$
        }

        function formatMoeda(value) {
            // Certifique-se de que o valor está formatado mesmo quando estiver vazio
            const val = parseFloat(value || 0) > 200 ? 200 : parseFloat(value || 0);
            return val.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });

        }
    </script>

</div>
