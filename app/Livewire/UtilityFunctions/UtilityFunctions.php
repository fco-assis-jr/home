<?php

namespace App\Livewire\UtilityFunctions;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait UtilityFunctions
{
    use LivewireAlert;
    public function toast($type, $message)
    {
        $this->alert($type, $message, [
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    public function formatMoeda($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}
