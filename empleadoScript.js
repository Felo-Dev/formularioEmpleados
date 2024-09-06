$(document).ready(function () {
    $("#form-empleado").on("submit", function (event) {
        event.preventDefault();

        if ($("input[name='roles[]']:checked").length === 0) {
            alert("Debe seleccionar al menos un rol.");
        } else {
            const formData = $(this).serialize();

            $.post("empleadoController.php", formData)
                .done(function (response) {
                    if (response.success) {
                        alert(response.message);
                        $("#form-empleado")[0].reset();
                        Empleado.listarEmpleados();
                    } else {
                        alert(response.message);
                    }
                })
                .fail(function () {
                    alert("Error al crear o actualizar el empleado");
                });
        }
    });

    Empleado.listarEmpleados();
});

class Empleado {
    // Método para listar empleados
    static listarEmpleados() {
        $.get("empleadoController.php", { listar: true })
            .done(function (response) {
                console.log("Respuesta del servidor:", response);
                // Asegúrate de que response es un array
                if (Array.isArray(response)) {
                    let tabla = "";
                    response.forEach((emp) => {
                        tabla += `
                            <tr id="empleado-${emp.id}">
                                <td>${emp.id}</td>
                                <td>${emp.nombre}</td>
                                <td>${emp.email}</td>
                                <td>${emp.sexo}</td>
                                <td>${emp.area}</td>
                                <td>${emp.descripcion}</td>
                                <td>
                                    <a onclick="Empleado.editarEmpleado(${emp.id})"><p class="fas fa-edit"></p></a>   
                                </td>
                                 <td>
                                    <a onclick="Empleado.eliminarEmpleado(${emp.id})"><p class="fa-solid fa-trash"></p></a>
                                 </td>
                            </tr>`;
                    });
                    $("#tabla-empleados tbody").html(tabla);
                } else {
                    alert("La respuesta del servidor no es un array.");
                }
            })
            .fail(function () {
                alert("Error al obtener la lista de empleados");
            });
    }

    // Método para eliminar empleados
    static eliminarEmpleado(id) {
        if (confirm("¿Estás seguro de que deseas eliminar este empleado?")) {
            $.post("empleadoController.php", { id: id, eliminar: true })
                .done(function (response) {
                    if (response.success) {
                        alert(response.message);
                        Empleado.listarEmpleados();
                    } else {
                        alert(response.message || "Error al eliminar el empleado");
                    }
                })
                .fail(function () {
                    alert("Error al eliminar el empleado");
                });
        }
    }

    // Método para editar empleados
    static editarEmpleado(id) {
        $.get(`empleadoController.php?id=${id}`)
            .done(function (response) {
                const empleado = response;
                $("#id").val(empleado.id);
                $("#nombre").val(empleado.nombre);
                $("#email").val(empleado.email);
                $("#telefono").val(empleado.telefono);
            })
            .fail(function () {
                alert("Error al cargar los datos del empleado");
            });
    }
}
