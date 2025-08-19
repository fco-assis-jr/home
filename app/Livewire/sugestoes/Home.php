<?php

namespace App\Livewire\sugestoes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use mysql_xdevapi\Exception;


class Home extends Component
{
    use LivewireAlert;

    public $codigo;
    public $nome;
    public $valor;
    public $quantidade;
    public $data;
    public $codfornec;
    public $codsecao;
    public $codcategoria;
    public $codprod;
    public $itens = [];
    public $indexEditando = null;
    public $pclib_fil = [];
    public $codfilial;
    public $unid;
    public $edit = false;
    public $selectedFilial = 'false';
    protected $listeners = ['confirmed'];

    public function mount()
    {
        $pclib = DB::connection('oracle')->select(
            "SELECT codigoa
             FROM   pclib
             WHERE  codfunc = ? AND codtabela = 1 order by TO_NUMBER(codigoa)",
            [auth()->user()->matricula]
        );
        $this->pclib_fil = $pclib;
    }

    public function buscar()
    {
        try {
            if (empty($this->codfilial)) {
                $this->toast('error', 'Filial não informada!');
                return;
            }

            if ($this->codigo) {
                $this->buscarProduto($this->codigo);
            } else {
                $this->toast('error', 'Código do produto não Informado!');
                return;
            }
        } catch (Exception $e) {
            $this->toast('error', 'Erro ao buscar o produto!');
            return;
        }
    }

    public function buscarProduto($codigo)
    {
        try {
            if ($this->edit = false) {
                foreach ($this->itens as $item) {
                    if ($item['codigo'] == $codigo) {
                        $this->toast('info', 'Produto já adicionado!');
                        return;
                    }
                }
            }

            $produtos = DB::connection('oracle')->select(
                "SELECT e.codauxiliar,
                        p.descricao,
                        e.ptabela,
                        (select SUBSTR (buscaprecos($this->codfilial,1,$codigo,SYSDATE ), 1,
                        INSTR (buscaprecos($this->codfilial,1,$codigo,SYSDATE ), ';', 1) - 1)
                        from dual) pvenda,
                         e.codfilial,
                         e.unidade,
                         f.codfornec,
                         p.codsec,
                         p.codcategoria,
                         p.codprod
                      FROM       pcembalagem e
                             INNER JOIN
                                 pcprodut p
                             ON e.codprod = p.codprod
                             INNER JOIN pcfornec f
                             ON p.codfornec = f.codfornec
                     WHERE   ( E.CODAUXILIAR = ?
                      OR E.CODPROD = ? )
                         AND E.CODFILIAL = ?
                         AND NVL(E.PVENDA, 0) > 0",
                                [$codigo,$codigo, $this->codfilial]
                            );

            if (empty($produtos)) {
                $this->toast('error', 'Produto não encontrado!');
                return;
            }

            $this->nome = $produtos[0]->descricao;
            $this->valor = 'R$ ' . number_format($produtos[0]->pvenda, 2, ',', '.');
            $this->unid = $produtos[0]->unidade;
            $this->codfornec = $produtos[0]->codfornec;
            $this->codsecao = $produtos[0]->codsec;
            $this->codcategoria = $produtos[0]->codcategoria;
            $this->codprod = $produtos[0]->codprod;
            $this->dispatch('nome-preenchido'); // Dispara um evento para focar no campo de quantidade
            $this->adicionarItem();
            $this->edit = false;

        } catch (Exception $e) {
            $this->toast('error', 'Erro ao buscar o produto!');
            return;
        }
    }

    public function adicionarItem()
    {
        $this->validate([
            'codigo' => 'required',
            'codfilial' => 'required',
            'nome' => 'required',
            'quantidade' => 'required|numeric',
            'valor' => 'required',
            'data' => 'required|date',
            'codfornec' => 'required',
        ]);

        if (is_null($this->indexEditando)) {
            $this->itens[] = [
                'codigo' => $this->codigo,
                'filial' => $this->codfilial,
                'nome' => $this->nome,
                'quantidade' => $this->quantidade,
                'valor' => $this->valor,
                'data' => date_format(date_create($this->data), 'd/m/Y'),
                'unid' => $this->unid,
                'codfornec' => $this->codfornec,
                'codsecao' => $this->codsecao,
                'codcategoria' => $this->codcategoria,
                'codprod' => $this->codprod
            ];
        } else {
            // Edita o item existente
            $this->itens[$this->indexEditando] = [
                'codigo' => $this->codigo,
                'filial' => $this->codfilial,
                'nome' => $this->nome,
                'quantidade' => $this->quantidade,
                'valor' => $this->valor,
                'data' => date_format(date_create($this->data), 'd/m/Y'),
                'unid' => $this->unid,
                'codfornec' => $this->codfornec,
                'codsecao' => $this->codsecao,
                'codcategoria' => $this->codcategoria,
                'codprod' => $this->codprod
            ];
            $this->indexEditando = null;
        }
        $this->selectedFilial = 'true';
        $this->reset(['codigo', 'nome', 'quantidade', 'valor', 'data', 'codfornec', 'codsecao', 'codcategoria', 'codprod']); // Limpa os campos do formulário
        $this->dispatch('NovoItem'); // Dispara um evento para focar no campo de código
    }

