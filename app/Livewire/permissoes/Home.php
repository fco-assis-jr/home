<?php

namespace App\Livewire\Permissoes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use mysql_xdevapi\Exception;

class Home extends Component
{
    use LivewireAlert;

    public $query = '';
    public $results = [];
    public $modules = [];
    public $selectedMatricula;

    public function mount()
    {
        $this->CarregarModulos();
    }

    public function CarregarModulos()
    {
        $results = DB::connection('oracle')->select("
            SELECT DISTINCT M.CODMOD,
                            M.DESCRICAO,
                            NVL(MI.CODCONTRO, 0) AS CODCONTROLE,
                            NVL(MI.DESCRICAO, 'SEM CONTROLE') AS CONTROLE_DESCRICAO
            FROM BDC_MODULO M
            LEFT JOIN BDC_MODULOCONTRO MI ON M.CODMOD = MI.CODMOD
        ");

        $modules = [];
        foreach ($results as $result) {
            $codmod = $result->codmod;

            if (!isset($modules[$codmod])) {
                $modules[$codmod] = [
                    'codmod' => $result->codmod,
                    'descricao' => $result->descricao,
                    'modulo_acesso' => false, // Desmarcado por padrão
                    'controles' => []
                ];
            }

            $modules[$codmod]['controles'][] = [
                'codcontrole' => $result->codcontrole,
                'controle_descricao' => $result->controle_descricao,
                'controle_acesso' => false // Desmarcado por padrão
            ];
        }

        $this->modules = array_values($modules);
    }

    public function search()
    {
        if (strlen($this->query) > 2) {
            $this->results = DB::connection('oracle')->select("
                SELECT DISTINCT matricula || ' | ' || nome AS display, matricula
                FROM pcempr
                WHERE situacao = 'A'
                AND (UPPER(nome) LIKE UPPER(:query) OR UPPER(matricula) LIKE UPPER(:query))
            ", ['query' => '' . $this->query . '%']);
        } else {
            $this->results = [];
        }
    }

    public function savePermissions($permissions)
    {

        if (empty($this->selectedMatricula)) {
            $this->toast('error', 'Selecione o usuario!');
            return;
        }
        try {
            $matricula = $this->selectedMatricula;
            $usuarioConcedeu = auth()->user()->matricula;
            $dataAtual = now()->format('d/m/Y');

            DB::transaction(function () use ($permissions, $matricula, $usuarioConcedeu, $dataAtual) {
                // Remove permissões existentes para o usuário
                DB::connection('oracle')->table('BDC_CONTROC')->where('CODUSUARIO', $matricula)->delete();
                DB::connection('oracle')->table('BDC_CONTROI')->where('CODUSUARIO', $matricula)->delete();

                foreach ($permissions as $moduloId => $modulo) {
                    // Insere ou atualiza o acesso ao módulo
                    if ($modulo['modulo_acesso']) {
                        DB::connection('oracle')->table('BDC_CONTROC')->insert([
                            'CODMOD' => (int)$moduloId,
                            'CODUSUARIO' => (int)$matricula,
                            'CONCEDEU' => $usuarioConcedeu,
                            'DATA' => DB::raw("TO_DATE('{$dataAtual}', 'DD/MM/YYYY')"),
                            'ACESSO' => 'S',
                        ]);
                    }

                    // Verifica se há controles no módulo e insere apenas os controles específicos
                    if (isset($modulo['controles']) && is_array($modulo['controles'])) {
                        foreach ($modulo['controles'] as $controlId => $controlAccess) {
                            if ($controlAccess) { // Apenas insere controles com acesso verdadeiro
                                DB::connection('oracle')->table('BDC_CONTROI')->insert([
                                    'CODMOD' => (int)$moduloId, // Associado ao módulo correto
                                    'CONTROLE' => (int)$controlId,
                                    'CODUSUARIO' => (int)$matricula,
                                    'CONCEDEU' => $usuarioConcedeu,
                                    'DATA' => DB::raw("TO_DATE('{$dataAtual}', 'DD/MM/YYYY')"),
                                    'ACESSO' => 'S',
                                ]);
                            }
                        }
                    }
                }
            });

            $this->toast('success', 'Permissão salva com sucesso!');
        } catch (\Exception $e) {

            $this->toast('error', 'Permissão salva com sucesso!');
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

    public function loadPermissions($matricula)
    {
        $this->selectedMatricula = $matricula;
                $results = DB::connection('oracle')->select("
        SELECT
           M.CODMOD,
               M.DESCRICAO AS descricao,
               NVL(C.ACESSO, 'N') AS MODULO_ACESSO,
               MI.CODCONTRO as codcontrole,
               NVL(MI.DESCRICAO, 'SEM CONTROLE') AS CONTROLE_DESCRICAO,
               NVL(I.ACESSO, 'N') AS CONTROLE_ACESSO
        FROM
            BDC_MODULO M
        LEFT JOIN
            BDC_MODULOCONTRO MI ON M.CODMOD = MI.CODMOD
        LEFT JOIN
            BDC_CONTROC C ON M.CODMOD = C.CODMOD AND C.CODUSUARIO = :matricula
        LEFT JOIN
            BDC_CONTROI I ON MI.CODCONTRO = I.CONTROLE
            AND M.CODMOD = I.CODMOD
            AND I.CODUSUARIO = :matricula
        WHERE
            (MI.CODCONTRO IS NOT NULL OR C.CODMOD IS NOT NULL)
        ORDER BY
            M.CODMOD, MI.CODCONTRO", ['matricula' => $matricula]);

        $modules = [];
        foreach ($results as $result) {
            $codmod = $result->codmod;

            if (!isset($modules[$codmod])) {
                $modules[$codmod] = [
                    'codmod' => $result->codmod,
                    'descricao' => $result->descricao,
                    'modulo_acesso' => $result->modulo_acesso === 'S',
                    'controles' => []
                ];
            }

            $modules[$codmod]['controles'][] = [
                'codcontrole' => $result->codcontrole,
                'controle_descricao' => $result->controle_descricao,
                'controle_acesso' => $result->controle_acesso === 'S'
            ];
        }

        return array_values($modules); // Sempre retorna um array, mesmo vazio
    }

    public function render()
    {
        return view('livewire.permissoes.home')->layout('layouts.home-layout');
    }
}
