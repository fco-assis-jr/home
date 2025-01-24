<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF VIEW</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .header div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            white-space: nowrap;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f4f4f4;
            font-size: 8px;
        }

        .red {
            color: red;
        }

        td {
            white-space: nowrap;
            font-size: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:nth-child(odd) {
            background-color: #fff;
        }
    </style>
</head>
<body>
<div class="header" style="padding-bottom: 1rem">
    <div>
        <span style="position: absolute; margin-left: 950px; margin-top: -30px; font-size: 10px">
            {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}<br>
            <span style="color: red; font-size: 10px">{{ auth()->user()->matricula }} | {{ auth()->user()->usuariobd }}</span>
        </span>
    </div>
    <div style="width: 100%; text-align: center; position: absolute; margin-top: 10px">
        <span><b>{{ $itensc[0]->tabeladesc }}: {{ $itensc[0]->descselecionado }}</b></span>
    </div>
</div>
<div>
    <table>
        <thead>
        <tr>
            <th>CODSUG</th>
            <th>CODPROD</th>
            <th>PRODUTO</th>
            <th>CODAUXILIAR</th>
            <th>VALOR PRODUTO</th>
            <th>QUANTIDADE</th>
            <th>DATA VENCIMENTO</th>
            <th>VL OFERTA</th>
            <th>INI OFERTA</th>
            <th>FIM OFERTA</th>
        </tr>
        </thead>
        <tbody>
        @foreach($itensc as $index => $item)
            <tr wire:key="{{ $index }}">
                <td>{{ $item->codsug }}</td>
                <td>{{ $item->codprod }}</td>
                <td style="text-align: left">{{ $item->descricao_produto }}</td>
                <td>{{ $item->codauxiliar }}</td>
                <td>{{ $item->valor_produto }}</td>
                <td>{{ $item->quantidade }} {{ $item->unidade }}</td>
                <td>{{ $item->data_vencimento }}</td>
                <td>{{ $item->vl_oferta }}</td>
                <td>{{ $item->inioferta }}</td>
                <td>{{ $item->fimoferta }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--<div style="position: absolute; margin-top: 50px; font-size: 10px; margin-left: 570px; width: 45%;">
        <textarea rows="50" cols="100" style="text-align: justify; height: 400px; border: none; font-family: Arial, sans-serif;font-size: 13px; padding: 10px">     {{ $itensc[0]->descricaosug }}</textarea><br>
    </div>--}}
</div>

</body>
</html>


