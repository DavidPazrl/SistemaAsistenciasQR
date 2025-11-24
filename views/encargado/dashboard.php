<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';

session_start();
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['rol']) !== 'encargado') {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Encargado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
        }
        
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-mobile.active {
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .sidebar-desktop {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
            .sidebar-mobile {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-orange-100 to-red-100 min-h-screen">

    <!-- Botón menú móvil -->
    <button id="menu-toggle" class="menu-toggle fixed top-4 left-4 z-50 bg-gradient-to-r from-red-500 to-orange-500 text-white p-3 rounded-full shadow-lg md:hidden">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <nav id="sidebar" class="sidebar-mobile fixed md:static w-64 backdrop-blur-xl bg-gradient-to-b from-orange-300/20 to-red-300/20 text-gray-900 border-r border-white/20 shadow-xl p-6 flex flex-col space-y-4 rounded-r-3xl h-full z-40">
            <h2 class="text-3xl font-bold text-center text-red-600 drop-shadow mb-6">
                Panel Encargado
            </h2>

            <ul class="space-y-3">
                <li>
                    <button type="button" id="btn-agregar" class="flex items-center gap-3 px-4 py-2 rounded-full hover:bg-white/40 transition cursor-pointer w-full text-left">
                        <i class="fa-solid fa-house text-red-500"></i> Inicio
                    </button>
                </li>

                <li>
                    <button type="button" id="btn-reportes" class="flex items-center gap-3 px-4 py-2 rounded-full hover:bg-white/40 transition cursor-pointer w-full text-left">
                        <i class="fa-solid fa-chart-bar text-red-500"></i> Reportes
                    </button>
                </li>

                <li>
                    <button type="button" id="btn-AgregarAlumno" class="flex items-center gap-3 px-4 py-2 rounded-full hover:bg-white/40 transition cursor-pointer w-full text-left">
                        <i class="fa-solid fa-user-plus text-red-500"></i> Agregar Alumno
                    </button>
                </li>

                <li>
                    <form method="POST" action="<?php echo BASE_URL; ?>logout.php" style="margin: 0;">
                        <button type="submit" class="flex items-center justify-center gap-2 px-4 py-2 w-full bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-full shadow hover:opacity-90 transition">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>

            <!-- Usuario -->
            <div class="mt-auto pt-6 text-center text-sm text-gray-700 border-t border-white/30">
                <p>Usuario:
                    <span class="font-semibold text-red-600">
                        <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </span>
                </p>
            </div>
        </nav>

        <!-- CONTENIDO -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            
            <!-- SECCIÓN INICIO -->
            <div id="inicio" class="bg-white/40 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-red-600 drop-shadow">
                        Bienvenido Encargado <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </h1>
                </div>

                <!-- Cámara QR -->
                <div class="bg-white/60 rounded-2xl p-6 mb-6 shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">
                        <i class="fas fa-qrcode text-red-500 mr-2"></i>Escanear QR
                    </h3>
                    <div class="flex justify-center mb-4">
                        <video id="camera" autoplay playsinline muted class="rounded-xl border-4 border-red-300 shadow-lg w-full max-w-2xl" style="min-height: 500px; height: 600px;"></video>
                        <canvas id="canvas" hidden></canvas>
                    </div>
                    
                    <!-- Input para lector 2D -->
                    <div class="max-w-md mx-auto">
                        <label for="scannerInput" class="block text-sm font-medium text-gray-700 mb-2">
                            O escanea con lector externo:
                        </label>
                        <input type="text" id="scannerInput" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" placeholder="Enfoca aquí y escanea con el lector..." autocomplete="off">
                    </div>
                </div>

                <!-- Botón Marcar Faltas -->
                <div class="text-center mb-6">
                    <button type="button" id="btnMarcarFaltas" class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-8 py-3 rounded-full shadow-lg hover:opacity-90 transition text-lg font-semibold">
                        <i class="fas fa-user-times mr-2"></i>Marcar Faltas del Día
                    </button>
                    <div id="mensajeFaltas" class="mt-4 p-4 rounded-xl" style="display:none;"></div>
                </div>

                <!-- Historial de ingresos -->
                <div class="bg-white/60 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">
                        <i class="fas fa-history text-red-500 mr-2"></i>Historial Reciente
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="tablaHistorial">
                            <thead>
                                <tr class="bg-gradient-to-r from-red-500 to-orange-500 text-white">
                                    <th class="px-4 py-3 rounded-tl-xl">Nombre</th>
                                    <th class="px-4 py-3">Apellidos</th>
                                    <th class="px-4 py-3">Fecha Entrada</th>
                                    <th class="px-4 py-3 rounded-tr-xl">Método</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/80">
                                <!-- Datos dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN REPORTES -->
            <div id="reportes" style="display:none;" class="bg-white/40 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-4xl font-bold text-center text-red-600 drop-shadow mb-8">
                    <i class="fas fa-chart-bar mr-2"></i>Sección de Reportes
                </h2>

                <!-- Filtros -->
                <div class="bg-white/60 rounded-2xl p-6 mb-6 shadow-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grado</label>
                            <select id="filtroGrado" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition">
                                <option value="">Todos los grados</option>
                                <option value="1">1°</option>
                                <option value="2">2°</option>
                                <option value="3">3°</option>
                                <option value="4">4°</option>
                                <option value="5">5°</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sección</label>
                            <select id="filtroSeccion" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition">
                                <option value="">Todas las secciones</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                            <input type="date" id="fechaInicio" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                            <input type="date" id="fechaFin" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3 justify-center mt-6">
                        <button type="button" id="btnGenerarReporte" class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-6 py-2 rounded-full shadow-lg hover:opacity-90 transition">
                            <i class="fas fa-file-alt mr-2"></i>Generar Reporte
                        </button>
                        <button type="button" id="btnExportarExcel" class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-6 py-2 rounded-full shadow-lg hover:opacity-90 transition">
                            <i class="fas fa-file-excel mr-2"></i>Exportar a Excel
                        </button>
                    </div>
                </div>

                <!-- Tabla de reportes -->
                <div class="bg-white/60 rounded-2xl p-6 shadow-lg overflow-x-auto">
                    <table class="w-full text-sm" id="tablaReportes">
                        <thead>
                            <tr class="bg-gradient-to-r from-red-500 to-orange-500 text-white">
                                <th class="px-4 py-3 rounded-tl-xl">Nombre</th>
                                <th class="px-4 py-3">Documento</th>
                                <th class="px-4 py-3">Grado</th>
                                <th class="px-4 py-3">Sección</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3 rounded-tr-xl">Asistencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/80">
                            <!-- Datos dinámicos -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECCIÓN AGREGAR ALUMNO -->
            <div id="agregarAlumno" style="display:none;" class="bg-white/40 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-4xl font-bold text-center text-red-600 drop-shadow mb-8">
                    <i class="fas fa-user-plus mr-2"></i>Ingresar Alumno
                </h2>
                
                <div class="bg-white/60 rounded-2xl p-6 shadow-lg max-w-4xl mx-auto">
                    <form id="formAgregarAlumno">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                <input type="text" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="nombre" name="nombre" required>
                            </div>
                            
                            <div>
                                <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-2">Apellidos</label>
                                <input type="text" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="apellidos" name="apellidos" required>
                            </div>
                            
                            <div>
                                <label for="documento" class="block text-sm font-medium text-gray-700 mb-2">Documento</label>
                                <input type="text" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="documento" name="documento" required>
                            </div>
                            
                            <div>
                                <label for="grado" class="block text-sm font-medium text-gray-700 mb-2">Grado</label>
                                <select class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="grado" name="grado" required>
                                    <option value="">Selecciona...</option>
                                    <option value="1">1°</option>
                                    <option value="2">2°</option>
                                    <option value="3">3°</option>
                                    <option value="4">4°</option>
                                    <option value="5">5°</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="seccion" class="block text-sm font-medium text-gray-700 mb-2">Sección</label>
                                <select class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="seccion" name="seccion" required>
                                    <option value="">Selecciona...</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                                <input type="date" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="fecha" name="fecha" required>
                            </div>
                            
                            <div>
                                <label for="hora" class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                                <input type="time" class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="hora" name="hora" required>
                            </div>
                            
                            <div>
                                <label for="tipoRegistro" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Registro</label>
                                <select class="w-full px-4 py-2 rounded-full border-2 border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 transition" id="tipoRegistro" name="tipo" required>
                                    <option value="entrada">Entrada</option>
                                    <option value="salida">Salida</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <button type="submit" class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-8 py-3 rounded-full shadow-lg hover:opacity-90 transition text-lg font-semibold">
                                <i class="fas fa-check mr-2"></i>Agregar Alumno
                            </button>
                        </div>
                    </form>
                    <div id="mensajeAgregar" class="mt-4 p-4 rounded-xl text-center" style="display:none;"></div>
                </div>
            </div>

            <!-- Mensaje y Carnet (modales) -->
            <div id="mensaje" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-full shadow-lg z-50" style="display:none;" role="status" aria-live="polite">
                <i class="fas fa-check-circle mr-2"></i>Alumno Encontrado
            </div>
            
            <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40" style="display:none;"></div>
            
            <div id="carnet" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-3xl shadow-2xl p-6 z-50 max-w-md w-full" style="display:none;">
                <img id="fotoAlumno" src="" alt="Foto del alumno" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-red-300 shadow-lg">
                <div class="text-center">
                    <h4 id="nombreAlumno" class="text-2xl font-bold text-gray-800 mb-4"></h4>
                    <div class="space-y-2 text-left">
                        <p class="text-gray-700"><strong>Documento:</strong> <span id="documentoAlumno"></span></p>
                        <p class="text-gray-700"><strong>Grado:</strong> <span id="gradoAlumno"></span></p>
                        <p class="text-gray-700"><strong>Sección:</strong> <span id="seccionAlumno"></span></p>
                    </div>
                </div>
            </div>
        </main>

    </div>

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
        
        // Toggle menú móvil
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Navegación entre secciones
        document.getElementById('btn-agregar').addEventListener('click', function() {
            document.getElementById('inicio').style.display = 'block';
            document.getElementById('reportes').style.display = 'none';
            document.getElementById('agregarAlumno').style.display = 'none';
            document.getElementById('sidebar').classList.remove('active');
        });

        document.getElementById('btn-reportes').addEventListener('click', function() {
            document.getElementById('inicio').style.display = 'none';
            document.getElementById('reportes').style.display = 'block';
            document.getElementById('agregarAlumno').style.display = 'none';
            document.getElementById('sidebar').classList.remove('active');
        });

        document.getElementById('btn-AgregarAlumno').addEventListener('click', function() {
            document.getElementById('inicio').style.display = 'none';
            document.getElementById('reportes').style.display = 'none';
            document.getElementById('agregarAlumno').style.display = 'block';
            document.getElementById('sidebar').classList.remove('active');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js?v=2"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/encargado/dashboard.js"></script>
</body>

</html>