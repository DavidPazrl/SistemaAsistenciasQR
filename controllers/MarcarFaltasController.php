<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'config/database.php';

date_default_timezone_set('America/Lima');
header('Content-Type: application/json; charset=utf-8');

// Verificar que sea encargado o administrador
if (!isset($_SESSION['rol']) || (strtolower($_SESSION['rol']) !== 'encargado' && strtolower($_SESSION['rol']) !== 'administrador')) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "
        INSERT INTO asistencia (idEstudiante, fechaEntrada, tipoAsistencia, metodo)
        SELECT e.idEstudiante, CURDATE(), 'Falto', 'Sistema'
        FROM estudiante e
        WHERE NOT EXISTS (
            SELECT 1 FROM asistencia a 
            WHERE a.idEstudiante = e.idEstudiante 
            AND DATE(a.fechaEntrada) = CURDATE()
        )
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $faltasRegistradas = $stmt->rowCount();
    
    echo json_encode([
        'status' => 'success',
        'message' => "Se registraron $faltasRegistradas faltas para el día de hoy",
        'faltas' => $faltasRegistradas
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>