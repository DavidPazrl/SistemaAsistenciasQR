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

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/cabo-soft" rel="stylesheet">

    <script>
        function cargarSeccion(seccion) {
            const iframe = document.getElementById("contenido");
            iframe.src = "<?php echo BASE_URL; ?>views/admin/partials/" + seccion + ".php";
        }
    </script>
</head>

<body class="bg-gradient-to-br from-orange-100 to-red-100 min-h-screen">

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <nav class="
            w-64 backdrop-blur-xl bg-gradient-to-b from-orange-300/20 to-red-300/20 text-gray-900
            border-r border-white/20 shadow-xl p-6 flex flex-col space-y-4 rounded-r-3xl
        ">
            <h2 class="text-3xl font-bold text-center text-red-600 drop-shadow mb-6">
                Panel Admin
            </h2>

            <ul class="space-y-3">
                <li>
                    <a onclick="cargarSeccion('inicio')" class="flex items-center gap-3 px-4 py-2
                        rounded-full hover:bg-white/40 transition cursor-pointer">
                        <i class="fa-solid fa-house text-red-500"></i> Inicio
                    </a>
                </li>

                <li>
                    <a onclick="cargarSeccion('gestion_admin')" class="flex items-center gap-3 px-4 py-2
                        rounded-full hover:bg-white/40 transition cursor-pointer">
                        <i class="fa-solid fa-user-gear text-red-500"></i> Gestión Admins
                    </a>
                </li>

                <li>
                    <a onclick="cargarSeccion('gestion_encargados')" class="flex items-center gap-3 px-4 py-2
                        rounded-full hover:bg-white/40 transition cursor-pointer">
                        <i class="fa-solid fa-users-gear text-red-500"></i> Gestión Encargados
                    </a>
                </li>

                <li>
                    <a onclick="cargarSeccion('gestion_alumnos')" class="flex items-center gap-3 px-4 py-2
                        rounded-full hover:bg-white/40 transition cursor-pointer">
                        <i class="fa-solid fa-user-graduate text-red-500"></i> Gestión Alumnos
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>logout.php"
                       class="flex items-center justify-center gap-2 px-4 py-2
                              bg-gradient-to-r from-red-500 to-orange-500
                              text-white rounded-full shadow hover:opacity-90 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Cerrar Sesión
                    </a>
                </li>
            </ul>

            <!-- Usuario -->
            <div class="mt-auto pt-6 text-center text-sm text-gray-700 border-t border-white/30">
                <p>Usuario:
                    <span class="font-semibold text-red-600">
                        <?php echo htmlspecialchars($nombreUsuario); ?>
                    </span>
                </p>
            </div>
        </nav>

        <!-- CONTENIDO -->
        <main class="flex-1 p-4">
            <iframe id="contenido"
                    src="<?php echo BASE_URL; ?>views/admin/partials/inicio.php"
                    class="w-full h-full rounded-3xl border border-white/20
                           shadow-2xl bg-white/40 backdrop-blur-xl">
            </iframe>
        </main>

    </div>
</body>
</html>
