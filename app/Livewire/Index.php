<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    public $qtsugestoes;
    public $qtocorrencias;
    public $jsonOcorrencias = [];
    public $formattedData = [];

    use LivewireAlert;

    public function mount()
    {
        $this->qtsugestoes = $this->countSugestoes();
        $this->qtocorrencias = $this->countOcorrencias();
        $this->jsonOcorrencias = $this->graficoOcorrencias();
        $this->formattedData = $this->graficoOcorrenciasFilial();
    }

public function graficoOcorrenciasFilial()
{
    $registros = DB::connection('oracle')->select(/** @lang text */ '
        SELECT FILIAL AS FILIAL,
               (SELECT DESCRICAO
                FROM BDC_REGISTROS_TIPOS@DBL200
                WHERE CODTIPO = TIPO_REGISTRO) AS TIPO_REGISTRO,
               COUNT(*) AS QUANTIDADE
        FROM BDC_REGISTROS_OCORRENCIAS@DBL200
        GROUP BY FILIAL, TIPO_REGISTRO
        ORDER BY FILIAL, TIPO_REGISTRO
    ');

    $formattedData = [];

    foreach ($registros as $registro) {
        $filial = $registro->filial;
        $tipoRegistro = $registro->tipo_registro;
        $quantidade = $registro->quantidade;

        if (!isset($formattedData[$filial])) {
            $formattedData[$filial] = [];
        }

        $formattedData[$filial][] = [
            'name' => $tipoRegistro,
            'value' => $quantidade,
        ];
    }

    return $formattedData;
}

    public function graficoOcorrencias()
    {
        $data = DB::connection('oracle')->select(query: /** @lang text */ '
                    SELECT (SELECT DESCRICAO
                    FROM BDC_REGISTROS_TIPOS@DBL200
                    WHERE CODTIPO = TIPO_REGISTRO) AS NAME,
                    COUNT (*) AS VALUE,
                    ROUND (
                       (  COUNT (*)
                        * 100.0
                        / (SELECT COUNT (*) FROM BDC_REGISTROS_OCORRENCIAS@DBL200))) AS PORCENTAGEM
                    FROM BDC_REGISTROS_OCORRENCIAS@DBL200
                    GROUP BY TIPO_REGISTRO
                    ');

        return array_map(function ($item) {
            return [
                'name' => $item->name,
                'value' => $item->value,
            ];
        }, $data);
    }

    public function countSugestoes()
    {
        // Conta o número total de sugestões
        $resultado = DB::connection('oracle')->select("SELECT COUNT(*) AS total FROM BDC_SUGESTOESC@DBL200");
        return $resultado[0]->total;
    }

    public function countOcorrencias()
    {
        // Conta o número total de ocorrências
        $resultado = DB::connection('oracle')->select("SELECT COUNT(*) AS total FROM bdc_registros_ocorrencias@dbl200");
        return $resultado[0]->total;
    }

    public function render()
    {
        return view('livewire.index')->layout('layouts.home-layout');
    }
}
