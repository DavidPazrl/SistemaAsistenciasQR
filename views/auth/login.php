<?php
session_start();
require_once "../../config.php";
if (isset($_SESSION['usuario'])){
    
    if ($_SESSION['rol'] === 'admin'){
        header("Location: " . BASE_URL . "views/admin/dashboard.php");
    } else {
        header("Location: " . BASE_URL . "views/encargado/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AsistenciaQR</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/cabo-soft" rel="stylesheet">

</head>

<body class="relative h-screen overflow-hidden">
    
<!-- CONTENEDOR LOGIN -->
<div class="relative z-10 flex items-center justify-center h-full">

    <!-- CAJA DEL LOGIN-->
    <div class="bg-white/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-xl w-96 border border-white/20">
        <h2 class="text-2xl font-bold mb-4 text-center text-red-500" style="font-family: 'Cabo Soft', sans-serif;">
    Iniciar Sesion
    </h2>


        <?php if (isset($_GET['error'])): ?>
            <p class="text-red-600 text-center mb-4">Usuario o contraseña incorrectos</p>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>controllers/AuthController.php" method="POST" class="space-y-5">

        

<!-- Usuario -->
<div class="mb-6 relative">

    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 
       text-red-500 text-lg z-30"></i>

    <input type="text" name="usuario" 
        class="relative z-10 w-full rounded-full px-12 py-1.5
        bg-gradient-to-r from-orange-300/30 to-red-300/30
        backdrop-blur-md border border-white/30
        text-white focus:outline-none focus:ring-2 focus:ring-orange-300">
</div>

<!-- Contraseña -->
<div class="mb-6 relative">

    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 
       text-red-500 text-lg z-30"></i>

    <input type="password" name="password" 
        class="relative z-10 w-full rounded-full px-12 py-1.5
        bg-gradient-to-r from-orange-300/30 to-red-300/30
        backdrop-blur-md border border-white/30
        text-white focus:outline-none focus:ring-2 focus:ring-orange-300">
</div>


<!-- boton -->
       <div class="flex justify-center">
    <button type="submit"
 
    class="px-8 py-1.5 rounded-full text-white text-sm
    bg-gradient-to-r from-orange-400 to-red-500
    hover:from-orange-500 hover:to-red-600
    transition-all duration-300 shadow">
    Ingresar
</button>

</div>


        </form>
    </div>

</div>



<!-- SLIDER RESPONSIVE INFINITO -->
<div class="absolute inset-0 overflow-hidden">
    <div class="continuous-slider">
        
        <!-- 8 imágenes originales -->
        <img src="../../assets/img/imagen3.jpeg" class="slide-img">
        <img src="../../assets/img/imagen1.jpeg" class="slide-img">
        <img src="../../assets/img/imagen2.jpeg" class="slide-img">
        <img src="../../assets/img/imagen4.jpeg" class="slide-img">
        <img src="../../assets/img/imagen5.jpeg" class="slide-img">
        <img src="../../assets/img/imagen6.jpeg" class="slide-img">
        <img src="../../assets/img/imagen7.jpeg" class="slide-img">
        <img src="../../assets/img/imagen8.jpeg" class="slide-img">

        <!-- Copias -->
        <img src="../../assets/img/imagen3.jpeg" class="slide-img">
        <img src="../../assets/img/imagen1.jpeg" class="slide-img">
        <img src="../../assets/img/imagen2.jpeg" class="slide-img">
        <img src="../../assets/img/imagen4.jpeg" class="slide-img">
        <img src="../../assets/img/imagen5.jpeg" class="slide-img">
        <img src="../../assets/img/imagen6.jpeg" class="slide-img">
        <img src="../../assets/img/imagen7.jpeg" class="slide-img">
        <img src="../../assets/img/imagen8.jpeg" class="slide-img">

    </div>
</div>

<style>
    .continuous-slider {
        display: flex;
        height: 100%;
        animation: slide 40s linear infinite;
    }

    .slide-img {
        min-width: 100vw;   /* 🔥 Cada imagen ocupa SIEMPRE el ancho real de la pantalla */
        height: 100vh;
        object-fit: cover;
        flex-shrink: 0;     /* 🔥 Nunca se aplastan */
    }

    /* Mover todas las imagenes exactas sin importar el viewport */
    @keyframes slide {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-800vw); } /* 8 imágenes originales */
    }
</style>





</body>



</html>