<?php
session_start();
require_once __DIR__ . '/../../config.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit();
}
$nombreUsuario = $_SESSION['usuario'];

require_once ROOT . 'controllers/AlumnoController.php';
$controller = new AlumnoController();
$alumnos = $controller->index();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin/dashboard.css">
    <script>
        function cargarSeccion(seccion) {
            const iframe = document.getElementById("contenido");
            iframe.src = "<?php echo BASE_URL; ?>views/admin/partials/" + seccion + ".php";
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Menu lateral -->
        <nav class="sidebar">
            <h2>Panel Admin</h2>
            <ul>
                <li><a href="#" onclick="cargarSeccion('inicio')">Inicio</a></li>
                <li><a href="#" onclick="cargarSeccion('gestion_usuarios')">Gestión Usuarios</a></li>
                <li><a href="#" onclick="cargarSeccion('gestion_encargados')">Gestión Encargados</a></li>
                <li><a href="#" onclick="cargarSeccion('gestion_alumnos')">Gestión Alumnos</a></li>
                <li><a href="#" onclick="cargarSeccion('calendario')">Calendario</a></li>
                <li><a href="<?php echo BASE_URL; ?>logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>

        <!-- Contenido dinamico -->
        <main class="main-content">
            <iframe id="contenido" src="<?php echo BASE_URL; ?>views/admin/partials/inicio.php" frameborder="0"></iframe>
        </main>
    </div>
</body>
</html>
