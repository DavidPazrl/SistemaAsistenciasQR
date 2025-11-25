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

    // Obtener reportes entre dos fechas 
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

    public function getReporteParaExcel($grado, $seccion, $fechaInicio, $fechaFin)
    {

        $estudiantes = $this->getEstudiantesPorGradoSeccion($grado, $seccion);
        
        $diasHabiles = $this->getDiasHabiles($fechaInicio, $fechaFin);
        
        $asistencias = $this->getAsistenciasPorPeriodo($grado, $seccion, $fechaInicio, $fechaFin);
        
        $asistenciasPorEstudiante = [];
        foreach ($asistencias as $asist) {
            $idEstudiante = $asist['idEstudiante'];
            $fecha = $asist['fecha'];
            $asistenciasPorEstudiante[$idEstudiante][$fecha] = $asist['tipoAsistencia'];
        }
        
        $datosExcel = [];
        foreach ($estudiantes as $estudiante) {
            $fila = [
                'idEstudiante' => $estudiante['idEstudiante'],
                'nombreCompleto' => $estudiante['Apellidos'] . ' ' . $estudiante['Nombre'],
                'documento' => $estudiante['documento'],
                'asistenciasPorDia' => []
            ];
            
            foreach ($diasHabiles as $dia) {
                $idEst = $estudiante['idEstudiante'];
                
                if (isset($asistenciasPorEstudiante[$idEst][$dia])) {
                    $tipo = $asistenciasPorEstudiante[$idEst][$dia];
                    
                    switch ($tipo) {
                        case 'Asistio':
                            $fila['asistenciasPorDia'][$dia] = 1;
                            break;
                        case 'Falto':
                            $fila['asistenciasPorDia'][$dia] = 0;
                            break;
                        case 'Tardanza':
                            $fila['asistenciasPorDia'][$dia] = 'T';
                            break;
                        default:
                            $fila['asistenciasPorDia'][$dia] = ''; 
                    }
                } else {
                    $fila['asistenciasPorDia'][$dia] = '';
                }
            }
            
            $datosExcel[] = $fila;
        }
        
        return [
            'estudiantes' => $datosExcel,
            'diasHabiles' => $diasHabiles,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'grado' => $grado,
            'seccion' => $seccion
        ];
    }

    private function getEstudiantesPorGradoSeccion($grado, $seccion)
    {
        $query = "
            SELECT 
                idEstudiante,
                Nombre,
                Apellidos,
                documento,
                Grado,
                Seccion
            FROM {$this->table_estudiante}
            WHERE Grado = :grado AND Seccion = :seccion
            ORDER BY Apellidos ASC, Nombre ASC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":grado", $grado);
        $stmt->bindParam(":seccion", $seccion);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAsistenciasPorPeriodo($grado, $seccion, $fechaInicio, $fechaFin)
    {
        $query = "
            SELECT 
                a.idEstudiante,
                DATE(a.fechaEntrada) AS fecha,
                a.tipoAsistencia
            FROM {$this->table_asistencia} a
            INNER JOIN {$this->table_estudiante} e ON a.idEstudiante = e.idEstudiante
            WHERE e.Grado = :grado 
                AND e.Seccion = :seccion
                AND DATE(a.fechaEntrada) BETWEEN :fechaInicio AND :fechaFin
            ORDER BY a.fechaEntrada ASC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":grado", $grado);
        $stmt->bindParam(":seccion", $seccion);
        $stmt->bindParam(":fechaInicio", $fechaInicio);
        $stmt->bindParam(":fechaFin", $fechaFin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getDiasHabiles($fechaInicio, $fechaFin)
    {
        $diasHabiles = [];
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        
        while ($inicio <= $fin) {
            $diaSemana = $inicio->format('N');
            
            if ($diaSemana >= 1 && $diaSemana <= 5) {
                $diasHabiles[] = $inicio->format('Y-m-d');
            }
            
            $inicio->modify('+1 day');
        }
        
        return $diasHabiles;
    }

    public function formatearRangoFechas($fechaInicio, $fechaFin)
    {
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');
        
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $diaInicio = $inicio->format('d');
        $mesInicio = $meses[(int)$inicio->format('n')];
        
        $diaFin = $fin->format('d');
        $mesFin = $meses[(int)$fin->format('n')];
        
        // Si es el mismo mes
        if ($inicio->format('n') == $fin->format('n')) {
            return "$diaInicio - $diaFin de $mesInicio";
        } else {
            return "$diaInicio de $mesInicio - $diaFin de $mesFin";
        }
    }
}
?>