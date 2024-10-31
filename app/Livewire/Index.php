<?php

namespace App\Livewire;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class Index extends Component
{
    use LivewireAlert;

    public function render()
    {
        return view('livewire.index')->layout('layouts.home-layout');
    }
}
