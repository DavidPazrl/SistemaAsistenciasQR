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

        return $this->alumno->create();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AlumnoController();
    if ($controller->store($_POST)) {
        header("Location: ../views/admin/alumnos/index.php?success=1");
    } else {
        header("Location: ../views/admin/alumnos/create.php?error=1");
    }
    exit();
}