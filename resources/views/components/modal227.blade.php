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

                        <div class="flex justify-center gap-5">
                            <div class="row font-bold mb-2"
                                 style="font-size: 14px; display: grid; justify-content: center; align-items: center;">
                                <div class="col-md-12">
                                    <label class="form-label">NUM. VERBA</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $item['NUMVERBA'] }}"
                                           id="numoferta{{ $item['CODFORNEC'] }}"
                                           wire:change="updateValue2('NUMVERBA', $event.target.value)"
                                    >
                                </div>
                                <div class="col-md-12 flex gap-10">
                                    <div>
                                        <label class="form-label">INICIO OFERTA</label>
                                        <input type="date"
                                               class="form-control"
                                               value="{{ $item['INIOFERTA'] }}"
                                               id="inioferta{{ $item['CODFORNEC'] }}"
                                               wire:change="updateValue2('INIOFERTA', $event.target.value)"
                                        >
                                    </div>
                                    <div>
                                        <label class="form-label">FIM OFERTA</label>
                                        <input type="date"
                                               class="form-control"
                                               value="{{ $item['FIMOFERTA'] }}"
                                               id="fimoferta{{ $item['CODFORNEC'] }}"
                                               wire:change="updateValue2('FIMOFERTA', $event.target.value)"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row font-bold mb-2"
                                     style="font-size: 14px; display: grid; justify-content: center; align-items: center;">
                                    <div class="col-md-12">
                                        <label class="form-label"   >OBSERVAÇÃO</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="5" cols="80" maxlength="2000"
                                                  wire:change="updateValue2('DESCRICAO_SUGESTAO', $event.target.value)" >{{ $item['DESCRICAO_SUGESTAO'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php $mostrarFornecedor = false; @endphp <!-- Garante que não será exibido novamente -->
                    @endif
                @endforeach
            @endif
        </div>
        <div id="tabelaAvaliar_component">
            <x-tabela227 :dados="$dados_cursor"/>
        </div>
    </div>
    <div class="modal-footer gap-3">
        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"
                wire:click.prevent="salvar_dados()">Salvar
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
    </div>
</div>
