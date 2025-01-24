<div>
    <div wire:loading wire:target="filtrar">
        <div
            style="display: flex; justify-content: center; align-items: center; background-color: black; position: fixed; top: 0; left: 0; z-index: 99999999; width: 100%; height: 100%; opacity: 0.75;">
            <p class="loader"></p>
        </div>
    </div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Relatórios</h1>
            <p>Relatórios de sugestões por filtros</p>
        </div>
    </div>

    <section class="login-content w-full" style="justify-content: flex-start; min-height: 0 !important;">
        <div class="login-box w-full" id="login-box" wire:ignore.self>

            <div class="login-form">
                <div class=" p-5">
                    <h3 class="tile-title text-center pb-4">ESCOLHA UM FILTRO</h3>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
                        <button wire:click="filtrar('codsec','descricao', 'pcsecao', 'SEÇÃO')"
                                class="btn btn-danger w-full"
                                onclick="ShowSelect('SEÇÃO')">SEÇÃO
                        </button>
                        <button wire:click="filtrar('codcategoria','categoria', 'pccategoria', 'CATEGORIA')"
                                class="btn btn-danger w-full"
                                onclick="ShowSelect('CATEGORIA')">CATEGORIA
                        </button>
                        <button wire:click="filtrar('codfornec','fornecedor', 'pcfornec', 'FORNECEDOR')"
                                class="btn btn-danger w-full"
                                onclick="ShowSelect('FORNECEDOR')">FORNECEDOR
                        </button>
                        <button wire:click="filtrar('codauxiliar','descricao', 'bdc_sugestoesi@dbl200', 'CODAUXILIAR')"
                                class="btn btn-danger w-full"
                                onclick="ShowSelect('CODAUXILIAR','descricao')">CODAUXILIAR
                        </button>
                        <button wire:click="filtrar('codprod', 'descricao', 'bdc_sugestoesi@dbl200', 'CODPROD')"
                                class="btn btn-danger w-full"
                                onclick="ShowSelect('CODPROD')">CODPROD
                        </button>
                    </div>

                    <div style="{{ !empty($selected) ? 'display: block;' : 'display: none;' }}">
                        <div id="select" class="pt-5 flex justify-center w3-animate-opacity"
                             style="transition: 0.9s;">
                            <select class="form-control" id="secao" style="width: 40%;"
                                    wire:change="updateValue($event.target.value)">
                                <option id="optionName" wire:ignore></option>
                                @foreach($selected as $secao)
                                    <option value="{{ $secao->id }}">{{ $secao->id }} | {{ $secao->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-center p-4">
                            <button wire:click="gerarRelatorio"
                                    {{$buttonDisabled ? 'disabled' : ''}} class="btn btn-primary">
                                GERAR RELATÓRIO
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="forget-form">
                <div>
                    <h3 class="tile-title">DADOS FILTRADOS</h3>
                    <h5 class="text-center mb-3">{{ $tabela }}: {{ $descselecionado }}</h5>
                </div>
                <div class="table-containerDados">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr class="text-uppercase text-center">
                            <th class="text-center">CODSUG</th>
                            <th class="text-center">{{ $tabela }}</th>
                            <th class="text-center">DATA CRIAÇÃO</th>
                            <th class="text-center">FILIAL</th>
                            <th class="text-center">QT ITENS</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($dados_filtrados as $index => $item)
                            <tr class="text-uppercase text-center align-middle cursor-pointer" wire:key="{{ $index }}"
                                wire:click="OpenPDF({{ $item->codsug }}, {{ $item->tabela }}, '{{ $item->data }}', {{ $item->codfilial }})">
                                <td class="text-center">{{ $item->codsug }}</td>
                                <td class="text-center">{{ $item->tabela }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->data)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $item->codfilial }}</td>
                                <td class="text-center">{{ $item->quantidade }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end pt-2">
                    <button class="btn btn-secondary" type="button" onclick="removeFlipped()">VOLTAR</button>
                </div>
            </div>

        </div>
    </section>

    <script>

        function removeFlipped() {
            $('.login-box').removeClass('flipped');
            let div = document.getElementById('login-box');
            div.style.minHeight = '400px';
            return false;
        }

        function ShowSelect(value) {
            let checkExist = setInterval(() => {
                let option = document.getElementById("optionName");

                if (option) {
                    clearInterval(checkExist); // Para a verificação quando o elemento existir

                    if (value === "SEÇÃO" || value === "CATEGORIA") {
                        option.innerHTML = "SELECIONE UMA " + value;
                        option.selected = true;
                    } else {
                        option.innerHTML = "SELECIONE UM " + value;
                        option.selected = true;
                    }
                }
            }, 100); // Verifica a cada 100ms
        }
    </script>

</div>
