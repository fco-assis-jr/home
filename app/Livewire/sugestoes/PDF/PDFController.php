<?php

namespace App\Livewire\sugestoes\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;


class PDFController extends Controller
{

    public $itensc = [];

    public function visualizarPDF(Request $request)
    {
        // Recuperar a chave do cache passada na URL
        $cacheKey = $request->get('cacheKey');

        // Buscar os dados do cache usando a chave
        $value = Cache::get($cacheKey);

        if (!$value) {
            return response()->json(['message' => 'Abra Novamento o PDF!'], 404);
        }

        foreach ($value as $key => &$item) {
           $item['VL_REEMBOLSO'] = 'R$ ' . number_format($item['VL_REEMBOLSO'], 2, ',', '.');
           $item['VL_OFERTA'] = 'R$ ' . number_format($item['VL_OFERTA'], 2, ',', '.');
       }


        $pdf = Pdf::loadView('livewire.sugestoes.PDF.pdf-view', ['itensc' => $value])
            ->setPaper('a4', 'landscape');



        return $pdf->stream('relatorio.pdf');
    }

    public function formatMoeda($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }


}
