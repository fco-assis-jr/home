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
        <div class="container mt-4">
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
                            <tr class="text-uppercase text-center align-middle cursor-pointer {{ $item->status == 'COMPLETO' ? 'bg-blue-400 text-white' : 'bg-red-400 text-white' }}"
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
            <x-modalFichaFornecedor :dados="$cabecario_227_agrupado" :nome="$nome" :filial="$filial" />
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
