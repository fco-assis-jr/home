<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Tipos Ocorrências</h1>
            <p>Cadastros de ocorrências</p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="tile">
                    <div class="table-responsive">
                        <div class="mb-3 flex justify-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="bi bi-bookmark-plus"></i>
                            </button>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-uppercase text-center">
                                <th class="text-center">CODTIPO</th>
                                <th class="text-center">DESCRIÇÃO</th>
                                <th class="text-center">AÇÃO</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($ocorrencias as $index => $item)
                                <tr class="text-uppercase text-center align-middle">
                                    <td class="text-center">{{ $item->codtipo }}</td>
                                    <td class="text-center">{{ $item->descricao }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-warning cursor-pointer" wire:click="AbrirModalEdit({{ $item->codtipo }},'{{ $item->descricao }}')">Editar</span>
                                        <span class="badge bg-danger cursor-pointer" wire:click="excluir({{ $item->codtipo }})">Excluir</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Cadastro -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Cadrato de Ocorrência</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="cadastro">
                                        <div class="mb-3">
                                            <label for="nome">Nome Ocorrência</label>
                                            <input type="text" class="form-control text-uppercase" id="nome" wire:model="ocorrencia" required autocomplete="off">
                                        </div>
                                        <div class="flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="exampleModalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Cadrato de Ocorrência</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="editar">
                                        <div class="mb-3">
                                            <label for="nome">Nome Ocorrência</label>
                                            <input type="text" class="form-control text-uppercase" id="nome" wire:model="descricao" required autocomplete="off">
                                        </div>
                                        <div class="flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Editar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


</div>
