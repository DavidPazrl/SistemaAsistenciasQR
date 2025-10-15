document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const inicio = document.getElementById("inicio");
    const reportes = document.getElementById("reportes");
    const video = document.getElementById("camera");
    const canvas = document.getElementById("canvas");
    const mensaje = document.getElementById("mensaje");
    const ctx = canvas.getContext("2d");
    let scanning = false;
    let stream = null;

    //Control del menu
    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        content.classList.toggle("active");
        toggle.style.left = sidebar.classList.contains("active") ? "250px" : "15px";
    });

    //Camara
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
                handleQRDetected(code.data);
            }
        }
        if (stream) requestAnimationFrame(tick);
    }

    async function handleQRDetected(qrValue) {
        mensaje.style.display = "block";
        mensaje.textContent = "Buscando alumno...";
        try {
            const response = await fetch("../../controllers/VerificarQRController.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ qr: qrValue })
            });
            const data = await response.json();
            if (data.status === "success") mostrarCarnet(data.data);
            else {
                mensaje.textContent = "Alumno no encontrado";
                setTimeout(() => { mensaje.style.display = "none"; scanning = false; }, 2000);
            }
        } catch {
            mensaje.textContent = "Error al buscar alumno";
            setTimeout(() => { mensaje.style.display = "none"; scanning = false; }, 2000);
        }
    }

    function mostrarCarnet(alumno) {
        mensaje.style.display = "none";
        document.getElementById("fotoAlumno").src = "../../assets/img/fotodefecto.png";
        document.getElementById("nombreAlumno").textContent = `${alumno.Nombre} ${alumno.Apellidos}`;
        document.getElementById("dniAlumno").textContent = alumno.DNI;
        document.getElementById("gradoAlumno").textContent = alumno.Grado;
        document.getElementById("seccionAlumno").textContent = alumno.Seccion;

        document.getElementById("overlay").style.display = "block";
        document.getElementById("carnet").style.display = "flex";

        setTimeout(() => {
            document.getElementById("overlay").style.display = "none";
            document.getElementById("carnet").style.display = "none";
            scanning = false;
        }, 3000);
    }

    document.getElementById('btn-reportes').addEventListener('click', () => {
        inicio.style.display = "none";
        reportes.style.display = "block";
        detenerCamara();
    });

    document.getElementById('btn-agregar').addEventListener('click', () => {
        reportes.style.display = "none";
        inicio.style.display = "block";
        iniciarCamara();
    });

    document.getElementById("btnGenerarReporte").addEventListener("click", async () => {
        const grado = document.getElementById("filtroGrado").value;
        const seccion = document.getElementById("filtroSeccion").value;
        const periodo = document.getElementById("filtroPeriodo").value;

        const response = await fetch("../../controllers/ReporteController.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `grado=${grado}&seccion=${seccion}&periodo=${periodo}`
        });
        const data = await response.json();

        const tbody = document.querySelector("#tablaReportes tbody");
        tbody.innerHTML = "";

        data.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${row.Nombre} ${row.Apellidos}</td>
                <td>${row.DNI}</td>
                <td>${row.Grado}</td>
                <td>${row.Seccion}</td>
                <td>${row.fechaEntrada || ""}</td>
                <td>${row.tipoAsistencia || "Sin registro"}</td>
            `;
            tbody.appendChild(tr);
        });
    });

    document.getElementById('btnExportarExcel').addEventListener('click', () => {
        const grado = document.getElementById('filtroGrado').value;
        const seccion = document.getElementById('filtroSeccion').value;
        const periodo = document.getElementById('filtroPeriodo').value;

    
        const url = `../../controllers/ReporteController.php?accion=exportar&grado=${grado}&seccion=${seccion}&periodo=${periodo}`;
        window.location.href = url;
    });


    iniciarCamara();
});
