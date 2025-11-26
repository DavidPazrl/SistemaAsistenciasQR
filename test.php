<?php
require_once 'config.php';
require_once ROOT . 'models/Reporte.php';
require_once ROOT . 'config/database.php';

$db = (new Database())->getConnection();
$reporte = new Reporte($db);

// Parámetros de prueba 
$grado = 1;
$seccion = 'A';
$fechaInicio = '2025-11-03';
$fechaFin = '2025-11-28';

// Obtener datos formateados
$datos = $reporte->getReporteParaExcel($grado, $seccion, $fechaInicio, $fechaFin);
$rangoFechas = $reporte->formatearRangoFechas($fechaInicio, $fechaFin);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Reporte - Vista Previa Excel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #d32f2f;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .info-section {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            gap: 40px;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #1976d2;
            min-width: 150px;
        }
        
        .info-value {
            color: #333;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-card.warning { border-left-color: #ff9800; }
        .stat-card.danger { border-left-color: #f44336; }
        .stat-card.info { border-left-color: #2196f3; }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        
        .excel-preview {
            overflow-x: auto;
            margin-top: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }
        
        th {
            background: #90caf9;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #64b5f6;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        td {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        .student-name {
            text-align: left;
            font-weight: 500;
            background: #f5f5f5;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .asistio {
            background: #c8e6c9;
            color: #2e7d32;
            font-weight: bold;
        }
        
        .falto {
            background: #ffcdd2;
            color: #c62828;
            font-weight: bold;
        }
        
        .tardanza {
            background: #fff9c4;
            color: #f57f17;
            font-weight: bold;
        }
        
        .vacio {
            background: #fafafa;
            color: #999;
        }
        
        .btn-exportar {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .btn-exportar:hover {
            background: #45a049;
        }
        
        .leyenda {
            display: flex;
            gap: 30px;
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .leyenda-box {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .alert {
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 CONTROL DE ASISTENCIAS - VISTA PREVIA</h1>
        
        <!-- Información del Reporte -->
        <div class="info-section">
            <div class="info-row">
                <div>
                    <span class="info-label">INSTITUCIÓN:</span>
                    <span class="info-value">40024 MANUEL GONZALES PRADA</span>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <span class="info-label">NIVEL:</span>
                    <span class="info-value">SECUNDARIA</span>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <span class="info-label">GRADO:</span>
                    <span class="info-value"><?php echo $datos['grado']; ?></span>
                </div>
                <div>
                    <span class="info-label">SECCIÓN:</span>
                    <span class="info-value"><?php echo $datos['seccion']; ?></span>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <span class="info-label">PERÍODO:</span>
                    <span class="info-value"><?php echo $rangoFechas; ?></span>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card info">
                <div class="stat-label">Total Estudiantes</div>
                <div class="stat-value"><?php echo count($datos['estudiantes']); ?></div>
            </div>
            <div class="stat-card info">
                <div class="stat-label">Días Hábiles</div>
                <div class="stat-value"><?php echo count($datos['diasHabiles']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Registros Totales</div>
                <div class="stat-value">
                    <?php 
                    $totalRegistros = 0;
                    foreach ($datos['estudiantes'] as $est) {
                        foreach ($est['asistenciasPorDia'] as $val) {
                            if ($val !== '') $totalRegistros++;
                        }
                    }
                    echo $totalRegistros;
                    ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Max. Posibles</div>
                <div class="stat-value">
                    <?php echo count($datos['estudiantes']) * count($datos['diasHabiles']); ?>
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="leyenda">
            <div class="leyenda-item">
                <div class="leyenda-box asistio"></div>
                <span><strong>1</strong> = Asistió</span>
            </div>
            <div class="leyenda-item">
                <div class="leyenda-box falto"></div>
                <span><strong>0</strong> = Faltó</span>
            </div>
            <div class="leyenda-item">
                <div class="leyenda-box tardanza"></div>
                <span><strong>T</strong> = Tardanza</span>
            </div>
            <div class="leyenda-item">
                <div class="leyenda-box vacio"></div>
                <span><strong>-</strong> = Sin registro</span>
            </div>
        </div>

        <?php if (empty($datos['estudiantes'])): ?>
            <div class="no-data">
                No hay estudiantes registrados para Grado <?php echo $grado; ?> Sección <?php echo $seccion; ?>
            </div>
        <?php else: ?>
            <!-- Tabla de Asistencias (Simula el Excel) -->
            <div class="excel-preview">
                <table>
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">APELLIDOS Y NOMBRES</th>
                            <?php foreach ($datos['diasHabiles'] as $dia): ?>
                                <th><?php echo date('d', strtotime($dia)); ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr style="background: #e3f2fd;">
                            <th style="font-size: 11px;">Fecha completa</th>
                            <?php foreach ($datos['diasHabiles'] as $dia): ?>
                                <th style="font-size: 11px;">
                                    <?php 
                                    $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                                    echo $dias[date('w', strtotime($dia))]; 
                                    ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos['estudiantes'] as $estudiante): ?>
                            <tr>
                                <td class="student-name" title="<?php echo $estudiante['nombreCompleto']; ?>">
                                    <?php echo $estudiante['nombreCompleto']; ?>
                                </td>
                                <?php foreach ($datos['diasHabiles'] as $dia): ?>
                                    <?php 
                                    $valor = $estudiante['asistenciasPorDia'][$dia] ?? '';
                                    $clase = '';
                                    $display = $valor;
                                    
                                    if ($valor === 1) {
                                        $clase = 'asistio';
                                        $display = '✓';
                                    } elseif ($valor === 0) {
                                        $clase = 'falto';
                                        $display = '✗';
                                    } elseif ($valor === 'T') {
                                        $clase = 'tardanza';
                                        $display = 'T';
                                    } else {
                                        $clase = 'vacio';
                                        $display = '-';
                                    }
                                    ?>
                                    <td class="<?php echo $clase; ?>">
                                        <?php echo $display; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Botón para exportar -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="controllers/ReporteController.php?accion=exportar&grado=<?php echo $grado; ?>&seccion=<?php echo $seccion; ?>&fechaInicio=<?php echo $fechaInicio; ?>&fechaFin=<?php echo $fechaFin; ?>" 
                   class="btn-exportar">
                    Exportar a Excel
                </a>
            </div>
        <?php endif; ?>

        <!-- Información de Debug -->
        <details style="margin-top: 30px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
            <summary style="cursor: pointer; font-weight: bold; color: #1976d2;">
                Ver datos en formato JSON (Debug)
            </summary>
            <pre style="margin-top: 20px; padding: 15px; background: #263238; color: #aed581; border-radius: 5px; overflow-x: auto; font-size: 12px;">
<?php echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
            </pre>
        </details>
    </div>
</body>
</html>