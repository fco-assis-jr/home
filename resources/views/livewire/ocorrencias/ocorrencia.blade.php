<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Registros das Ocorrências</h1>
            <p>Listando os Registros das Ocorrências</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="container mt-4" wire:ignore>
            <div class="tile">
                <table class="table table-bordered table-hover" id="sampleTable">
                    <thead>
                    <tr class="text-center text-uppercase">
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Filial</th>
                        <th>Registro</th>
                        <th>Data da Ocorrência</th>
                        <th>Número da Transação</th>
                        <th>Funcionário</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($ocorrencias as $index => $item)
                        <tr class="text-center cursor-pointer" wire:click="abrirModal({{ $item->id }})">
                            <td class="text-center">{{ $item->id }}</td>
                            <td class="text-center">{{ $item->nome_usuario }}</td>
                            <td class="text-center">{{ $item->filial }}</td>
                            <td class="text-center">{{ $item->tipo_registro }}</td>
                            <td class="text-center">{{ $item->data }}</td>
                            <td class="text-center">{{ $item->numero_transacao }}</td>
                            <td class="text-center">{{ $item->nome_func }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div >

    <!-- Modal Ocorrencia -->
    <div class="modal fade" id="ModalOcorrencia" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Detalhe da Ocorrência</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($ModalOcorrencia)
                        <div class="mb-2">
                            <textarea class="form-control" style="text-align: justify" rows="10">{{ $ModalOcorrencia[0]->descricao }}</textarea>
                        </div>
                        <div class="grid">
                            <span>Data criação: {{ $ModalOcorrencia[0]->data_criacao }}</span>
                            <span>Usuário criação: {{ $ModalOcorrencia[0]->nome_usuario }}</span>
                            <span>Número da Transação: {{ $ModalOcorrencia[0]->numero_transacao }}</span>
                        </div>
                        <div class="flex justify-center gap-4 pt-2">
                            @foreach($imagem as $index => $item)
                                @php
                                    $fileExtension = pathinfo($item->file_name, PATHINFO_EXTENSION);
                                @endphp
                                <a href="{{ asset('storage/ocorrencia_files/'.$item->file_name) }}" target="_blank">
                                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                        <i class="bi bi-image" style="font-size: 50px;"></i>
                                    @elseif(strtolower($fileExtension) === 'pdf')
                                        <i class="bi bi-file-earmark-pdf" style="font-size: 50px;"></i>
                                    @else
                                        <i class="bi bi-file-earmark" style="font-size: 50px;"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer flex justify-content-between">
                    <button type="button" class="btn btn-info" wire:click="OpenDuplicarModal">Duplicar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Duplicar Ocorrencia -->
    <div class="modal fade" id="ModalDuplicarOcorrencia" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Duplicar Ocorrência</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Selecione a ocorrência para ser duplicada</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md mb-3">
                                                    <label for="nome">Tipo de Ocorrência</label>
                                                    <select class="form-select" id="exampleFormControlSelect1" wire:model="duplicTipo">
                                                        <option value="">Selecione um tipo de ocorrência</option>
                                                        @foreach ($tipo_registro as $index => $item)
                                                            <option value="{{ $item->codtipo }}">{{ $item->descricao }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md mb-3">
                                                    <label for="nome">Funcionário</label>
                                                    <input type="text" class="form-control" wire:model="search" wire:input="matriculas" autocomplete="off">
                                                    <ul class="list-group mt-2 position-absolute z-40">
                                                        @foreach ($func as $index => $item)
                                                            <li class="list-group-item cursor-pointer hover:bg-gray-200 rounded-md p-2" wire:click="selectUser('{{ $item->nome }}', {{ $item->matricula }})">
                                                                {{ $item->nome }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-md mb-3">
                                                    <label for="ocorrencia">Descrição da Ocorrência</label>
                                                    <textarea class="form-control" rows="10" id="ocorrencia" wire:model="duplicDescricao"></textarea>
                                                </div>
                                                <div class="modal-footer flex justify-content-between">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                    <button type="button" class="btn btn-secondary" wire:click="cadastrar">Enviar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
