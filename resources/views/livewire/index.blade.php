<div> <!-- Elemento raiz que envolve todo o conteúdo do componente Livewire -->
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

    <!-- Gráfico de Pizza para Tipos de Ocorrências -->
    <div class="col-md-6">
        <div class="tile">
            <!-- Título dinâmico para os gráficos -->
            <h3 id="currentSelection" style="text-align: center; margin-top: 10px;">Tipos de Ocorrências</h3>

            <!-- Botão "home" que só aparece ao sair do gráfico inicial -->
            <button id="homeButton" onclick="voltarAoGraficoAnterior()" style="display: none; margin: 10px 0;">Voltar
            </button>

            <!-- Container do gráfico -->
            <div id="graficoInicial" style="height: 400px;"></div>
        </div>
    </div>
</div> <!-- Fim do elemento raiz -->

<!-- Importa a biblioteca do ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<script>
    let chart;
    const occurrenceTypesData = @json($jsonOcorrencias); // Dados dos tipos de ocorrência
    const detailedData = @json($jsonOcorrenciasFilial);  // Dados detalhados por filial e funcionário

    const graficoElement = document.getElementById('graficoInicial');
    const homeButton = document.getElementById('homeButton');
    const currentSelection = document.getElementById('currentSelection');

    // Pilha para armazenar o histórico de navegação
    const navigationStack = [];

    if (graficoElement && !chart) {
        chart = echarts.init(graficoElement);

        function setupAllTypesChart() {
            const pieChartOptions = {
                tooltip: {trigger: 'item', formatter: '{b}: {c} ({d}%)'},
                legend: {orient: 'vertical', left: 'left'},
                series: [{
                    name: 'Tipos de Ocorrências',
                    type: 'pie',
                    radius: '50%',
                    data: occurrenceTypesData,
                    label: {
                        formatter: '{b}: {d}%',  // Exibe o nome e a porcentagem na label
                        fontSize: 10
                    },
                    labelLine: {length: 10, length2: 10, smooth: true}
                }]
            };
            chart.setOption(pieChartOptions);
            currentSelection.innerText = "Tipos de Ocorrências";
            homeButton.style.display = 'none'; // Esconde o botão "Voltar" na tela principal
            chart.off('click');
            chart.on('click', handleTypeClick);
        }

        function handleTypeClick(params) {
            const tipoOcorrencia = params.name;
            if (detailedData[tipoOcorrencia]) {
                // Salva o estado atual na pilha antes de mudar para o próximo
                navigationStack.push(setupAllTypesChart);

                currentSelection.innerText = `Ocorrências por Filial - ${tipoOcorrencia}`;
                homeButton.style.display = 'inline-block';

                const filiaisData = detailedData[tipoOcorrencia].map(filial => ({
                    name: filial.name,
                    value: filial.value
                }));

                const barStackedOptions = {
                    tooltip: {trigger: 'axis', axisPointer: {type: 'shadow'}},
                    legend: {data: [tipoOcorrencia]},
                    xAxis: {type: 'category', data: filiaisData.map(f => `Filial ${f.name}`)},
                    yAxis: {type: 'value'},
                    series: [{
                        name: tipoOcorrencia,
                        type: 'bar',
                        stack: 'total',
                        label: {show: true, formatter: '{c}'}, // Exibe apenas o valor
                        data: filiaisData.map(f => f.value)
                    }]
                };

                chart.setOption(barStackedOptions);
                chart.off('click');
                chart.on('click', (filialParams) => handleFilialClick(filialParams, tipoOcorrencia));
            }
        }

        function handleFilialClick(filialParams, tipoOcorrencia) {
            // Remove o prefixo "Filial" para obter apenas o número da filial
            const filialName = filialParams.name.replace("Filial ", "").trim();
            const filialData = detailedData[tipoOcorrencia].find(filial => filial.name === filialName);

            if (filialData && filialData.employees) {
                // Salva o estado atual na pilha antes de mudar para o próximo
                navigationStack.push(() => handleTypeClick({name: tipoOcorrencia}));

                currentSelection.innerText = `Funcionários na Filial ${filialName} - ${tipoOcorrencia}`;

                const employeeBarOptions = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {type: 'shadow'},
                        formatter: '{b}: {c}' // Exibe apenas o nome e o valor no tooltip
                    },
                    xAxis: {
                        type: 'category',
                        data: filialData.employees.map(emp => `${emp.name} - ${emp.fullName}`),
                        axisLabel: {
                            rotate: 0,
                            fontSize: 10,
                            overflow: 'truncate',
                            interval: 0,
                            formatter: function (value) {
                                return value.length > 10 ? value.substring(0, 10) + '...' : value;
                            }
                        }
                    },
                    yAxis: {type: 'value'},
                    grid: {bottom: 100},
                    series: [{
                        name: 'Ocorrências por Funcionário',
                        type: 'bar',
                        label: {show: true, formatter: '{c}'}, // Exibe apenas o valor
                        data: filialData.employees.map(emp => emp.value)
                    }]
                };

                chart.setOption(employeeBarOptions);
                chart.off('click'); // Desativa o clique adicional neste nível
            } else {
                console.warn("Dados de funcionários não encontrados para a filial:", filialName);
            }
        }

        function voltarAoGraficoAnterior() {
            if (navigationStack.length > 0) {
                const previousChartSetup = navigationStack.pop(); // Pega o estado anterior da pilha
                chart.clear();
                previousChartSetup(); // Volta para o gráfico anterior
                if (navigationStack.length === 0) {
                    homeButton.style.display = 'none'; // Esconde o botão "Voltar" se estiver na tela inicial
                    currentSelection.innerText = "Tipos de Ocorrências";
                }
            }
        }

        // Inicia o gráfico principal
        setupAllTypesChart();
    }
</script>
