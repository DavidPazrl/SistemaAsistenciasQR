<?php
class Alumno {
    private $conn;
    private $table_name = "estudiante";

    public $idEstudiante;
    public $Nombre;
    public $Apellidos;
    public $DNI;
    public $Grado;
    public $Seccion;
    public $qr_code;

    public function __construct($db){
        $this->conn = $db;
    }

    //Listar Alumnos
    public function getAll(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //Insertar Alumno
    public function create(){
        $query = "CALL insertar_estudiante(:Nombre, :Apellidos, :DNI, :Grado, :Seccion, :qr_code)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":Nombre", $this->Nombre);
        $stmt->bindParam(":Apellidos", $this->Apellidos);
        $stmt->bindParam(":DNI", $this->DNI);
        $stmt->bindParam(":Grado", $this->Grado);
        $stmt->bindParam(":Seccion", $this->Seccion);
        $stmt->bindParam(":qr_code", $this->qr_code);

        return $stmt->execute();
    }

}