<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Avaliação de Sugestões</h1>
            <p>Aliviando as sugestões cadastradas</p>
        </div>
    </div>

    <div wire:loading wire:target="modalOpen, modalOpenOptions, salvar_dados">
        <div
            style="display: flex; justify-content: center; align-items: center; background-color: black; position: fixed; top: 0; left: 0; z-index: 99999999; width: 100%; height: 100%; opacity: 0.75;">
            <p class="loader"></p>
        </div>
    </div>

    <div class="row justify-content-center" wire:ignore>
        <div class="container">
            <div class="tile">
                <h3 class="tile-title text-center">TABELA DE SUGESTÕES</h3>
                <div class="table-responsive">
                    <table style="width: 100%;">
                        <thead>
                        </thead>
                        <tbody class="filtrosTable">
                        <tr style=" height: 0px;" id="filter_col1" data-column="0">
                            <td class="text-uppercase font-bold">Codsug</td>
                            <td><input type="text" class="column_filter form-control h-6" id="col0_filter"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col0_regex"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col0_smart" checked="checked"></td>
                        </tr>
                        <tr style=" height: 0px;" id="filter_col2" data-column="1">
                            <td class="text-uppercase font-bold">Nome</td>
                            <td><input type="text" class="column_filter form-control h-6" id="col1_filter"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col1_regex"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col1_smart" checked="checked"></td>
                        </tr>
                        <tr style=" height: 0px;" id="filter_col4" data-column="3">
                            <td class="text-uppercase font-bold">Codfilial</td>
                            <td><input type="text" class="column_filter form-control h-6" id="col3_filter"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col3_regex"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col3_smart" checked="checked"></td>
                        </tr>
                        <tr style=" height: 0px;" id="filter_col5" data-column="4">
                            <td class="text-uppercase font-bold">Qt itens</td>
                            <td><input type="text" class="column_filter form-control h-6" id="col4_filter"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col4_regex"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col4_smart" checked="checked" autocomplete="off"></td>
                        </tr>
                        <tr style=" height: 0px;" id="filter_col6" data-column="5">
                            <td class="text-uppercase font-bold">Data</td>
                            <td><input type="text" class="column_filter form-control h-6" id="col5_filter"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col5_regex"></td>
                            <td style="display: none"><input type="checkbox" class="column_filter form-control h-6"
                                                             id="col5_smart" checked="checked"></td>
                        </tr>
                        <tr>
                            <td class="flex align-items-center justify-center gap-10" style="height: 25px;">
                                <span class="text-red-400 font-bold hover:text-white cursor-pointer" title="ABERTAS"
                                      id="spanOpen"><i
                                        class="bi bi-circle-fill text-lg"></i></span>
                                <span class="text-blue-400 font-bold hover:text-white cursor-pointer" title="FECHADO"
                                      id="spanClose"><i
                                        class="bi bi-circle-fill text-lg"></i></span>
                                <span class="text-white font-bold hover:text-white cursor-pointer" title="TODOS"
                                      id="spanAll"><i
                                        class="bi bi-circle-fill text-lg"></i></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-dark table-bordered table-hover" id="example">
                        <thead>
                        <tr class="text-uppercase text-center">
                            <th class="text-center">CODSUG</th>
                            <th class="text-center">NOME</th>
                            <th class="text-center">STATUS%</th>
                            <th class="text-center">CODFILIAL</th>
                            <th class="text-center">QT ITENS</th>
                            <th class="text-center">DATA CRIAÇÃO</th>
                            <th class="d-none">STATUS</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($itensc as $index => $item)
                            <tr class="text-uppercase text-center align-middle cursor-pointer {{ $item->status == 'COMPLETO' ? 'bg-blue-400 text-white' : 'bg-red-400 text-white' }}"
                                wire:click="modalOpen({{$item->codsug}})">
                                <td class="text-center"
                                    style="color: {{ $item->status == 'COMPLETO' ? '#60a5fa' : '#f87171' }}">{{ $item->codsug }}</td>
                                <td class="text-center"
                                    style="color: {{ $item->status == 'COMPLETO' ? '#60a5fa' : '#f87171' }}">{{ $item->nome }}</td>
                                <td class="text-center">
                                    <div class="relative w-full h-6 bg-gray-200 rounded">
                                        <div
                                            class="absolute top-0 left-0 h-6 rounded flex items-center justify-center"
                                            style="width: {{ $item->perc_aceite == 0 ? '30px' : $item->perc_aceite . '%' }};
                                            background-color: {{ $item->perc_aceite == 0 ? '#d1d5db' : ($item->status == 'COMPLETO' ? '#60a5fa' : '#f87171') }};">
                                            <span class="text-sm" style="color: {{ $item->perc_aceite == 0 ? '#000' : '#fff' }};">{{ $item->perc_aceite }}%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"
                                    style="color: {{ $item->status == 'COMPLETO' ? '#60a5fa' : '#f87171' }}">{{ $item->codfilial }}</td>
                                <td class="text-center"
                                    style="color: {{ $item->status == 'COMPLETO' ? '#60a5fa' : '#f87171' }}">{{ $item->qtd_aguardando }}</td>
                                <td class="text-center"
                                    style="color: {{ $item->status == 'COMPLETO' ? '#60a5fa' : '#f87171' }}">{{ $item->data }}</td>
                                <td class="text-center d-none">{{ $item->status == 'COMPLETO' ? 'FECHADO' : 'ABERTO' }}</td>
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
            <x-modalFichaFornecedor :dados="$cabecario_227_agrupado" :nome="$nome" :filial="$filial"/>
        </div>
    </div>

    <!-- Modal 227 -->
    <div class="modal fade" id="ModalTableAvaliar227" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <x-modal227 :dados="$dados_cursor"/>
        </div>
    </div>


</div>
