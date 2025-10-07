<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents("php://input"), true);
$qr = isset($input['qr']) ? trim($input['qr']) : '';

if (empty($qr)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se envió el código QR'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT idEstudiante, Nombre, Apellidos, DNI, Grado, Seccion, qr_code 
              FROM estudiante 
              WHERE qr_code = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$qr]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($alumno) {
        echo json_encode([
            'status' => 'success',
            'data' => $alumno
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alumno no encontrado'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de conexión o consulta: ' . $e->getMessage()
    ]);
}
