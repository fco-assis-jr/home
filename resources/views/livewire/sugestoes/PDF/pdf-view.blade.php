<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$titulo}}</title>


    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .topo-direito {
            position: absolute;
            top: 0;
            right: 0;
            padding: 10px;
            font-size: 14px;
        }
        .conteudo {
            margin-top: 40px;
            text-align: center;
        }
        .container {
            width: 100%;
            margin: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }
        .header div {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        .header .icon {
            font-size: 14px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            padding: 2px 4px;
            text-align: center;
            border: 1px solid #000;
            line-height: 1;
        }
        .table th {
            background-color: #eaeaea;
            font-weight: bold;
        }
        .table td {
            background-color: #f7f7f7;
        }
        .assinatura {
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }
        .linha-assinatura {
            margin-top: 20px;
            width: 300px;
            border-top: 1px solid #000;
        }
        .valor {
            text-align: right;
            font-family: Arial, sans-serif;
        }
        .td-valor {
            position: relative;
            font-family: Arial, sans-serif;
            padding-right: 10px;
        }
        .simbolo {
            position: absolute;
            left: 2.5;
        }
        .valor {
            text-align: right;
            display: inline-block;
            width: 100%;
        }
         .rodape {
            position: absolute;
            bottom: 10px;
            left: 10px; /* Alinha o texto à esquerda */
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="topo-direito">
    <strong>{{$itensc['pcempr']->usuariobd}} | {{$itensc['pcempr']->matricula}}</strong>
</div>


<div class="conteudo">
    <h1>Sugetão Preço</h1>
    <p></p>
</div>
<div class="container">


    <div class="header">

        <div>
            <i class="fa-solid fa-user icon"></i>
            <span>Nome: {{$itensc[0]->nome_guerra}} | {{$itensc[0]->matricula}}</span>
        </div>
        <div>
            <i class="fa-solid fa-building icon"></i>
            <span>Filial: {{$itensc['itensi'][0]->codfilial}}</span>
        </div>
        <div>
            <i class="fa-solid fa-calendar-days icon"></i>
            <span>Data da Solicitação: {{\Carbon\Carbon::parse($itensc[0]->data)->format('d/m/Y H:i:s')}}</span>
        </div>
    </div>

    <!-- Tabela -->
    <table class="table">
        <thead>
        <tr>
            <th>CODPROD</th>
            <th>CODAUXILIAR</th>
            <th>NOME</th>
            <th>UNIDADE</th>
            <th>PVENDA</th>
            <th>VALOR SUGERIDO</th>
            <th>DATA VENCIMENTO</th>
            <th>QUANTIDADE</th>

        </tr>
        </thead>
        <tbody>

        @foreach($itensc['itensi'] as $itensi)
            <tr >

                <td>{{$itensi->codprod}}</td>
                <td>{{$itensi->codauxiliar}}</td>
                <td>{{$itensi->descricao}}</td>
                <td>{{$itensi->unidade}}</td>

                <td class="td-valor">
                    <span class="simbolo">R$</span>
                    <span class="valor">{{number_format($itensi->preco, 2, ',', '.')}}</span>
                </td>
                <td class="td-valor">
                    <span class="simbolo">R$</span>
                    <span class="valor">{{number_format($itensi->valor_sugerido, 2, ',', '.')}}</span>
                </td>
                <td>{{\Carbon\Carbon::parse($itensi->data_vencimento)->format('d/m/Y') }}</td>
                <td>{{$itensi->quantidade}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="assinatura">
        <div class="linha-assinatura"></div>
        <p>{{$itensc['pcempr']->nome}}</p>
    </div>
</div>
<br>
 <div class="rodape">
        <p>Data e Hora da Impressão: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
