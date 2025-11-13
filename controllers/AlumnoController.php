<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'config/database.php';
require_once ROOT . 'models/Alumno.php';

class AlumnoController
{
    private $db;
    private $alumno;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->alumno = new Alumno($this->db);
    }

    // Listar alumnos 
    public function index($grado = null, $seccion = null)
    {
        return $this->alumno->getAll($grado, $seccion);
    }

    // Guardar alumno
    public function store($data)
    {
        $this->alumno->Nombre = $data['Nombre'];
        $this->alumno->Apellidos = $data['Apellidos'];
        $this->alumno->documento = $data['documento'];
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
    public function update($data)
    {
        $this->alumno->idEstudiante = $data['idEstudiante'];
        $this->alumno->Nombre = $data['Nombre'];
        $this->alumno->Apellidos = $data['Apellidos'];
        $this->alumno->documento = $data['documento'];
        $this->alumno->Grado = $data['Grado'];
        $this->alumno->Seccion = $data['Seccion'];

        try {
            if ($this->alumno->existeDocumentoEnOtro($this->alumno->documento, $this->alumno->idEstudiante)) {
                return "duplicate";
            }
            if ($this->alumno->update()) {
                return "success";
            }
            return "error";
        } catch (PDOException $e) {
            return "error";
        }
    }

    // Eliminar alumno
    public function delete($id)
    {
        return $this->alumno->delete($id) ? "success" : "error";
    }

    // Excel import
    public function importExcel($filePath)
    {
        require_once ROOT . 'vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $importados = 0;
            $noImportados = [];

            $gradoTexto = $worksheet->getCell('L8')->getValue();
            $seccion = $worksheet->getCell('S8')->getValue(); 

            $mapGrado = [
                'PRIMERO' => 1,
                'SEGUNDO' => 2,
                'TERCERO' => 3,
                'CUARTO' => 4,
                'QUINTO' => 5
            ];
            $grado = $mapGrado[strtoupper(trim($gradoTexto))] ?? null;

            foreach ($worksheet->getRowIterator(13) as $row) { 
                $rowIndex = $row->getRowIndex();

                $documento = trim($worksheet->getCell("D{$rowIndex}")->getValue());
                $apellidoP = trim($worksheet->getCell("K{$rowIndex}")->getValue());
                $apellidoM = trim($worksheet->getCell("M{$rowIndex}")->getValue());
                $nombre = trim($worksheet->getCell("P{$rowIndex}")->getValue());

                if (empty($documento) || empty($nombre)) {
                    $noImportados[] = [
                        'Fila' => $rowIndex,
                        'Documento' => $documento,
                        'Nombre' => $nombre,
                        'Motivo' => 'Faltan datos obligatorios'
                    ];
                    continue;
                }

                $data = [
                    'Nombre' => $nombre,
                    'Apellidos' => $apellidoP . ' ' . $apellidoM,
                    'documento' => $documento,
                    'Grado' => $grado,
                    'Seccion' => $seccion
                ];

                $resultado = $this->store($data);

                if ($resultado === "success") {
                    $importados++;
                } elseif ($resultado === "duplicate") {
                    $noImportados[] = [
                        'Fila' => $rowIndex,
                        'Documento' => $documento,
                        'Nombre' => $nombre,
                        'Motivo' => 'Duplicado'
                    ];
                }
            }

            return [
                'importados' => $importados,
                'noImportados' => $noImportados
            ];

        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }



    public function generarQR($id)
    {
        $alumno = $this->alumno->getById($id);
        if (!$alumno) {
            return "Alumno no encontrado";
        }
        require_once ROOT . 'libs/phpqrcode/qrlib.php';
        $qrCodeValue = "QR" . $alumno['documento'];
        $filePath = ROOT . 'qr_images/' . $qrCodeValue . '.png';

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
                $result = $controller->importExcel($tmpPath);
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'error_subida']);
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
