<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use App\Models\Pclib;
use Dflydev\DotAccessData\Data;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use mysql_xdevapi\Exception;


class Index extends Component
{
    use LivewireAlert;



    public function render()
    {
        return view('livewire.index')->layout('layouts.home-layout');
    }
}
