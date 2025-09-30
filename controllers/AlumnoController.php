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

    // Listar alumnos 
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

    // Excel import
    public function importExcel($filePath) {
        require_once __DIR__ . '/../vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $importados = 0;

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex == 1) continue; // saltar encabezado

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = trim($cell->getValue());
                }

                if (count($rowData) >= 5 && !empty($rowData[0]) && !empty($rowData[2])) {
                    $data = [
                        "Nombre"   => $rowData[0],
                        "Apellidos"=> $rowData[1],
                        "DNI"      => $rowData[2],
                        "Grado"    => $rowData[3],
                        "Seccion"  => $rowData[4],
                    ];
                    $resultado = $this->store($data);
                    if ($resultado === "success" || $resultado === "duplicate") {
                        $importados++;
                    }
                }
            }
            return "success: " . $importados . " registros importados.";
        } catch (Exception $e) {
            return "error: " . $e->getMessage();
        }
    }

    public function generarQR($id) {
        $alumno = $this->alumno->getById($id);
        if (!$alumno) {
            return "Alumno no encontrado";
        }
        require_once __DIR__ . '/../libs/phpqrcode/qrlib.php';
        $qrCodeValue = "QR" . $alumno['DNI'];
        $filePath = __DIR__ . '/../qr_images/' . $qrCodeValue . '.png';
        
        if (!file_exists($filePath)) {
            QRcode::png($qrCodeValue, $filePath, QR_ECLEVEL_L, 4);
        }

        if (empty($alumno['qr_code'])) {
            $this->alumno->idEstudiante = $id;
            $this->alumno->qr_code = $qrCodeValue;
            if (!$this->alumno->updateQR()) {
                return "Error al actualizar QR en la base de datos";
            }
        }
        return "success";
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
        case 'importExcel':
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['file']['tmp_name'];
                echo $controller->importExcel($tmpPath);
            } else {
                echo "error_subida";
            }
            break;
        case 'generarQR':
            echo $controller->generarQR($_POST['id']);
            break;
        default:
            echo "invalid_action";
            break;
    }
    exit();
}
