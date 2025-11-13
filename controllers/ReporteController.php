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
    $data = $reporte->getReportes($grado, $seccion, "semana"); // valor por defecto
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

    if ($fechaInicio && $fechaFin) {
        $data = $reporte->getReportesPorFechas($grado, $seccion, $fechaInicio, $fechaFin);
    } else {
        $data = $reporte->getReportes($grado, $seccion, "semana");
    }

    // Ruta absoluta al archivo
    $inputFileName = ROOT . 'assets/excel/ExcelAsistencias.xlsx';
    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    for ($i = 3; $i <= 1000; $i++) {
        foreach (range('B', 'I') as $col) {
            $sheet->setCellValue("{$col}{$i}", '');
        }
    }

    $row = 3;
    foreach ($data as $r) {
        $sheet->setCellValue("B{$row}", "QR" . $r['documento']);
        $sheet->setCellValue("C{$row}", $r['Nombre'] . ' ' . $r['Apellidos']);
        $sheet->setCellValue("D{$row}", $r['tipoAsistencia'] ?? '');
        $sheet->setCellValue("E{$row}", $r['Grado'] ?? '');
        $sheet->setCellValue("F{$row}", $r['Seccion'] ?? '');
        $sheet->setCellValue("G{$row}", $r['fechaEntrada'] ?? '');
        $sheet->setCellValue("H{$row}", $r['horaEntrada'] ?? '');
        $sheet->setCellValue("I{$row}", $r['horaSalida'] ?? '');
        $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte_Asistencias.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}
?>