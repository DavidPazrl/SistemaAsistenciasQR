<?php
session_start();
if (isset($_SESSION['usuario'])){
    
    if ($_SESSION['rol'] === 'Admin'){
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../encargado/dashboard.php");
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
    <link rel="stylesheet" href="../../assets//css/style.css">
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if(isset($_GET['error'])): ?>
        <p style="color:red;">Usuario o contraseña incorrectos</p>
    <?php endif; ?>

    <form action="../../controllers/AuthController.php" method="POST">
        <label for = "usuario">Usuario:</label><br>
        <input type="text" name="usuario" required><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Ingresar</button>
    </form>
</body>
</html>