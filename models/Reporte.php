<?php
class Reporte {
    private $conn;
    private $table_estudiante = "estudiante";
    private $table_asistencia = "asistencia";

    public function __construct($db){
        $this->conn = $db;
    }

    // Obtener reportes filtrados
    public function getReportes($grado = null, $seccion = null, $periodo = "semana"){
        $query = "SELECT e.Nombre, e.Apellidos, e.DNI, e.Grado, e.Seccion, a.fechaEntrada, a.tipoAsistencia
                  FROM " . $this->table_estudiante . " e
                  LEFT JOIN " . $this->table_asistencia . " a ON e.idEstudiante = a.idEstudiante
                  WHERE 1=1";

        if ($grado !== null && $grado !== "") {
            $query .= " AND e.Grado = :grado";
        }
        if ($seccion !== null && $seccion !== "") {
            $query .= " AND e.Seccion = :seccion";
        }

        // Filtrar por fecha segun semana o mes
        if($periodo === "semana"){
            $query .= " AND a.fechaEntrada >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif($periodo === "mes"){
            $query .= " AND a.fechaEntrada >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }

        $stmt = $this->conn->prepare($query);

        if ($grado !== null && $grado !== "") {
            $stmt->bindParam(":grado", $grado);
        }
        if ($seccion !== null && $seccion !== "") {
            $stmt->bindParam(":seccion", $seccion);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
