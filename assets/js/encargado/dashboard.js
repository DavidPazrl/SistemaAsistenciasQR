document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const inicio = document.getElementById("inicio");
    const reportes = document.getElementById("reportes");
    const agregarAlumno = document.getElementById("agregarAlumno");
    const video = document.getElementById("camera");
    const canvas = document.getElementById("canvas");
    const mensaje = document.getElementById("mensaje");
    const ctx = canvas.getContext("2d");
    const btnAgregarAlumno = document.getElementById("btn-AgregarAlumno");
    const formAgregarAlumno = document.getElementById("formAgregarAlumno");
    const mensajeAgregar = document.getElementById("mensajeAgregar");

    let scanning = false;
    let stream = null;

    function agregarAlHistorial(alumno, metodo) {
        const tbody = document.querySelector("#tablaHistorial tbody");
        const tr = document.createElement("tr");

        const ahora = new Date();
        const fechaHora = `${ahora.toLocaleDateString('es-PE')} ${ahora.toLocaleTimeString('es-PE')}`;

        tr.innerHTML = `
        <td>${alumno.Nombre}</td>
        <td>${alumno.Apellidos}</td>
        <td>${fechaHora}</td>
        <td>${metodo}</td>
    `;

        tbody.insertBefore(tr, tbody.firstChild);
    }

    async function cargarHistorial() {
        try {
            const response = await fetch(BASE_URL + "controllers/AlumnoController.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=getHistorial"
            });
            const result = await response.json();

            if (result.status === "success") {
                const tbody = document.querySelector("#tablaHistorial tbody");
                tbody.innerHTML = "";

                result.data.forEach(registro => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                    <td>${registro.nombre}</td>
                    <td>${registro.apellidos}</td>
                    <td>${registro.fecha}</td>
                    <td>${registro.metodo}</td>
                `;
                    tbody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error("Error cargando historial:", error);
        }
    }

    // Control del menu
    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        content.classList.toggle("active");
        toggle.style.left = sidebar.classList.contains("active") ? "250px" : "15px";
    });

    // Camara
    async function iniciarCamara() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(tick);
            } catch {
                alert("No se pudo acceder a la cámara. Verifica los permisos del navegador.");
            }
        }
    }

    function detenerCamara() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
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
                scanning = true;
                handleQRDetected(code.data, 'Cámara');
            }
        }
        if (stream) requestAnimationFrame(tick);
    }

    async function handleQRDetected(qrValue, metodo = 'Cámara') {
        mensaje.style.display = "block";
        mensaje.textContent = "Buscando alumno...";
        try {
            const response = await fetch(BASE_URL + "controllers/VerificarQRController.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ qr: qrValue, metodo: metodo })
            });
            const data = await response.json();
            if (data.status === "success") mostrarCarnet(data.data, metodo);
            else {
                mensaje.textContent = "Alumno no encontrado";
                setTimeout(() => { mensaje.style.display = "none"; scanning = false; }, 2000);
            }
        } catch {
            mensaje.textContent = "Error al buscar alumno";
            setTimeout(() => { mensaje.style.display = "none"; scanning = false; }, 2000);
        }
    }

    function mostrarCarnet(alumno, metodo = 'Cámara') {
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
        }, 3000);
    }

    // Detectar escaneo desde lector 2D
    const scannerInput = document.getElementById('scannerInput');

    if (scannerInput) {
        scannerInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const qrValue = scannerInput.value.trim();

                if (qrValue) {
                    handleQRDetected(qrValue, 'Escáner');
                    scannerInput.value = '';
                }
            }
        });
    }

    // Mostrar secciones
    document.getElementById('btn-reportes').addEventListener('click', () => {
        inicio.style.display = "none";
        agregarAlumno.style.display = "none";
        reportes.style.display = "block";
        detenerCamara();
    });

    document.getElementById('btn-agregar').addEventListener('click', () => {
        reportes.style.display = "none";
        agregarAlumno.style.display = "none";
        inicio.style.display = "block";
        iniciarCamara();
        setTimeout(() => {
            const scannerInput = document.getElementById('scannerInput');
            if (scannerInput) scannerInput.focus();
        }, 200);
    });

    btnAgregarAlumno.addEventListener('click', () => {
        inicio.style.display = "none";
        reportes.style.display = "none";
        agregarAlumno.style.display = "block";
        detenerCamara();
    });

    // Enviar formulario de agregar alumno 
    formAgregarAlumno.addEventListener("submit", async (e) => {
        e.preventDefault();

        const documento = formAgregarAlumno.documento.value.trim();
        const fecha = formAgregarAlumno.fecha.value;
        const hora = formAgregarAlumno.hora.value;
        const tipo = formAgregarAlumno.tipoRegistro.value;

        if (!documento || !fecha || !hora || !tipo) {
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
            mensajeAgregar.style.display = "block";
            mensajeAgregar.className = "alert alert-danger text-center";
            mensajeAgregar.textContent = "Error al agregar alumno";
            setTimeout(() => { mensajeAgregar.style.display = "none"; }, 3000);
            console.error(error);
        }
    });

    // Generar reporte
    document.getElementById("btnGenerarReporte").addEventListener("click", async () => {
        const grado = document.getElementById("filtroGrado").value;
        const seccion = document.getElementById("filtroSeccion").value;
        const fechaInicio = document.getElementById("fechaInicio").value;
        const fechaFin = document.getElementById("fechaFin").value;

        const response = await fetch(BASE_URL + "controllers/ReporteController.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `grado=${grado}&seccion=${seccion}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`
        });
        const data = await response.json();

        const tbody = document.querySelector("#tablaReportes tbody");
        tbody.innerHTML = "";

        data.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${row.Nombre} ${row.Apellidos}</td>
                <td>${row.documento}</td>
                <td>${row.Grado}</td>
                <td>${row.Seccion}</td>
                <td>${row.fechaEntrada || ""}</td>
                <td>${row.tipoAsistencia || "Sin registro"}</td>
            `;
            tbody.appendChild(tr);
        });
    });

    // Exportar Excel
    document.getElementById('btnExportarExcel').addEventListener('click', () => {
        const grado = document.getElementById('filtroGrado').value;
        const seccion = document.getElementById('filtroSeccion').value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;

        const url = `${BASE_URL}controllers/ReporteController.php?accion=exportar&grado=${grado}&seccion=${seccion}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
        window.location.href = url;
    });

    // Autocompletar datos al ingresar Documento
    document.getElementById('documento').addEventListener('blur', async () => {
        const documento = document.getElementById('documento').value.trim();
        if (documento === "") return;

        try {
            const response = await fetch(BASE_URL + "controllers/ADDAlumnoController.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "buscarDocumento", documento })
            });
            const data = await response.json();
            if (data.status === "success") {
                document.getElementById('nombre').value = data.data.Nombre;
                document.getElementById('apellidos').value = data.data.Apellidos;
                document.getElementById('grado').value = data.data.Grado;
                document.getElementById('seccion').value = data.data.Seccion;
            } else {
                document.getElementById('nombre').value = '';
                document.getElementById('apellidos').value = '';
                document.getElementById('grado').value = '';
                document.getElementById('seccion').value = '';
            }
        } catch (e) {
            console.error("Error buscando alumno:", e);
        }
    });

    // Marcar faltas del día
    document.getElementById('btnMarcarFaltas')?.addEventListener('click', async () => {
        if (!confirm('¿Estás seguro de marcar las faltas del día?\n\nEsto registrará como "Falto" a todos los alumnos que no marcaron asistencia hoy.\n\nEsta acción NO se puede deshacer.')) {
            return;
        }

        const btnMarcarFaltas = document.getElementById('btnMarcarFaltas');
        const mensajeFaltas = document.getElementById('mensajeFaltas');

        btnMarcarFaltas.disabled = true;
        btnMarcarFaltas.textContent = 'Procesando...';

        try {
            const response = await fetch(BASE_URL + 'controllers/MarcarFaltasController.php', {
                method: 'POST'
            });
            const result = await response.json();

            mensajeFaltas.style.display = 'block';

            if (result.status === 'success') {
                mensajeFaltas.className = 'alert alert-success mt-3';
                mensajeFaltas.textContent = `${result.message}`;

                // Recargar historial para ver las faltas
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
            btnMarcarFaltas.textContent = 'Marcar Faltas del Día';
        }
    });

    cargarHistorial();
    iniciarCamara();
});