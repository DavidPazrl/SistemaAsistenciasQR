<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'controllers/AlumnoController.php';

$controller = new AlumnoController();

$grado = $_GET['grado'] ?? null;
$seccion = $_GET['seccion'] ?? null;
$alumnos = $controller->index($grado, $seccion);
?>

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Tipografía -->
<link href="https://fonts.cdnfonts.com/css/cabo-soft" rel="stylesheet">

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>assets/js/admin/gestion_alumnos.js?v=2"></script>


<div class="p-6">

    <!-- TÍTULO -->
    <h2 class="text-4xl font-bold mb-8 flex items-center justify-center gap-3 text-red-500"
    style="font-family: 'Cabo Soft', sans-serif;
           text-shadow: 0 0 8px rgba(255, 50, 50, 0.6);">

    <i class="fa-solid fa-user-graduate text-red-600 drop-shadow-[0_0_8px_rgba(255,240,150,0.8)]"></i>
    Gestion de Alumnos
    </h2>




<!-- FILTROS -->
<form method="GET" id="filtro-form"
      class="mb-6 bg-white shadow-lg border border-gray-200 rounded-xl p-4">

    <div class="flex flex-wrap items-center gap-6">

        <!-- Grado -->
        <div class="flex flex-col flex-1 min-w-[250px]">
            <label class="block font-medium">Grado:</label>
            <select name="grado"
                class="border w-full rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
                <option value="">Todos</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= ($grado == $i ? "selected" : "") ?>>
                        <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Sección -->
        <div class="flex flex-col flex-1 min-w-[250px]">
            <label class="block font-medium">Sección:</label>
            <select name="seccion"
                class="border w-full rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
                <option value="">Todas</option>
                <option value="A" <?= ($seccion == "A" ? "selected" : "") ?>>A</option>
                <option value="B" <?= ($seccion == "B" ? "selected" : "") ?>>B</option>
            </select>
        </div>

        <!-- Botón Buscar -->
        <button type="submit"
            class="px-4 py-2 rounded-full bg-gradient-to-r from-orange-400 to-red-500
                   text-white shadow-md hover:from-yellow-400 hover:to-yellow-600 
                   transition flex items-center justify-center"
            title="Buscar">
            <i class="fa fa-search text-lg"></i>
        </button>

        <!-- AGREGAR -->
        <button id="btn-agregar" type="button"
            class="flex items-center gap-2 px-6 py-2 rounded-full
                   font-semibold text-white
                   bg-gradient-to-r from-orange-400 to-red-500
                   hover:from-yellow-400 hover:to-yellow-600
                   transition shadow-md">

            <i class="fa-solid fa-user-plus"></i> Agregar Alumno
        </button>

        <!-- IMPORTAR EXCEL -->
        <button id="btn-importar" type="button"
            class="flex items-center gap-2 px-6 py-2 rounded-full
                   font-semibold text-white
                   bg-gradient-to-r from-orange-400 to-red-500
                   hover:from-orange-500 to-red-600
                   transition shadow-md">

            <i class="fa-solid fa-file-import"></i> Importar Excel
        </button>

        <input type="file" id="input-excel" accept=".xlsx,.xls" class="hidden">

    </div>

</form>



    <!-- TABLA -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-xl border border-gray-200">
        <table id="tabla-alumnos" class="min-w-full text-left">
            <thead class="bg-gradient-to-r from-orange-400 to-red-500 text-white uppercase text-sm">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Apellidos</th>
                    <th class="px-4 py-3">Documento</th>
                    <th class="px-4 py-3">Grado</th>
                    <th class="px-4 py-3">Sección</th>
                    <th class="px-4 py-3">QR Code</th>
                    <th class="px-4 py-3 text-center">Operaciones</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">

                <?php while ($row = $alumnos->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="px-4 py-3"><?= $row['Nombre']; ?></td>
                        <td class="px-4 py-3"><?= $row['Apellidos']; ?></td>
                        <td class="px-4 py-3"><?= $row['documento']; ?></td>
                        <td class="px-4 py-3"><?= $row['Grado']; ?></td>
                        <td class="px-4 py-3"><?= $row['Seccion']; ?></td>
                        <td class="px-4 py-3"><?= $row['qr_code']; ?></td>

                        <td class="px-4 py-3 flex justify-center gap-3">

                        <!-- Editar (dorado suave) -->
                        <button class="editar text-amber-600 hover:text-amber-700 transition"
                            data-id="<?= $row['idEstudiante']; ?>" title="Editar">
                            <i class="fa-solid fa-pen-to-square text-lg"></i>
                        </button>

                        <!-- Eliminar (dorado más intenso / ámbar oscuro) -->
                        <button class="eliminar text-amber-700 hover:text-amber-800 transition"
                            data-id="<?= $row['idEstudiante']; ?>" title="Eliminar">
                            <i class="fa-solid fa-trash text-lg"></i>
                        </button>

                        <!-- QR / CARNET -->
                        <?php if (!empty($row['qr_code'])): ?>

                            <!-- Ver QR (amarillo vibrante) -->
                            <button class="ver-qr text-yellow-500 hover:text-yellow-600 transition"
                                data-id="<?= $row['idEstudiante']; ?>" title="Ver QR">
                                <i class="fa-solid fa-qrcode text-lg"></i>
                            </button>

                            <!-- Imprimir carnet (dorado elegante) -->
                            <button class="imprimir-carnet text-yellow-600 hover:text-yellow-700 transition"
                                data-id="<?= $row['idEstudiante']; ?>" title="Carnet">
                                <i class="fa-solid fa-id-card text-lg"></i>
                            </button>

                        <?php else: ?>

                            <!-- Generar QR (amarillo fuerte tono acción) -->
                            <button class="generar-qr text-yellow-600 hover:text-yellow-700 transition"
                                data-id="<?= $row['idEstudiante']; ?>" title="Generar QR">
                                <i class="fa-solid fa-plus text-lg"></i>
                            </button>

                        <?php endif; ?>

                    </td>

                    </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div>

    <div id="mensaje" class="mt-4"></div>

</div>




<!-- MODAL -->
<div id="modal-alumno"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-xl">

        <h3 id="modal-titulo" class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-user-pen text-blue-600"></i>
            Agregar Alumno
        </h3>

        <form id="form-alumno" class="space-y-4">

            <input type="hidden" name="idEstudiante" id="idEstudiante">

            <div>
                <label class="block font-medium">Nombre:</label>
                <input type="text" name="Nombre" required
                       pattern="[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,50}"
                       class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block font-medium">Apellidos:</label>
                <input type="text" name="Apellidos" required
                       pattern="[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,50}"
                       class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block font-medium">Documento:</label>
                <input type="text" name="documento" maxlength="15" required
                       pattern="\d{8,15}"
                       class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block font-medium">Grado:</label>
                <select name="Grado" required
                        class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
                    <option value="">Seleccione</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <label class="block font-medium">Sección:</label>
                <select name="Seccion" required
                        class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="btn-cerrar"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg">
                    Cancelar
                </button>

                <button type="submit"
                    class="px-4 py-2 rounded-lg text-white font-semibold
                    bg-gradient-to-r from-yellow-300 to-yellow-500
                    hover:from-yellow-400 hover:to-yellow-600
                    transition shadow-md">
                    Guardar
                </button>
            </div>
        </form>

    </div>
</div>

   