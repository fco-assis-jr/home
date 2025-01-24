<?php

namespace App\Livewire\sugestoes;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class Relatorios extends Component
{

    public $selected = [];
    public $filtro;
    public $tabela;
    public $selecionados;
    public $descselecionado;
    public $buttonDisabled = true;
    public $dados_filtrados = [];
    public $messages = false;

    public function filtrar($filtro, $coluna, $tabela, $escolhido)
    {
        $this->buttonDisabled = true;
        $this->filtro = $filtro;

        $colunasPermitidas = ['codsec', 'codcategoria', 'codfornec', 'codauxiliar', 'codprod'];
        $tabelasPermitidas = [
            'pcsecao'     => 'descricao',
            'pccategoria' => 'categoria',
            'pcfornec'    => 'fornecedor',
            'bdc_sugestoesi@dbl200' => 'descricao'
        ];

        if (!in_array($filtro, $colunasPermitidas) || !array_key_exists($tabela, $tabelasPermitidas)) {
            throw new \Exception("Parâmetros inválidos: filtro = $filtro, tabela = $tabela");
        }

        $colunaDescricao = $tabelasPermitidas[$tabela];
        $this->tabela =$escolhido;

        // Monta a query de forma segura
        $sql = "SELECT si.$filtro AS id, tr.$colunaDescricao AS descricao
            FROM bdc_sugestoesc@dbl200 sc
            INNER JOIN bdc_sugestoesi@dbl200 si
                ON sc.codsug = si.codsug AND sc.codusuario = :codusuario
            INNER JOIN $tabela tr
                ON si.$filtro = tr.$filtro
            GROUP BY si.$filtro, tr.$colunaDescricao";

        $selectQuery = DB::connection('oracle')->select($sql, [
            'codusuario' => auth()->user()->matricula
        ]);

        if (empty($selectQuery)) {
            $this->messages = true;
            return;
        }
        $this->selected = $selectQuery;
    }

    public function gerarRelatorio()
    {
        foreach ($this->selected as $key => $value) {
            if ($value->id == $this->selecionados) {
                $this->descselecionado = $value->descricao;
            }
        }


        $relatorio = DB::connection('oracle')->select("
        SELECT   sc.codsug, si." . $this->filtro . " as tabela , sc.data ,sc.codfilial, count(1) as quantidade
        FROM       bdc_sugestoesc@dbl200 sc
        INNER JOIN bdc_sugestoesi@dbl200 si
        ON     sc.codsug = si.codsug
        AND sc.codusuario = :matricula
        AND si." . $this->filtro . " = :selecionado group by sc.codsug, si." . $this->filtro . ", sc.data,sc.codfilial
    ", [
            'matricula' => auth()->user()->matricula,
            'selecionado' => $this->selecionados
        ]);
        $this->dados_filtrados = $relatorio;
        $this->dispatch('flipForm');
        $this->dispatch('relatorio', $relatorio);

    }

    public function updateValue($value)
    {
        $this->selecionados = $value;
        $this->buttonDisabled = false;
    }

    public function OpenPDF($codsug, $tabela, $data, $codfilial)
    {

        $relatorio = DB::connection('oracle')->select("
        SELECT
            sc.codsug,
            si." . $this->filtro . " as tabela ,
            to_char(sc.data, 'DD/MM/YYYY') as data_criacao,
            sc.codfilial,
            si.codprod,
            si.codauxiliar,
            si.valor_produto,
            si.quantidade,
            si.unid as unidade,
            si.vl_reembolso,
            si.vl_oferta,
            si.numverba,
            si.inioferta,
            si.fimoferta,
            si.descricao as descricao_produto,
            to_char(si.data_vencimento, 'DD/MM/YYYY') as data_vencimento,
            si.descricaosug
        FROM       bdc_sugestoesc@dbl200 sc
            INNER JOIN bdc_sugestoesi@dbl200 si
            ON  sc.codsug = si.codsug
            AND sc.codusuario = :matricula
            AND si." . $this->filtro . " = :selecionado
            AND sc.codsug = :codsug
            AND si." . $this->filtro . " = :tabela
            AND sc.data = :data
            AND sc.codfilial = :codfilial
    ", [
            'matricula' => auth()->user()->matricula,
            'selecionado' => $this->selecionados,
            'codsug' => $codsug,
            'tabela' => $tabela,
            'data' => $data,
            'codfilial' => $codfilial
        ]);

        $relatorio[0]->descselecionado = $this->descselecionado;
        $relatorio[0]->tabeladesc = $this->tabela;

        $this->VisualizarPDF($relatorio);

    }

    public function VisualizarPDF($data)
    {
        $seconds = 300; //  5 minutos
        $cacheKey = 'pdf_data_relatorio' . md5(json_encode($data));
        $value = Cache::remember($cacheKey, $seconds, function () use ($data) {
            return $data;
        });
        $this->dispatch('abrir-nova-aba', [
            'url' => route('sugestoes.visualizar-pdf-relatorio', ['cacheKey-relatorio' => $cacheKey])
        ]);
    }



    public function render()
    {
        return view('livewire.sugestoes.relatorios')->layout('layouts.home-layout');
    }

}
