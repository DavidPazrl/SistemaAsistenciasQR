<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['rol'] !== 'Admin') {
    header("Location: ../../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../controllers/AlumnoController.php';
$controller = new AlumnoController();
$alumnos = $controller->index();
?>


<h1>Gestión de Alumnos</h1>
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>DNI</th>
        <th>Grado</th>
        <th>Sección</th>
        <th>QR</th>
    </tr>
    <?php while ($row = $alumnos->fetch(PDO::FETCH_ASSOC)) : ?>
        <tr>
            <td><?php echo $row['idEstudiante']; ?></td>
            <td><?php echo $row['Nombre']; ?></td>
            <td><?php echo $row['Apellidos']; ?></td>
            <td><?php echo $row['DNI']; ?></td>
            <td><?php echo $row['Grado']; ?></td>
            <td><?php echo $row['Seccion']; ?></td>
            <td><?php echo $row['qr_code']; ?></td>
        </tr>
    <?php endwhile; ?>
</table>
