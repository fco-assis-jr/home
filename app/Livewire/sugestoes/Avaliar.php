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
    public $data_criacao = [];
    public $status;
    public $cabecario_227_agrupado = [];
    public $dados_cursor = [];
    protected $listeners = ['confirmar'];



    public function mount()
    {
        $this->home();
    }

    public function home()
    {
        $itens = DB::connection('oracle')->select(
            "WITH status_sugestao
                                AS (  SELECT   codsug,
                                               CASE
                                                   WHEN SUM(CASE
                                                                WHEN    numverba IS NULL
                                                                     OR TRIM (numverba) = ''
                                                                     OR inioferta IS NULL
                                                                     OR fimoferta IS NULL
                                                                     OR vl_oferta <= 0
                                                                     OR vl_reembolso <= 0
                                                                THEN
                                                                    1
                                                                ELSE
                                                                    0
                                                            END) > 0
                                                   THEN
                                                       'INCOMPLETO'
                                                   ELSE
                                                       'COMPLETO'
                                               END
                                                   AS status
                                        FROM   bdc_sugestoesi@dbl200
                                    GROUP BY   codsug)
                          SELECT   c.codsug,
                                   p.nome,
                                   TO_CHAR (c.data, 'DD/MM/YYYY HH24:MI:SS') AS data,
                                   c.codfilial,
                                   (SELECT   COUNT (1)
                                      FROM   bdc_sugestoesi@dbl200 i
                                     WHERE   i.codsug = c.codsug)
                                       AS qtd_aguardando,
                                   (SELECT   s.status
                                      FROM   status_sugestao s
                                     WHERE   s.codsug = c.codsug)
                                       AS status
                            FROM       bdc_sugestoesc@dbl200 c
                                   INNER JOIN
                                       pcempr p
                                   ON p.matricula = c.codusuario
                        ORDER BY   c.codsug DESC"
        );
        $this->itensc = $itens;
    }

    public function modalOpen($index)
    {
        try {
            $produtos = DB::connection('oracle')->select(
                "
                        WITH status_sugestao
                                AS (  SELECT   codfornec,
                                               codsug,
                                               CASE
                                                   WHEN SUM(CASE
                                                                WHEN    numverba IS NULL
                                                                     OR TRIM (numverba) = ''
                                                                     OR inioferta IS NULL
                                                                     OR fimoferta IS NULL
                                                                     OR vl_oferta <= 0
                                                                     OR vl_reembolso <= 0
                                                                THEN
                                                                    1
                                                                ELSE
                                                                    0
                                                            END) > 0
                                                   THEN
                                                       'INCOMPLETO'
                                                   ELSE
                                                       'COMPLETO'
                                               END
                                                   AS status
                                        FROM   bdc_sugestoesi@dbl200
                                       WHERE   codsug = :codsug
                                    GROUP BY   codfornec, codsug)
                          SELECT   DISTINCT
                                   c.codsug,
                                   e.codprod,
                                   TO_CHAR (c.data, 'DD/MM/YYYY HH24:MI:SS') AS data,
                                   i.codsugitem,
                                   i.codsug,
                                   i.codauxiliar,
                                   i.descricao,
                                   i.valor_produto,
                                   NVL (i.valor_sugerido, 0) AS valor_sugerido,
                                   TO_CHAR (i.data_vencimento, 'DD/MM/YYYY') AS data_vencimento,
                                   i.quantidade,
                                   i.status,
                                   i.unid,
                                   c.codfilial,
                                   p.codauxiliar AS prod_codauxiliar,
                                   emp.nome,
                                   f.codfornec,
                                   f.fornecedor AS fornecedor,
                                   i.vl_reembolso,
                                   i.vl_oferta,
                                   i.numverba,
                                   TO_CHAR (i.inioferta, 'YYYY-MM-DD') AS inioferta,
                                   TO_CHAR (i.fimoferta, 'YYYY-MM-DD') AS fimoferta,
                                   (SELECT   s.status
                                      FROM   status_sugestao s
                                     WHERE   s.codsug = c.codsug AND s.codfornec = f.codfornec)
                                       AS status_status,
                                   i.descricaosug AS descricaosug
                            FROM                       bdc_sugestoesi@dbl200 i
                                                   JOIN
                                                       bdc_sugestoesc@dbl200 c
                                                   ON i.codsug = c.codsug
                                               JOIN
                                                   pcembalagem e
                                               ON e.codauxiliar = i.codauxiliar
                                           JOIN
                                               pcprodut p
                                           ON p.codprod = e.codprod
                                       JOIN
                                           pcempr emp
                                       ON c.codusuario = emp.matricula
                                   JOIN
                                       pcfornec f
                                   ON f.codfornec = i.codfornec
                           WHERE   c.codsug = :codsug
                        ORDER BY   i.codsugitem ASC",
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
                                'ITENS_STATUS' => $value['status_status'],
                                'DATACRIACAO' => $value['data'],
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
                        $value2['NUMVERBA'] = $value['numverba'] ?? null;
                        $value2['INIOFERTA'] = $value['inioferta'] ?? null;
                        $value2['FIMOFERTA'] = $value['fimoferta'] ?? null;
                        $value2['DESCRICAO_SUGESTAO'] = $value['descricaosug'] ?? null;
                        $this->dados_cursor[] = $value2;
                    }
                }
            }
        }
        $this->dispatch("ModalTableAvaliar227");
    }

    public function updateValue($index, $field, $value)
    {

        $valor_produto = preg_replace('/[^\d.,]+/', '', $value);
        $valor_produto = str_replace(',', '.', $valor_produto);

        if (isset($this->dados_cursor[$index]) && array_key_exists($field, $this->dados_cursor[$index])) {
            $this->dados_cursor[$index][$field] = $valor_produto;
        }

    }

    public function updateValue2($campo, $value)
    {
        foreach ($this->dados_cursor as $key => $item) {
            $this->dados_cursor[$key][$campo] = $value;
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
                            status = 1,
                            numverba = :numverba,
                            inioferta = :inioferta,
                            fimoferta = :fimoferta
                      WHERE codsugitem = :codsugitem
                        AND codsug = :codsug",
                    [
                        'vl_reembolso' => floatval(str_replace(',', '.', $value['VL_REEMBOLSO'])),
                        'vl_oferta' => floatval(str_replace(',', '.', $value['VL_OFERTA'])),
                        'codsugitem' => $value['CODSUGITEM'],
                        'codsug' => $value['CODSUG'],
                        'numverba' => $value['NUMVERBA'],
                        'inioferta' => $value['INIOFERTA'],
                        'fimoferta' => $value['FIMOFERTA']
                    ]
                );
                $this->toast('success', 'Item atualizado com sucesso!');
            } catch (\Exception $e) {
                $this->toast('error', 'Erro ao atualizar item!');
            }
        }

        DB::connection('oracle')->update(
            "UPDATE bdc_sugestoesi@dbl200
                SET DESCRICAOSUG = :descricao_sugestao
              WHERE codfornec = :codfornec and codsug = :codsug",
            [
                'descricao_sugestao' => $this->dados_cursor[0]['DESCRICAO_SUGESTAO'],
                'codfornec' => $this->dados_cursor[0]['CODFORNEC'],
                'codsug' => $this->dados_cursor[0]['CODSUG']
            ] );

        $this->VisualizarPDF($this->dados_cursor);
        $this->modalOpen($this->dados_cursor[0]['CODSUG']);
        $this->home();
        $this->dados_cursor = [];

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
