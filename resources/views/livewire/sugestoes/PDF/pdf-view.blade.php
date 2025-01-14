<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Técnica por Fornecedor Master</title>
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
        }

        .red {
            color: red;
        }

        td {
            white-space: nowrap;
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
<div class="header" style="padding-bottom: 3rem">
    <div>
        <span style="position: absolute; font-size: 20px"><b>227 - FICHA TECNICA POR FORNECEDOR</b></span>
        <span style="position: absolute; margin-left: 880px">
            {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}<br>
            <span style="color: red; font-size: 14px">{{ auth()->user()->matricula }} | {{ auth()->user()->usuariobd }}</span>
        </span>
    </div>
    <div style="position: absolute; margin-top: 50px;">
        <span>FORNECEDOR: {{ $itensc[0]['CODFORNEC'] }} - {{ $itensc[0]['FORNECEDOR'] }} </span><br>
        <span style="color: red">FILIAL:{{ $itensc[0]['CODFILIAL'] }}</span>
        {{-- temos que colocar o numero da pagina aqui --}}

    </div>
</div>
<div>
    <table>
        <thead>
        <tr>
            <th style="height: 25px;">
                <div class="col-md-12">
                    <div class="row">
                        <div style="font-size: 8px">
                            <span style="position: absolute;">COD.</span>
                            <span style="position: absolute; margin-left: 32px">DESCRIÇÃO</span>
                            <span style="position: absolute; margin-left: 225px">VENCIMENTO</span>
                        </div>
                    </div>
                </div>
            </th>
            <th style="width: 13%;">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left: 20px; position: absolute;">
                        ÚLTIMA ENTRADA
                    </div>
                    <div style="text-align: left; position: absolute">
                        <span style="position: absolute; margin-left: -5px">DATA</span>
                        <span style="position: absolute; margin-left: 45px">VALOR</span>
                        <span style="position: absolute; margin-left: 95px">QTDE</span>
                    </div>
                </div>
            </th>
            <th style="width: 13%">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left:10px; position: absolute;">
                        QUANTIDADE VENDA MÊS
                    </div>
                    <div style="text-align: left; position: absolute">
                        <span style="position: absolute; margin-left: -5px">ATUAL</span>
                        <span style="position: absolute; margin-left: 32px">ANT1</span>
                        <span style="position: absolute; margin-left: 70px">ANT2</span>
                        <span style="position: absolute; margin-left: 100px">ANT3</span>
                    </div>
                    <div style="text-align: left; position: absolute; margin-top: 10px; margin-left: 35px">
                        MÉDIA GIRO
                    </div>
                </div>
            </th>
            <th style="width: 13%">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left: 30px; position: absolute;">
                        ESTOQUE
                    </div>
                    <div style="text-align: left; position: absolute">
                        <span style="position: absolute; margin-left: -5px">DISP</span>
                        <span style="position: absolute; margin-left: 25px">FAT CD</span>
                        <span style="position: absolute; margin-left: 60px">PED CD</span>
                        <span style="position: absolute; margin-left: 100px">DIAS</span>
                    </div>
                </div>
            </th>
            <th style="width: 15%">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left: 30px; position: absolute;">
                        MARAGEM - PREÇO
                    </div>
                    <div style="text-align: left; position: absolute">
                        <span style="position: absolute; margin-left: -5px">P.VENDA</span>
                        <span style="position: absolute; margin-left: 40px">MG-ATUAL</span>
                        <span style="position: absolute; margin-left: 90px">MG-WINTHOR</span>
                    </div>
                </div>
            </th>
            <th style="width: 6%">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left: -2px; position: absolute;">
                        SUGUESTÃO
                    </div>
                </div>
            </th>
            <th style="width: 7%">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left: -4px; position: absolute;">
                        VL REEMBOLSO
                    </div>
                </div>
            </th>
            <th style="width: 5%">
                <div style="font-size: 8px">
                    <div style="margin-top: -15px; margin-left: -5px; position: absolute;">
                        VL OFERTA
                    </div>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($itensc as $index => $item)
            <tr>
                <td class="text-uppercase text-center" style="width: 28%">
                    <div class="row">
                        <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: -10px; position: absolute">
                            <span>{{ $item['CODPROD'] }} | {{ Str::limit($item['DESCRICAO'].' '.$item['EMBALAGEMMASTER'], 40, '...') }}</span>
                        </div>
                        <div style="font-size: 9px;text-align: left; margin-top: 5px; position: absolute">
                            <span style="position: absolute; ">{{ $item['CODAUXILIAR'] }}</span>
                            <span style="position: absolute; margin-left: 100px">{{ $item['ICMS'] ?? null }}</span>
                            <span style="color: red;position: absolute; margin-left: 230px">{{ $item['DATA_VENCIMENTO'] }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-uppercase text-center ">
                    <div class="row">
                        <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: -10px; position: absolute">
                            <span style="position: absolute; margin-left: -5px;">{{ date('d/m/Y', strtotime($item['DTULTENT'])) }}</span>
                            <span style="position: absolute; margin-left: 50px">{{ round($item['VALORENT'], 2) }}</span>
                            <span style="position: absolute; margin-left: 100px">{{ $item['QTULTENT'] }}</span>
                        </div>
                        <div style="font-size: 9px;text-align: left; margin-top: 5px; position: absolute">
                            <span style="position: absolute; margin-left: -5px">UNIDADE ></span>
                            <span style="position: absolute; margin-left: 55px">{{ $item['CD_UNIDADE'] }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-uppercase text-center">
                    <div class="row">
                        <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: -10px; position: absolute">
                            <span style="position: absolute">{{ $item['QTVENDMES'] }}</span>
                            <span style="position: absolute; margin-left: 40px">{{ $item['QTVENDMES1'] }}</span>
                            <span style="position: absolute; margin-left: 80px">{{ $item['QTVENDMES2'] }}</span>
                            <span style="position: absolute; margin-left: 105px">{{ $item['QTVENDMES3'] }}</span>
                        </div>
                        <div style="font-size: 9px;text-align: left; margin-top: 5px; position: absolute">
                            <span style="position: absolute">Dia {{ $item['QTGIRODIA'] }}</span>
                            <span style="position: absolute; margin-left: 45px">Sem {{ $item['QTGIROSEMANA'] }}</span>
                            <span style="position: absolute; margin-left: 90px">Mês {{ $item['QTGIROMES'] }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-uppercase text-center">
                    <div class="row">
                        <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: -10px; position: absolute">
                            <span style="position: absolute;">{{ $item['QTESTGER'] }}</span>
                            <span style="position: absolute; margin-left: 25px">Fat CD</span>
                            <span style="position: absolute; margin-left: 60px">Ped CD</span>
                            <span style="position: absolute; margin-left: 105px">{{ $item['ESTDIAS'] }}</span>
                        </div>
                        <div style="font-size: 9px;text-align: left; margin-top: 5px; position: absolute">
                            <span style="color: red; position: absolute">QT {{ $item['QUANTIDADE'] }}</span>
                            <span style="position: absolute; margin-left: 60px">CD CX></span>
                            <span style="position: absolute; margin-left: 105px">{{ $item['CD_CX'] }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="row">
                        <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: -10px; position: absolute">
                            <span style="position: absolute;">{{ $item['MARGEM_PVENDA'] }}</span>
                            <span style="position: absolute; margin-left: 55px">{{ $item['MARGEM_ATUAL'] }}</span>
                            <span style="position: absolute; margin-left: 105px">{{ $item['MARGEM_WINTHOR'] }}</span>
                        </div>
                    </div>
                </td>
                <td style="height: 20px">
                    <div class="row">
                        <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: -10px; position: absolute">
                            <span>{{ $item['SUGCOMPRA'] }}</span>
                        </div>
                        <div style="font-size: 9px;text-align: left; margin-top: 5px; position: absolute">
                            <span>Pend. </span>
                            <span>{{ $item['QTPEDIDA'] }}</span>
                        </div>
                    </div>
                </td>
                <td style="width: 6%;">
                    <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: 5px; position: absolute">
                        <span style="margin-left: 0px">{{ $item['VL_REEMBOLSO'] }}</span>
                    </div>
                </td>
                <td style="width: 6%;">
                    <div style="font-size: 9px; padding-right: 20px; text-align: left; margin-top: 5px; position: absolute">
                        <span id="vlOferta" style="margin-left: -5px;">
                            {{ $item['VL_OFERTA'] }}
                        </span>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>


