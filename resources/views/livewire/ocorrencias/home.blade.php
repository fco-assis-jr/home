<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Registros de Ocorrências</h1>
            <p>Cadastros de ocorrências</p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="tile">
                    <h3 class="tile-title text-center mb-4">Formulário de Cadastro</h3>
                    <form wire:submit.prevent="cadastrar()" class="flex justify-content-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md mb-3">
                                    <label for="nome">Data de Ocorrência</label>
                                    <input type="date" class="form-control" placeholder="Data de Ocorrência" wire:model="data_ocorrencia">
                                </div>
                                <div class="col-md mb-3">
                                    <label for="nome">Tipo de Ocorrência</label>
                                    <select class="form-select" id="exampleFormControlSelect1" wire:model="tipo_ocorrencia">
                                        <option value="">Selecione um tipo de ocorrência</option>
                                        @foreach ($Tipo_ocorrencias as $index => $item)
                                            <option value="{{ $item->codtipo }}">{{ $item->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md mb-3">
                                    <label for="nome">Filial da Ocorrência</label>
                                    <select class="form-select" id="exampleFormControlSelect1" wire:model="filial">
                                        <option value="">Selecione uma Filial</option>
                                        @foreach ($Filiais as $index => $item)
                                            <option value="{{ $item->codfil }}">{{ $item->nomfil }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md mb-3">
                                    <label for="nome">Valor da Ocorrência</label>
                                    <input type="text" id="valor_ocorrencia" class="form-control" wire:model="valor_ocorrencia" onkeyup="formatarMoedaH(this)">
                                </div>
                                <div class="col-md mb-3">
                                    <label for="nome">Número de Transação</label>
                                    <input type="number" class="form-control" wire:model="numero_transacao">
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
                            </div>
                            <div class="grid justify-content-center text-center mb-4">
                                <div class="input-file-container mb-2">
                                    <input class="input-file w-full" id="my-file" type="file" multiple wire:model="files">
                                    <label tabindex="0" for="my-file" class="input-file-trigger w-full">Selecione um arquivo...</label>
                                </div>
                                @if(empty(!$files))
                                    <p>
                                        <span id="accepted-files">{{ count($files) }} arquivo(s) selecionado(s)</span>
                                    </p>
                                @endif
                            </div>
                            <div class="row flex justify-content-center">
                                <div class="col-md-6 mb-3">
                                    <textarea class="form-control" style="text-align: justify" placeholder="Observações da Ocorrência" wire:model="observacoes" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const input = document.querySelector('#my-file');
        const acceptedFiles = document.querySelector('#accepted-files');

        input.addEventListener('change', function (e) {
            const files = e.target.files;
            let acceptedCount = 0;
            let rejectedCount = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.match('image.*') || file.type === 'application/pdf') {
                    acceptedCount++;
                } else {
                    rejectedCount++;
                }
            }

            acceptedFiles.textContent = `QT de ${acceptedCount} arquivos`;

            // Alerta caso existam arquivos rejeitados
            if (rejectedCount > 0) {
                // Não limpe o input se houver arquivos válidos
                if (acceptedCount === 0) {
                    input.value = ''; // Reseta o input se todos forem rejeitados
                }
            }
        });

        function formatarMoedaH(input) {
            let valor = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos
            valor = (parseInt(valor) / 100).toFixed(2); // Divide por 100 para transformar centavos em decimais
            input.value = 'R$ ' + valor
                .replace('.', ',') // Substitui ponto por vírgula para o formato brasileiro
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Adiciona pontos como separador de milhar
            input.dispatchEvent(new Event('input'));
        }


    </script>
</div>
