<?php

namespace App\Livewire\sugestoes\PDF;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PDFControllerComprovante extends Controller
{
    /**
     * Gera o comprovante em PDF de uma requisição de sugestão (bdc_sugestoesc/bdc_sugestoesi).
     *
     * Diferente dos outros PDFs do módulo, este consulta os dados direto do Oracle pelo
     * codsug (em vez de usar Cache) para poder ser reimpresso a qualquer momento a partir
     * da tela de Solicitados, mesmo muito tempo depois do cadastro original.
     */
    public function visualizarComprovante($codsug)
    {
        $cabecalho = DB::connection('oracle')->selectOne(
            'SELECT c.codsug,
                    c.codfilial,
                    c.codusuario,
                    TO_CHAR(c.data, \'DD/MM/YYYY HH24:MI:SS\') AS data_criacao,
                    p.nome AS nome_usuario
             FROM   bdc_sugestoesc c
             INNER JOIN pcempr p ON p.matricula = c.codusuario
             WHERE  c.codsug = ?',
            [$codsug]
        );

        if (!$cabecalho) {
            abort(404, 'Requisição não encontrada!');
        }

        // Só o próprio solicitante (ou quem tem acesso ao módulo de Permissões) pode reimprimir o comprovante
        $matriculaLogado = auth()->user()->matricula;
        $temAcessoTotal = collect(session('bdc_controc'))->contains(fn ($item) => $item->codmod == '800');

        if ($cabecalho->codusuario != $matriculaLogado && !$temAcessoTotal) {
            abort(403, 'Você não tem permissão para visualizar esta requisição!');
        }

        $itens = DB::connection('oracle')->select(
            'SELECT i.codsugitem,
                    i.codauxiliar,
                    i.descricao,
                    i.valor_produto,
                    i.quantidade,
                    i.unid,
                    TO_CHAR(i.data_vencimento, \'DD/MM/YYYY\') AS data_vencimento,
                    i.codfornec,
                    i.status,
                    i.vl_oferta,
                    f.fornecedor
             FROM   bdc_sugestoesi i
             LEFT JOIN pcfornec f ON f.codfornec = i.codfornec
             WHERE  i.codsug = ?
             ORDER BY i.codsugitem',
            [$codsug]
        );

        foreach ($itens as $item) {
            $item->valor_produto = 'R$ ' . number_format($item->valor_produto, 2, ',', '.');
            $item->vl_oferta = $item->vl_oferta > 0
                ? 'R$ ' . number_format($item->vl_oferta, 2, ',', '.')
                : null;
        }

        $pdf = Pdf::loadView('livewire.sugestoes.PDF.pdf-comprovante', [
            'cabecalho' => $cabecalho,
            'itens' => $itens,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("comprovante-sugestao-{$codsug}.pdf");
    }
}
