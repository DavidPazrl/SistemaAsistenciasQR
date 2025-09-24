document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form-alumno");
    const mensaje = document.getElementById("mensaje");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        // Validaciones adicionales antes de enviar
        const nombre = form.Nombre.value.trim();
        const apellidos = form.Apellidos.value.trim();
        const dni = form.DNI.value.trim();
        const grado = form.Grado.value;
        const seccion = form.Seccion.value.toUpperCase().trim();

        if (!/^[A-Za-z\s]+$/.test(nombre)) {
            mensaje.style.color = "red";
            mensaje.innerText = "El nombre solo puede contener letras.";
            return;
        }

        if (!/^[A-Za-z\s]+$/.test(apellidos)) {
            mensaje.style.color = "red";
            mensaje.innerText = "Los apellidos solo pueden contener letras.";
            return;
        }

        if (!/^\d{8}$/.test(dni)) {
            mensaje.style.color = "red";
            mensaje.innerText = "El DNI debe contener 8 números.";
            return;
        }

        if (!["1","2","3","4","5"].includes(grado)) {
            mensaje.style.color = "red";
            mensaje.innerText = "El grado debe ser entre 1 y 5.";
            return;
        }

        if (!/^[AB]$/i.test(seccion)) {
            mensaje.style.color = "red";
            mensaje.innerText = "La sección solo puede ser A o B.";
            return;
        }

        // Enviar el formulario si pasa las validaciones
        const formData = new FormData(form);

        fetch("/controllers/AlumnoController.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                mensaje.style.color = "green";
                mensaje.innerText = "Alumno agregado correctamente.";
                form.reset();
                setTimeout(() => location.reload(), 1000);
            } else if (data.trim() === "duplicate") {
                mensaje.style.color = "red";
                mensaje.innerText = "El DNI ingresado ya existe.";
            } else {
                mensaje.style.color = "red";
                mensaje.innerText = "Error al agregar alumno.";
            }
        })
        .catch(err => {
            mensaje.style.color = "red";
            mensaje.innerText = "Error en la petición.";
            console.error(err);
        });
    });
});
