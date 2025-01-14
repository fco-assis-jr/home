<?php

namespace App\Livewire\ocorrencias;

use Livewire\Component;
use Livewire\WithFileUploads;

// Adicione esta linha
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Home extends Component
{
    use LivewireAlert, WithFileUploads;

    // Adicione esta linha

    public $Tipo_ocorrencias = [];
    public $Filiais = [];
    public $data_ocorrencia;
    public $tipo_ocorrencia;
    public $filial;
    public $matricula;
    public $numero_transacao;
    public $valor_ocorrencia = 'R$ 0,00';
    public $observacoes;
    public $search;
    public $func = [];
    public $files = [];

    public function mount()
    {
        $Tipo_ocorrencias = DB::connection('oracle')->select('select codtipo, descricao from bdc_registros_tipos@dbl200 order by codtipo');
        $this->Tipo_ocorrencias = $Tipo_ocorrencias;

        $Filiais = DB::connection('oracle')->select('SELECT   pc.codigo AS codfil, pc.contato AS nomfil
                                      FROM       pcfilial pc
                                             INNER JOIN
                                                 r030fil@dblsenior fil
                                             ON pc.codigo = fil.codfil and pc.codigo <> 11');
        $this->Filiais = $Filiais;


    }

    public function cadastrar()
    {
        if (empty($this->data_ocorrencia) || empty($this->tipo_ocorrencia) || empty($this->matricula) || empty($this->filial) || empty($this->observacoes)) {
            $this->alert('error', 'Preencha todos os campos!');
            return;
        }

        $data_ocorrencia = $this->data_ocorrencia;
        $tipo_ocorrencia = $this->tipo_ocorrencia;
        $matricula = $this->matricula;
        $numero_transacao = $this->numero_transacao;
        $filial = $this->filial;
        $observacoes = $this->observacoes;
        $valor_ocorrencia = str_replace(['R$ ', '.', ','], ['', '', '.'], $this->valor_ocorrencia);

        $matricula = DB::connection('oracle')->select('select matricula from pcempr where matricula = ?', [$matricula]);
        if (empty($matricula)) {
            $this->alert('error', 'Matrícula do funcionário não encontrado!');
            return;
        }

        $matricula = $matricula[0]->matricula;

        $seq = DB::connection('oracle')->select('select seq_reg_ocorrencias_id.NEXTVAL@dbl200 as seq from dual');
        $seq = $seq[0]->seq;

        DB::connection('oracle')->insert('insert into bdc_registros_ocorrencias@dbl200 (id, codusuario, tipo_registro, data, filial, codfunc, data_criacao, descricao, numero_transacao, valor_ocorrencia)
        values (?, ?, ?, ?, ?, ?, SYSDATE, ?, ?, ?)',
            [$seq, auth()->user()->matricula, $tipo_ocorrencia, $data_ocorrencia, $filial, $matricula, $observacoes, $numero_transacao, $valor_ocorrencia]);

        $files = $this->files;
        $directory = public_path('ocorrencia_files');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($files as $file) {
            if ($file->isValid()) {
                $fileName = $file->getClientOriginalName();
                $fileName = md5($fileName . time()) . '.' . $file->getClientOriginalExtension();

                $file->storeAs('ocorrencia_files', $fileName, 'public');
                DB::connection('oracle')->insert('insert into bdc_registros_dirimg@dbl200 (id_ocorrencia, file_name) values (?, ?)', [$seq, $fileName]);
            } else {
                $this->alert('error', 'Erro ao processar o arquivo: ' . $file->getClientOriginalName());
            }
        }

        $this->data_ocorrencia = null;
        $this->tipo_ocorrencia = null;
        $this->matricula = null;
        $this->numero_transacao = null;
        $this->valor_ocorrencia = 'R$ 0,00';
        $this->filial = null;
        $this->observacoes = null;
        $this->files = null;
        $this->search = null;
        $this->func = [];
        $this->alert('success', 'Registro cadastrado com sucesso!');
    }


    public function matriculas()
    {
        if (empty($this->search)) {
            $this->func = [];
            return;
        }
        $mat = DB::connection('oracle')->select("select matricula|| ' - '  || nome AS nome, matricula from pcempr where ( matricula like ? or upper(nome) like upper(?) ) and rownum <= 5", [$this->search . '%', $this->search . '%']);
        $this->func = $mat;
    }

    public function selectUser($nome, $matricula)
    {
        $this->search = $nome;
        $this->matricula = $matricula;
        $this->func = [];
    }

    public function render()
    {
        return view('livewire.ocorrencias.home')->layout('layouts.home-layout');
    }
}
