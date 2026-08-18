<?php

namespace App\Livewire\sugestoes\PDF;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PDFControllerComprovante extends Controller
{
    /**
     * Gera o comprovante em PDF de uma requisição de sugestão (bdc_sugestoesc/bdc_sugestoesi).
     *
     * Diferente dos outros PDFs do módulo, este consulta os dados direto do Oracle pelo
     * codsug (em vez de usar Cache) para poder ser reimpresso a qualquer momento a partir
     * da tela de Solicitados, mesmo muito tempo depois do cadastro original.
     *
     * Como o cabeçalho (bdc_sugestoesc) é compartilhado por todos os itens do mesmo
     * usuário/filial/dia, um parâmetro opcional "itens" (lista de codsugitem separados por
     * vírgula) permite mostrar só os itens de uma gravação específica — usado ao salvar em
     * /sugestoes/home, para não imprimir junto itens de gravações anteriores no mesmo dia.
     * Quando "itens" não é informado (reimpressão a partir de Solicitados), mostra a
     * requisição inteira.
     */
    public function visualizarComprovante(Request $request, $codsug)
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

        $sql = 'SELECT i.codsugitem,
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
                WHERE  i.codsug = ?';
        $bindings = [$codsug];

        $codsugitensFiltro = array_filter(array_map('intval', explode(',', (string) $request->query('itens'))));
        if (!empty($codsugitensFiltro)) {
            $placeholders = implode(',', array_fill(0, count($codsugitensFiltro), '?'));
            $sql .= " AND i.codsugitem IN ({$placeholders})";
            $bindings = array_merge($bindings, array_values($codsugitensFiltro));
        }

        $sql .= ' ORDER BY i.codsugitem';

        $itens = DB::connection('oracle')->select($sql, $bindings);

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
