<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "personal";

    public $idPersonal;
    public $nombre;
    public $apellido;
    public $usuario;
    public $contraseña;
    public $rol;
    
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Verificar usuario y contraseña
    public function login($usuario, $password) {
        $query = " SELECT * FROM " . $this->table_name . " WHERE usuario = :usuario LIMIT 1 ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['contraseña'] === hash("sha256", $password)){
                return $row;
            }
        }
        return false;
    }
}
?>