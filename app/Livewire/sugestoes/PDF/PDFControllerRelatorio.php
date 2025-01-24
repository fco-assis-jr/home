<?php

namespace App\Livewire\sugestoes\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;


class PDFControllerRelatorio extends Controller
{

    public $itensc = [];

    public function visualizarPDFrelatorio(Request $request)
    {
        // Recuperar a chave do cache passada na URL
        $cacheKey = $request->get('cacheKey-relatorio');

        // Buscar os dados do cache usando a chave
        $value = Cache::get($cacheKey);

        if (!$value) {
            return response()->json(['message' => 'Abra Novamento o PDF!'], 404);
        }

        foreach ($value as $key => &$item) {
           $item->vl_reembolso = 'R$ ' . number_format($item->vl_reembolso, 2, ',', '.');
           $item->vl_oferta = 'R$ ' . number_format($item->vl_oferta, 2, ',', '.');
           $item->valor_produto = 'R$ ' . number_format($item->valor_produto, 2, ',', '.');
       }

        $pdf = Pdf::loadView('livewire.sugestoes.PDF.pdf-view-relatorio', ['itensc' => $value])
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio.pdf');
    }

    public function formatMoeda($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }


}
