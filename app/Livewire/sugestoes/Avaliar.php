<?php

namespace App\Livewire\sugestoes;


use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PDO;

class Avaliar extends Component
{
    use LivewireAlert;

    public $itensc = [];
    public $itensi = [];
    public $codfilial;
    public $data_inicial;
    public $data_final;
    public $nome;
    public $filial;
    public $data_criacao;
    public $status;
    public $cabecario_227_agrupado = [];
    public $dados_cursor = [];
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
                                      emp.nome,
                                      f.codfornec,
                                      f.fornecedor fornecedor,
                                      i.vl_reembolso,
                                      i.vl_oferta
                      FROM   bdc_sugestoesi@dbl200 i,
                             bdc_sugestoesc@dbl200 c,
                             pcembalagem e,
                             pcprodut p,
                             pcempr emp,
                             pcfornec f
                     WHERE       i.codsug = c.codsug
                             AND e.codauxiliar = i.codauxiliar
                             AND c.codusuario = emp.matricula
                             AND p.codprod = e.codprod
                             AND c.codfilial = e.codfilial
                             AND f.codfornec = i.codfornec
                             AND c.codsug = :codsug order by i.codsugitem asc",
                ['codsug' => $index]
            );

            $this->nome = $produtos[0]->nome;
            $this->filial = $produtos[0]->codfilial;
            $this->data_criacao = $produtos[0]->data;
            $this->dispatch('ModalTableAvaliar');

            $produtos_com_dados = [];

            foreach ($produtos as $produto) {
                // Busca os dados adicionais para o produto
                $resultado = $this->buscarProdutoRotina($produto->codprod, $produto->codfilial);

                // Mesclar os dados do produto com os resultados encontrados (ou vazio caso não haja dados)
                $produto_com_dados = (array)$produto; // Converte o produto (caso seja objeto) para array
                $produto_com_dados['consulta_dados'] = !empty($resultado) ? $resultado : null;

                $produtos_com_dados[] = $produto_com_dados;
            }

            $this->itensi = $produtos_com_dados;


            // agora vamos entrar dentro do array consulta_dados e vamos agrupar o CODFORNEC,FORNECEDOR,PRAZOENTREGA,OBSERVACAO e vamos colocar dentro do array $cabecario_227_agrupado

            $this->cabecario_227_agrupado = [];
            foreach ($this->itensi as $key => $value) {
                if (!empty($value['consulta_dados'])) {
                    foreach ($value['consulta_dados'] as $key2 => $value2) {
                        if (!isset($this->cabecario_227_agrupado[$value2['CODFORNEC']])) {
                            // Inicializa o fornecedor na primeira vez
                            $this->cabecario_227_agrupado[$value2['CODFORNEC']] = [
                                'CODFORNEC' => $value2['CODFORNEC'],
                                'FORNECEDOR' => $value2['FORNECEDOR'],
                                'PRAZOENTREGA' => $value2['PRAZOENTREGA'],
                                'OBSERVACAO' => $value2['OBSERVACAO'],
                                'QUANTIDADE' => 0, // Inicializa a quantidade
                            ];
                        }

                        // Incrementa a quantidade para este fornecedor
                        $this->cabecario_227_agrupado[$value2['CODFORNEC']]['QUANTIDADE']++;
                    }
                }
            }

        } catch (\Exception $e) {
            $this->toast('error', 'Erro ao buscar produto!');
        }
    }

    public function modalOpenOptions($codfornec)
    {
        $this->dados_cursor = [];
        foreach ($this->itensi as $key => $value) {
            if (!empty($value['consulta_dados'])) {
                foreach ($value['consulta_dados'] as $key2 => $value2) {
                    if ($value2['CODFORNEC'] == $codfornec) {
                        $value2['CODSUGITEM'] = $value['codsugitem'] ?? null;
                        $value2['CODSUG'] = $value['codsug'] ?? null;
                        $value2['DATA_VENCIMENTO'] = $value['data_vencimento'] ?? null;
                        $value2['QUANTIDADE'] = $value['quantidade'] ?? null;
                        $value2['VL_REEMBOLSO'] = $value['vl_reembolso'] ?? null;
                        $value2['VL_OFERTA'] = $value['vl_oferta'] ?? null;
                        $this->dados_cursor[] = $value2;
                    }
                }
            }
        }
        $this->dispatch("ModalTableAvaliar227");
    }

    public function updateValue($index, $field, $value)
    {
        // Remover 'R$', ponto ou vírgula e espaços visíveis ou invisíveis (incluindo o No-Break Space)
        $valor_produto = preg_replace('/[^\d.,]+/', '', $value);
        $valor_produto = str_replace(',', '.', $valor_produto);

        if (isset($this->dados_cursor[$index]) && array_key_exists($field, $this->dados_cursor[$index])) {
            $this->dados_cursor[$index][$field] = $valor_produto;
        }
    }

    public function salvar_dados()
    {
        foreach ($this->dados_cursor as $key => $value) {
            try {
                DB::connection('oracle')->update(
                    "UPDATE bdc_sugestoesi@dbl200
                        SET
                            vl_reembolso = :vl_reembolso,
                            vl_oferta = :vl_oferta,
                            status = 1
                      WHERE codsugitem = :codsugitem
                        AND codsug = :codsug",
                    [
                        'vl_reembolso' => floatval(str_replace(',', '.', $value['VL_REEMBOLSO'])),
                        'vl_oferta' => floatval(str_replace(',', '.', $value['VL_OFERTA'])),
                        'codsugitem' => $value['CODSUGITEM'],
                        'codsug' => $value['CODSUG']
                    ]
                );
                $this->toast('success', 'Item atualizado com sucesso!');
            } catch (\Exception $e) {
                $this->toast('error', 'Erro ao atualizar item!');
            }
        }

        $this->VisualizarPDF($this->dados_cursor);
        /*$this->redirect(route('sugestoes.avaliar'));*/

    }

    public function buscarProdutoRotina($codprod, $codfilial)
    {
        DB::beginTransaction();

        $dt_inicial = \DateTime::createFromFormat('Y-m-d', date('Y-m-01'))->format('d/m/Y');
        $dt_final = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y');

        $this->data_inicial = $dt_inicial;
        $this->data_final = $dt_final;

        $dtinicio = $dt_inicial;
        $dtfim = $dt_final;
        $filialcod = $codfilial;
        $prodcod = $codprod;

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

                DB::commit();

                return $finalResult;
            } else {
                DB::commit();
                return []; // Nenhum cursor retornado
            }

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

    public function VisualizarPDF($data)
    {
        $seconds = 300; //  5 minutos
        $cacheKey = 'pdf_data_' . md5(json_encode($data));
        $value = Cache::remember($cacheKey, $seconds, function () use ($data) {
            return $data;
        });
        $this->dispatch('abrir-nova-aba', [
            'url' => route('sugestoes.visualizar-pdf', ['cacheKey' => $cacheKey])
        ]);
    }


    public function render()
    {
        return view('livewire.sugestoes.avaliar')->layout('layouts.home-layout');
    }
}
