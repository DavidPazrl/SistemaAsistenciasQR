<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/AlumnoController.php';
$controller = new AlumnoController();
$alumnos = $controller->index();
?>

<div class="gestion-alumnos-container" style="display:flex; gap:20px;">

    <!-- Tabla de Alumnos -->
    <div class="tabla-alumnos" style="flex:2;">
        <h2>Lista de Alumnos</h2>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%;" id="tabla-alumnos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>DNI</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>QR Code</th>
                </tr>
            </thead>
            <tbody>
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
            </tbody>
        </table>
    </div>

    <!-- Formulario para agregar Alumno -->
    <div class="formulario-alumno" style="flex:1; border:1px solid #ccc; padding:10px;">
    <h2>Agregar Alumno</h2>
    <form id="form-alumno">
        <label>Nombre:</label><br>
        <input type="text" name="Nombre" required><br><br>

        <label>Apellidos:</label><br>
        <input type="text" name="Apellidos" required><br><br>

        <label>DNI:</label><br>
        <input type="text" name="DNI" maxlength="8" required><br><br>

        <label>Grado:</label><br>
        <input type="number" name="Grado" min="1" max="12" required><br><br>

        <label>Sección:</label><br>
        <input type="text" name="Seccion" maxlength="5"><br><br>

        <button type="submit">Agregar Alumno</button>
    </form>
    <div id="mensaje" style="margin-top:10px; min-height:20px;"></div>
    <script src="/assets/js/gestion_alumnos.js"></script>

</div>
