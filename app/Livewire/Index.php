<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Exception;

class Index extends Component
{
    public $qtsugestoes;
    public $qtocorrencias;
    public $jsonOcorrenciasFilial = [];
    public $jsonOcorrencias = [];

    use LivewireAlert;

    public function mount()
    {
        try {
            $this->qtsugestoes = $this->countSugestoes();
            $this->qtocorrencias = $this->countOcorrencias();
            $this->jsonOcorrencias = $this->graficoOcorrenciasTotal();
            $this->jsonOcorrenciasFilial = $this->graficoOcorrenciasFilial();
        } catch (Exception $e) {
            $this->alert('error', 'Erro ao consultar o banco de dados. Tente novamente mais tarde.');
        }
    }

    private function graficoOcorrenciasFilial()
    {
        try {
            $registros = DB::connection('oracle')->select('
                SELECT FILIAL AS FILIAL,
                       (SELECT DESCRICAO FROM BDC_REGISTROS_TIPOS@DBL200 WHERE CODTIPO = TIPO_REGISTRO) AS TIPO_REGISTRO,
                       COUNT(*) AS QUANTIDADE
                FROM BDC_REGISTROS_OCORRENCIAS@DBL200
                GROUP BY FILIAL, TIPO_REGISTRO
                ORDER BY FILIAL, TIPO_REGISTRO
            ');

            $jsonOcorrenciasFilial = [];
            foreach ($registros as $registro) {
                $jsonOcorrenciasFilial[$registro->filial][] = [
                    'name' => $registro->tipo_registro,
                    'value' => $registro->quantidade,
                ];
            }

            return $jsonOcorrenciasFilial;
        } catch (Exception $e) {
            $this->alert('error', 'Erro ao carregar dados de ocorrências por filial.');
            return [];
        }
    }

    private function graficoOcorrenciasTotal()
    {
        try {
            $registros = DB::connection('oracle')->select('
                SELECT (SELECT DESCRICAO FROM BDC_REGISTROS_TIPOS@DBL200 WHERE CODTIPO = TIPO_REGISTRO) AS TIPO_REGISTRO,
                       COUNT(*) AS QUANTIDADE
                FROM BDC_REGISTROS_OCORRENCIAS@DBL200
                GROUP BY TIPO_REGISTRO
                ORDER BY TIPO_REGISTRO
            ');

            return array_map(fn($registro) => [
                'name' => $registro->tipo_registro,
                'value' => $registro->quantidade,
            ], $registros);
        } catch (Exception $e) {
            $this->alert('error', 'Erro ao carregar dados de ocorrências totais.');
            return [];
        }
    }

    private function countSugestoes()
    {
        try {
            return DB::connection('oracle')
                ->selectOne("SELECT COUNT(*) AS total FROM BDC_SUGESTOESC@DBL200")
                ->total;
        } catch (Exception $e) {
            $this->alert('error', 'Erro ao contar sugestões.');
            return 0;
        }
    }

    private function countOcorrencias()
    {
        try {
            return DB::connection('oracle')
                ->selectOne("SELECT COUNT(*) AS total FROM bdc_registros_ocorrencias@dbl200")
                ->total;
        } catch (Exception $e) {
            $this->alert('error', 'Erro ao contar ocorrências.');
            return 0;
        }
    }

    public function render()
    {
        return view('livewire.index')->layout('layouts.home-layout');
    }
}
