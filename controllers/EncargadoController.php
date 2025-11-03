<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'config/database.php';
require_once ROOT . 'models/Usuario.php';

class EncargadoController
{
    private $db;
    private $usuario;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    // Listar encargados
    public function index()
    {
        $query = "SELECT * FROM personal WHERE rol = 'Encargado'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener por ID
    public function getById($id)
    {
        $query = "SELECT * FROM personal WHERE idPersonal = :id AND rol = 'Encargado'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar nuevo encargado
    public function store($data)
    {
        // Validar campos requeridos
        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['usuario']) || empty($data['contrasena'])) {
            error_log("Faltan campos obligatorios en store()");
            return "error";
        }

        try {
            $query = "INSERT INTO personal (nombre, apellido, usuario, contrasena, rol)
                      VALUES (:nombre, :apellido, :usuario, :contrasena, :rol)";
            $stmt = $this->db->prepare($query);

            $hashed = hash("sha256", $data['contrasena']);

            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':usuario', $data['usuario']);
            $stmt->bindParam(':contrasena', $hashed);
            $stmt->bindParam(':rol', $data['rol']);

            $stmt->execute();
            return "success";
        } catch (PDOException $e) {
            return "error";
        }
    }

    // Actualizar encargado
    public function update($data)
    {
        try {
            $query = "UPDATE personal 
                      SET nombre = :nombre, apellido = :apellido, usuario = :usuario
                      WHERE idPersonal = :idPersonal AND rol = 'Encargado'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':usuario', $data['usuario']);
            $stmt->bindParam(':idPersonal', $data['idPersonal']);
            $stmt->execute();
            return "success";
        } catch (PDOException $e) {
            error_log("Error SQL en update(): " . $e->getMessage());
            return "error";
        }
    }

    // Eliminar encargado
    public function delete($id)
    {
        try {
            $query = "DELETE FROM personal WHERE idPersonal = :id AND rol = 'Encargado'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return "success";
        } catch (PDOException $e) {
            error_log("Error SQL en delete(): " . $e->getMessage());
            return "error";
        }
    }
}

// Manejo de peticiones AJAX 
$controller = new EncargadoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        case 'list':
            $stmt = $controller->index();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
        default:
            echo "invalid_action";
            break;
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (isset($_GET['idPersonal'])) {
        echo json_encode($controller->getById($_GET['idPersonal']));
    } else {
        echo json_encode(['error' => 'Falta idPersonal']);
    }
    exit;
}
?>
