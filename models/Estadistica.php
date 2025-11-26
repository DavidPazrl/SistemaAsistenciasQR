<?php
require_once __DIR__ . '/../config/database.php';

class Estadistica {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtiene estadísticas de asistencia por aula (Grado + Sección)
     * @return array
     */
    public function getEstadisticasPorAula() {
        try {
            $query = "
                SELECT 
                    CONCAT(e.Grado, '°', e.Seccion) as aula,
                    e.Grado,
                    e.Seccion,
                    COUNT(DISTINCT e.idEstudiante) as total_estudiantes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) as presentes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Falto' THEN 1 END) as ausentes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Tardanza' THEN 1 END) as tardanzas,
                    COUNT(CASE WHEN a.tipoAsistencia IN ('Falta justificada', 'Tardanza justificada') THEN 1 END) as justificadas,
                    ROUND((COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) * 100.0 / 
                           NULLIF(COUNT(a.idAsistencia), 0)), 2) as porcentaje_asistencia
                FROM estudiante e
                LEFT JOIN asistencia a ON e.idEstudiante = a.idEstudiante
                GROUP BY e.Grado, e.Seccion
                ORDER BY e.Grado, e.Seccion
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasPorAula: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene asistencias por fecha (últimos N días)
     * @param int $dias
     * @return array
     */
    public function getAsistenciasPorFecha($dias = 7) {
        try {
            $query = "
                SELECT 
                    DATE(fechaEntrada) as fecha,
                    COUNT(CASE WHEN tipoAsistencia = 'Asistio' THEN 1 END) as presentes,
                    COUNT(CASE WHEN tipoAsistencia = 'Falto' THEN 1 END) as ausentes,
                    COUNT(CASE WHEN tipoAsistencia = 'Tardanza' THEN 1 END) as tardanzas,
                    COUNT(CASE WHEN tipoAsistencia = 'Falta justificada' THEN 1 END) as faltas_justificadas
                FROM asistencia
                WHERE fechaEntrada >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                GROUP BY DATE(fechaEntrada)
                ORDER BY fecha ASC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAsistenciasPorFecha: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene top N estudiantes con mejor asistencia
     * @param int $limit
     * @return array
     */
    public function getTopEstudiantesAsistencia($limit = 5) {
        try {
            $query = "
                SELECT 
                    e.Nombre,
                    e.Apellidos,
                    CONCAT(e.Grado, '°', e.Seccion) as aula,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) as presentes,
                    COUNT(a.idAsistencia) as total_registros,
                    ROUND((COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) * 100.0 / 
                           NULLIF(COUNT(a.idAsistencia), 0)), 2) as porcentaje
                FROM estudiante e
                INNER JOIN asistencia a ON e.idEstudiante = a.idEstudiante
                GROUP BY e.idEstudiante, e.Nombre, e.Apellidos, e.Grado, e.Seccion
                HAVING COUNT(a.idAsistencia) >= 5
                ORDER BY porcentaje DESC, presentes DESC
                LIMIT :limit
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getTopEstudiantesAsistencia: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene resumen general de asistencias del día
     * @return array
     */
    public function getResumenGeneral() {
        try {
            $query = "
                SELECT 
                    (SELECT COUNT(*) FROM estudiante) as total_estudiantes,
                    (SELECT COUNT(DISTINCT CONCAT(Grado, Seccion)) FROM estudiante) as total_aulas,
                    COUNT(CASE WHEN tipoAsistencia = 'Asistio' THEN 1 END) as total_presentes,
                    COUNT(CASE WHEN tipoAsistencia = 'Falto' THEN 1 END) as total_ausentes,
                    COUNT(CASE WHEN tipoAsistencia = 'Tardanza' THEN 1 END) as total_tardanzas,
                    COUNT(CASE WHEN tipoAsistencia IN ('Falta justificada', 'Tardanza justificada') THEN 1 END) as total_justificadas,
                    ROUND((COUNT(CASE WHEN tipoAsistencia = 'Asistio' THEN 1 END) * 100.0 / 
                           NULLIF(COUNT(*), 0)), 2) as porcentaje_general
                FROM asistencia
                WHERE DATE(fechaEntrada) = CURDATE()
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getResumenGeneral: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtiene distribución de estados de asistencia (para gráfico circular)
     * @return array
     */
    public function getDistribucionEstados() {
        try {
            $query = "
                SELECT 
                    tipoAsistencia as estado,
                    COUNT(*) as cantidad,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM asistencia)), 2) as porcentaje
                FROM asistencia
                GROUP BY tipoAsistencia
                ORDER BY cantidad DESC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getDistribucionEstados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene comparativa de asistencia entre aulas por mes
     * @param int $mes
     * @param int $anio
     * @return array
     */
    public function getComparativaAulasMes($mes = null, $anio = null) {
        try {
            $mes = $mes ?? date('n');
            $anio = $anio ?? date('Y');
            
            $query = "
                SELECT 
                    CONCAT(e.Grado, '°', e.Seccion) as aula,
                    COUNT(DISTINCT e.idEstudiante) as total_estudiantes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) as presentes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Falto' THEN 1 END) as ausentes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Tardanza' THEN 1 END) as tardanzas,
                    ROUND((COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) * 100.0 / 
                           NULLIF(COUNT(a.idAsistencia), 0)), 2) as porcentaje
                FROM estudiante e
                LEFT JOIN asistencia a ON e.idEstudiante = a.idEstudiante
                WHERE MONTH(a.fechaEntrada) = :mes AND YEAR(a.fechaEntrada) = :anio
                GROUP BY e.Grado, e.Seccion
                ORDER BY e.Grado, e.Seccion
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getComparativaAulasMes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene estadísticas por método de registro (cámara/manual)
     * @return array
     */
    public function getEstadisticasPorMetodo() {
        try {
            $query = "
                SELECT 
                    metodo,
                    COUNT(*) as total,
                    COUNT(CASE WHEN tipoAsistencia = 'Asistio' THEN 1 END) as presentes,
                    COUNT(CASE WHEN tipoAsistencia = 'Falto' THEN 1 END) as ausentes,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM asistencia)), 2) as porcentaje_uso
                FROM asistencia
                GROUP BY metodo
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasPorMetodo: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene estadísticas por grado
     * @return array
     */
    public function getEstadisticasPorGrado() {
        try {
            $query = "
                SELECT 
                    e.Grado,
                    COUNT(DISTINCT e.idEstudiante) as total_estudiantes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) as presentes,
                    COUNT(CASE WHEN a.tipoAsistencia = 'Falto' THEN 1 END) as ausentes,
                    ROUND((COUNT(CASE WHEN a.tipoAsistencia = 'Asistio' THEN 1 END) * 100.0 / 
                           NULLIF(COUNT(a.idAsistencia), 0)), 2) as porcentaje_asistencia
                FROM estudiante e
                LEFT JOIN asistencia a ON e.idEstudiante = a.idEstudiante
                GROUP BY e.Grado
                ORDER BY e.Grado
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasPorGrado: " . $e->getMessage());
            return [];
        }
    }
}