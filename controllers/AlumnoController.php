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

    // Listar alumnos (con filtros opcionales)
    public function index($grado = null, $seccion = null) {
        return $this->alumno->getAll($grado, $seccion);
    }

    // Guardar alumno
    public function store($data) {
        $this->alumno->Nombre = $data['Nombre'];
        $this->alumno->Apellidos = $data['Apellidos'];
        $this->alumno->DNI = $data['DNI'];
        $this->alumno->Grado = $data['Grado'];
        $this->alumno->Seccion = $data['Seccion'];
        $this->alumno->qr_code = "QR" . $data['DNI'];

        try {
            $this->alumno->create();
            return "success"; 
        } catch (PDOException $e) {
            if ($e->getCode() == "45000") {   
                return "duplicate";
            } elseif ($e->getCode() == "23000") { 
                return "duplicate";
            } else {
                return "error";
            }
        }
    }

    // Actualizar alumno
    public function update($data){
        $this->alumno->idEstudiante = $data['idEstudiante'];
        $this->alumno->Nombre = $data['Nombre'];
        $this->alumno->Apellidos = $data['Apellidos'];
        $this->alumno->DNI = $data['DNI'];
        $this->alumno->Grado = $data['Grado'];
        $this->alumno->Seccion = $data['Seccion'];

        try {
            if ($this->alumno->existeDNIEnOtro($this->alumno->DNI, $this->alumno->idEstudiante)){
                return "duplicate";
            }
            if($this->alumno->update()){
                return "success";
            }
            return "error";
        } catch (PDOException $e){
            return "error";
        }
    } 
    
    // Eliminar alumno
    public function delete($id) {
        return $this->alumno->delete($id) ? "success" : "error";
    }

    // Importar desde Excel (pendiente)
    public function importExcel($filePath){
        // implementar
    }
}

// --- Responder a AJAX ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AlumnoController();
    $action = $_POST['action'] ?? null;

    switch ($action) {
        case 'store':
            echo $controller->store($_POST);
            break;
        case 'update':
            echo $controller->update($_POST);
            break;
        case 'delete':
            echo $controller->delete($_POST['id']);
            break;
        case 'filter':
            $grado = $_POST['Grado'] ?? null;
            $seccion = $_POST['Seccion'] ?? null;
            $result = $controller->index($grado, $seccion);
            $alumnos = $result->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($alumnos);
            break;
        default:
            echo "invalid_action";
            break;
    }
    exit();
}
