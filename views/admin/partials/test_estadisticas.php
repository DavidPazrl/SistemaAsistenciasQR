<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Estadísticas - Sistema de Asistencias QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen p-6">
        <!-- Header -->
        <div class="max-w-7xl mx-auto mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Dashboard de Estadísticas</h1>
                <p class="text-gray-600">Sistema de Asistencias con QR - Vista de Prueba</p>
                <p class="text-sm text-gray-500 mt-2">Backend: <code class="bg-gray-100 px-2 py-1 rounded">EstadisticaController.php</code></p>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="max-w-7xl mx-auto mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Resumen del Día</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="resumenCards">

            </div>
        </div>

        <!-- Gráficos -->
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Gráfico de Barras por Aula -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800">Asistencia por Aula</h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">Grado + Sección</span>
                </div>
                <div id="chartAulas" class="w-full h-96"></div>
            </div>

            <!-- Grid de 2 columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico Circular -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Distribución de Estados</h2>
                    <div id="chartDistribucion" class="w-full h-80"></div>
                </div>

                <!-- Gráfico de Métodos -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">📱 Métodos de Registro</h2>
                    <div id="chartMetodos" class="w-full h-80"></div>
                </div>
            </div>

            <!-- Gráfico por Grados -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Asistencia por Grado</h2>
                <div id="chartGrados" class="w-full h-96"></div>
            </div>

            <!-- Gráfico de Líneas (Tendencia) -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800">Tendencia de Asistencias</h2>
                    <select id="selectDias" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="7">Últimos 7 días</option>
                        <option value="15">Últimos 15 días</option>
                        <option value="30">Últimos 30 días</option>
                    </select>
                </div>
                <div id="chartTendencia" class="w-full h-96"></div>
            </div>

            <!-- Comparativa Mensual -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800">Comparativa Mensual</h2>
                    <div class="flex gap-3">
                        <select id="selectMes" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11" selected>Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                        <input type="number" id="selectAnio" value="2025" min="2020" max="2030" 
                               class="px-4 py-2 border-2 border-gray-300 rounded-lg w-24 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div id="chartComparativa" class="w-full h-96"></div>
            </div>

            <!-- Top Estudiantes -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Top 5 Estudiantes con Mejor Asistencia</h2>
                <div id="topEstudiantes" class="space-y-3">
      
                </div>
            </div>
        </div>

        <!-- Estado de carga -->
        <div id="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white p-8 rounded-xl shadow-2xl text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-700 text-lg font-semibold">Cargando datos...</p>
            </div>
        </div>
    </div>

    <script>
        // Configuración base
        const BASE_URL = window.location.origin + '/proyectos/SistemaAsistenciasQR/controllers/EstadisticaController.php';
        
        // Función para mostrar loading
        function showLoading(show = true) {
            document.getElementById('loading').classList.toggle('hidden', !show);
        }

        // Función para crear tarjetas de resumen
        function createResumenCards(data) {
            const cards = [
                {
                    title: 'Total Estudiantes',
                    value: data.total_estudiantes || 0,
                    icon: '',
                    color: 'border-blue-500',
                    bg: 'bg-blue-50'
                },
                {
                    title: 'Presentes Hoy',
                    value: data.total_presentes || 0,
                    icon: '',
                    color: 'border-green-500',
                    bg: 'bg-green-50'
                },
                {
                    title: 'Ausentes Hoy',
                    value: data.total_ausentes || 0,
                    icon: '',
                    color: 'border-red-500',
                    bg: 'bg-red-50'
                },
                {
                    title: 'Asistencia General',
                    value: (data.porcentaje_general || 0) + '%',
                    icon: '',
                    color: 'border-purple-500',
                    bg: 'bg-purple-50'
                }
            ];

            const container = document.getElementById('resumenCards');
            container.innerHTML = cards.map(card => `
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 ${card.color} ${card.bg} hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">${card.title}</p>
                            <p class="text-4xl font-bold text-gray-800 mt-2">${card.value}</p>
                        </div>
                        <div class="text-5xl opacity-80">${card.icon}</div>
                    </div>
                </div>
            `).join('');
        }

        // Función para crear Top Estudiantes
        function createTopEstudiantes(estudiantes) {
            const container = document.getElementById('topEstudiantes');
            
            if (!estudiantes || estudiantes.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-gray-500 text-lg">📭 No hay datos suficientes</p>
                        <p class="text-gray-400 text-sm mt-2">Los estudiantes necesitan al menos 5 registros</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = estudiantes.map((estudiante, index) => {
                const medals = ['🥇', '🥈', '🥉', '🎖️', '⭐'];
                const colors = ['bg-yellow-50', 'bg-gray-50', 'bg-orange-50', 'bg-blue-50', 'bg-purple-50'];
                const medal = medals[index] || '🎖️';
                const color = colors[index] || 'bg-gray-50';
                
                return `
                    <div class="flex items-center justify-between p-5 ${color} rounded-xl hover:shadow-md transition-all border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="text-3xl">${medal}</span>
                            <div>
                                <p class="font-bold text-gray-800 text-lg">${estudiante.Nombre} ${estudiante.Apellidos}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="bg-white px-2 py-1 rounded-md">${estudiante.aula}</span>
                                    <span class="ml-2">• ${estudiante.presentes} asistencias de ${estudiante.total_registros} registros</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-green-600">${estudiante.porcentaje}%</p>
                            <p class="text-xs text-gray-500 mt-1">Asistencia</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Cargar gráfico de barras 
        async function loadChartAulas() {
            try {
                const response = await fetch(`${BASE_URL}?action=aulas`);
                const data = await response.json();
                
                FusionCharts.ready(function() {
                    new FusionCharts({
                        type: 'column2d',
                        renderAt: 'chartAulas',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: data
                    }).render();
                });
            } catch (error) {
                console.error('Error cargando gráfico de aulas:', error);
            }
        }

        // Cargar gráfico circular
        async function loadChartDistribucion() {
            try {
                const response = await fetch(`${BASE_URL}?action=distribucion`);
                const data = await response.json();
                
                FusionCharts.ready(function() {
                    new FusionCharts({
                        type: 'doughnut2d',
                        renderAt: 'chartDistribucion',
                        width: '100%',
                        height: '320',
                        dataFormat: 'json',
                        dataSource: data
                    }).render();
                });
            } catch (error) {
                console.error('Error cargando gráfico de distribución:', error);
            }
        }

        // Cargar gráfico de métodos
        async function loadChartMetodos() {
            try {
                const response = await fetch(`${BASE_URL}?action=metodos`);
                const data = await response.json();
                
                FusionCharts.ready(function() {
                    new FusionCharts({
                        type: 'pie2d',
                        renderAt: 'chartMetodos',
                        width: '100%',
                        height: '320',
                        dataFormat: 'json',
                        dataSource: data
                    }).render();
                });
            } catch (error) {
                console.error('Error cargando gráfico de métodos:', error);
            }
        }

        // Cargar gráfico por grados
        async function loadChartGrados() {
            try {
                const response = await fetch(`${BASE_URL}?action=grados`);
                const data = await response.json();
                
                FusionCharts.ready(function() {
                    new FusionCharts({
                        type: 'bar2d',
                        renderAt: 'chartGrados',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: data
                    }).render();
                });
            } catch (error) {
                console.error('Error cargando gráfico de grados:', error);
            }
        }

        // Cargar gráfico de tendencia
        async function loadChartTendencia(dias = 7) {
            try {
                const response = await fetch(`${BASE_URL}?action=tendencia&dias=${dias}`);
                const data = await response.json();
                
                FusionCharts.ready(function() {
                    new FusionCharts({
                        type: 'msline',
                        renderAt: 'chartTendencia',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: data
                    }).render();
                });
            } catch (error) {
                console.error('Error cargando gráfico de tendencia:', error);
            }
        }

        // Cargar comparativa mensual
        async function loadChartComparativa(mes = null, anio = null) {
            try {
                const url = mes && anio 
                    ? `${BASE_URL}?action=comparativa&mes=${mes}&anio=${anio}`
                    : `${BASE_URL}?action=comparativa`;
                    
                const response = await fetch(url);
                const data = await response.json();
                
                FusionCharts.ready(function() {
                    new FusionCharts({
                        type: 'mscolumn2d',
                        renderAt: 'chartComparativa',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: data
                    }).render();
                });
            } catch (error) {
                console.error('Error cargando comparativa:', error);
            }
        }

        // Cargar resumen
        async function loadResumen() {
            try {
                const response = await fetch(`${BASE_URL}?action=resumen`);
                const data = await response.json();
                
                if (data.success) {
                    createResumenCards(data.resumen);
                    createTopEstudiantes(data.topEstudiantes);
                }
            } catch (error) {
                console.error('Error cargando resumen:', error);
            }
        }

        // Event listeners
        document.getElementById('selectDias').addEventListener('change', function() {
            loadChartTendencia(this.value);
        });

        document.getElementById('selectMes').addEventListener('change', function() {
            const mes = this.value;
            const anio = document.getElementById('selectAnio').value;
            loadChartComparativa(mes, anio);
        });

        document.getElementById('selectAnio').addEventListener('change', function() {
            const mes = document.getElementById('selectMes').value;
            const anio = this.value;
            loadChartComparativa(mes, anio);
        });

        // Inicializar todo
        async function init() {
            showLoading(true);
            
            try {
                await Promise.all([
                    loadResumen(),
                    loadChartAulas(),
                    loadChartDistribucion(),
                    loadChartMetodos(),
                    loadChartGrados(),
                    loadChartTendencia(),
                    loadChartComparativa()
                ]);
            } catch (error) {
                console.error('Error inicializando:', error);
            } finally {
                showLoading(false);
            }
        }

        // Ejecutar al cargar la página
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>