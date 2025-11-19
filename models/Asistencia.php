<?php
require_once __DIR__ . '/../config/database.php';

class Asistencia
{
    private $conn;
    private $table_name = "asistencia";
    private $idAsistencia;
    private $idEstudiante;
    private $idPersonal;
    private $fechaEntrada;
    private $fechaSalida;
    private $tipoAsistencia;
    private $metodo;
    private $Nombre;
    private $Apellidos;


    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Registro de Asistencias
    public function registrarAsistencia($idEstudiante, $fecha, $idPersonal = null, $tipoAsistencia = "Asistio", $metodo = "camara")
    {
        $query = "INSERT INTO asistencia (idEstudiante, idPersonal, fechaEntrada, tipoAsistencia, metodo) 
              VALUES (:idEstudiante, :idPersonal, :fechaEntrada, :tipoAsistencia, :metodo)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idEstudiante', $idEstudiante);
        $stmt->bindParam(':idPersonal', $idPersonal);
        $stmt->bindParam(':fechaEntrada', $fecha);
        $stmt->bindParam(':tipoAsistencia', $tipoAsistencia);
        $stmt->bindParam(':metodo', $metodo);

        return $stmt->execute();
    }


    public function registrarSalida($idEstudiante, $fechaSalida)
    {
        $query = "UPDATE asistencia 
                SET fechaSalida = :fechaSalida 
                WHERE idEstudiante = :idEstudiante AND DATE(fechaEntrada) = DATE(:fechaSalida)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':idEstudiante', $idEstudiante);
        return $stmt->execute();
    }

    // Listar Para Historial
    public function getHist()
    {
        $query = "
        SELECT 
            e.Nombre AS nombre,
            e.Apellidos AS apellidos,
            a.fechaEntrada AS fecha,
            a.metodo AS metodo
        FROM {$this->table_name} a
        INNER JOIN estudiante e ON a.idEstudiante = e.idEstudiante
        ORDER BY a.fechaEntrada DESC
        LIMIT 10
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

}

?>