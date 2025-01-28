<div>
    <style>
        /* Estilo para o campo de autocomplete */
        #autocompleteInput {
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        #autocompleteInput:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        #autocompleteResults {
            position: absolute;
            z-index: 1000;
            max-height: 250px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
            width: 100%;
        }

        #autocompleteResults li {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease;
        }

        #autocompleteResults li:hover, #autocompleteResults li.active {
            background-color: #f8f9fa;
        }
    </style>

    <div class="app-title">
        <h1><i class="bi bi-speedometer"></i> Gestão de Permissões</h1>
        <p>Selecione os módulos e controles para conceder permissões:</p>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="tile">
                    <h3 class="tile-title text-center mb-4">Permissões</h3>
                    <div class="tile-body">
                        <!-- Campo de Autocomplete -->
                        <div class="relative">
                            <input
                                    type="text"
                                    id="autocompleteInput"
                                    wire:model.debounce.500ms="query"
                                    class="form-input w-full"
                                    placeholder="Digite para buscar..."
                                    onfocus="BuscaResultado()"
                                    oninput="BuscaResultado()"
                            />
                            <!-- Resultados -->
                            <ul id="autocompleteResults" class="hidden" wire:ignore.self>
                                @foreach ($results as $result)
                                    <li onclick="SelecionarResultado('{{ $result->matricula }}', '{{ $result->display }}')">
                                        {{ $result->display }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Botões de Marcar/Desmarcar Todos -->
                        <div class="my-3">
                            <button id="markAll" class="btn btn-primary btn-sm">Marcar Todos</button>
                            <button id="unmarkAll" class="btn btn-secondary btn-sm">Desmarcar Todos</button>
                        </div>

                        <!-- Tabela de Permissões -->
                        <table id="permissionsTable" class="table table-bordered mt-4">
                            <thead>
                            <tr>
                                <th>Módulo / Controle</th>
                                <th>Permissão</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($modules as $module)
                                <tr class="module-row">
                                    <td>
                                        <strong>[{{ $module['codmod'] }}]</strong> {{ $module['descricao'] }}
                                    </td>
                                    <td>
                                        <!-- Checkbox de permissão para o módulo -->
                                        <input type="checkbox" class="form-check-input module-checkbox"
                                               id="module-{{ $module['codmod'] }}"
                                               data-module="{{ $module['codmod'] }}"
                                                {{ $module['modulo_acesso'] ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                @foreach (collect($module['controles'])->sortBy('codcontrole') as $controle)
                                    @if ($controle['codcontrole'] != 0 && $controle['controle_descricao'] !== 'SEM CONTROLE')
                                        <tr class="sub-item-row" data-parent="{{ $module['codmod'] }}">
                                            <td class="ps-4">
                                                <strong>[{{ $controle['codcontrole'] }}
                                                    ]</strong> {{ $controle['controle_descricao'] }}
                                            </td>
                                            <td>
                                                <!-- Checkbox de permissão para o controle -->
                                                <input type="checkbox" class="form-check-input control-checkbox"
                                                       id="control-{{ $module['codmod'] }}-{{ $controle['codcontrole'] }}"
                                                       data-control="{{ $controle['codcontrole'] }}"
                                                       data-parent="{{ $module['codmod'] }}"
                                                        {{ $controle['controle_acesso'] ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center mt-4">
                            <button id="savePermissions" class="btn btn-success">Salvar Permissões</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Busca os resultados no campo de autocomplete
        function BuscaResultado() {
            const input = document.getElementById('autocompleteInput');
            const results = document.getElementById('autocompleteResults');

            if (input.value.length > 2) {
            @this.search()
                ; // Chama o método search no Livewire
                results.classList.remove('hidden'); // Exibe os resultados
            } else {
                results.classList.add('hidden'); // Esconde os resultados
            }
        }

        // Quando o usuário seleciona um resultado no autocomplete
        function SelecionarResultado(matricula, display) {
            const input = document.getElementById('autocompleteInput');
            const results = document.getElementById('autocompleteResults');

            input.value = display; // Atualiza o campo de entrada com o nome selecionado
            results.classList.add('hidden'); // Esconde os resultados

            // Envia a matrícula selecionada para carregar permissões
        @this.call('loadPermissions', parseInt(matricula)).then(modules => {
            AtualizarCheckbox(modules); // Atualiza os checkboxes com os dados retornados
        })
            ;
        }

        // Atualiza os checkboxes com base nos dados retornados
        function AtualizarCheckbox(modules) {
            console.log('Modules recebidos para atualizar checkboxes:', modules);

            // Primeiro, desmarca todos os checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false; // Desmarca todos
            });

            // Atualiza os checkboxes com base nos dados recebidos
            modules.forEach(module => {
                // Marcar/desmarcar o checkbox do módulo
                const moduleCheckbox = document.getElementById(`module-${module.codmod}`);
                if (moduleCheckbox) {
                    moduleCheckbox.checked = module.modulo_acesso; // Marca se o módulo tem acesso
                }

                // Marcar/desmarcar os checkboxes dos controles
                module.controles
                    .sort((a, b) => a.codcontrole - b.codcontrole) // Ordena os controles
                    .forEach(control => {
                        // Verifica se o codcontrole é válido
                        if (control.codcontrole && control.codcontrole !== 0) {
                            const controlCheckbox = document.getElementById(`control-${module.codmod}-${control.codcontrole}`);
                            if (controlCheckbox) {
                                controlCheckbox.checked = control.controle_acesso; // Marca se o controle tem acesso
                            } else {
                                console.warn(`Checkbox não encontrado para controle ID: ${module.codmod}-${control.codcontrole}`);
                            }
                        }
                    });
            });
        }

        // Marcar todos os checkboxes
        document.getElementById('markAll').addEventListener('click', function () {
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        // Desmarcar todos os checkboxes
        document.getElementById('unmarkAll').addEventListener('click', function () {
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Salvar permissões
        document.getElementById('savePermissions').addEventListener('click', function () {
            const permissions = {};

            // Iterar pelos módulos
            document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
                const moduleId = moduleCheckbox.getAttribute('data-module');

                permissions[moduleId] = {
                    codmodulo: moduleId,
                    modulo_acesso: moduleCheckbox.checked,
                    controles: {}
                };

                // Iterar pelos controles pertencentes ao módulo
                document.querySelectorAll(`.control-checkbox[data-parent="${moduleId}"]`).forEach(controlCheckbox => {
                    const controlId = controlCheckbox.getAttribute('data-control');
                    permissions[moduleId].controles[controlId] = controlCheckbox.checked;
                });
            });

            console.log('Dados para salvar:', permissions);

        @this.call('savePermissions', permissions)
            .then(() => {

            })
            .catch(error => {

            })
            ;
        });
    </script>
</div>
