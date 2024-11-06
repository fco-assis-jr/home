<div> <!-- Elemento raiz do componente Livewire -->
    <div class="app-title">
        <h1><i class="bi bi-speedometer"></i> Dashboard</h1>
        <p>Resumo das Ocorrências</p>
    </div>

    <div class="row">
        @foreach(session('pccontro') as $pccontro)
            @if($pccontro->codrotina == 1444)
                <div class="col-md-6 col-lg-3">
                    <div class="widget-small primary coloured-icon"><i class="icon bi bi-people fs-1"></i>
                        <div class="info">
                            <h4>QT SUGESTOES</h4>
                            <p><b>{{ $qtsugestoes }}</b></p>
                        </div>
                    </div>
                </div>
            @endif
            @if($pccontro->codrotina == 8177)
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

    <!-- Gráfico de Pizza para Tipos de Ocorrências com filtro por filial -->
    <div class="col-md-6">
        <div class="tile">
            <!-- Título dinâmico para os gráficos -->
            <h3 id="currentSelection" style="text-align: center; margin-top: 10px;">Tipos de Ocorrências</h3>

            <!-- Filtro de Filial integrado na mesma div -->
            <div style="text-align: center; margin: 10px 0;">
                <label for="filialSelect">Selecionar Filial:</label>
                <select id="filialSelect" onchange="atualizarGraficoPorFilial()" style="margin-left: 5px;">
                    <option value="">Todas as Filiais</option>
                    @foreach(array_keys($jsonOcorrenciasFilial) as $filial)
                        <option value="{{ $filial }}">Filial {{ $filial }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botão "Voltar" que só aparece ao sair do gráfico inicial -->
            <button id="homeButton" onclick="voltarAoGraficoAnterior()" style="display: none; margin: 10px 0;">Voltar</button>

            <!-- Container do gráfico -->
            <div id="graficoOcorrencias" style="height: 400px;"></div>
        </div>
    </div>
</div> <!-- Fim do elemento raiz -->

<!-- Importa a biblioteca do ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<script>
    let chart;
    const allData = @json($jsonOcorrenciasFilial); // Dados detalhados para todas as filiais
    const graficoElement = document.getElementById('graficoOcorrencias');
    const filialSelect = document.getElementById('filialSelect');
    const currentSelection = document.getElementById('currentSelection');
    const homeButton = document.getElementById('homeButton');

    // Pilha para armazenar o histórico de navegação
    const navigationStack = [];

    if (graficoElement) {
        chart = echarts.init(graficoElement);

        // Função para renderizar o gráfico principal ou filtrado
        function renderChart(data, titulo) {
            const chartOptions = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'horizontal',
                    bottom: 10,
                    data: data.map(item => item.name)
                },
                series: [{
                    name: 'Tipos de Ocorrências',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: true,
                        formatter: '{b}: {d}%',
                        position: 'outside'
                    },
                    labelLine: {
                        length: 10,
                        length2: 10
                    },
                    data: data
                }]
            };

            chart.setOption(chartOptions);
            currentSelection.innerText = titulo;
        }

        // Função para atualizar o gráfico conforme a seleção da filial
        function atualizarGraficoPorFilial() {
            const filialSelecionada = filialSelect.value;
            if (filialSelecionada && allData[filialSelecionada]) {
                renderChart(allData[filialSelecionada], `Tipos de Ocorrências - Filial ${filialSelecionada}`);
            } else {
                // Exibe dados agregados de todas as filiais se nenhuma for selecionada
                const todosOsDados = Object.values(allData).flat();
                renderChart(todosOsDados, "Tipos de Ocorrências - Todas as Filiais");
            }
        }

        // Função para voltar ao gráfico anterior usando a pilha
        function voltarAoGraficoAnterior() {
            if (navigationStack.length > 0) {
                const previousChartSetup = navigationStack.pop();
                chart.clear();
                previousChartSetup();
                if (navigationStack.length === 0) {
                    homeButton.style.display = 'none';
                    currentSelection.innerText = "Tipos de Ocorrências";
                }
            }
        }

        // Renderiza o gráfico inicial
        atualizarGraficoPorFilial();
    }
</script>
