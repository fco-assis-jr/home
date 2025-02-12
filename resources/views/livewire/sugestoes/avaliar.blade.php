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
                                    <div class="relative w-full h-5 bg-gray-500 rounded overflow-hidden shadow-md">
                                        <!-- Porcentagem fixa no centro -->
                                        <span
                                            class="absolute inset-0 flex items-center justify-center text-sm font-bold"
                                            style="
                                            color: #fff;
                                            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
                                            z-index: 10;
                                        ">{{ $item->perc_aceite }}%</span>
                                        <!-- Barra de progresso -->
                                        <div
                                            class="absolute top-0 left-0 h-full rounded transition-all"
                                            style="
                                            width: {{ $item->perc_aceite == 0 ? '15%' : $item->perc_aceite . '%' }};
                                            background-color: {{ $item->perc_aceite == 0 ? '#a3a3a3' : ($item->status == 'COMPLETO' ? '#60a5fa' : '#f87171') }};
                                            z-index: 5;">
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


    <script>
        function filterGlobal(table) {
            let filter = document.querySelector('#global_filter');
            let regex = document.querySelector('#global_regex');
            let smart = document.querySelector('#global_smart');

            table.search(filter.value, regex.checked, smart.checked).draw();
        }

        function filterColumn(table, i) {
            let filter = document.querySelector('#col' + i + '_filter');
            let regex = document.querySelector('#col' + i + '_regex');
            let smart = document.querySelector('#col' + i + '_smart');

            table.column(i).search(filter.value, regex.checked, smart.checked).draw();
        }

        let table = new DataTable('#example', {
            order: [[0, 'desc']],
            scrollCollapse: true,
            scrollY: '45vh',
            layout: {
                topStart: null,
                topEnd: null,
                top: null,
                bottom: 'info',
                bottomStart: 'pageLength',
                bottomEnd: 'paging'
            },
            language: {
                "sEmptyTable": "Nenhum dado disponível na tabela",
                "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas no total)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sSearch": "Buscar:",
                "sZeroRecords": "Nenhum registro encontrado",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior"
                }
            },
        });

        /*document.querySelector('.dt-search').style.display = 'none';*/
        document.querySelectorAll('input.global_filter').forEach((el) => {
            el.addEventListener(el.type === 'text' ? 'keyup' : 'change', () =>
                filterGlobal(table)
            );
        });

        document.querySelectorAll('input.column_filter').forEach((el) => {
            let tr = el.closest('tr');
            let columnIndex = tr.getAttribute('data-column');

            el.addEventListener(el.type === 'text' ? 'keyup' : 'change', () =>
                filterColumn(table, columnIndex)
            );
        });

        document.addEventListener('DOMContentLoaded', () => {
            table.column(6).search('ABERTO').draw();
            document.querySelector('#spanOpen').addEventListener('click', () => {
                table.column(6).search('ABERTO').draw();
            });
        });

        document.querySelector('#spanClose').addEventListener('click', () => {
            table.column(6).search('FECHADO').draw();
        });

        document.querySelector('#spanAll').addEventListener('click', () => {
            table.column(6).search('').draw();
        });
    </script>


</div>
