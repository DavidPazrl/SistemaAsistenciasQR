<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'encargado'){
    header("Location: ../auth/login.php");
    exit();
}
?>
<h1>Bienvenido Encargado <?php echo $_SESSION['usuario']; ?></h1>
<form method="POST" action="../../logout.php" style="display:inline;">
    <button type="submit">Cerrar sesión</button>
</form>