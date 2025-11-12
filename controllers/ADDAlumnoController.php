<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'config/database.php';
require_once ROOT . 'models/Alumno.php';

header('Content-Type: application/json');

try {

    class ADDAlumnoController {
        private $db;
        private $alumno;

        public function __construct() {
            $database = new Database();
            $this->db = $database->getConnection();
            $this->alumno = new Alumno($this->db);
        }

        // Registrar asistencia segun tipo
        public function registrarAsistencia($data) {

            $dni = $data['documento'] ?? '';
            $tipoRegistro = $data['tipo'] ?? 'entrada'; 
            $fecha = $data['fecha'] ?? date('Y-m-d');      
            $hora = $data['hora'] ?? date('H:i:s');        

            if (!$dni || !$fecha || !$hora) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
                return;
            }

            $alumnoData = $this->alumno->getByDNI($dni);
            if (!$alumnoData) {
                echo json_encode(["status" => "error", "message" => "Alumno no encontrado"]);
                return;
            }

            $alumnoID = $alumnoData['idEstudiante'];
            $idPersonal = $_SESSION['idPersonal'] ?? null;

            if ($tipoRegistro === 'entrada') {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM asistencia WHERE idEstudiante = :idEstudiante AND DATE(fechaEntrada) = :fecha");
                $stmt->bindParam(':idEstudiante', $alumnoID);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    echo json_encode(["status" => "duplicate", "message" => "Entrada ya registrada para esta fecha"]);
                    return;
                }

                // Determinar tipo de asistencia segun hora
                $horaLimite = new DateTime("08:00");
                $horaAlumno = new DateTime($hora);
                $tipoAsistencia = ($horaAlumno <= $horaLimite) ? "Asistio" : "Tardanza";

                // Registrar entrada
                try {
                    $this->alumno->registrarAsistencia($alumnoID, $fecha . ' ' . $hora, $idPersonal, $tipoAsistencia);
                    echo json_encode(["status" => "success", "message" => "Entrada registrada correctamente"]);
                } catch (PDOException $e) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error al registrar entrada",
                        "detalle" => $e->getMessage()
                    ]);
                }

            } elseif ($tipoRegistro === 'salida') {
                // Registrar salida
                try {
                    $horaSalida = $fecha . ' ' . $hora;
                    $this->alumno->registrarSalida($alumnoID, $horaSalida);
                    echo json_encode(["status" => "success", "message" => "Salida registrada correctamente"]);
                } catch (PDOException $e) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error al registrar salida",
                        "detalle" => $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Tipo de registro inválido"]);
            }
        }

        public function buscarPorDocumento($dni) {
            $alumno = $this->alumno->getByDocumento($dni);
            if ($alumno) {
                echo json_encode(["status" => "success", "data" => $alumno]);
            } else {
                echo json_encode(["status" => "not_found"]);
            }
        }
    }

    // Manejo POST
    $controller = new ADDAlumnoController();
    $input = json_decode(file_get_contents('php://input'), true);

    if (is_array($input) && !empty($input)) {
        if (isset($input['action']) && $input['action'] === 'buscarDocumento') {
            $controller->buscarPorDocumento($input['dni']);
        } else {
            $controller->registrarAsistencia($input);
        }
    } else {
        $controller->registrarAsistencia($_POST);
    }


} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
