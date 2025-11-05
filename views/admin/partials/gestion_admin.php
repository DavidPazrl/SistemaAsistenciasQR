<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'controllers/AdminController.php';

$controller = new AdminController();
$admins = $controller->index();
?>

<div class="contenedor-admin">
    <h2 class="titulo-seccion">
        <i class="fa-solid fa-users-gear"></i> Gestión de Admins
    </h2>
    
    <!-- Boton agregar -->
    <div class="acciones">
        <button id="btn-agregar" class="btn-agregar">
            <i class="fa-solid fa-user-plus"></i> Agregar Admin
        </button>
    </div>

    <!-- Mensaje -->
    <div id="mensaje" class="mensaje"></div>

    <!-- Tabla de admin -->
    <div class="tabla-contenedor">
        <table id="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                while ($row = $admins->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($row['Nombre']); ?></td>
                        <td><?= htmlspecialchars($row['Apellido']); ?></td>
                        <td><?= htmlspecialchars($row['usuario']); ?></td>
                        <td><?= htmlspecialchars($row['rol']); ?></td>
                        <td class="operaciones">
                            <button class="editar" 
                                data-id="<?= $row['idPersonal']; ?>"
                                data-nombre="<?= htmlspecialchars($row['Nombre']); ?>"
                                data-apellido="<?= htmlspecialchars($row['Apellido']); ?>"
                                data-usuario="<?= htmlspecialchars($row['usuario']); ?>"
                                data-rol="<?= htmlspecialchars($row['rol']); ?>"
                                title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="eliminar" 
                                data-id="<?= $row['idPersonal']; ?>" 
                                title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar/editar Admin -->
<div id="modal-admin" class="modal">
    <div class="modal-contenido">
        <h3 id="modal-titulo">
            <i class="fa-solid fa-user-pen"></i> Agregar Admin
        </h3>
        <form id="form-admin">
            <input type="hidden" name="idPersonal" id="idPersonal">

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required>

            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" maxlength="20" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" minlength="4">

            <label for="rol">Rol:</label>
            <input type="text" name="rol" id="rol" placeholder="Admin" required>

            <div class="modal-botones">
                <button type="submit" id="btn-guardar" class="btn-guardar">Guardar</button>
                <button type="button" id="btn-cerrar" class="btn-cerrar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>assets/js/admin/gestion_admin.js"></script>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin/gestion_admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

