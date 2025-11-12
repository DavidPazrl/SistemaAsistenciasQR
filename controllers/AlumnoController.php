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
            if ($this->alumno->existeDNIEnOtro($this->alumno->DNI, $this->alumno->idEstudiante)) {
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

            // Mapa de grados
            $gradosMap = [
                'primero' => '1',
                'segundo' => '2',
                'tercero' => '3',
                'cuarto' => '4',
                'quinto' => '5',
                'sexto' => '6'
            ];

            // Leer grado y sección desde las celdas superiores
            $gradoTexto = strtolower(trim($worksheet->getCell('L5')->getValue()));
            $seccion = trim($worksheet->getCell('T5')->getValue());

            // Depuración de encabezados
            error_log("=== IMPORTACIÓN INICIADA ===");
            error_log("Grado leído: $gradoTexto | Sección: $seccion");

            // Convertir texto a número
            $grado = $gradosMap[$gradoTexto] ?? $gradoTexto;

            // Recorremos desde fila 12
            foreach ($worksheet->getRowIterator(12) as $row) {
                $rowIndex = $row->getRowIndex();

                // Leer columnas según formato
                $documento = trim($worksheet->getCell("D{$rowIndex}")->getValue());
                $apellidoPaterno = trim($worksheet->getCell("I{$rowIndex}")->getValue());
                $apellidoMaterno = trim($worksheet->getCell("J{$rowIndex}")->getValue());
                $nombre = trim($worksheet->getCell("K{$rowIndex}")->getValue());

                // Log: valores de fila
                error_log("Fila {$rowIndex} → Doc: {$documento}, ApP: {$apellidoPaterno}, ApM: {$apellidoMaterno}, Nom: {$nombre}");

                // Saltar filas vacías
                if (empty($nombre) && empty($apellidoPaterno) && empty($documento)) {
                    error_log("Fila {$rowIndex} vacía, saltada.");
                    continue;
                }

                $apellidos = trim($apellidoPaterno . " " . $apellidoMaterno);

                // Documento vacío → generar
                if (empty($documento)) {
                    $documento = 'SIN-DOC-' . $rowIndex;
                }

                // Preparar datos
                $data = [
                    "Nombre" => $nombre,
                    "Apellidos" => $apellidos,
                    "documento" => $documento,
                    "Grado" => $grado,
                    "Seccion" => $seccion,
                    "qr_code" => 'QR' . $documento
                ];

                // Log antes de guardar
                error_log("Intentando guardar: " . json_encode($data));

                // Guardar
                $resultado = $this->store($data);
                error_log("Resultado del guardado (fila {$rowIndex}): " . $resultado);

                if ($resultado === "success" || $resultado === "duplicate") {
                    $importados++;
                }
            }

            error_log("=== IMPORTACIÓN FINALIZADA: {$importados} registros importados ===");

            return "success: {$importados} registros importados.";
        } catch (Exception $e) {
            error_log("ERROR en importación: " . $e->getMessage());
            return "error: " . $e->getMessage();
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
