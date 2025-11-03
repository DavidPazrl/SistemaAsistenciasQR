document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-encargado");
    const modalTitulo = document.getElementById("modal-titulo");
    const form = document.getElementById("form-encargado");
    const btnAgregar = document.getElementById("btn-agregar");
    const btnCerrar = document.getElementById("btn-cerrar");
    const mensaje = document.getElementById("mensaje");

    // Abrir modal para agregar encargado
    btnAgregar.addEventListener("click", () => {
        modalTitulo.textContent = "Agregar Encargado";
        form.reset();
        form.dataset.action = "store"; 
        modal.style.display = "flex";
    });

    // Cerrar modal
    btnCerrar.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Guardar o actualizar encargado
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        // Asegurar acción correcta
        if (form.dataset.action === "update") {
            formData.append("action", "update");
            formData.append("idPersonal", form.idPersonal.value);
        } else {
            formData.append("action", "store");
        }

        // Enviar al servidor
        fetch(BASE_URL + "controllers/EncargadoController.php", {
            method: "POST",
            body: formData
        })
        .then(async res => {
            const text = await res.text();

            // Procesar resultado
            switch (text.trim()) {
                case "success":
                    mostrarMensaje("Encargado guardado correctamente", "green");
                    setTimeout(() => location.reload(), 1000);
                    break;
                case "duplicate":
                    mostrarMensaje("El usuario ya existe", "orange");
                    break;
                case "error":
                    mostrarMensaje("Error al guardar el encargado (ver consola o logs PHP)", "red");
                    break;
                default:
                    mostrarMensaje("Respuesta inesperada del servidor", "red");
                    break;
            }
        })
        .catch(err => {
            console.error("Error en la petición fetch:", err);
            mostrarMensaje("Error en la conexión con el servidor", "red");
        });
    });

    // Editar encargado
    document.querySelectorAll(".editar").forEach(btn => {
        btn.addEventListener("click", () => {
            const fila = btn.closest("tr");
            const id = btn.dataset.id;
            const nombre = fila.children[1].textContent.trim();
            const apellido = fila.children[2].textContent.trim();
            const usuario = fila.children[3].textContent.trim();
            const rol = fila.children[4].textContent.trim();

            modalTitulo.textContent = "Editar Encargado";
            form.idPersonal.value = id;
            form.nombre.value = nombre;  
            form.apellido.value = apellido; 
            form.usuario.value = usuario;
            form.rol.value = rol;
            form.dataset.action = "update";

            modal.style.display = "flex";
        });
    });

    // Eliminar encargado
    document.querySelectorAll(".eliminar").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;

            Swal.fire({
                title: "¿Estás seguro?",
                text: "Este encargado será eliminado permanentemente",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append("action", "delete");
                    formData.append("id", id); 

                    fetch(BASE_URL + "controllers/EncargadoController.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.text())
                    .then(data => {
                        console.log("Respuesta eliminación:", data);
                        if (data.trim() === "success") {
                            Swal.fire("Eliminado", "El encargado ha sido eliminado correctamente", "success");
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire("Error", "No se pudo eliminar el encargado", "error");
                        }
                    })
                    .catch(err => {
                        console.error("Error al eliminar:", err);
                        Swal.fire("Error", "Error en la conexión al eliminar", "error");
                    });
                }
            });
        });
    });

    function mostrarMensaje(texto, color) {
        mensaje.style.color = color;
        mensaje.innerText = texto;
    }
});
