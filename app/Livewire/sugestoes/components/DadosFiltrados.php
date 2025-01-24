<?php

namespace App\Livewire\Sugestoes\Components;

use Livewire\Component;

class DadosFiltrados extends Component
{
    public $dados_filtrados = [];
    public $tabela;

    public function mount($dados_filtrados, $tabela) // Parâmetro corrigido
    {
        $this->dados_filtrados = $dados_filtrados;
        $this->tabela = $tabela;
    }

    public function render()
    {
        return view('livewire.sugestoes.components.dados-filtrados');
    }
}
