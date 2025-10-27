document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-alumno");
    const modalTitulo = document.getElementById("modal-titulo");
    const form = document.getElementById("form-alumno");
    const btnAgregar = document.getElementById("btn-agregar");
    const btnCerrar = document.getElementById("btn-cerrar");
    const mensaje = document.getElementById("mensaje");
    const inputExcel = document.getElementById("input-excel");

    btnAgregar.addEventListener("click", () => {
        modalTitulo.textContent = "Agregar Alumno";
        form.reset();
        form.dataset.action = "store";
        modal.style.display = "flex";
    });

    btnCerrar.addEventListener("click", () => {
        modal.style.display = "none";
    });
    
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        if (form.dataset.action === "update") {
            formData.append("action", "update");
            formData.append("idEstudiante", form.idEstudiante.value);
        } else {
            formData.append("action", "store");
        }

        for (let pair of formData.entries()) {
            console.log(pair[0]+ ': ' + pair[1]);
        }

        fetch(BASE_URL + "controllers/AlumnoController.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            console.log("Respuesta del servidor:", data);
            if (data.trim() === "success"){
                mostrarMensaje("Alumno Guardado Correctamente","green");
                setTimeout(() => location.reload(), 1000);
            } else if (data.trim() === "duplicate"){
                mostrarMensaje("El DNI ingresado ya existe", "red");
            } else {
                mostrarMensaje("Error al guardar el alumno", "red");
            }
        })
        .catch(() => {
            mostrarMensaje("Error en la petición", "red");
        });
    });


    document.querySelectorAll(".editar").forEach(btn => {
        btn.addEventListener("click", () => {
            const fila = btn.closest("tr");
            const id = btn.dataset.id;
            const nombre = fila.children[1].textContent;
            const apellidos = fila.children[2].textContent;
            const dni = fila.children[3].textContent;
            const grado = fila.children[4].textContent;
            const seccion = fila.children[5].textContent;

            modalTitulo.textContent = "Editar Alumno";
            form.idEstudiante.value = id;
            form.Nombre.value = nombre;
            form.Apellidos.value = apellidos;
            form.DNI.value = dni;
            form.Grado.value = grado;
            form.Seccion.value = seccion;
            form.dataset.action = "update";

            modal.style.display = "flex";
        });
    });

    document.querySelectorAll(".eliminar").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            Swal.fire({
                title: "Estas seguro?",
                text: "Este alumno sera eliminado permanentemente",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed){
                    const formData = new FormData();
                    formData.append("action", "delete");
                    formData.append("id", id);
                    fetch(BASE_URL + "controllers/AlumnoController.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.text())
                    .then(data => {
                        if (data.trim() === "success"){
                            Swal.fire(
                                "Eliminado",
                                "El alumno ha sido eliminado correctamente",
                                "success"
                            );
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire(
                                "Error",
                                "No se pudo eliminar el alumno",
                                "error"
                            );
                        }
                    })        
                }
            });
        });       
    });

    //Importacion de Excel
    document.getElementById("btn-importar").addEventListener("click",() => {
        inputExcel.click();
    });

    inputExcel.addEventListener("change", () => {
        if (inputExcel.files.length > 0){
            const archivo = inputExcel.files[0];
            mostrarMensaje("Archivo Seleccionado: " + archivo.name, "blue");
            const formData = new FormData();
            formData.append("action", "importExcel");
            formData.append("file", archivo);

            fetch(BASE_URL + "controllers/AlumnoController.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                console.log("Respuesta del servidor:", data);

                if (data.startsWith("success")) {
                    Swal.fire({
                        title: "Importación exitosa",
                        text: data, 
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload(); 
                    });
                } else {
                    Swal.fire({
                        title: "Error en la importación",
                        text: data,
                        icon: "error",
                        confirmButtonText: "Cerrar"
                    });
                }
            })
            .catch(error => {
                mostrarMensaje("Error en la peticion: "+ error, "red");
            });
        }
    });

    document.querySelectorAll(".generar-qr").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const formData = new FormData();
            formData.append("action", "generarQR");
            formData.append("id", id);

            fetch(BASE_URL + "controllers/AlumnoController.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if (data.startsWith("success")) {
                    Swal.fire({
                        title: "QR generado",
                        icon: "success",
                        confirmButtonText: "Ok"
                    }).then(() => location.reload());
                } else {
                    Swal.fire("Error", data, "error");
                }
            });
        });
    });

    document.querySelectorAll(".ver-qr").forEach(btn => {
        btn.addEventListener("click", () => {
            const qrCode = btn.closest("tr").querySelector("td:nth-child(6)").textContent;
            Swal.fire({
                title: "QR del Alumno",
                html: `<img src="${BASE_URL}qr_images/${qrCode}.png" style="width:200px;height:200px;">`,
                confirmButtonText: "Cerrar"
            });
        });
    });

    document.querySelectorAll(".imprimir-carnet").forEach(btn => {
        btn.addEventListener("click", () => {
            const fila = btn.closest("tr");
            const qrCode = fila.querySelector("td:nth-child(6)").textContent;
            const nombre = fila.children[0].textContent;
            const apellidos = fila.children[1].textContent;
            const win = window.open("", "_blank");
            win.document.write(`
                <h3>Carnet del Alumno</h3>
                <p>${nombre} ${apellidos}</p>
                <img src="${BASE_URL}qr_images/${qrCode}.png" style="width:150px;height:150px;">
            `);
            win.document.close();
            win.print();
        });
    });

    function mostrarMensaje(texto, color){
        mensaje.style.color = color;
        mensaje.innerText = texto;
    }
});
