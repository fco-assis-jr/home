<?php

namespace App\Livewire\sugestoes\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;


class PDFController extends Controller
{

    public $itensc = [];

    public function visualizarPDF(Request $request)
    {

        $this->itensc = $request->input('itensc');
        $pdf = Pdf::loadView('livewire.sugestoes.PDF.pdf-view', ['itensc' => $this->itensc])->setPaper('a4', 'landscape');
        return $pdf->stream('relatorio.pdf');
    }

}
