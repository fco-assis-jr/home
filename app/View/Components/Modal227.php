<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal227 extends Component
{


    public $dados_cursor;
    public $data_inicial;
    public $data_final;


    public function __construct($dados)
    {
        $this->dados_cursor = $dados;
        $dt_inicial = \DateTime::createFromFormat('Y-m-d', date('Y-m-01'))->format('d/m/Y');
        $dt_final = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y');

        $this->data_inicial = $dt_inicial;
        $this->data_final = $dt_final;
    }


    public function render(): View|Closure|string
    {
        return view('components.modal227');
    }
}
