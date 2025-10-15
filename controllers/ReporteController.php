<?php
require_once "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once "../models/Reporte.php";
require_once "../config/database.php";

if (isset($_GET['accion']) && $_GET['accion'] === 'exportar') {
    exportarExcel();
    exit();
}

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$reporte = new Reporte($db);

$grado = $_POST['grado'] ?? null;
$seccion = $_POST['seccion'] ?? null;
$periodo = $_POST['periodo'] ?? 'semana';

$data = $reporte->getReportes($grado, $seccion, $periodo);
echo json_encode($data);

function exportarExcel()
{
    require_once "../vendor/autoload.php";
    require_once "../models/Reporte.php";
    require_once "../config/database.php";

    $db = (new Database())->getConnection();
    $reporte = new Reporte($db);

    $grado = $_GET['grado'] ?? null;
    $seccion = $_GET['seccion'] ?? null;
    $periodo = $_GET['periodo'] ?? 'semana';

    $data = $reporte->getReportes($grado, $seccion, $periodo);

    // Ruta absoluta al archivo
    $inputFileName = __DIR__ . '/../assets/excel/ExcelAsistencias.xlsx';
    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Limpia desde fila 3 hacia abajo
    // Limpiar solo las columnas de datos (B a I), dejando el formato de las demás intacto
    for ($i = 3; $i <= 1000; $i++) {
        foreach (range('B', 'I') as $col) {
            $sheet->setCellValue("{$col}{$i}", '');
        }
    }


    // Empieza en fila 3 (porque fila 2 tiene títulos)
    $row = 3;
    foreach ($data as $r) {
        $sheet->setCellValue("B{$row}", "QR" . $r['DNI']);
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
