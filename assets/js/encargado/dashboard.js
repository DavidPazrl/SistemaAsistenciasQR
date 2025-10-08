document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const video = document.getElementById("camera");
    const canvas = document.getElementById("canvas");
    const mensaje = document.getElementById("mensaje");
    const ctx = canvas.getContext("2d");
    let scanning = false;

    toggle.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        content.classList.toggle("active");
    });

    // Activar camara
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices
            .getUserMedia({ video: { facingMode: "environment" } })
            .then((stream) => {
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(tick);
            })
            .catch(() => {
                alert("No se pudo acceder a la cámara. Verifica los permisos del navegador.");
            });
    }

    // Escaneo continuo
    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });

            if (code && !scanning) {
                scanning = true;
                handleQRDetected(code.data);
            }
        }
        requestAnimationFrame(tick);
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

            if (data.status === "success") {
                mostrarCarnet(data.data);
            } else {
                mensaje.textContent = "Alumno no encontrado";
                setTimeout(() => {
                    mensaje.style.display = "none";
                    scanning = false;
                }, 2000);
            }
        } catch {
            mensaje.textContent = "Error al buscar alumno";
            setTimeout(() => {
                mensaje.style.display = "none";
                scanning = false;
            }, 2000);
        }
    }

    function mostrarCarnet(alumno) {
        mensaje.style.display = "none";
        const foto = document.getElementById("fotoAlumno");
        const nombre = document.getElementById("nombreAlumno");
        const dni = document.getElementById("dniAlumno");
        const grado = document.getElementById("gradoAlumno");
        const seccion = document.getElementById("seccionAlumno");
        const overlay = document.getElementById("overlay");
        const carnet = document.getElementById("carnet");

        foto.src = "../../assets/img/fotodefecto.png";
        
        nombre.textContent = `${alumno.Nombre} ${alumno.Apellidos}`;
        dni.textContent = alumno.DNI;
        grado.textContent = alumno.Grado;
        seccion.textContent = alumno.Seccion;

        overlay.style.display = "block";
        carnet.style.display = "flex";

        setTimeout(() => {
            overlay.style.display = "none";
            carnet.style.display = "none";
            scanning = false;
        }, 3000);
    }


});
