<?php
session_start();
$nombreUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : "Admin";
?>
<div class="inicio">
    <h1>Bienvenido, <?php echo $nombreUsuario; ?> </h1>
    <p>Implementar un inicio decente</p>
</div>
