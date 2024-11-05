<div>
    <div class="app-title">
        <div>
            <h1><i class="bi bi-speedometer"></i> Dashboard</h1>
            <p>Resumo das Rotinas</p>
        </div>
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

    <!-- Gráfico de Tipos de Ocorrências -->
    <div class="col-md-6">
        <div class="tile">
            <!-- Título dinâmico que será atualizado conforme os gráficos -->
            <h3 id="currentSelection" style="text-align: center; margin-top: 10px;">Tipos de Ocorrências</h3>

            <!-- Botão "home" que só aparece ao sair do gráfico inicial -->
            <button id="homeButton" onclick="voltarAoGraficoInicial()" style="display: none; margin: 10px 0;">home</button>

            <div class="ratio ratio-16x9">
                <div id="graficoInicial" style="height: 120%;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

    <script>
        let chart;

        // Dados JSON dinâmico carregados no JavaScript
        const occurrenceTypesData = @json($jsonOcorrencias); // Mostra todos os tipos de ocorrência
        const detailedData = @json($jsonOcorrenciasFilial);  // Dados detalhados para cada tipo de ocorrência, filial e funcionário

        // Elemento para exibir o tipo de ocorrência ou filial selecionado
        const currentSelection = document.getElementById('currentSelection');

        // Configuração do gráfico inicial (Todos os Tipos de Ocorrência)
        const allTypesChartOptions = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}: {c} ({d}%)'
            },
            legend: {
                orient: 'vertical',
                left: 'left'
            },
            series: [
                {
                    name: 'Tipos de Ocorrências',
                    type: 'pie',
                    radius: '50%',
                    data: occurrenceTypesData,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        const graficoElement = document.getElementById('graficoInicial');
        const homeButton = document.getElementById('homeButton'); // Referência ao botão "home"

        if (graficoElement && !chart) { // Garante que o gráfico é inicializado apenas uma vez
            chart = echarts.init(graficoElement);

            // Função para configurar o gráfico inicial com o evento de drill-down para filiais
            function setupAllTypesChart() {
                chart.setOption(allTypesChartOptions);
                currentSelection.innerText = "Tipos de Ocorrências"; // Reseta o título ao inicializar o gráfico
                console.log("Gráfico inicial de todos os tipos de ocorrência inicializado.");

                // Configura o evento de clique para o drill-down do gráfico de filiais
                chart.off('click'); // Limpa qualquer evento de clique anterior
                chart.on('click', function (params) {
                    const tipoOcorrencia = params.name;
                    console.log("Tipo de Ocorrência clicado:", tipoOcorrencia);

                    if (detailedData[tipoOcorrencia]) {
                        currentSelection.innerText = `Tipo de Ocorrência: ${tipoOcorrencia}`; // Atualiza o título com o tipo de ocorrência
                        homeButton.style.display = 'inline-block';

                        const filiaisData = detailedData[tipoOcorrencia].map(filial => {
                            return {
                                name: filial.name,
                                value: parseInt(filial.value)
                            };
                        });

                        const filialChartOptions = {
                            tooltip: {
                                trigger: 'item',
                                formatter: '{b}: {c} ({d}%)'
                            },
                            legend: {
                                orient: 'vertical',
                                left: 'left'
                            },
                            series: [
                                {
                                    name: `Ocorrências por Filial - ${tipoOcorrencia}`,
                                    type: 'pie',
                                    radius: '50%',
                                    data: filiaisData,
                                    emphasis: {
                                        itemStyle: {
                                            shadowBlur: 10,
                                            shadowOffsetX: 0,
                                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                                        }
                                    }
                                }
                            ]
                        };

                        chart.setOption(filialChartOptions);
                        console.log("Drill-down para gráfico de filiais.");

                        // Configura evento de clique para drill-down dos funcionários dentro da filial
                        chart.off('click');
                        chart.on('click', function (filialParams) {
                            const filialName = filialParams.name;
                            const filialData = detailedData[tipoOcorrencia].find(filial => filial.name === filialName);

                            if (filialData && filialData.employees) {
                                currentSelection.innerText = `Filial: ${filialName} - ${tipoOcorrencia}`; // Atualiza o título com a filial e o tipo de ocorrência

                                const employeeChartOptions = {
                                    tooltip: {
                                        trigger: 'axis',
                                        axisPointer: { type: 'shadow' }
                                    },
                                    xAxis: {
                                        type: 'category',
                                        data: filialData.employees.map(emp => emp.name)
                                    },
                                    yAxis: {
                                        type: 'value'
                                    },
                                    series: [
                                        {
                                            name: 'Ocorrências por Funcionário',
                                            type: 'bar',
                                            data: filialData.employees.map(emp => parseInt(emp.value))
                                        }
                                    ]
                                };

                                chart.setOption(employeeChartOptions);
                                console.log("Drill-down para gráfico de funcionários.");
                            } else {
                                console.warn("Dados de funcionários não encontrados para a filial:", filialName);
                            }
                        });
                    } else {
                        console.warn("Dados detalhados não encontrados para o tipo de ocorrência:", tipoOcorrencia);
                    }
                });
            }

            // Inicializa o gráfico inicial pela primeira vez
            setupAllTypesChart();
        } else {
            console.error("Elemento 'graficoInicial' não encontrado ou gráfico já inicializado.");
        }

        // Função para voltar ao gráfico inicial de tipos de ocorrência
        function voltarAoGraficoInicial() {
            if (chart) {
                chart.clear(); // Limpa o gráfico atual
                setupAllTypesChart(); // Redefine o gráfico para o gráfico inicial de tipos de ocorrência
                homeButton.style.display = 'none'; // Oculta o botão "home"
                currentSelection.innerText = "Tipos de Ocorrências"; // Reseta o título ao voltar para o gráfico inicial
                console.log("Retornando ao gráfico inicial de tipos de ocorrência.");
            } else {
                console.error("Erro: o gráfico não está inicializado.");
            }
        }
    </script>
</div>
