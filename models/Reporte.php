<?php
class Reporte
{
    private $conn;
    private $table_estudiante = "estudiante";
    private $table_asistencia = "asistencia";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Obtener reportes filtrados
    public function getReportes($grado = null, $seccion = null, $periodo = "semana")
    {
        $query = "
            SELECT 
                e.Nombre,
                e.Apellidos,
                e.documento,
                e.Grado,
                e.Seccion,
                a.tipoAsistencia,
                DATE(a.fechaEntrada) AS fechaEntrada,
                TIME(a.fechaEntrada) AS horaEntrada,
                TIME(a.fechaSalida) AS horaSalida
            FROM {$this->table_estudiante} e
            LEFT JOIN {$this->table_asistencia} a 
                ON e.idEstudiante = a.idEstudiante
            WHERE 1=1
        ";

        if (!empty($grado)) {
            $query .= " AND e.Grado = :grado";
        }
        if (!empty($seccion)) {
            $query .= " AND e.Seccion = :seccion";
        }

        // Filtro de tiempo
        if ($periodo === "semana") {
            $query .= " AND a.fechaEntrada >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($periodo === "mes") {
            $query .= " AND a.fechaEntrada >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($grado)) {
            $stmt->bindParam(":grado", $grado);
        }
        if (!empty($seccion)) {
            $stmt->bindParam(":seccion", $seccion);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  obtener reportes entre dos fechas específicas
    public function getReportesPorFechas($grado = null, $seccion = null, $fechaInicio, $fechaFin)
    {
        $query = "
            SELECT 
                e.Nombre,
                e.Apellidos,
                e.documento,
                e.Grado,
                e.Seccion,
                a.tipoAsistencia,
                DATE(a.fechaEntrada) AS fechaEntrada,
                TIME(a.fechaEntrada) AS horaEntrada,
                TIME(a.fechaSalida) AS horaSalida
            FROM {$this->table_estudiante} e
            LEFT JOIN {$this->table_asistencia} a 
                ON e.idEstudiante = a.idEstudiante
            WHERE DATE(a.fechaEntrada) BETWEEN :fechaInicio AND :fechaFin
        ";

        if (!empty($grado)) {
            $query .= " AND e.Grado = :grado";
        }
        if (!empty($seccion)) {
            $query .= " AND e.Seccion = :seccion";
        }

        $query .= " ORDER BY a.fechaEntrada ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fechaInicio", $fechaInicio);
        $stmt->bindParam(":fechaFin", $fechaFin);

        if (!empty($grado)) {
            $stmt->bindParam(":grado", $grado);
        }
        if (!empty($seccion)) {
            $stmt->bindParam(":seccion", $seccion);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>