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
            <li><a href="#">Agregar alumno manualmente</a></li>
            <li>
                <form method="POST" action="../../logout.php" style="margin: 0;">
                    <button type="submit" class="logout-btn">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </nav>

    <div id="content">
        <div class="bienvenida">
            <h1>Bienvenido Encargado <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
        </div>

        <div class="camera-container">
            <video id="camera" autoplay playsinline muted></video>
            <canvas id="canvas" hidden></canvas>
        </div>

        <div id="mensaje" class="alert alert-success text-center" style="display:none;" role="status" aria-live="polite">
            Alumno Encontrado
        </div>

        <!-- Carnet de alumno -->
        <div id="overlay"></div>
        <div id="carnet">
            <img id="fotoAlumno" src="" alt="Foto del alumno">
            <h4 id="nombreAlumno"></h4>
            <p><strong>DNI:</strong> <span id="dniAlumno"></span></p>
            <p><strong>Grado:</strong> <span id="gradoAlumno"></span></p>
            <p><strong>Sección:</strong> <span id="seccionAlumno"></span></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script src="../../assets/js/encargado/dashboard.js" defer></script>
</body>
</html>
