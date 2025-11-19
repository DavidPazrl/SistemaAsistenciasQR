<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
session_start();

// Si no hay usuario logueado, redirige
if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit();
}

$usuario = $_SESSION['usuario']; // nombre del usuario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema QR</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.cdnfonts.com/css/cabo-soft" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-r from-orange-400 to-red-500 min-h-screen">

    <div class="max-w-5xl mx-auto mt-10 p-6 bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-orange-300">

        <!-- Encabezado -->
        <div class="text-center mb-8">

            <h1 class="text-5xl font-extrabold mb-6 flex items-center justify-center gap-3 text-red-600"
    style="font-family: 'Cabo Soft', sans-serif;
           text-shadow: 0 0 12px rgba(255, 50, 50, 0.7);">
    Bienvenido <?= htmlspecialchars($usuario); ?>
</h1>

        </div>

        <!-- Sección principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Imagen del colegio -->
            <div class="flex justify-center">
                <div class="p-4 bg-white/70 backdrop-blur-sm rounded-xl shadow-lg w-full text-center border border-orange-300">
                    
                    <img src="<?= BASE_URL ?>assets/img/insignia.png"
                        alt="Insignia del Colegio"
                        class="w-40 h-40 object-contain mx-auto opacity-90 drop-shadow-lg">

                    <p class="text-orange-700 mt-2 text-sm">
                       
                    </p>
                </div>
            </div>

            <!-- Sección Tutoriales -->
            <div class="flex">
                <div class="p-4 bg-white/70 backdrop-blur-sm rounded-xl shadow-lg w-full border border-orange-300">

                    <h2 class="text-xl font-semibold mb-4 text-orange-700">Tutoriales / Ayuda</h2>

                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-play text-red-500 drop-shadow"></i>
                            <a href="#" class="text-orange-700 hover:text-red-600 hover:underline">Cómo registrar un alumno</a>
                        </li>

                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-play text-red-500 drop-shadow"></i>
                            <a href="#" class="text-orange-700 hover:text-red-600 hover:underline">Cómo generar QR</a>
                        </li>

                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-play text-red-500 drop-shadow"></i>
                            <a href="#" class="text-orange-700 hover:text-red-600 hover:underline">Cómo tomar asistencia</a>
                        </li>
                    </ul>

                    <p class="text-orange-700 text-sm mt-4">Puedes agregar más tutoriales o videos según sea necesario.</p>
                </div>
            </div>

        </div>

        <!-- Ideas extra -->
        <div class="mt-10 bg-white/70 backdrop-blur-sm p-5 rounded-xl shadow-inner border border-orange-300">
            <h2 class="text-xl font-semibold mb-3 text-orange-700">Ideas adicionales para esta página</h2>

            <ul class="space-y-2 text-orange-800">
                <li>✔ Mostrar avisos importantes del colegio</li>
                <li>✔ Estadísticas rápidas (alumnos, asistencias, etc.)</li>
                <li>✔ Acceso rápido a los módulos más usados</li>
                <li>✔ Noticias internas o calendario escolar</li>
            </ul>
        </div>

    </div>

</body>


</html>
