<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'controllers/EncargadoController.php';

$controller = new EncargadoController();
$encargados = $controller->index();
?>

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Tipografía personalizada -->
<link href="https://fonts.cdnfonts.com/css/cabo-soft" rel="stylesheet">

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>assets/js/admin/gestion_encargados.js"></script>



<div class="p-6">

    <!-- TÍTULO -->
    <h2 class="text-4xl font-bold mb-6 flex items-center justify-center gap-3 text-red-500"
        style="font-family: 'Cabo Soft', sans-serif; 
              text-shadow: 0 0 8px rgba(255, 50, 50, 0.6);">
        <i class="fa-solid fa-users-gear text-red-600 drop-shadow-[0_0_6px_rgba(255,100,0,0.6)]"></i>
        Gestion de Encargados
    </h2>


    <!-- BOTÓN AGREGAR -->
    <div class="mb-4 flex justify-end">
        <button id="btn-agregar"
            class="flex items-center gap-2 px-6 py-2 rounded-full
                   font-['Cabo Soft'] text-sm text-white
                   bg-gradient-to-r from-orange-400 to-red-500
                   hover:from-orange-500 to-red-600
                   transition-all duration-300 shadow-md">
            <i class="fa-solid fa-user-plus"></i>
            Agregar Encargado
        </button>
    </div>


    <!-- MENSAJE -->
    <div id="mensaje" class="mb-4"></div>


    <!-- TABLA -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-xl border border-gray-200">
        <table id="tabla-encargados" class="min-w-full text-left">
            <thead class="bg-gradient-to-r from-orange-400 to-red-500 text-white uppercase text-sm font-ultralight">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Apellido</th>
                    <th class="px-4 py-3">Usuario</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3 text-center">Operaciones</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">
                <?php 
                $i = 1;
                while ($row = $encargados->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-4 py-3"><?= $i++; ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['Nombre']); ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['Apellido']); ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['usuario']); ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['rol']); ?></td>

                        <!-- BOTONES -->
                        <td class="px-4 py-3 flex justify-center gap-2">

                            <!-- Editar -->
                            <button class="editar text-amber-500 hover:text-amber-600 transition"
                                data-id="<?= $row['idPersonal']; ?>"
                                data-nombre="<?= htmlspecialchars($row['Nombre']); ?>"
                                data-apellido="<?= htmlspecialchars($row['Apellido']); ?>"
                                data-usuario="<?= htmlspecialchars($row['usuario']); ?>"
                                data-rol="<?= htmlspecialchars($row['rol']); ?>"
                                title="Editar">
                                <i class="fa-solid fa-pen-to-square text-lg"></i>
                            </button>

                            <!-- Eliminar -->
                            <button class="eliminar text-yellow-500 hover:text-yellow-600 transition"
                                data-id="<?= $row['idPersonal']; ?>"
                                title="Eliminar">
                                <i class="fa-solid fa-trash text-lg"></i>
                            </button>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>
</div>




<!-- MODAL -->
<div id="modal-encargado"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-xl">

        <h3 id="modal-titulo" class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-user-pen text-orange-600"></i>
            Agregar Encargado
        </h3>

        <form id="form-encargado" class="space-y-4">
            <input type="hidden" name="idPersonal" id="idPersonal">

            <div>
                <label for="nombre" class="block font-medium">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
            </div>

            <div>
                <label for="apellido" class="block font-medium">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
            </div>

            <div>
                <label for="usuario" class="block font-medium">Usuario:</label>
                <input type="text" id="usuario" name="usuario" maxlength="20" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
            </div>

            <div>
                <label for="contrasena" class="block font-medium">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" minlength="4"
                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
            </div>

            <div>
                <label for="rol" class="block font-medium">Rol:</label>
                <input type="text" id="rol" name="rol" placeholder="Encargado" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="btn-cerrar"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg">
                    Cancelar
                </button>

                <button type="submit" id="btn-guardar"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg">
                    Guardar
                </button>
            </div>
        </form>

    </div>
</div>
