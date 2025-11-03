<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "personal"; 

    public $idPersonal;
    public $nombre;
    public $apellido;
    public $usuario;
    public $contrasena;
    public $rol;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Verificar usuario 
    public function login($usuario, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['contrasena'] === hash("sha256", $password)) {
                return $row;
            }
        }
        return false;
    }

    // Obtener todos los encargados
    public function obtenerEncargados() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE rol = 'Encargado'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener un encargado por ID
    public function obtenerEncargadoPorId($idPersonal) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idPersonal = :idPersonal LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idPersonal", $idPersonal);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar nuevo encargado
    public function registrarEncargado($nombre, $apellido, $usuario, $contrasena, $rol = 'Encargado') {
        try {
            $hash = hash("sha256", $contrasena);

            $query = "INSERT INTO " . $this->table_name . " 
                      (nombre, apellido, usuario, contrasena, rol) 
                      VALUES (:nombre, :apellido, :usuario, :contrasena, :rol)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellido", $apellido);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->bindParam(":contrasena", $hash);
            $stmt->bindParam(":rol", $rol);

            $resultado = $stmt->execute();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    // Actualizar encargado
    public function actualizarEncargado($idPersonal, $nombre, $apellido, $usuario, $rol) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, apellido = :apellido, usuario = :usuario, rol = :rol
                  WHERE idPersonal = :idPersonal";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":idPersonal", $idPersonal);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellido", $apellido);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":rol", $rol);

        return $stmt->execute();
    }

    // Eliminar encargado
    public function eliminarEncargado($idPersonal) {
        $query = "DELETE FROM " . $this->table_name . " WHERE idPersonal = :idPersonal";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idPersonal", $idPersonal);
        return $stmt->execute();
    }
}
?>
