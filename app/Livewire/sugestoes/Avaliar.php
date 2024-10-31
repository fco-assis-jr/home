<?php

namespace App\Livewire\sugestoes;


use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PDO;
use Illuminate\Support\Facades\Crypt;

class Avaliar extends Component
{
    use LivewireAlert;
    public $itensc = [];
    public $itensi = [];
    public $codprod;
    public $codauxiliar_master;
    public $codfilial;
    public $data_inicial;
    public $data_final;
    public $nome;
    public $filial;
    public $data_criacao;
    public $dados_cursor = [];
    public $codsug;
    public $valor_sugerido;
    public $codsugitem;
    public $status;
    protected $listeners = ['confirmar'];

    public function mount()
    {

        $itens = DB::connection('oracle')->select(
            "SELECT  distinct c.codsug,
                             p.nome,
                             TO_CHAR(c.data, 'DD/MM/YYYY HH24:MI:SS') data,
                             c.codfilial,
                             (select count(1) from bdc_sugestoesi@dbl200 i where i.codsug = c.codsug ) as qtd_aguardando
                      FROM   bdc_sugestoesc@dbl200 c,
                             pcempr p
                     WHERE   p.matricula = c.codusuario order by c.codsug desc"
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
                                          nvl(i.valor_sugerido,0) valor_sugerido,
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
                                 AND c.codsug = :codsug order by i.codsugitem asc",
                ['codsug' => $index] );

            $this->itensi = $produtos;
            $this->nome = $produtos[0]->nome;
            $this->filial = $produtos[0]->codfilial;

            // dd($produtos[0]->codfilial);
            $this->data_criacao = $produtos[0]->data;
            $this->dispatch('ModalTableAvaliar');
        } catch (\Exception $e) {
            $this->toast('error', 'Erro ao buscar produto!');
        }
    }

    public function modalOpenOptions($codigo, $codauxiliar, $codfilial)
    {
        $this->codprod = $codigo;
        $this->codauxiliar_master = $codauxiliar;
        $this->codfilial = $codfilial;
        $this->buscarProdutoRotina();
        $this->dispatch('ModalOptions');
    }

    public function StatusItem($CodSugItem,$Codsug,$CodStatus)
    {

        $this->alert('warning', 'Você tem certeza?', [
            'toast' => true,
            'timer' => 50000,
            'position' => 'center',
            'timerProgressBar' => true,
            'showCancelButton' => true,
            'showConfirmButton' => true,
            'onCancel' => 'cancelDeletion',
            'onConfirmed' => 'confirmar',
            'data' => ['CodSugItem' => $CodSugItem,'Codsug' => $Codsug,'CodStatus' => $CodStatus]
        ]);

    }

    public function confirmar($data)
    {

        try {
            if ($data['CodStatus']==1){
                DB::connection('oracle')->update("UPDATE BDC_SUGESTOESI@DBL200 SET STATUS = 1 WHERE CODSUGITEM = ?", [$data['CodSugItem']]);
            }
            if ($data['CodStatus']==2){
                DB::connection('oracle')->update("UPDATE BDC_SUGESTOESI@DBL200 SET STATUS = 2 WHERE CODSUGITEM = ?", [$data['CodSugItem']]);
            }
            $this->toast('success', 'Confirmado com Sucesso!');
            $this->modalOpen($data['Codsug']);

        }catch (\Exception $e) {
            $this->toast('error', 'Erro ao Alterar Status!');
        }

    }

    public function getStatusBadge($status)
    {
        $badgeClass = match ($status) {
            '0' => 'badge bg-secondary',
            '1' => 'badge bg-primary',
            '2' => 'badge bg-danger',
            default => 'badge bg-light',
        };

        $statusText = match ($status) {
            '0' => 'AGUARDANDO',
            '1' => 'CONFIRMADO',
            '2' => 'REJEITADO',
            default => 'INDEFINIDO',
        };

        return [
            'class' => $badgeClass,
            'text' => $statusText,
        ];
    }

    public function getStyleTable($status)
    {
        return match ($status) {
            '0' => 'table-warning',
            '1' => 'table-primary',
            '2' => 'table-danger',
            default => '',
        };
    }

    public function buscarProdutoRotina()
    {

        DB::beginTransaction();

        $dt_inicial = \DateTime::createFromFormat('Y-m-d', date('Y-m-01'))->format('d/m/Y');
        $dt_final = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y');


        $this->data_inicial = $dt_inicial;
        $this->data_final = $dt_final;


        $dtinicio = $dt_inicial;
        $dtfim =  $dt_final;
        $filialcod = $this->codfilial;
        $prodcod = $this->codprod;

        try {
            $finalResult = [];
            $query = "
                    BEGIN
                        :cursor := bdc_f_sugestoes(
                            :dtinicio,
                            :dtfim,
                            :filialcod,
                            :prodcod
                        );
                    END;
                ";
            $pdo = DB::connection('oracle')->getPdo();
            $stmt = $pdo->prepare($query);

            $stmt->bindParam(':cursor', $cursor, PDO::PARAM_STMT);
            $stmt->bindParam(':dtinicio', $dtinicio);
            $stmt->bindParam(':dtfim', $dtfim);
            $stmt->bindParam(':filialcod', $filialcod, PDO::PARAM_INT);
            $stmt->bindParam(':prodcod', $prodcod, PDO::PARAM_INT);


            $stmt->execute();


            if ($cursor) {

                oci_execute($cursor);

                while ($row = oci_fetch_assoc($cursor)) {
                    $finalResult[] = $row;
                }

                oci_free_statement($cursor);


                if ($finalResult){
                    $this->dados_cursor = $finalResult;
                    $this->dispatch('ModalTableAvaliar227');
                } else {
                    $this->toast('info', 'Nenhum registro encontrado!');
                }
            } else {
                $this->toast('info', 'Nenhum cursor foi retornado.');
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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


    public function updateItem($codsug, $codsugitem, $valor_sugerido)
    {
        try {
            $valor_produto = str_replace(['R$ ', '.', ','], ['', '', '.'], $valor_sugerido);

            if (floatval($valor_produto)==0.0){
                $this->toast('error', 'Valor Sugerido Zerado!');
                return;
            }

            DB::connection('oracle')->update(
                "UPDATE bdc_sugestoesi@dbl200
                    SET
                        valor_sugerido = :valor_sugerido,
                        status = 1
                  WHERE codsug = :codsug
                    AND codsugitem = :codsugitem",
                [
                    'valor_sugerido' => $valor_produto,
                    'codsug' => $codsug,
                    'codsugitem' => $codsugitem
                ]
            );
            $this->modalOpen($codsug);
            $this->toast('success', 'Item atualizado com sucesso!');

        } catch (\Exception $e) {
            $this->toast('error', 'Erro ao atualizar item!');
        }
    }

    public function VisualizarPDF($data){

        $verificar = DB::connection('oracle')->select("SELECT * FROM BDC_SUGESTOESI@DBL200 WHERE STATUS=1 AND CODSUG = ?",[$data]);

        if (empty($verificar)){
            $this->toast('error', 'Aceitar Sugestão!');
            return;
        }

        $url = route('sugestoes.visualizar-pdf', ['itensc' => Crypt::encrypt($data)]);
        $this->dispatch('abrir-nova-aba', ['url' => $url]);

    }

    public function render()
    {
        return view('livewire.sugestoes.avaliar')->layout('layouts.home-layout');
    }
}
