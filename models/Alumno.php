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
    public function getAll($grado = null, $seccion = null){
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        if ($grado !== null){
            $query .= " AND Grado = :grado";
        }
        if ($seccion !== null){
            $query .= " AND Seccion = :seccion";
        }
        
        $stmt = $this->conn->prepare($query);

        if ($grado !== null){
            $stmt->bindParam(":grado", $grado);
        }
        if ($seccion !==null) {
            $stmt->bindParam(":seccion", $seccion);
        }
        $stmt->execute();
        return $stmt;
    }

    //Listar Por grado y seccion
    public function getByFiltro($grado, $seccion){
        $query = "SELECT * FROM " . $this->table_name . " WHERE Grado = :grado AND Seccion = :seccion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":grado", $grado);
        $stmt->bindParam(":seccion", $seccion);
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

    //Editar
    public function update(){
        $query = "UPDATE " . $this->table_name . " 
                  SET Nombre = :Nombre, Apellidos = :Apellidos, DNI = :DNI,
                      Grado = :Grado, Seccion = :Seccion
                  Where idEstudiante = :idEstudiante";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Nombre", $this->Nombre);
        $stmt->bindParam(":Apellidos", $this->Apellidos);
        $stmt->bindParam(":DNI", $this->DNI);
        $stmt->bindParam(":Grado", $this->Grado);
        $stmt->bindParam(":Seccion", $this->Seccion);
        $stmt->bindParam(":idEstudiante", $this->idEstudiante);
        return $stmt->execute();
    }

    //Eliminar
    public function delete($id){
        $query = "DELETE FROM " . $this->table_name . " WHERE idEstudiante = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Verificar si existe un DNI en otro alumno distinto
    public function existeDNIEnOtro($dni, $idEstudiante) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                WHERE DNI = :dni AND idEstudiante != :idEstudiante";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":dni", $dni);
        $stmt->bindParam(":idEstudiante", $idEstudiante);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

}