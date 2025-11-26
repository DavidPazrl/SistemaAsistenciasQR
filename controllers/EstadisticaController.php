<?php
if (session_start() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'config/database.php';
require_once ROOT . 'models/Estadistica.php';

class EstadisticaController {
    private $estadistica;
    
    public function __construct() {
        $this->estadistica = new Estadistica();
    }
    
    public function getChartAulas() {
        header('Content-Type: application/json');
        
        $data = $this->estadistica->getEstadisticasPorAula();
        
        // Formato para FusionCharts
        $chartData = [
            'chart' => [
                'caption' => 'Asistencia por Aula',
                'subCaption' => 'Porcentaje de asistencia por Grado y Sección',
                'xAxisName' => 'Aula',
                'yAxisName' => 'Porcentaje de Asistencia',
                'numberSuffix' => '%',
                'theme' => 'fusion',
                'plotToolText' => '<b>$label</b><br>Asistencia: $dataValue%<br>Estudiantes: $totalEstudiantes',
                'paletteColors' => '#10b981,#3b82f6,#8b5cf6,#f59e0b,#ef4444'
            ],
            'data' => []
        ];
        
        foreach ($data as $row) {
            $chartData['data'][] = [
                'label' => $row['aula'],
                'value' => $row['porcentaje_asistencia'] ?? 0,
                'totalEstudiantes' => $row['total_estudiantes']
            ];
        }
        
        echo json_encode($chartData);
    }
    
    public function getChartTendencia() {
        header('Content-Type: application/json');
        
        $dias = isset($_GET['dias']) ? intval($_GET['dias']) : 7;
        $data = $this->estadistica->getAsistenciasPorFecha($dias);
        
        $chartData = [
            'chart' => [
                'caption' => 'Tendencia de Asistencias',
                'subCaption' => "Últimos $dias días",
                'xAxisName' => 'Fecha',
                'yAxisName' => 'Cantidad de Estudiantes',
                'theme' => 'fusion',
                'lineThickness' => '2',
                'anchorRadius' => '4'
            ],
            'categories' => [
                [
                    'category' => []
                ]
            ],
            'dataset' => [
                [
                    'seriesname' => 'Presentes',
                    'color' => '#10b981',
                    'data' => []
                ],
                [
                    'seriesname' => 'Ausentes',
                    'color' => '#ef4444',
                    'data' => []
                ],
                [
                    'seriesname' => 'Tardanzas',
                    'color' => '#f59e0b',
                    'data' => []
                ]
            ]
        ];
        
        foreach ($data as $row) {
            $chartData['categories'][0]['category'][] = [
                'label' => date('d/m', strtotime($row['fecha']))
            ];
            
            $chartData['dataset'][0]['data'][] = ['value' => $row['presentes']];
            $chartData['dataset'][1]['data'][] = ['value' => $row['ausentes']];
            $chartData['dataset'][2]['data'][] = ['value' => $row['tardanzas']];
        }
        
        echo json_encode($chartData);
    }
    
 
    public function getChartDistribucion() {
        header('Content-Type: application/json');
        
        $data = $this->estadistica->getDistribucionEstados();
        
        $colores = [
            'Asistio' => '#10b981',
            'Falto' => '#ef4444',
            'Tardanza' => '#f59e0b',
            'Falta justificada' => '#8b5cf6',
            'Tardanza justificada' => '#06b6d4'
        ];
        
        $chartData = [
            'chart' => [
                'caption' => 'Distribución de Asistencias',
                'subCaption' => 'Por tipo de registro',
                'numberSuffix' => '%',
                'theme' => 'fusion',
                'decimals' => '1',
                'showPercentValues' => '1',
                'showLegend' => '1'
            ],
            'data' => []
        ];
        
        foreach ($data as $row) {
            $chartData['data'][] = [
                'label' => $row['estado'],
                'value' => $row['cantidad'],
                'color' => $colores[$row['estado']] ?? '#6b7280'
            ];
        }
        
        echo json_encode($chartData);
    }
    

    public function getResumen() {
        header('Content-Type: application/json');
        
        $resumen = $this->estadistica->getResumenGeneral();
        $topEstudiantes = $this->estadistica->getTopEstudiantesAsistencia(5);
        
        echo json_encode([
            'success' => true,
            'resumen' => $resumen,
            'topEstudiantes' => $topEstudiantes
        ]);
    }
    
    public function getChartComparativaMensual() {
        header('Content-Type: application/json');
        
        $mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
        
        $data = $this->estadistica->getComparativaAulasMes($mes, $anio);
        
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        $chartData = [
            'chart' => [
                'caption' => 'Comparativa Mensual por Aula',
                'subCaption' => $meses[$mes] . ' ' . $anio,
                'xAxisName' => 'Aula',
                'yAxisName' => 'Cantidad de Registros',
                'theme' => 'fusion',
                'showValues' => '1'
            ],
            'categories' => [
                [
                    'category' => []
                ]
            ],
            'dataset' => [
                [
                    'seriesname' => 'Presentes',
                    'color' => '#10b981',
                    'data' => []
                ],
                [
                    'seriesname' => 'Ausentes',
                    'color' => '#ef4444',
                    'data' => []
                ],
                [
                    'seriesname' => 'Tardanzas',
                    'color' => '#f59e0b',
                    'data' => []
                ]
            ]
        ];
        
        foreach ($data as $row) {
            $chartData['categories'][0]['category'][] = [
                'label' => $row['aula']
            ];
            
            $chartData['dataset'][0]['data'][] = ['value' => $row['presentes']];
            $chartData['dataset'][1]['data'][] = ['value' => $row['ausentes']];
            $chartData['dataset'][2]['data'][] = ['value' => $row['tardanzas'] ?? 0];
        }
        
        echo json_encode($chartData);
    }
    
  
    public function getChartMetodos() {
        header('Content-Type: application/json');
        
        $data = $this->estadistica->getEstadisticasPorMetodo();
        
        $chartData = [
            'chart' => [
                'caption' => 'Métodos de Registro',
                'subCaption' => 'Uso de cámara vs manual',
                'theme' => 'fusion',
                'showPercentValues' => '1',
                'decimals' => '1'
            ],
            'data' => []
        ];
        
        foreach ($data as $row) {
            $chartData['data'][] = [
                'label' => ucfirst($row['metodo']),
                'value' => $row['total']
            ];
        }
        
        echo json_encode($chartData);
    }
    

    public function getChartGrados() {
        header('Content-Type: application/json');
        
        $data = $this->estadistica->getEstadisticasPorGrado();
        
        $chartData = [
            'chart' => [
                'caption' => 'Asistencia por Grado',
                'subCaption' => 'Porcentaje de asistencia',
                'xAxisName' => 'Grado',
                'yAxisName' => 'Porcentaje',
                'numberSuffix' => '%',
                'theme' => 'fusion',
                'paletteColors' => '#3b82f6'
            ],
            'data' => []
        ];
        
        foreach ($data as $row) {
            $chartData['data'][] = [
                'label' => $row['Grado'] . '°',
                'value' => $row['porcentaje_asistencia'] ?? 0,
                'tooltext' => '<b>Grado ' . $row['Grado'] . '°</b><br>Asistencia: $dataValue%<br>Estudiantes: ' . $row['total_estudiantes']
            ];
        }
        
        echo json_encode($chartData);
    }
    
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'aulas':
                $this->getChartAulas();
                break;
            case 'tendencia':
                $this->getChartTendencia();
                break;
            case 'distribucion':
                $this->getChartDistribucion();
                break;
            case 'resumen':
                $this->getResumen();
                break;
            case 'comparativa':
                $this->getChartComparativaMensual();
                break;
            case 'metodos':
                $this->getChartMetodos();
                break;
            case 'grados':
                $this->getChartGrados();
                break;
            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Acción no válida']);
                break;
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new EstadisticaController();
    $controller->handleRequest();
}