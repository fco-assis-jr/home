<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalFichaFornecedor extends Component
{
    public $cabecario_227_agrupado = []; // Renomeado para corresponder ao nome recebido
    public $data_inicial;
    public $data_final;
    public $nome;
    public $filial;
    public $primeiroFornecedor;

    public function __construct($dados, $nome, $filial)
    {

        $dt_inicial = \DateTime::createFromFormat('Y-m-d', date('Y-m-01'))->format('d/m/Y');
        $dt_final = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y');

        $this->cabecario_227_agrupado = $dados;
        $this->data_inicial = $dt_inicial;
        $this->data_final = $dt_final;
        $this->nome = $nome;
        $this->filial = $filial;
        $this->primeiroFornecedor = array_key_first($this->cabecario_227_agrupado);

    }

    public function render(): View|Closure|string
    {
        return view('components.modalFichaFornecedor');
    }
}
