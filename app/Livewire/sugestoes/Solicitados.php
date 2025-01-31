<?php

namespace App\Livewire\sugestoes;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;

class Solicitados extends Component
{
    use LivewireAlert, WithPagination;
    public $itensc = [];
    public $itensi = [];
    public $codsug;
    public $codsugitem;
    public $quantidade;
    public $data_vencimento;
    public $nome;
    public $filial;
    public $data_criacao;



    public function mount()
    {

        $vali = false;
        $session = session('bdc_controc');
        $hasCodmod800 = collect($session)->contains(function ($item) {
            return $item->codmod == "800";
        });

        if ($hasCodmod800) {
            $vali = true;
        } else {
            $vali = false;
        }


        $itens = DB::connection('oracle')->select(
            "SELECT  distinct c.codsug,
                             p.nome,
                             TO_CHAR(c.data, 'DD/MM/YYYY HH24:MI:SS') data,
                             c.codfilial,
                             (select count(1) from bdc_sugestoesi@dbl200 i where i.codsug = c.codsug ) as qtd_aguardando
                      FROM   bdc_sugestoesc@dbl200 c,
                             pcempr p
                     WHERE   p.matricula = c.codusuario
                     and c.codusuario = :codusuario
                     order by c.codsug desc ",
            ['codusuario' => auth()->user()->matricula]
        );
        $this->itensc = $itens;
    }

    public function modalOpen($index)
    {
        try {
            $produtos = DB::connection('oracle')->select(
                "SELECT   DISTINCT c.codsug,
                                          e.codprod,
                                          TO_CHAR (c.data, 'DD/MM/YYYY HH24:MI:SS') data,
                                          i.codsugitem,
                                          i.codsug,
                                          i.codauxiliar,
                                          i.descricao,
                                          i.valor_produto,
                                          i.valor_sugerido,
                                          TO_CHAR(i.data_vencimento, 'DD/MM/YYYY') data_vencimento,
                                          i.quantidade,
                                          i.status,
                                          i.unid,
                                          c.codfilial,
                                          p.codauxiliar prod_codauxiliar,
                                          emp.nome
                          FROM   bdc_sugestoesi@dbl200 i,
                                 bdc_sugestoesc@dbl200 c,
                                 pcembalagem e,
                                 pcprodut p,
                                 pcempr emp
                         WHERE       i.codsug = c.codsug
                                 AND e.codauxiliar = i.codauxiliar
                                 AND c.codusuario = emp.matricula
                                 AND p.codprod = e.codprod
                                 AND c.codfilial = e.codfilial
                                 AND c.codsug = :codsug order by i.codsugitem", ['codsug' => $index]
            );
            $this->itensi = $produtos;
            $this->nome = $produtos[0]->nome;
            $this->filial = $produtos[0]->codfilial;
            $this->data_criacao = $produtos[0]->data;
            $this->dispatch('ModalTableAvaliar');
        } catch (\Exception $e) {
            $this->toast('error', 'Erro ao buscar produto!');
        }
    }

    public function editItem($codsug, $codsugitem, $quantidade, $data_vencimento)
    {
        $this->codsug = $codsug;
        $this->codsugitem = $codsugitem;
        $this->quantidade = $quantidade;

        $data_convertida = \DateTime::createFromFormat('d/m/Y', $data_vencimento);
        $this->data_vencimento = $data_convertida ? $data_convertida->format('Y-m-d') : null;

        $this->dispatch('ModalEditItem');
    }

    public function updateItem()
    {
        try {
            $data_convertida = \DateTime::createFromFormat('Y-m-d', $this->data_vencimento);
            $data_vencimento = $data_convertida ? $data_convertida->format('d/m/Y') : null;

            DB::connection('oracle')->update(
                "UPDATE bdc_sugestoesi@dbl200
                    SET quantidade = :quantidade,
                        data_vencimento = TO_DATE(:data_vencimento, 'DD/MM/YYYY')
                  WHERE codsug = :codsug
                    AND codsugitem = :codsugitem",
                [
                    'quantidade' => $this->quantidade,
                    'data_vencimento' => $data_vencimento,
                    'codsug' => $this->codsug,
                    'codsugitem' => $this->codsugitem
                ]
            );

            $this->modalOpen($this->codsug);
            $this->toast('success', 'Item atualizado com sucesso!');
            $this->dispatch('closeModalEditItem');
        } catch (\Exception $e) {
            $this->toast('error', 'Erro ao atualizar item!');
        }
    }

    public function toast($type, $message)
    {
        $this->alert($type, $message, [
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    public function formatMoeda($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public function render()
    {
        return view('livewire.sugestoes.solicitados')->layout('layouts.home-layout');
    }
}
