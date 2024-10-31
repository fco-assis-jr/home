<?php

namespace App\Livewire\sugestoes\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;


class PDFController extends Controller
{

    public function visualizarPDF(Request $request)
    {

        $codsug = Crypt::decrypt($request->input('itensc'));

        $header = DB::connection('oracle')->select("SELECT *
                                    FROM BDC_SUGESTOESC@DBL200 C, PCEMPR P
                                    WHERE C.CODUSUARIO = P.MATRICULA AND C.CODSUG = ?",[$codsug] );


        $itensc=$header;

            $itensi = DB::connection('oracle')->select("SELECT (select SUBSTR (buscaprecos(c.codfilial,1,i.codauxiliar,SYSDATE ), 1,
                                        INSTR (buscaprecos(c.codfilial,1,i.codauxiliar,SYSDATE ), ';', 1) - 1)
                                        from dual) preco,i.*,c.*,e.*
                                            FROM BDC_SUGESTOESI@DBL200 I, BDC_SUGESTOESC@DBL200 C, PCEMBALAGEM E
                                                WHERE C.CODSUG = I.CODSUG
                                                AND E.CODAUXILIAR = I.CODAUXILIAR
                                                AND C.CODFILIAL = E.CODFILIAL
                                                AND I.STATUS=1
                                                AND I.CODSUG =  ?", [$itensc[0]->codsug]);
        $itensc['itensi'] = $itensi;
        $itensc['pcempr'] = auth()->user();


        $dados = [
            'titulo' => 'Relatório',
            'conteudo' => '',
            'itensc' => $itensc,
        ];

        $pdf = Pdf::loadView('livewire.sugestoes.PDF.pdf-view', $dados)->setPaper('a4', 'landscape');


        return $pdf->stream('relatorio.pdf');
    }
}
