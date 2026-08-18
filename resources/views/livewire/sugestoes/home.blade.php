<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Cadastros de Produtos</h1>
            <p>Cadastros de Produtos para o sistema de sugestoes</p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="tile">
                    <h3 class="tile-title text-center mb-4">Formulário de Cadastro</h3>
                    <div class="tile-body">
                        <form wire:submit.prevent="buscar" id="formulario">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md">
                                            <label class="form-label">Filial</label>
                                            <select class="form-select" wire:model="codfilial" @if($selectedFilial == 'true') disabled @endif>
                                                @foreach ($pclib_fil as $index => $item)
                                                    @if($index == 0)
                                                        <option value="">Escolha uma Filial</option>
                                                    @endif
                                                    <option value="{{ $item->codigoa }}">FILIAL {{ $item->codigoa }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label class="form-label">Código do Produto</label>
                                            <input
                                                class="form-control"
                                                type="number"
                                                placeholder="Digite o código"
                                                wire:model="codigo"
                                                id="codigo"
                                                autocomplete="off"
                                            >
                                        </div>
                                        <div class="col-md">
                                            <label class="form-label">Nome do Produto</label>
                                            <input
                                                class="form-control"
                                                type="text"
                                                readonly
                                                placeholder="Nome do produto será preenchido"
                                                value="@if($nome) {{ $nome }} | {{$unid}}  @endif"
                                                id="nome_produto"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md">
                                            <label class="form-label">Valor Produto</label>
                                            <input
                                                class="form-control"
                                                type="text"
                                                placeholder="Valor do produto"
                                                readonly
                                                value="{{ $valor }}"
                                                id="valor_produto"
                                                autocomplete="off"
                                            >
                                        </div>
                                        <div class="col-md">
                                            <label class="form-label">Quantidade</label>
                                            <input
                                                class="form-control"
                                                type="number"
                                                step="0.001"
                                                placeholder="Digite a quantidade"
                                                wire:model="quantidade"
                                                id="quantidade"
                                                autocomplete="off"
                                            >
                                        </div>
                                        <div class="col-md">
                                            <label class="form-label">Data de Vencimento</label>
                                            <input
                                                class="form-control"
                                                type="date"
                                                wire:model="data"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center mt-3">
                                    <input
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Adicionar"
                                    >
                                </div>
                            </div>
                        </form>


                        @if (!empty($itens))
                            <h4 class="mt-5 text-center">Itens Cadastrados</h4>
                            <div style="overflow: auto; height: 300px;">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                    <tr class="text-uppercase text-center">
                                        <th>Código</th>
                                        <th>Filial</th>
                                        <th>Nome</th>
                                        <th>Quantidade</th>
                                        <th>Unid</th>
                                        <th>Valor</th>
                                        <th>Data de Vencimento</th>
                                        <th>Código Fornecedor</th>
                                        <th>Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($itens as $index => $item)
                                        <tr class="text-uppercase text-center align-middle">
                                            <td>{{ $item['codigo'] }}</td>
                                            <td>{{ $item['filial'] }}</td>
                                            <td>{{ $item['nome'] }}</td>
                                            <td>{{ $item['quantidade'] }}</td>
                                            <td>{{ $item['unid'] }}</td>
                                            <td>{{ $item['valor'] }}</td>
                                            <td>{{ $item['data'] }}</td>
                                            <td>{{ $item['codfornec']  }}</td>
                                            <td class="flex justify-center gap-3">
                                                <button class="btn btn-primary" wire:click.prevent="editarItem({{ $index }})"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-danger" wire:click.prevent="removerItem({{ $index }})"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-3 pt-3">
                                <button
                                    class="btn btn-outline-danger"
                                    type="button"
                                    wire:click.prevent="limparFormulario"
                                >
                                    <i class="bi bi-eraser"></i> Limpar Formulário
                                </button>
                                <div class="d-flex gap-3">
                                    <button class="btn btn-success" wire:click.prevent="salvarItens" id="span-loading"  onclick="spanLoadingHome();">Salvar Itens</button>
                                    <button class="btn btn-primary" type="button" disabled id="button-loading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Enviando...
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        const STORAGE_KEY_ITENS = 'sugestoes_home_itens';
        const STORAGE_KEY_FILIAL = 'sugestoes_home_codfilial';
        const STORAGE_KEY_SELECTED_FILIAL = 'sugestoes_home_selected_filial';

        function salvarNoStorage(chave, valor) {
            try {
                localStorage.setItem(chave, JSON.stringify(valor));
            } catch (e) {
                // Provavelmente storage cheio (quota excedida) — avisa o usuário sem travar o formulário
                console.error('Não foi possível salvar o rascunho do formulário no navegador:', e);
            }
        }

        // Sempre que a lista de itens (ou a filial selecionada) mudar no servidor,
        // guarda uma cópia no localStorage do navegador
        $wire.$watch('itens', (value) => salvarNoStorage(STORAGE_KEY_ITENS, value));
        $wire.$watch('codfilial', (value) => salvarNoStorage(STORAGE_KEY_FILIAL, value));
        $wire.$watch('selectedFilial', (value) => salvarNoStorage(STORAGE_KEY_SELECTED_FILIAL, value));

        // Ao (re)abrir a página, recarrega o rascunho salvo, se existir
        try {
            const itensSalvos = JSON.parse(localStorage.getItem(STORAGE_KEY_ITENS) || 'null');
            const filialSalva = JSON.parse(localStorage.getItem(STORAGE_KEY_FILIAL) || 'null');
            const selectedFilialSalva = JSON.parse(localStorage.getItem(STORAGE_KEY_SELECTED_FILIAL) || '"false"');

            if ((itensSalvos && itensSalvos.length > 0) || filialSalva) {
                $wire.restaurarItens(itensSalvos || [], filialSalva, selectedFilialSalva || 'false');
            }
        } catch (e) {
            console.error('Não foi possível restaurar o rascunho do formulário salvo no navegador:', e);
        }

        // Quando o formulário é limpo (botão "Limpar Formulário") ou os itens são salvos com sucesso,
        // remove o rascunho do navegador
        $wire.on('formulario-limpo', () => {
            localStorage.removeItem(STORAGE_KEY_ITENS);
            localStorage.removeItem(STORAGE_KEY_FILIAL);
            localStorage.removeItem(STORAGE_KEY_SELECTED_FILIAL);
        });
    </script>
    @endscript
</div>
