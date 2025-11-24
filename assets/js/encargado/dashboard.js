document.addEventListener("DOMContentLoaded", function () {
    console.log("=== INICIANDO DASHBOARD ===");
    
    // Verificar elementos
    const toggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const inicio = document.getElementById("inicio");
    const reportes = document.getElementById("reportes");
    const agregarAlumno = document.getElementById("agregarAlumno");
    const video = document.getElementById("camera");
    const canvas = document.getElementById("canvas");
    const mensaje = document.getElementById("mensaje");
    const btnAgregarAlumno = document.getElementById("btn-AgregarAlumno");
    const formAgregarAlumno = document.getElementById("formAgregarAlumno");
    const mensajeAgregar = document.getElementById("mensajeAgregar");

    console.log("Elementos encontrados:");
    console.log("- toggle:", !!toggle);
    console.log("- sidebar:", !!sidebar);
    console.log("- content:", !!content);
    console.log("- inicio:", !!inicio);
    console.log("- reportes:", !!reportes);
    console.log("- agregarAlumno:", !!agregarAlumno);
    console.log("- video:", !!video);
    console.log("- canvas:", !!canvas);
    console.log("- mensaje:", !!mensaje);
    console.log("- btnAgregarAlumno:", !!btnAgregarAlumno);
    console.log("- formAgregarAlumno:", !!formAgregarAlumno);
    console.log("- mensajeAgregar:", !!mensajeAgregar);

    const ctx = canvas ? canvas.getContext("2d") : null;
    let scanning = false;
    let stream = null;

    function agregarAlHistorial(alumno, metodo) {
        console.log("agregarAlHistorial:", alumno, metodo);
        const tbody = document.querySelector("#tablaHistorial tbody");
        if (!tbody) {
            console.error("No se encontró #tablaHistorial tbody");
            return;
        }
        
        const tr = document.createElement("tr");
        const ahora = new Date();
        const fechaHora = `${ahora.toLocaleDateString('es-PE')} ${ahora.toLocaleTimeString('es-PE')}`;

        tr.innerHTML = `
        <td class="px-4 py-3 border-b border-gray-200">${alumno.Nombre}</td>
        <td class="px-4 py-3 border-b border-gray-200">${alumno.Apellidos}</td>
        <td class="px-4 py-3 border-b border-gray-200">${fechaHora}</td>
        <td class="px-4 py-3 border-b border-gray-200">${metodo}</td>
    `;

        tbody.insertBefore(tr, tbody.firstChild);
        console.log("Fila agregada al historial");
    }

    async function cargarHistorial() {
        console.log("Cargando historial...");
        try {
            const response = await fetch(BASE_URL + "controllers/AlumnoController.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=getHistorial"
            });
            const result = await response.json();
            console.log("Historial recibido:", result);

            if (result.status === "success") {
                const tbody = document.querySelector("#tablaHistorial tbody");
                tbody.innerHTML = "";

                result.data.forEach(registro => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                    <td class="px-4 py-3 border-b border-gray-200">${registro.nombre}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${registro.apellidos}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${registro.fecha}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${registro.metodo}</td>
                `;
                    tbody.appendChild(tr);
                });
                console.log("Historial cargado exitosamente");
            }
        } catch (error) {
            console.error("Error cargando historial:", error);
        }
    }

    // Control del menu
    if (toggle && sidebar && content) {
        console.log("Agregando listener al toggle");
        toggle.addEventListener("click", () => {
            console.log("Toggle clicked");
            sidebar.classList.toggle("active");
            content.classList.toggle("active");
            const isActive = sidebar.classList.contains("active");
            console.log("Sidebar active:", isActive);
            toggle.style.left = isActive ? "250px" : "15px";
        });
    } else {
        console.error("No se pudo configurar el toggle del menú");
    }

    // Camara
    async function iniciarCamara() {
        console.log("Iniciando cámara...");
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                console.log("Cámara iniciada correctamente");
                requestAnimationFrame(tick);
            } catch (error) {
                console.error("Error al acceder a la cámara:", error);
                alert("No se pudo acceder a la cámara. Verifica los permisos del navegador.");
            }
        } else {
            console.error("getUserMedia no está disponible");
        }
    }

    function detenerCamara() {
        console.log("Deteniendo cámara...");
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
            console.log("Cámara detenida");
        }
    }

    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });

            if (code && !scanning) {
                console.log("QR detectado:", code.data);
                scanning = true;
                handleQRDetected(code.data, 'Cámara');
            }
        }
        if (stream) requestAnimationFrame(tick);
    }

    async function handleQRDetected(qrValue, metodo = 'Cámara') {
        console.log("Procesando QR:", qrValue, "Método:", metodo);
        mensaje.style.display = "block";
        mensaje.textContent = "Buscando alumno...";
        
        try {
            const response = await fetch(BASE_URL + "controllers/VerificarQRController.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ qr: qrValue, metodo: metodo })
            });
            const data = await response.json();
            console.log("Respuesta del servidor:", data);
            
            if (data.status === "success") {
                mostrarCarnet(data.data, metodo);
            } else {
                mensaje.textContent = "Alumno no encontrado";
                setTimeout(() => { mensaje.style.display = "none"; scanning = false; }, 2000);
            }
        } catch (error) {
            console.error("Error al buscar alumno:", error);
            mensaje.textContent = "Error al buscar alumno";
            setTimeout(() => { mensaje.style.display = "none"; scanning = false; }, 2000);
        }
    }

    function mostrarCarnet(alumno, metodo = 'Cámara') {
        console.log("Mostrando carnet:", alumno);
        mensaje.style.display = "none";
        document.getElementById("fotoAlumno").src = BASE_URL + "assets/img/fotodefecto.png";
        document.getElementById("nombreAlumno").textContent = `${alumno.Nombre} ${alumno.Apellidos}`;
        document.getElementById("documentoAlumno").textContent = alumno.documento;
        document.getElementById("gradoAlumno").textContent = alumno.Grado;
        document.getElementById("seccionAlumno").textContent = alumno.Seccion;

        document.getElementById("overlay").style.display = "block";
        document.getElementById("carnet").style.display = "flex";
        agregarAlHistorial(alumno, metodo);

        setTimeout(() => {
            document.getElementById("overlay").style.display = "none";
            document.getElementById("carnet").style.display = "none";
            scanning = false;
            console.log("Carnet ocultado");
        }, 3000);
    }

    // Detectar escaneo desde lector 2D
    const scannerInput = document.getElementById('scannerInput');

    if (scannerInput) {
        console.log("Configurando scanner input");
        scannerInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const qrValue = scannerInput.value.trim();
                console.log("Scanner input Enter:", qrValue);

                if (qrValue) {
                    handleQRDetected(qrValue, 'Escáner');
                    scannerInput.value = '';
                }
            }
        });
    } else {
        console.error("Scanner input no encontrado");
    }

    // Mostrar secciones
    const btnReportes = document.getElementById('btn-reportes');
    const btnAgregar = document.getElementById('btn-agregar');

    if (btnReportes) {
        console.log("Configurando btn-reportes");
        btnReportes.addEventListener('click', () => {
            console.log("Click en btn-reportes");
            inicio.style.display = "none";
            agregarAlumno.style.display = "none";
            reportes.style.display = "block";
            detenerCamara();
            if (sidebar) sidebar.classList.remove("active");
        });
    } else {
        console.error("btn-reportes no encontrado");
    }

    if (btnAgregar) {
        console.log("Configurando btn-agregar");
        btnAgregar.addEventListener('click', () => {
            console.log("Click en btn-agregar");
            reportes.style.display = "none";
            agregarAlumno.style.display = "none";
            inicio.style.display = "block";
            iniciarCamara();
            setTimeout(() => {
                const scannerInput = document.getElementById('scannerInput');
                if (scannerInput) scannerInput.focus();
            }, 200);
            if (sidebar) sidebar.classList.remove("active");
        });
    } else {
        console.error("btn-agregar no encontrado");
    }

    if (btnAgregarAlumno) {
        console.log("Configurando btn-AgregarAlumno");
        btnAgregarAlumno.addEventListener('click', () => {
            console.log("Click en btn-AgregarAlumno");
            inicio.style.display = "none";
            reportes.style.display = "none";
            agregarAlumno.style.display = "block";
            detenerCamara();
            if (sidebar) sidebar.classList.remove("active");
        });
    } else {
        console.error("btn-AgregarAlumno no encontrado");
    }

    // Enviar formulario de agregar alumno 
    if (formAgregarAlumno) {
        console.log("Configurando formulario agregar alumno");
        formAgregarAlumno.addEventListener("submit", async (e) => {
            e.preventDefault();
            console.log("Submit formulario agregar alumno");

            const documento = formAgregarAlumno.documento.value.trim();
            const fecha = formAgregarAlumno.fecha.value;
            const hora = formAgregarAlumno.hora.value;
            const tipo = formAgregarAlumno.tipo.value;

            console.log("Datos formulario:", { documento, fecha, hora, tipo });

            if (!documento || !fecha || !hora || !tipo) {
                console.error("Campos incompletos");
                mensajeAgregar.style.display = "block";
                mensajeAgregar.className = "alert alert-danger text-center";
                mensajeAgregar.textContent = "Por favor completa todos los campos obligatorios";
                return;
            }

            const data = { documento, fecha, hora, tipo };

            try {
                const response = await fetch(BASE_URL + "controllers/ADDAlumnoController.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                console.log("Respuesta agregar alumno:", result);

                mensajeAgregar.style.display = "block";
                if (result.status === "success") {
                    mensajeAgregar.className = "alert alert-success text-center";
                    mensajeAgregar.textContent = result.message;

                    const alumnoData = {
                        Nombre: document.getElementById('nombre').value,
                        Apellidos: document.getElementById('apellidos').value
                    };
                    agregarAlHistorial(alumnoData, 'Manual');

                    formAgregarAlumno.reset();
                } else {
                    mensajeAgregar.className = "alert alert-danger text-center";
                    mensajeAgregar.textContent = result.message;
                }

                setTimeout(() => { mensajeAgregar.style.display = "none"; }, 3000);
            } catch (error) {
                console.error("Error en submit:", error);
                mensajeAgregar.style.display = "block";
                mensajeAgregar.className = "alert alert-danger text-center";
                mensajeAgregar.textContent = "Error al agregar alumno";
                setTimeout(() => { mensajeAgregar.style.display = "none"; }, 3000);
            }
        });
    } else {
        console.error("formAgregarAlumno no encontrado");
    }

    // Generar reporte
    const btnGenerarReporte = document.getElementById("btnGenerarReporte");
    if (btnGenerarReporte) {
        console.log("Configurando btnGenerarReporte");
        btnGenerarReporte.addEventListener("click", async () => {
            console.log("Click en generar reporte");
            const grado = document.getElementById("filtroGrado").value;
            const seccion = document.getElementById("filtroSeccion").value;
            const fechaInicio = document.getElementById("fechaInicio").value;
            const fechaFin = document.getElementById("fechaFin").value;

            console.log("Filtros:", { grado, seccion, fechaInicio, fechaFin });

            const response = await fetch(BASE_URL + "controllers/ReporteController.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `grado=${grado}&seccion=${seccion}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`
            });
            const data = await response.json();
            console.log("Datos del reporte:", data);

            const tbody = document.querySelector("#tablaReportes tbody");
            tbody.innerHTML = "";

            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="px-4 py-3 border-b border-gray-200">${row.Nombre} ${row.Apellidos}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${row.documento}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${row.Grado}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${row.Seccion}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${row.fechaEntrada || ""}</td>
                    <td class="px-4 py-3 border-b border-gray-200">${row.tipoAsistencia || "Sin registro"}</td>
                `;
                tbody.appendChild(tr);
            });
            console.log("Reporte generado");
        });
    } else {
        console.error("btnGenerarReporte no encontrado");
    }

    // Exportar Excel
    const btnExportarExcel = document.getElementById('btnExportarExcel');
    if (btnExportarExcel) {
        console.log("Configurando btnExportarExcel");
        btnExportarExcel.addEventListener('click', () => {
            console.log("Click en exportar Excel");
            const grado = document.getElementById('filtroGrado').value;
            const seccion = document.getElementById('filtroSeccion').value;
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;

            const url = `${BASE_URL}controllers/ReporteController.php?accion=exportar&grado=${grado}&seccion=${seccion}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
            console.log("URL de exportación:", url);
            window.location.href = url;
        });
    } else {
        console.error("btnExportarExcel no encontrado");
    }

    // Autocompletar datos al ingresar Documento
    const inputDocumento = document.getElementById('documento');
    if (inputDocumento) {
        console.log("Configurando autocompletar documento");
        inputDocumento.addEventListener('blur', async () => {
            const documento = inputDocumento.value.trim();
            console.log("Blur en documento:", documento);
            if (documento === "") return;

            try {
                const response = await fetch(BASE_URL + "controllers/ADDAlumnoController.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "buscarDocumento", documento })
                });
                const data = await response.json();
                console.log("Datos del alumno:", data);
                
                if (data.status === "success") {
                    document.getElementById('nombre').value = data.data.Nombre;
                    document.getElementById('apellidos').value = data.data.Apellidos;
                    document.getElementById('grado').value = data.data.Grado;
                    document.getElementById('seccion').value = data.data.Seccion;
                    console.log("Datos autocompletados");
                } else {
                    document.getElementById('nombre').value = '';
                    document.getElementById('apellidos').value = '';
                    document.getElementById('grado').value = '';
                    document.getElementById('seccion').value = '';
                    console.log("Alumno no encontrado, limpiando campos");
                }
            } catch (e) {
                console.error("Error buscando alumno:", e);
            }
        });
    } else {
        console.error("Input documento no encontrado");
    }

    // Marcar faltas del día
    const btnMarcarFaltas = document.getElementById('btnMarcarFaltas');
    if (btnMarcarFaltas) {
        console.log("Configurando btnMarcarFaltas");
        btnMarcarFaltas.addEventListener('click', async () => {
            console.log("Click en marcar faltas");
            if (!confirm('¿Estás seguro de marcar las faltas del día?\n\nEsto registrará como "Falto" a todos los alumnos que no marcaron asistencia hoy.\n\nEsta acción NO se puede deshacer.')) {
                return;
            }

            const mensajeFaltas = document.getElementById('mensajeFaltas');

            btnMarcarFaltas.disabled = true;
            btnMarcarFaltas.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            try {
                const response = await fetch(BASE_URL + 'controllers/MarcarFaltasController.php', {
                    method: 'POST'
                });
                const result = await response.json();
                console.log("Resultado marcar faltas:", result);

                mensajeFaltas.style.display = 'block';

                if (result.status === 'success') {
                    mensajeFaltas.className = 'alert alert-success mt-3';
                    mensajeFaltas.textContent = `${result.message}`;
                    cargarHistorial();
                } else {
                    mensajeFaltas.className = 'alert alert-danger mt-3';
                    mensajeFaltas.textContent = `${result.message}`;
                }

                setTimeout(() => {
                    mensajeFaltas.style.display = 'none';
                }, 5000);

            } catch (error) {
                console.error('Error:', error);
                mensajeFaltas.style.display = 'block';
                mensajeFaltas.className = 'alert alert-danger mt-3';
                mensajeFaltas.textContent = 'Error al marcar faltas';
            } finally {
                btnMarcarFaltas.disabled = false;
                btnMarcarFaltas.innerHTML = '<i class="fas fa-user-times mr-2"></i>Marcar Faltas del Día';
            }
        });
    } else {
        console.error("btnMarcarFaltas no encontrado");
    }

    console.log("Cargando historial inicial...");
    cargarHistorial();
    
    console.log("Iniciando cámara inicial...");
    iniciarCamara();
    
    console.log("=== DASHBOARD INICIADO ===");
});