<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'config/database.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents("php://input"), true);
$qr = isset($input['qr']) ? trim($input['qr']) : '';

if (empty($qr)) {
    echo json_encode(['status' => 'error', 'message' => 'No se envió el código QR']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT idEstudiante, Nombre, Apellidos, documento, Grado, Seccion, qr_code 
              FROM estudiante 
              WHERE qr_code = ? 
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$qr]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        echo json_encode(['status' => 'error', 'message' => 'Alumno no encontrado']);
        exit;
    }

    $idPersonal = null;
    if (isset($_SESSION['idPersonal'])) {
        $idPersonal = $_SESSION['idPersonal'];
    } elseif (isset($_SESSION['usuario'])) {
        $q2 = "SELECT idPersonal FROM personal WHERE usuario = ? LIMIT 1";
        $s2 = $db->prepare($q2);
        $s2->execute([$_SESSION['usuario']]);
        $r2 = $s2->fetch(PDO::FETCH_ASSOC);
        if ($r2) $idPersonal = $r2['idPersonal'];
    }
    $qCheck = "SELECT * 
               FROM asistencia 
               WHERE idEstudiante = ? 
               AND DATE(fechaEntrada) = CURDATE()
               LIMIT 1";
    $sCheck = $db->prepare($qCheck);
    $sCheck->execute([$alumno['idEstudiante']]);
    $registro = $sCheck->fetch(PDO::FETCH_ASSOC);

    // Controlar la hora
    $horaActual = date('H:i:s');
    $horaLimite = '08:05:00'; 

    if (!$registro) {
        $tipoAsistencia = ($horaActual <= $horaLimite) ? 'Asistio' : 'Tardanza';

        $insert = "INSERT INTO asistencia (idEstudiante, idPersonal, fechaEntrada, tipoAsistencia)
                   VALUES (?, ?, NOW(), ?)";
        $sIns = $db->prepare($insert);
        $sIns->execute([$alumno['idEstudiante'], $idPersonal, $tipoAsistencia]);

        echo json_encode([
            'status' => 'success',
            'message' => "Entrada registrada correctamente ($tipoAsistencia)",
            'data' => $alumno,
            'accion' => 'entrada'
        ]);

    } elseif ($registro && is_null($registro['fechaSalida'])) {
        $update = "UPDATE asistencia 
                   SET fechaSalida = NOW()
                   WHERE idAsistencia = ?";
        $sUpd = $db->prepare($update);
        $sUpd->execute([$registro['idAsistencia']]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Salida registrada correctamente',
            'data' => $alumno,
            'accion' => 'salida'
        ]);
    } else {
        echo json_encode([
            'status' => 'warning',
            'message' => 'El alumno ya marcó entrada y salida hoy.',
            'data' => $alumno,
            'accion' => 'completo'
        ]);
    }

    exit;

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de BD: ' . $e->getMessage()]);
    exit;
}
?>
