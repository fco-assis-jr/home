<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Avaliação de Sugestões</h1>
            <p>Aliviando as sugestões cadastradas</p>
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
                    <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-table"></i> Detalhes da Sugestão</h5>
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
                    <table class="table table-bordered table-hover table-dark mt-3">
                        <thead>
                        <tr class="text-uppercase text-center">
                            <th>CODPROD</th>
                            <th>NOME</th>
                            <th>CODAUXILIAR</th>
                            <th>QUANTIDADE</th>
                            <th>VALOR SUGERIDO</th>
                            <th>DATA VENCIMENTO</th>
                            <th>STATUS</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($itensi as $index => $item)
                            <tr class="text-uppercase text-center align-middle {{ isset($item->status) ? $this->getStyleTable($item->status) : '' }}">
                                <td>{{ $item->codprod }}</td>
                                <td class="truncate" title="{{ $item->descricao }} | {{ $item->unid }}">
                                    <div style="width: 100%; overflow: auto;">
                                        {{ $item->descricao }} | {{ $item->unid }}
                                    </div>
                                </td>
                                <td>{{ $item->codauxiliar }}</td>
                                <td>{{ $item->quantidade }}</td>
                                <td id="valor_sugerido">
                                    {{ $this->formatMoeda($item->valor_sugerido ? $item->valor_sugerido : 0) }}
                                </td>
                                <td>{{ $item->data_vencimento }}</td>
                                <td>
                                    @php
                                        $statusInfo = $this->getStatusBadge($item->status);
                                    @endphp
                                    <span class="{{ $statusInfo['class'] }} w-full"
                                          style="font-size: 12px">{{ $statusInfo['text'] }}</span>
                                </td>
                                <td class="flex justify-content-evenly gap-3">
                                    <span
                                        class="badge bg-danger cursor-pointer"
                                        style="padding: 10px; display: flex; align-items: center"
                                        wire:click.prevent="StatusItem({{$item->codsugitem}},{{$item->codsug}},2)"
                                    >
                                         Rejeitar
                                    </span>

                                    <span
                                        class="badge bg-success cursor-pointer"
                                        style="padding: 10px; display: flex; align-items: center"
                                        onClick="toggleEdit(this, {{$item->codsug}}, {{$item->codsugitem}})"
                                    >
                                        Aceitar
                                    </span>

                                    <span
                                        class="badge bg-secondary cursor-pointer" style="padding: 10px;"
                                        id="span-loading"
                                        wire:click="modalOpenOptions({{$item->codprod}} , {{$item->prod_codauxiliar}}, {{ $item->codfilial }})"
                                        onclick="spanLoading();"
                                    >
                                        Analisar
                                    </span>
                                    <button class="badge bg-secondary" type="button" disabled id="button-loading"
                                            style="display: none;">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                              aria-hidden="true"></span>
                                        <span class="visually-hidden">Loading...</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="modal-footer">
                    @if(isset($item))
                        <button type="button" class="btn btn-secondary"
                                wire:click.prevent="VisualizarPDF({{ $item->codsug }})">Imprimir
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    @endif
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
                            @foreach ($dados_cursor as $index => $item)
                                <div class="row font-bold mb-2" style="font-size: 17px">
                                    <div class="col-md-4">
                                        <p class="modal-title" id="exampleModalLabel">
                                            FORNECEDOR: {{ $item['CODFORNEC'] }} - {{ $item['FORNECEDOR'] }}</p>
                                        <p class="modal-title" id="exampleModalLabel">CONTATO: {{ $item['TELFAB'] }}</p>
                                    </div>
                                    <div class="col-md">
                                        <p class="modal-title" id="exampleModalLabel">PRAZO DE
                                            ENTREGA: {{ $item['PRAZOENTREGA'] }} DIAS</p>
                                        <p class="modal-title" id="exampleModalLabel">
                                            ULT.RFENTE: {{ $item['FRETE'] }} </p>
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
                            @endforeach
                        @endif
                    </div>
                    <div class="flex justify-between gap-3">
                        <table class="table table-bordered table-hover  mt-3 border-1 border-black ">
                            <thead>
                            <tr class="text-uppercase text-center">
                                <th style="width: 23%">
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
                                                    <span>CODFAB</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th style="width: 15%">
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
                                <th style="width: 20%">
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
                                <th style="width: 15%">
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
                                <th style="width: 15%">
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
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dados_cursor as $index => $item)
                                <td class="text-uppercase text-center">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 flex gap-3 pb-2" style="font-size: 14px;">
                                                <div>
                                                    <span>{{ $item['CODPROD'] }}</span>
                                                </div>
                                                <div class="w-full text-left">
                                                    <span>{{ $item['DESCRICAO'].' '.$item['EMBALAGEMMASTER'] }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span>{{ $item['CODAUXILIAR'] }}</span>
                                                <span>{{ $item['ICMS'] }}</span>
                                                <span>{{ $item['CODFAB'] }}</span>
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
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
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
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
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
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span class="text-transparent">CD CX ></span>
                                                <span class="text-transparent">CD CX ></span>
                                                <span>CD CX></span>
                                                <span>{{ $item['CD_CX'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
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
                                            <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                                                <span>Pendente</span>
                                                <span>{{ $item['QTPEDIDA'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(isset($dados_cursor))
                        @foreach ($dados_cursor as $index => $item)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row font-bold" style="font-size: 17px">
                                        <div class="col-md-12 pb-2 flex gap-5 text-left">
                                            <span>FONECEDOR BLOQUEADO: {{ $item['BLOQUEIO'] }}</span>
                                            <span>DATA BLOQUEIO: {{ $item['DTBLOQUEIO'] }}</span>
                                        </div>
                                        <div class="col-md-12 pb-4 gap-5 text-left">
                                            <span>OBSERVACAO: {{ $item['OBSERVACAO'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit(button, codsug, codsugitem) {
            const row = button.closest("tr");
            const valorSugeridoCell = row.querySelector("#valor_sugerido");

            if (row.classList.contains("editing")) {
                // Captura o valor do input e substitui o conteúdo do <td> com o valor
                const valorInput = valorSugeridoCell.querySelector("input").value;
                valorSugeridoCell.innerHTML = valorInput;

                // Chama a função do Livewire para atualizar o valor
            @this.call('updateItem', codsug, codsugitem, valorInput)
                ;

                // Alterna o modo de exibição
                row.classList.remove("editing");
                button.textContent = "Aceitar";
            } else {

                const valorSugeridoAtual = valorSugeridoCell.textContent.trim();
                valorSugeridoCell.innerHTML = `
                    <input
                        name="valor_sugerido"
                        value="${valorSugeridoAtual}"
                        oninput="formatarMoeda(this)"
                        onkeydown="handleKeyPress(event, this, ${codsug}, ${codsugitem})"
                    >
                `;
                const input = valorSugeridoCell.querySelector("input");
                input.focus();
                input.setSelectionRange(input.value.length, input.value.length);

                row.classList.add("editing");
                button.textContent = "Salvar";
            }
        }

        function handleKeyPress(event, input, codsug, codsugitem) {
            if (event.key === 'Enter') {
                event.preventDefault();

                const valorInput = input.value;
                const valorSugeridoCell = input.closest("td");
                valorSugeridoCell.innerHTML = valorInput;

            @this.call('updateItem', codsug, codsugitem, valorInput)
                ;

                const row = input.closest("tr");
                if (row) {
                    row.classList.remove("editing");
                    const button = row.querySelector(".badge");
                    if (button) {
                        button.textContent = "Aceitar";
                    }
                }
            }
        }
    </script>

</div>
