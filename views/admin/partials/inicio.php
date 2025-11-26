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
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400&display=swap" rel="stylesheet">


    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-r from-orange-400 to-red-500 min-h-screen">

<div class="max-w-5xl mx-auto mt-10 p-6">

    <div class="grid grid-cols-[auto,1fr] grid-rows-[auto,auto] gap-x-6 items-start mt-6 justify-center">

        <!-- Imagen (ocupa 2 filas) -->
        <img src="<?= BASE_URL ?>assets/img/ggg.png"
            class="h-60 object-contain row-span-2 mt-10"
            style="filter: drop-shadow(0px 0px 8px rgba(0,0,0,0.4));">

        <!-- Texto Bienvenido (arriba a la derecha) -->
        <h1 class="text-[70px] font-light text-white"
            style="font-family: 'Quicksand', sans-serif;
                text-shadow: 0 0 18px rgba(255,255,255,0.85);">
            Bienvenido <?= htmlspecialchars($usuario); ?>
        </h1>

        <!-- Panel (debajo a la derecha, junto a la imagen) -->
        <div class="w-full p-8 py-24 rounded-2xl backdrop-blur-md"
            style="
                background: rgba(255,255,255,0.15);
                border: 1px solid rgba(255,255,255,0.25);
                box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            ">
        </div>

    </div>

    <!------------------------------------ Contenedor de los dos paneles -->
    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8 w-full">

        <!-- Panel Cumpleaños -->
        <div class="p-6 rounded-2xl backdrop-blur-md"
            style="
                background: rgba(255,255,255,0.15);   
                box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            ">
            <div class="absolute inset-0 rounded-2xl pointer-events-none"
                style="
                    padding: 1px;
                    background: linear-gradient(to right, #fb923c, #ef4444);
                    -webkit-mask: 
                        linear-gradient(#fff 0 0) content-box, 
                        linear-gradient(#fff 0 0);
                    -webkit-mask-composite: xor;
                    mask-composite: exclude;
                ">
            </div>
            <h2 class="text-2xl text-white font-light mb-4"
                style="text-shadow: 0 0 12px rgba(255,255,255,0.4);">
                🎉 Cumpleaños de Hoy
            </h2>

            <p class="text-white/80">
                Sin datos por ahora…
            </p>
        </div>

        <!-- Panel Notificaciones -->
        <div class="p-6 rounded-2xl backdrop-blur-md"
            style="
                background: rgba(255,255,255,0.15);
                box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            ">
            <div class="absolute inset-0 rounded-2xl pointer-events-none"
                style="
                    padding: 1px;
                    background: linear-gradient(to right, #fb923c, #ef4444);
                    -webkit-mask: 
                        linear-gradient(#fff 0 0) content-box, 
                        linear-gradient(#fff 0 0);
                    -webkit-mask-composite: xor;
                    mask-composite: exclude;
                ">
            </div>
            <h2 class="text-2xl text-white font-light mb-4"
                style="text-shadow: 0 0 12px rgba(255,255,255,0.4);">
                🔔 Notificaciones
            </h2>

            <p class="text-white/80">
                No hay avisos importantes.
            </p>
        </div>

    </div>


        <!-- Sección principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Imagen del colegio -->
            <div class="flex justify-center">
    <div class="relative z-20 mt-6">

        <img src="<?= BASE_URL ?>assets/img/ggg.png"
             alt="Insignia del Colegio"
             class="h-60 object-contain"
             style="filter: drop-shadow(0px 0px 8px rgba(0,0,0,0.4));">

        <p class="text-orange-700 mt-2 text-sm"></p>
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
