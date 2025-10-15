<?php
session_start();
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['rol']) !== 'encargado') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Encargado</title>
    <link rel="stylesheet" href="../../assets/css/encargado/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="menu-toggle" aria-label="Abrir menú">&#9776;</div>

    <nav id="sidebar">
        <div class="sidebar-header">
            Panel del Encargado
        </div>
        <ul class="components">
            <li><a href="#" id="btn-agregar">Inicio</a></li>
            <li><a href="#" id="btn-reportes">Reportes</a></li>
            <li>
                <form method="POST" action="../../logout.php" style="margin: 0;">
                    <button type="submit" class="logout-btn">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </nav>
    <div id="content">
        <div id="inicio">
            <div class="bienvenida">
                <h1>Bienvenido Encargado <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
            </div>

            <div class="camera-container">
                <video id="camera" autoplay playsinline muted></video>
                <canvas id="canvas" hidden></canvas>
            </div>
        </div>
        <div id="reportes" style="display:none;">
            <h2 class="text-center mt-4">Sección de Reportes</h2>

            <!-- Filtros -->
            <div class="filters my-4">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-3">
                        <select id="filtroGrado" class="form-select">
                            <option value="">Todos los grados</option>
                            <option value="1">1°</option>
                            <option value="2">2°</option>
                            <option value="3">3°</option>
                            <option value="4">4°</option>
                            <option value="5">5°</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroSeccion" class="form-select">
                            <option value="">Todas las secciones</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroPeriodo" class="form-select">
                            <option value="semana">Semana</option>
                            <option value="mes">Mes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button id="btnGenerarReporte" class="btn btn-primary w-100">Generar Reporte</button>
                        <button id="btnExportarExcel" class="btn btn-success flex-fill">Exportar a Excel</button>
                    </div>
                </div>
            </div>

            <!-- Tabla de reportes -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered mt-3" id="tablaReportes">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Grado</th>
                            <th>Sección</th>
                            <th>Fecha</th>
                            <th>Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <div id="mensaje" class="alert alert-success text-center" style="display:none;" role="status" aria-live="polite">
            Alumno Encontrado
        </div>
        <div id="overlay" style="display:none;"></div>
        <div id="carnet" style="display:none;">
            <img id="fotoAlumno" src="" alt="Foto del alumno">
            <div class="datos-alumno">
                <h4 id="nombreAlumno"></h4>
                <p><strong>DNI:</strong> <span id="dniAlumno"></span></p>
                <p><strong>Grado:</strong> <span id="gradoAlumno"></span></p>
                <p><strong>Sección:</strong> <span id="seccionAlumno"></span></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script src="../../assets/js/encargado/dashboard.js"></script>
</body>
</html>
