<?php
require_once "../models/Reporte.php";
require_once "../config/database.php";

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$reporte = new Reporte($db);

$grado = $_POST['grado'] ?? null;
$seccion = $_POST['seccion'] ?? null;
$periodo = $_POST['periodo'] ?? 'semana';

$data = $reporte->getReportes($grado, $seccion, $periodo);
echo json_encode($data);
?>
