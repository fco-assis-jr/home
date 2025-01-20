<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tabela227 extends Component
{
    public $dados_cursor;

    public function __construct($dados)
    {
        $this->dados_cursor = $dados;
    }

    public function render(): View|Closure|string
    {
        return view('components.tabela227');
    }
}
