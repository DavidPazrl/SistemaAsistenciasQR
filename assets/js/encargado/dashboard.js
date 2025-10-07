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
            .catch((err) => {
                console.error("No se pudo acceder a la cámara:", err);
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
        console.log("QR detectado:", qrValue);
        mensaje.style.display = "block";
        mensaje.textContent = "Buscando alumno...";

        try {
            const response = await fetch("../../controllers/VerificarQRController.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ qr: qrValue })
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
        } catch (error) {
            console.error("Error:", error);
            mensaje.textContent = "Error al buscar alumno";
            setTimeout(() => {
                mensaje.style.display = "none";
                scanning = false;
            }, 2000);
        }
    }

    function mostrarCarnet(alumno) {
        mensaje.style.display = "none";
        const overlay = document.createElement("div");
        overlay.id = "overlay";

        // Crear carnet emergente
        const carnetDiv = document.createElement("div");
        carnetDiv.id = "carnet";
        carnetDiv.innerHTML = `
            <img src="../../assets/img/user.png" alt="Alumno">
            <h4>${alumno.Nombre} ${alumno.Apellidos}</h4>
            <p><strong>DNI:</strong> ${alumno.DNI}</p>
            <p><strong>Grado:</strong> ${alumno.Grado}</p>
            <p><strong>Sección:</strong> ${alumno.Seccion}</p>
        `;

        document.body.appendChild(overlay);
        document.body.appendChild(carnetDiv);

        overlay.style.display = "block";
        carnetDiv.style.display = "block";

        setTimeout(() => {
            carnetDiv.remove();
            overlay.remove();
            scanning = false;
        }, 3000);
    }
});
