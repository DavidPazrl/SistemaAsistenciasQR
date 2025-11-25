<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'models/Reporte.php';
require_once ROOT . 'config/database.php';
require_once ROOT . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['accion']) && $_GET['accion'] === 'exportar') {
    exportarExcel();
    exit();
}

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$reporte = new Reporte($db);

$grado = $_POST['grado'] ?? null;
$seccion = $_POST['seccion'] ?? null;
$fechaInicio = $_POST['fechaInicio'] ?? null;
$fechaFin = $_POST['fechaFin'] ?? null;

if ($fechaInicio && $fechaFin) {
    $data = $reporte->getReportesPorFechas($grado, $seccion, $fechaInicio, $fechaFin);
} else {
    $data = $reporte->getReportes($grado, $seccion, "semana");
}

echo json_encode($data);

function exportarExcel()
{
    require_once ROOT . 'vendor/autoload.php';
    require_once ROOT . 'models/Reporte.php';
    require_once ROOT . 'config/database.php';

    $db = (new Database())->getConnection();
    $reporte = new Reporte($db);

    $grado = $_GET['grado'] ?? null;
    $seccion = $_GET['seccion'] ?? null;
    $fechaInicio = $_GET['fechaInicio'] ?? null;
    $fechaFin = $_GET['fechaFin'] ?? null;

    if (!$grado || !$seccion || !$fechaInicio || !$fechaFin) {
        die("Error: Faltan parámetros requeridos (grado, seccion, fechaInicio, fechaFin)");
    }

    $datosExcel = $reporte->getReporteParaExcel($grado, $seccion, $fechaInicio, $fechaFin);

    $inputFileName = ROOT . 'assets/excel/ExcelAsistencias.xlsx';
    
    if (!file_exists($inputFileName)) {
        die("Error: No se encuentra el archivo de plantilla en $inputFileName");
    }

    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('C7', $grado);
    $sheet->setCellValue('C9', $seccion);
    $rangoFechas = $reporte->formatearRangoFechas($fechaInicio, $fechaFin);
    $sheet->setCellValue('C14', $rangoFechas);

    $diasHabiles = $datosExcel['diasHabiles'];
    $columnaInicio = 'C'; 
    
    foreach ($diasHabiles as $index => $fecha) {
        $columna = chr(ord($columnaInicio) + $index); 
        
        $diaFormateado = date('d', strtotime($fecha));
        
        $sheet->setCellValue("{$columna}15", $diaFormateado);
        
        if ($columna === 'Y') break;
    }

    
    $estudiantes = $datosExcel['estudiantes'];
    $filaInicio = 18; 
    
    foreach ($estudiantes as $indexEst => $estudiante) {
        $fila = $filaInicio + $indexEst;
        
        $sheet->setCellValue("B{$fila}", $estudiante['nombreCompleto']);
        
        $asistenciasPorDia = $estudiante['asistenciasPorDia'];
        
        foreach ($diasHabiles as $index => $fecha) {
            $columna = chr(ord('C') + $index); 
            
            $valorAsistencia = $asistenciasPorDia[$fecha] ?? '';
            
            $sheet->setCellValue("{$columna}{$fila}", $valorAsistencia);
            
            if ($columna === 'Y') break;
        }
        
        if ($fila >= 42) break;
    }
    
    $nombreArchivo = "Reporte_Grado{$grado}_Seccion{$seccion}_" . date('Y-m-d') . ".xlsx";
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"{$nombreArchivo}\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}
?>