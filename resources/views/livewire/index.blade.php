<div>
    <div class="app-title">
        <h1><i class="bi bi-speedometer"></i> Dashboard</h1>
        <p>Resumo</p>
    </div>

    <div class="row">
        @foreach(session('bdc_controc') as $bdc_controc)
            @if($bdc_controc->codmod == 1444)
                <div class="col-md-6 col-lg-3">
                    <div class="widget-small primary coloured-icon"><i class="icon bi bi-people fs-1"></i>
                        <div class="info">
                            <h4>QT SUGESTOES</h4>
                            <p><b>{{ $qtsugestoes }}</b></p>
                        </div>
                    </div>
                </div>
            @endif
            @if($bdc_controc->codmod == 8177)
                <div class="col-md-6 col-lg-3">
                    <div class="widget-small danger coloured-icon"><i class="icon bi bi-exclamation-circle fs-1"></i>
                        <div class="info">
                            <h4>QT OCORRENCIAS</h4>
                            <p><b>{{ $qtocorrencias }}</b></p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @foreach(session('bdc_controc') as $bdc_controc)
        @if($bdc_controc->codmod == 8177)
            <div class="col-md-6">
                <div class="tile">
                    <h3 id="currentSelection" style="text-align: center; margin-top: 10px;">Tipos de Ocorrências</h3>
                    <div style="text-align: center; margin: 10px 0;">
                        <label for="filialSelect">Selecionar Filial:</label>
                        <select id="filialSelect" style="margin-left: 5px;">
                            <option value="">Todas as Filiais</option>
                            @foreach(array_keys($jsonOcorrenciasFilial) as $filial)
                                <option value="{{ $filial }}">Filial {{ $filial }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="graficoOcorrencias" style="height: 400px;"></div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const allData = @json($jsonOcorrenciasFilial); // Dados detalhados por filial
        const totalData = @json($jsonOcorrencias); // Total consolidado para todas as filiais
        const graficoElement = document.getElementById('graficoOcorrencias');
        const filialSelect = document.getElementById('filialSelect');
        const currentSelection = document.getElementById('currentSelection');
        let chart = echarts.init(graficoElement);

        // Função para renderizar o gráfico com rótulos personalizados
        function renderChart(data, titulo) {
            const options = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'horizontal',
                    bottom: 10,
                    data: data.map(item => item.name),
                    padding: [0, 0, -10, 0]
                },
                series: [{
                    name: 'Tipos de Ocorrências',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        formatter: '{b}: {c} ({d}%)'
                    },
                    data: data
                }]
            };
            chart.setOption(options);
            currentSelection.innerText = titulo;
        }

        // Função para atualizar o gráfico conforme a seleção da filial
        function atualizarGraficoPorFilial() {
            const filialSelecionada = filialSelect.value;
            if (filialSelecionada && allData[filialSelecionada]) {
                renderChart(allData[filialSelecionada], `Tipos de Ocorrências - Filial ${filialSelecionada}`);
            } else {
                renderChart(totalData, "Tipos de Ocorrências - Todas as Filiais");
            }
        }

        // Adiciona o evento 'change' ao select para chamar a função de atualização
        filialSelect.addEventListener('change', atualizarGraficoPorFilial);

        // Renderiza o gráfico inicial com o total consolidado
        atualizarGraficoPorFilial();
    });
</script>
