<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    public $qtsugestoes;
    public $qtocorrencias;
    public $jsonOcorrenciasFilial = [];

    use LivewireAlert;

    public function mount()
    {
        $this->qtsugestoes = $this->countSugestoes();
        $this->qtocorrencias = $this->countOcorrencias();
        $this->jsonOcorrenciasFilial = $this->graficoOcorrenciasFilial();
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

        $jsonOcorrenciasFilial = [];

        foreach ($registros as $registro) {
            $filial = $registro->filial;
            $tipoRegistro = $registro->tipo_registro;
            $quantidade = $registro->quantidade;

            if (!isset($jsonOcorrenciasFilial[$filial])) {
                $jsonOcorrenciasFilial[$filial] = [];
            }

            $jsonOcorrenciasFilial[$filial][] = [
                'name' => $tipoRegistro,
                'value' => $quantidade,
            ];
        }

        return $jsonOcorrenciasFilial;
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
