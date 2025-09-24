<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Alumno.php';

class AlumnoController {
    private $db;
    private $alumno;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->alumno = new Alumno($this->db);
    }

    public function index() {
        return $this->alumno->getAll();
    }

    public function store($data) {
        $this->alumno->Nombre = $data['Nombre'];
        $this->alumno->Apellidos = $data['Apellidos'];
        $this->alumno->DNI = $data['DNI'];
        $this->alumno->Grado = $data['Grado'];
        $this->alumno->Seccion = $data['Seccion'];
        $this->alumno->qr_code = "QR" . $data['DNI'];

        // Intentar guardar y manejar duplicados
        try {
            $this->alumno->create();
            return true; 
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                return "duplicate";
            } else {
                return false;
            }
        }
    }
}

// Solo responder a POST para AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AlumnoController();
    $result = $controller->store($_POST);

    if ($result === "duplicate") {
        echo "duplicate";
    } elseif ($result) {
        echo "success";
    } else {
        echo "error";
    }
    exit();
}
