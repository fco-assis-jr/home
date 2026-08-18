<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Sugestão {{ $cabecalho->codsug }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #212529;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #212529;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }

        .header .subtitulo {
            font-size: 11px;
            color: #555;
        }

        .header .protocolo {
            text-align: right;
            font-size: 11px;
        }

        .header .protocolo b {
            font-size: 14px;
        }

        .dados-requisicao {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .dados-requisicao td {
            padding: 4px 8px;
            font-size: 11px;
            vertical-align: top;
        }

        .dados-requisicao td.label {
            color: #555;
            white-space: nowrap;
            width: 1%;
        }

        table.itens {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.itens, table.itens th, table.itens td {
            border: 1px solid #999;
        }

        table.itens th {
            background-color: #f4f4f4;
            text-align: center;
            padding: 6px 4px;
            font-size: 10px;
            text-transform: uppercase;
        }

        table.itens td {
            padding: 6px 4px;
            font-size: 10.5px;
            text-align: center;
        }

        table.itens td.descricao {
            text-align: left;
        }

        table.itens tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f4f4f4;
        }

        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 9px;
            color: #fff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-ativo {
            background-color: #0d6efd;
        }

        .badge-lancado {
            background-color: #dc3545;
        }

        table.assinaturas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 35px;
        }

        table.assinaturas td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }

        .assinatura .espaco {
            height: 18px;
        }

        .assinatura .linha {
            border-top: 1px solid #212529;
        }

        .assinatura .label {
            margin-top: 4px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #333;
        }

        .rodape {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>COMPROVANTE DE SUGESTÃO</h1>
        <div class="subtitulo">Sistema de Sugestões</div>
    </div>
    <div class="protocolo">
        Nº da Requisição<br>
        <b>{{ $cabecalho->codsug }}</b>
    </div>
</div>

<table class="dados-requisicao">
    <tr>
        <td class="label">Solicitante:</td>
        <td>{{ $cabecalho->nome_usuario }} ({{ $cabecalho->codusuario }})</td>
        <td class="label">Filial:</td>
        <td>{{ $cabecalho->codfilial }}</td>
    </tr>
    <tr>
        <td class="label">Data/Hora da Requisição:</td>
        <td>{{ $cabecalho->data_criacao }}</td>
        <td class="label">Emitido em:</td>
        <td>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</td>
    </tr>
</table>

<table class="itens">
    <thead>
    <tr>
        <th>Cód.</th>
        <th style="text-align: left">Descrição</th>
        <th>Fornecedor</th>
        <th>Qtd.</th>
        <th>Unid.</th>
        <th>Valor</th>
        <th>Vencimento</th>
        <th>Status</th>
        <th>Vl. Oferta</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($itens as $item)
        <tr>
            <td>{{ $item->codauxiliar }}</td>
            <td class="descricao">{{ $item->descricao }}</td>
            <td>{{ $item->codfornec }} @if($item->fornecedor) - {{ $item->fornecedor }} @endif</td>
            <td>{{ $item->quantidade }}</td>
            <td>{{ $item->unid }}</td>
            <td>{{ $item->valor_produto }}</td>
            <td>{{ $item->data_vencimento }}</td>
            <td>
                <span class="badge {{ $item->status == '0' ? 'badge-ativo' : 'badge-lancado' }}">
                    {{ $item->status == '0' ? 'Ativo' : 'Lançado' }}
                </span>
            </td>
            <td>{{ $item->vl_oferta ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">Total de Itens</td>
        <td colspan="6">{{ count($itens) }}</td>
    </tr>
    </tfoot>
</table>

<table class="assinaturas">
    <tr>
        <td class="assinatura">
            <div class="espaco"></div>
            <div class="linha"></div>
            <div class="label">Setor Placas</div>
        </td>
        <td class="assinatura">
            <div class="espaco"></div>
            <div class="linha"></div>
            <div class="label">Encarregado</div>
        </td>
        <td class="assinatura">
            <div class="espaco"></div>
            <div class="linha"></div>
            <div class="label">Repositor</div>
        </td>
    </tr>
</table>

<div class="rodape">
    Este comprovante é gerado automaticamente pelo sistema e reflete a situação atual da requisição no momento da emissão.
</div>

</body>
</html>