    public function editarItem($index)
    {
        // Carrega os dados do item para edição
        $item = $this->itens[$index];
        $this->edit = true;
        $this->codigo = $item['codigo'];
        $this->nome = $item['nome'];
        $this->quantidade = $item['quantidade'];
        $this->valor = $item['valor'];
        $this->data = date('Y-m-d', strtotime(str_replace('/', '-', $item['data'])));
        $this->codfornec = $item['codfornec'];
        $this->codfilial = $item['filial'];
        $this->codsecao = $item['codsecao'];
        $this->codcategoria = $item['codcategoria'];
        $this->codprod = $item['codprod'];
        $this->indexEditando = $index;
    }

    public function removerItem($index)
    {
        $this->alert('warning', 'Você tem certeza que deseja deletar este item?', [
            'toast' => true,
            'timer' => 50000,
            'position' => 'center',
            'timerProgressBar' => true,
            'showCancelButton' => true,
            'showConfirmButton' => true,
            'onCancel' => 'cancelDeletion',
            'onConfirmed' => 'confirmed',
            'data' => ['index' => $index]
        ]);
    }

    public function confirmed($data)
    {
        $index = $data['index'];
        unset($this->itens[$index]);
        $this->itens = array_values($this->itens);
        $this->toast('success', 'Item deletado com sucesso!');
    }

    public function toast($type, $message)
    {
        $this->alert($type, $message, [
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    public function salvarItens()
    {
        try {

            $idsug = DB::connection('oracle')->select(
                'SELECT codsug as id FROM bdc_sugestoesc@dbl200
                         WHERE TRUNC(data) = TRUNC(SYSDATE)
                         AND codfilial = ?
                         AND codusuario = ?
                         AND ROWNUM = 1',
                [$this->codfilial, auth()->user()->matricula]
            );
            if (empty($idsug)) {
                $codsug = DB::connection('oracle')->select('select to_number(bdc_sugestoesc_seq.nextval@dbl200) as id from dual');

                DB::connection('oracle')->insert('insert into bdc_sugestoesc@dbl200 (codsug,codusuario,data,codfilial)
                                            values (? ,?, sysdate, ?)', [$codsug[0]->id, auth()->user()->matricula, $this->codfilial]);
                $idsug=$codsug;
            }

            if (empty($this->itens)) {
                $this->toast('error', 'Nenhum item para salvar!');
                return;
            }

            foreach ($this->itens as $item) {
                $valor_produto = str_replace(['R$ ', '.', ','], ['', '', '.'], $item['valor']);
                DB::connection('oracle')->insert('INSERT INTO bdc_sugestoesi@dbl200
                    (codsugitem, codsug, codauxiliar, descricao,  valor_produto, data_vencimento, quantidade, status, UNID, codfornec, codsec, codcategoria, codprod)
                    VALUES (bdc_sugestoes_seq.NEXTVAL@dbl200, ?, ?, ?, ?, TO_DATE(?, \'DD/MM/YYYY\'), ?, ?, ?, ?, ?, ?, ?)',
                    [
                        $idsug[0]->id,
                        $item['codigo'],
                        $item['nome'],
                        $valor_produto,
                        $item['data'],
                        $item['quantidade'],
                        '0',
                        $item['unid'],
                        $item['codfornec'],
                        $item['codsecao'],
                        $item['codcategoria'],
                        $item['codprod']
                    ]
                );
            }
            $this->toast('success', 'Itens salvos com sucesso!');
            $this->selectedFilial = 'false';
            $this->codfilial = '';
            $this->itens = [];
        } catch (Exception $e) {
            $this->toast('error', 'Erro ao salvar os itens no banco de dados!');
            return;
        }
    }

    public function render()
    {
        return view('livewire.sugestoes.home')->layout('layouts.home-layout');
    }

}
