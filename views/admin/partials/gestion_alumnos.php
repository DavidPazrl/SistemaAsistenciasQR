<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/AlumnoController.php';
$controller = new AlumnoController();

$grado = $_GET['grado'] ?? null;
$seccion = $_GET['seccion'] ?? null;
$alumnos = $controller->index($grado, $seccion); 
?>

<div class="gestion-alumnos-container">
    <h2>Gestión de Alumnos</h2>

    <!-- Filtros -->
    <form method="GET" id="filtro-form" class="filtros">
        <label>Grado:</label>
        <select name="grado">
            <option value="">Todos</option>
            <?php for ($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>" <?= ($grado==$i?"selected":"") ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label>Sección:</label>
        <select name="seccion">
            <option value="">Todas</option>
            <option value="A" <?= ($seccion=="A"?"selected":"") ?>>A</option>
            <option value="B" <?= ($seccion=="B"?"selected":"") ?>>B</option>
        </select>

        <button type="submit">Buscar</button>
    </form>

    <!-- Botones de accion -->
    <div class="acciones">
        <button id="btn-agregar"> Agregar Alumno</button>
        <button id="btn-importar"> Importar Excel</button>
        <input type="file" id="input-excel" accept=".xlsx,.xls" style="display:none;">
    </div>

    <!-- Tabla de alumnos -->
    <table id="tabla-alumnos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>DNI</th>
                <th>Grado</th>
                <th>Sección</th>
                <th>QR Code</th>
                <th>Operaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $alumnos->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?= $row['Nombre']; ?></td>
                    <td><?= $row['Apellidos']; ?></td>
                    <td><?= $row['DNI']; ?></td>
                    <td><?= $row['Grado']; ?></td>
                    <td><?= $row['Seccion']; ?></td>
                    <td><?= $row['qr_code']; ?></td>
                    <td class="operaciones">
                        <button class="editar" data-id="<?= $row['idEstudiante']; ?>" title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <button class="eliminar" data-id="<?= $row['idEstudiante']; ?>" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                        <?php if (!empty($row['qr_code'])): ?>
                            <button class="ver-qr" data-id="<?= $row['idEstudiante']; ?>" title="Ver QR">
                                <i class="fa-solid fa-qrcode"></i>
                            </button>

                            <button class="imprimir-carnet" data-id="<?= $row['idEstudiante']; ?>" title="Carnet">
                                <i class="fa-solid fa-id-card"></i>
                            </button>
                        <?php else: ?>
                            <button class="generar-qr" data-id="<?= $row['idEstudiante']; ?>" title="Generar QR">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div id="mensaje"></div>
</div>

<!-- Modal para agregar/editar -->
<div id="modal-alumno" class="modal">
    <div class="modal-contenido">
        <h3 id="modal-titulo">Agregar Alumno</h3>
        <form id="form-alumno">
            <input type="hidden" name="idEstudiante" id="idEstudiante">

            <label>Nombre:</label>
            <input type="text" name="Nombre" required pattern="[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,50}" title="Solo letras, minimo 2 caracteres, maximo 50">

            <label>Apellidos:</label>
            <input type="text" name="Apellidos" required pattern="[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,50}" title="Solo letras, minimo 2 caracteres, maximo 50">

            <label>DNI:</label>
            <input type="text" name="DNI" maxlength="8" required pattern="\d{8}" title="Debe tener Exactamente 8 digitos numericos">

            <label>Grado:</label>
            <select name="Grado" required>
                <option value="">Seleccione</option>
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <label>Sección:</label>
            <select name="Seccion" required>
                <option value="A">A</option>
                <option value="B">B</option>
            </select>

            <div class="modal-botones">
                <button type="submit">Guardar</button>
                <button type="button" id="btn-cerrar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="/assets/js/admin/gestion_alumnos.js"></script>
<link rel="stylesheet" href="/assets/css/admin/gestion_alumnos.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

