<div class="flex justify-between gap-3">
    <table class="table table-bordered table-hover  mt-3 border-1 border-black ">
        <thead>
        <tr class="text-uppercase text-center">
            <th>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 flex gap-3 pb-2" style="font-size: 15px;">
                            <div>
                                <span>COD</span>
                            </div>
                            <div class="w-full text-left">
                                <span>DESCRIÇÃO</span>
                            </div>
                            <div>
                                <span>VENCIMENTO</span>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
            <th>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                            <span>ÚLTIMA ENTRADA</span>
                        </div>
                        <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                            <span>Dt.Ult.Ent</span>
                            <span>Valor</span>
                            <span>Qtde</span>
                        </div>
                    </div>
                </div>
            </th>
            <th>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-2" style="font-size: 15px;">
                            <span>QUANTIDADE VENDA MÊS</span>
                        </div>
                        <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                            <span>Atual</span>
                            <span>Ant1</span>
                            <span>Ant2</span>
                            <span>Ant3</span>
                        </div>
                        <div class="col-md-12 text-center" style="font-size: 14px;">
                            <span>MÉDIA GIRO</span>
                        </div>
                    </div>
                </div>
            </th>
            <th>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                            <span>ESTOQUE</span>
                        </div>
                        <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                            <span>Disp</span>
                            <span>Fat CD</span>
                            <span>Ped CD</span>
                            <span>Dias</span>
                        </div>
                    </div>
                </div>
            </th>
            <th>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                            <span>MARGEM PREÇO</span>
                        </div>
                        <div class="col-md-12 flex justify-between gap-3" style="font-size: 14px;">
                            <span>P.Venda</span>
                            <span>Mg-Atual</span>
                            <span>Mg-Winthor</span>
                        </div>
                    </div>
                </div>
            </th>
            <th>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                            <span>SUGESTÃO</span>
                        </div>
                    </div>
                </div>
            </th>
            <th style="width: 7%">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                            <span>VL REEMBOLSO</span>
                        </div>
                    </div>
                </div>
            </th>
            <th style="width: 7%">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center pb-4" style="font-size: 15px;">
                            <span>VL OFERTA</span>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($dados_cursor as $index => $item)
            <tr class="{{ ($item['VL_REEMBOLSO'] <= 0 || $item['VL_OFERTA'] <= 0) ? 'bg-red-400 text-white' : '' }}" wire:key="{{ $index }}">
                <td class="text-uppercase text-center">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 flex gap-3 pb-2" style="font-size: 14px;">
                                <div>
                                    <span>{{ $item['CODPROD'] }}</span>
                                </div>
                                <div class="w-full text-left">
                                                        <span
                                                            title="{{ $item['DESCRICAO'].' '.$item['EMBALAGEMMASTER'] }}">
                                                            {{ Str::limit($item['DESCRICAO'].' '.$item['EMBALAGEMMASTER'], 30, '...') }}
                                                        </span>
                                </div>
                            </div>
                            <div class="col-md-12 flex justify-between gap-3"
                                 style="font-size: 14px;">
                                <span>{{ $item['CODAUXILIAR'] }}</span>
                                <span>{{ $item['ICMS'] }}</span>
                                <span class="{{ ($item['VL_REEMBOLSO'] <= 0 || $item['VL_OFERTA'] <= 0) ? 'text-white' : 'text-red-500' }}">{{ $item['DATA_VENCIMENTO'] }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-uppercase text-center ">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 flex justify-between gap-3 pb-2"
                                 style="font-size: 14px;">
                                <span>{{ date('d/m/Y', strtotime($item['DTULTENT'])) }}</span>
                                <span>{{ $this->formatMoeda($item['VALORENT']) }}</span>
                                <span>{{ $item['QTULTENT'] }}</span>
                            </div>
                            <div class="col-md-12 flex justify-between gap-3"
                                 style="font-size: 14px;">
                                <span>Unidade ></span>
                                <span>{{ $this->formatMoeda($item['CD_UNIDADE']) }}</span>
                                <span class="text-transparent">{{ $item['QTULTENT'] }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-uppercase text-center">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 flex justify-between gap-3 pb-2"
                                 style="font-size: 14px;">
                                <span>{{ $item['QTVENDMES'] }}</span>
                                <span>{{ $item['QTVENDMES1'] }}</span>
                                <span>{{ $item['QTVENDMES2'] }}</span>
                                <span>{{ $item['QTVENDMES3'] }}</span>
                            </div>
                            <div class="col-md-12 flex justify-between gap-3"
                                 style="font-size: 14px;">
                                <span>Dia {{ $item['QTGIRODIA'] }}</span>
                                <span>Sem {{ $item['QTGIROSEMANA'] }}</span>
                                <span>Mês {{ $item['QTGIROMES'] }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-uppercase text-center">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 flex justify-between gap-3 pb-2"
                                 style="font-size: 14px;">
                                <span>{{ $item['QTESTGER'] }}</span>
                                <span>Fat CD</span>
                                <span>Ped CD</span>
                                <span>{{ $item['ESTDIAS'] }}</span>
                            </div>
                            <div class="col-md-12 flex justify-between gap-3"
                                 style="font-size: 14px;">
                                <span class="text-transparent">CD CX ></span>
                                <span class="{{ ($item['VL_REEMBOLSO'] <= 0 || $item['VL_OFERTA'] <= 0) ? 'text-white' : 'text-red-500' }}">QT {{ $item['QUANTIDADE'] }}</span>
                                <span>CD CX></span>
                                <span>{{ $item['CD_CX'] }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 flex justify-between gap-3"
                                 style="font-size: 14px;">

                                @php
                                    $margem_pvenda = explode(' - ', $item['MARGEM_PVENDA']);
                                @endphp

                                @if (count($margem_pvenda) > 1)
                                    <span class="oferta-margem-pvenda">oferta</span>
                                    <span style="margin-left: -84px;margin-top: 10px;">{{ $margem_pvenda[0] }}</span>
                                @else
                                    {{ $margem_pvenda[0] }}
                                @endif

                                <span>{{ $item['MARGEM_ATUAL'] }}</span>
                                <span>{{ $item['MARGEM_WINTHOR'] }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 text-center pb-2" style="font-size: 14px;">
                                <span>{{ $item['SUGCOMPRA'] }}</span>
                            </div>
                            <div class="col-md-12 flex justify-between gap-3"
                                 style="font-size: 14px;">
                                <span>Pendente</span>
                                <span>{{ $item['QTPEDIDA'] }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="vertical-align:middle">
                    <input type="text" class="form-control" value="{{ $this->formatMoeda($item['VL_REEMBOLSO']) }}"
                           onkeyup="k(this);"
                           wire:change="updateValue('{{ $index }}', 'VL_REEMBOLSO', $event.target.value)">
                </td>
                <td style="vertical-align:middle">
                    <input type="text" class="form-control" value="{{ $this->formatMoeda($item['VL_OFERTA']) }}"
                           onkeyup="k(this);"
                           wire:change="updateValue('{{ $index }}', 'VL_OFERTA', $event.target.value)">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <script>
        function k(i) {
            let value = i.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            value = (parseInt(value || '0') / 100).toFixed(2); // Divide por 100 para incluir as casas decimais
            i.value = formatMoeda(value); // Formata como moeda com R$
        }

        function formatMoeda(value) {
            const val = parseFloat(value || 0) > 200 ? 200 : parseFloat(value || 0);
            return val.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });

        }
    </script>
</div>
