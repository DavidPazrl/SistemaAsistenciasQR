document.addEventListener("DOMContentLoaded", function(){
    const form = document.getElementById("form-alumno");
    const mensaje = document.getElementById("mensaje");

    form.addEventListener("submit", function(e){
        e.preventDefault();
        const formData = new FormData(this);

        fetch("/controllers/AlumnoController.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === "success") {
                mensaje.style.color = "green";
                mensaje.innerText = "Alumno agregado correctamente.";

                form.reset();

                setTimeout(() => location.reload(), 1000);
            } else if(data.trim() === "duplicate") {
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
        });
    });
});

