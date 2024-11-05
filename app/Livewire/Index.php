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
    public $jsonOcorrenciasFilial = [];

    use LivewireAlert;

    public function mount()
    {
        $this->qtsugestoes = $this->countSugestoes();
        $this->qtocorrencias = $this->countOcorrencias();
        $this->jsonOcorrencias = $this->graficoOcorrencias();
        $this->jsonOcorrenciasFilial = $this->graficoOcorrenciasFilial();
    }

    public function graficoOcorrenciasFilial()
    {
        $registros = DB::connection('oracle')->select(/** @lang text */ 'SELECT (SELECT DESCRICAO
                    FROM BDC_REGISTROS_TIPOS@DBL200
                    WHERE CODTIPO = TIPO_REGISTRO) AS TIPO_REGISTRO,
                    FILIAL,
                    COUNT (*) AS QTD_REGISTROS,tipo_registro as codtipo
                    FROM BDC_REGISTROS_OCORRENCIAS@DBL200
                    GROUP BY TIPO_REGISTRO, FILIAL
                    ORDER BY FILIAL, TIPO_REGISTRO');

        foreach ($registros as $registro) {
            $tipo = $registro->tipo_registro;
            $codtipo = $registro->codtipo;
            $filial = $registro->filial;
            $quantidade = $registro->qtd_registros;

            if (!isset($resultado[$tipo])) {
                $resultado[$tipo] = [];
            }

            $resultado[$tipo][] = [
                'name' => (string)$filial,
                'value' => $quantidade,
                'tipo' => $codtipo,
                'employees' => [],
            ];
        }
        foreach ($resultado as $tipo => &$registros) {
            foreach ($registros as &$registro) {

                $sql = DB::connection('oracle')->select(/** @lang text */ '
            SELECT CODFUNC, COUNT(*) AS QUANTIDADE_OCORRENCIAS
            FROM BDC_REGISTROS_OCORRENCIAS@DBL200
            WHERE TIPO_REGISTRO = ? AND FILIAL = ?
            GROUP BY CODFUNC
            ORDER BY QUANTIDADE_OCORRENCIAS DESC', [$registro['tipo'], $registro['name']]);


                $registro['employees'] = array_map(function ($row) {
                    return [
                        'name' => $row->codfunc,
                        'value' => $row->quantidade_ocorrencias
                    ];
                }, $sql);

            }
        }

        return $resultado;
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
