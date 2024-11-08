<?php

namespace App\Livewire\ocorrencias;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PDO;
use Illuminate\Support\Facades\Crypt;

class Ocorrencias extends Component
{
    use LivewireAlert;
    public $ocorrencias = [];
    public $ModalOcorrencia;
    public $imagem = [];
    public $tipo_registro = [];
    public $search;
    public $func= [];
    public $duplicDescricao;
    public $duplicTipo;
    public $matricula;

    public function mount()
    {
        $this->query();
    }

    public function query()
    {
        $this->ocorrencias = DB::connection('oracle')->select('SELECT   ro.id,
         pc_usuario.nome AS nome_usuario,
         tp.descricao as tipo_registro,
         to_char(ro.data, \'DD/MM/YYYY\') AS data,
         ro.filial,
         pc_func.nome AS nome_func,
         to_char(ro.data_criacao, \'DD/MM/YYYY HH24:MI:SS\') AS data_criacao,
         ro.descricao,
         ro.numero_transacao,
         ro.valor_ocorrencia
  FROM               bdc_registros_ocorrencias@dbl200 ro
                 INNER JOIN
                     bdc_registros_tipos@dbl200 tp
                 ON ro.tipo_registro = tp.codtipo
             LEFT JOIN
                 pcempr pc_usuario
             ON pc_usuario.matricula = ro.codusuario
         LEFT JOIN
             pcempr pc_func
         ON pc_func.matricula = ro.codfunc order by ro.id desc');


        $this->tipo_registro = DB::connection('oracle')->select('SELECT * FROM bdc_registros_tipos@dbl200');
    }

    public function abrirModal($id)
    {
        $ocorrencia = DB::connection('oracle')->select('SELECT   ro.id,
         ro.codusuario AS codusuario,
         pc_usuario.nome AS nome_usuario,
         tp.descricao as tipo_registro,
         to_char(ro.data, \'DD/MM/YYYY\') AS data,
         ro.filial,
         pc_func.nome AS nome_func,
         to_char(ro.data_criacao, \'DD/MM/YYYY HH24:MI:SS\') AS data_criacao,
         ro.descricao,
         ro.numero_transacao
  FROM               bdc_registros_ocorrencias@dbl200 ro
                 INNER JOIN
                     bdc_registros_tipos@dbl200 tp
                 ON ro.tipo_registro = tp.codtipo
             LEFT JOIN
                 pcempr pc_usuario
             ON pc_usuario.matricula = ro.codusuario
         LEFT JOIN
             pcempr pc_func
         ON pc_func.matricula = ro.codfunc
    WHERE ro.id = ?', [$id]);
        $this->ModalOcorrencia = $ocorrencia;

        //vamos buscar as imagens bdc_registros_dirimg@dbl200 dirimg
        $imagem = DB::connection('oracle')->select('SELECT
                     dirimg.id_ocorrencia,
                     dirimg.file_name
                 FROM
                     bdc_registros_dirimg@dbl200 dirimg
                 WHERE
                     dirimg.id_ocorrencia = ?', [$id]);
        $this->imagem = $imagem;
        $this->dispatch('abrirModalOcorrencia', $ocorrencia);
    }

    public function OpenDuplicarModal()
    {
        $this->dispatch('OpenDuplicarModal');
    }

    public function matriculas()
    {
        if (empty($this->search)) {
            $this->func = [];
            return;
        }
        $mat = DB::connection('oracle')->select("select matricula|| ' - '  || nome AS nome, matricula from pcempr where ( matricula like ? or upper(nome) like upper(?) ) and rownum <= 5", [$this->search.'%', $this->search.'%']);
        $this->func = $mat;
    }

    public function selectUser($nome, $matricula)
    {
        $this->search = $nome;
        $this->matricula = $matricula;
        $this->func = [];
    }

    public function cadastrar()
    {
        $matricula = $this->matricula;
        $tipo_ocorrencia = $this->duplicTipo;
        $descricao = $this->duplicDescricao;

        try {
            $filial = $this->ModalOcorrencia[0]->filial;
            $codusuario = $this->ModalOcorrencia[0]->codusuario;
            $numero_transacao = $this->ModalOcorrencia[0]->numero_transacao;
            $data_ocorrencia = $this->ModalOcorrencia[0]->data;
            $data_criacao = $this->ModalOcorrencia[0]->data_criacao;
            $id = $this->ModalOcorrencia[0]->id;

            //validar os dados para não enviar vazio
            if (empty($matricula) || empty($tipo_ocorrencia) || empty($descricao)) {
                $this->alert('error', 'Preencha todos os campos!');
                return;
            }

            $result = DB::connection('oracle')->select('select matricula from pcempr where matricula = ?', [$matricula]);
            if (empty($result)) {
                $this->alert('error', 'Matrícula do funcionário não encontrado!');
                return;
            }
            $matricula = $result[0]->matricula;
            $seq = DB::connection('oracle')->select('select seq_reg_ocorrencias_id.NEXTVAL@dbl200 as seq from dual')[0]->seq;

            DB::connection('oracle')->insert('
            insert into bdc_registros_ocorrencias@dbl200
            (id, codusuario, tipo_registro, data, filial, codfunc, data_criacao, descricao, numero_transacao)
            values (?, ?, ?, TO_DATE(?, \'DD/MM/YYYY\'), ?, ?, TO_DATE(?, \'DD/MM/YYYY HH24:MI:SS\'), ?, ?)',
                [$seq, $codusuario, $tipo_ocorrencia, $data_ocorrencia, $filial, $matricula, $data_criacao, $descricao, $numero_transacao]
            );

            $imagem = DB::connection('oracle')->select('SELECT dirimg.id_ocorrencia, dirimg.file_name FROM bdc_registros_dirimg@dbl200 dirimg WHERE dirimg.id_ocorrencia = ?', [$id]);
            foreach ($imagem as $item) {
                DB::connection('oracle')->insert('insert into bdc_registros_dirimg@dbl200 (id_ocorrencia, file_name) values (?, ?)', [$seq, $item->file_name]);
            }

            $this->alert('success', 'Ocorrência cadastrada com sucesso!');
            $this->redirect(request()->header('Referer'));
        } catch (\Exception $e) {
            $this->alert('error', 'Erro ao cadastrar a ocorrência.');
        }
    }

    public function formatarMoeda($value)
    {
        //vamos formatar o valor para o padrão brasileiro
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public function render()
    {
        return view('livewire.ocorrencias.ocorrencia')->layout('layouts.home-layout');
    }
}
