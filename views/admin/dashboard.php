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
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function cargarSeccion(seccion) {
            const iframe = document.getElementById("contenido");
            iframe.src = "<?php echo BASE_URL; ?>views/admin/partials/" + seccion + ".php";
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="flex h-screen">
        <!-- Menu lateral -->
        <nav class="w-64 bg-gray-900 text-white flex flex-col p-6 space-y-4">
            <h2 class="text-2xl font-bold mb-6 text-center border-b border-gray-700 pb-3">Panel Admin</h2>
            <ul class="space-y-2">
                <li><a href="#" onclick="cargarSeccion('inicio')" class="block px-4 py-2 rounded hover:bg-gray-700">Inicio</a></li>
                <li><a href="#" onclick="cargarSeccion('gestion_admin')" class="block px-4 py-2 rounded hover:bg-gray-700">Gestión Admins</a></li>
                <li><a href="#" onclick="cargarSeccion('gestion_encargados')" class="block px-4 py-2 rounded hover:bg-gray-700">Gestión Encargados</a></li>
                <li><a href="#" onclick="cargarSeccion('gestion_alumnos')" class="block px-4 py-2 rounded hover:bg-gray-700">Gestión Alumnos</a></li>
                <li><a href="#" onclick="cargarSeccion('calendario')" class="block px-4 py-2 rounded hover:bg-gray-700">Calendario</a></li>
                <li><a href="<?php echo BASE_URL; ?>logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-center">Cerrar Sesión</a></li>
            </ul>

            <div class="mt-auto pt-6 border-t border-gray-700 text-center text-sm text-gray-400">
                <p>Usuario: <span class="font-semibold text-gray-300"><?php echo htmlspecialchars($nombreUsuario); ?></span></p>
            </div>
        </nav>

        <!-- Contenido dinámico -->
        <main class="flex-1 bg-white shadow-inner">
            <iframe id="contenido" src="<?php echo BASE_URL; ?>views/admin/partials/inicio.php"
                class="w-full h-full border-0"></iframe>
        </main>
    </div>
</body>
</html>
