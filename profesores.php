<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Profesores</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Profesores Registrados</h2>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProfesorModal">
            Agregar Profesor
        </button>
        <table class="table table-striped table-bordered mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>ID Profesor</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Grados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="profesoresTableBody">
                <!-- Los datos de los profesores se cargarán aquí con AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar profesor -->
    <div class="modal fade" id="addProfesorModal" tabindex="-1" role="dialog" aria-labelledby="addProfesorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProfesorModalLabel">Agregar Nuevo Profesor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addProfesorForm">
                        <div class="form-group">
                            <label for="nombreProfesor">Nombre:</label>
                            <input type="text" class="form-control" id="nombreProfesor" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="especialidadProfesor">Especialidad:</label>
                            <input type="text" class="form-control" id="especialidadProfesor" name="especialidad" required>
                        </div>
                        <div class="form-group">
                            <label for="gradoProfesor">Grado:</label>
                            <div class="input-group">
                                <select class="form-control" id="gradoProfesor" name="grado_single">
                                    <!-- Opciones de grados se cargarán aquí con AJAX -->
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="addGradoBtn">Agregar Grado</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Grados Asignados:</label>
                            <ul id="selectedGradosList" class="list-group">
                                <!-- Grados seleccionados se mostrarán aquí -->
                            </ul>
                            <input type="hidden" name="grados_seleccionados" id="gradosSeleccionadosInput">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Profesor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar profesor -->
    <div class="modal fade" id="editProfesorModal" tabindex="-1" role="dialog" aria-labelledby="editProfesorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfesorModalLabel">Editar Profesor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editProfesorForm">
                        <input type="hidden" id="editProfesorId" name="profesor_id">
                        <div class="form-group">
                            <label for="editNombreProfesor">Nombre:</label>
                            <input type="text" class="form-control" id="editNombreProfesor" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="editEspecialidadProfesor">Especialidad:</label>
                            <input type="text" class="form-control" id="editEspecialidadProfesor" name="especialidad" required>
                        </div>
                        <div class="form-group">
                            <label for="editGradoProfesor">Grado:</label>
                            <div class="input-group">
                                <select class="form-control" id="editGradoProfesor" name="grado_single">
                                    <!-- Opciones de grados se cargarán aquí con AJAX -->
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="editAddGradoBtn">Agregar Grado</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Grados Asignados:</label>
                            <ul id="editSelectedGradosList" class="list-group">
                                <!-- Grados seleccionados se mostrarán aquí -->
                            </ul>
                            <input type="hidden" name="edit_grados_seleccionados" id="editGradosSeleccionadosInput">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlace a jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Enlace a Bootstrap JS (Popper.js es una dependencia de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Aquí irá el código JavaScript/AJAX para cargar los profesores
        $(document).ready(function() {
            // Función para cargar los profesores
            function cargarProfesores() {
                $.ajax({
                    url: 'get_profesores.php', // El script PHP que creamos
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var profesoresTableBody = $('#profesoresTableBody');
                        profesoresTableBody.empty(); // Limpiar la tabla antes de añadir nuevos datos

                        if (data.error) {
                            profesoresTableBody.append('<tr><td colspan="4" class="text-danger">' + data.error + '</td></tr>');
                        } else if (data.length > 0) {
                            $.each(data, function(index, profesor) {
                                profesoresTableBody.append('<tr>' +
                                    '<td>' + profesor.ProfesorID + '</td>' +
                                    '<td>' + profesor.Nombre + '</td>' +
                                    '<td>' + profesor.Especialidad + '</td>' +
                                    '<td>' + (profesor.Grados.length > 0 ? profesor.Grados.map(g => g.NombreGrado).join(', ') : 'N/A') + '</td>' +
                                    '<td>' +
                                        '<button type="button" class="btn btn-warning btn-sm edit-profesor-btn" data-id="' + profesor.ProfesorID + '" data-toggle="modal" data-target="#editProfesorModal">Editar</button> ' +
                                        '<button type="button" class="btn btn-danger btn-sm delete-profesor-btn" data-id="' + profesor.ProfesorID + '">Eliminar</button> ' +
                                        '<button type="button" class="btn btn-info btn-sm generate-pdf-btn" data-id="' + profesor.ProfesorID + '">Generar PDF</button>' +
                                    '</td>' +
                                    '</tr>');
                            });
                        } else {
                            profesoresTableBody.append('<tr><td colspan="4">No hay profesores registrados.</td></tr>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#profesoresTableBody').append('<tr><td colspan="4" class="text-danger">Error al cargar los profesores: ' + textStatus + ' - ' + errorThrown + '</td></tr>');
                    }
                });
            }

            // Llamar a la función para cargar los profesores cuando la página esté lista
            cargarProfesores();

            // Cargar grados cuando el modal de agregar profesor se muestre
            $('#addProfesorModal').on('show.bs.modal', function () {
                // Limpiar la lista de grados seleccionados y el array
                selectedGrados = [];
                renderSelectedGrados();

                $.ajax({
                    url: 'get_grados.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var gradoSelect = $('#gradoProfesor');
                        gradoSelect.empty();
                        gradoSelect.append('<option value="">Seleccione un Grado</option>'); // Opción por defecto
                        if (data.error) {
                            console.error('Error al cargar grados: ' + data.error);
                        } else {
                            $.each(data, function(index, grado) {
                                gradoSelect.append('<option value="' + grado.GradoID + '">' + grado.NombreGrado + '</option>');
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error al cargar los grados: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });

            // Array para almacenar los grados seleccionados
            var selectedGrados = [];

            // Función para renderizar los grados seleccionados
            function renderSelectedGrados() {
                var list = $('#selectedGradosList');
                list.empty();
                if (selectedGrados.length === 0) {
                    list.append('<li class="list-group-item text-muted">No hay grados seleccionados.</li>');
                } else {
                    $.each(selectedGrados, function(index, grado) {
                        list.append('<li class="list-group-item d-flex justify-content-between align-items-center" data-id="' + grado.GradoID + '">' +
                            grado.NombreGrado +
                            '<button type="button" class="btn btn-danger btn-sm remove-grado">X</button>' +
                            '</li>');
                    });
                }
                // Actualizar el campo oculto con los IDs de los grados seleccionados
                $('#gradosSeleccionadosInput').val(JSON.stringify(selectedGrados.map(g => g.GradoID)));
            }

            // Manejar clic en el botón "Agregar Grado"
            $('#addGradoBtn').on('click', function() {
                var selectedOption = $('#gradoProfesor option:selected');
                var gradoId = selectedOption.val();
                var gradoNombre = selectedOption.text();

                if (gradoId && gradoNombre && gradoId !== "" && !selectedGrados.some(g => g.GradoID === gradoId)) {
                    selectedGrados.push({ GradoID: gradoId, NombreGrado: gradoNombre });
                    renderSelectedGrados();
                }
            });

            // Manejar clic en el botón "X" para eliminar un grado
            $('#selectedGradosList').on('click', '.remove-grado', function() {
                var gradoToRemoveId = $(this).closest('li').data('id').toString(); // Asegurarse que sea string para comparación
                selectedGrados = selectedGrados.filter(g => g.GradoID !== gradoToRemoveId);
                renderSelectedGrados();
            });

            // Manejar el clic en el botón "Editar"
            $('#profesoresTableBody').on('click', '.edit-profesor-btn', function() {
                var profesorId = $(this).data('id');

                // Cargar grados para el modal de edición (igual que para agregar)
                $.ajax({
                    url: 'get_grados.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var editGradoSelect = $('#editGradoProfesor');
                        editGradoSelect.empty();
                        editGradoSelect.append('<option value="">Seleccione un Grado</option>');
                        if (data.error) {
                            console.error('Error al cargar grados para edición: ' + data.error);
                        } else {
                            $.each(data, function(index, grado) {
                                editGradoSelect.append('<option value="' + grado.GradoID + '">' + grado.NombreGrado + '</option>');
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error al cargar los grados para edición: ' + textStatus + ' - ' + errorThrown);
                    }
                });

                // Obtener datos del profesor a editar
                $.ajax({
                    url: 'get_profesor_by_id.php?id=' + profesorId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(profesor) {
                        if (profesor.error) {
                            alert('Error: ' + profesor.error);
                        } else {
                            // Rellenar el formulario del modal
                            $('#editProfesorId').val(profesor.ProfesorID);
                            $('#editNombreProfesor').val(profesor.Nombre);
                            $('#editEspecialidadProfesor').val(profesor.Especialidad);

                            // Cargar los grados actuales del profesor en la lista seleccionada
                            selectedGradosEdit = []; // Reiniciar la lista para edición
                            if (profesor.Grados && profesor.Grados.length > 0) {
                                $.each(profesor.Grados, function(index, grado) {
                                    selectedGradosEdit.push({ GradoID: grado.GradoID.toString(), NombreGrado: grado.NombreGrado });
                                });
                            }
                            renderSelectedGradosEdit();

                            // Mostrar el modal
                            $('#editProfesorModal').modal('show');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error al obtener datos del profesor: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });

            // Array para almacenar los grados seleccionados en el modal de edición
            var selectedGradosEdit = [];

            // Función para renderizar los grados seleccionados en el modal de edición
            function renderSelectedGradosEdit() {
                var list = $('#editSelectedGradosList');
                list.empty();
                if (selectedGradosEdit.length === 0) {
                    list.append('<li class="list-group-item text-muted">No hay grados seleccionados.</li>');
                } else {
                    $.each(selectedGradosEdit, function(index, grado) {
                        list.append('<li class="list-group-item d-flex justify-content-between align-items-center" data-id="' + grado.GradoID + '">' +
                            grado.NombreGrado +
                            '<button type="button" class="btn btn-danger btn-sm remove-grado-edit">X</button>' +
                            '</li>');
                    });
                }
                // Actualizar el campo oculto con los IDs de los grados seleccionados
                $('#editGradosSeleccionadosInput').val(JSON.stringify(selectedGradosEdit.map(g => g.GradoID)));
            }

            // Manejar clic en el botón "Agregar Grado" del modal de edición
            $('#editAddGradoBtn').on('click', function() {
                var selectedOption = $('#editGradoProfesor option:selected');
                var gradoId = selectedOption.val();
                var gradoNombre = selectedOption.text();

                if (gradoId && gradoNombre && gradoId !== "" && !selectedGradosEdit.some(g => g.GradoID === gradoId)) {
                    selectedGradosEdit.push({ GradoID: gradoId, NombreGrado: gradoNombre });
                    renderSelectedGradosEdit();
                }
            });

            // Manejar clic en el botón "X" para eliminar un grado del modal de edición
            $('#editSelectedGradosList').on('click', '.remove-grado-edit', function() {
                var gradoToRemoveId = $(this).closest('li').data('id').toString();
                selectedGradosEdit = selectedGradosEdit.filter(g => g.GradoID !== gradoToRemoveId);
                renderSelectedGradosEdit();
            });

            // Manejar el envío del formulario para agregar profesor
            $('#addProfesorForm').on('submit', function(e) {
                e.preventDefault(); // Evitar el envío normal del formulario

                // Asegurarse de que el campo hidden de grados seleccionados esté actualizado
                $('#gradosSeleccionadosInput').val(JSON.stringify(selectedGrados.map(g => g.GradoID)));

                $.ajax({
                    url: 'add_profesor.php',
                    type: 'POST',
                    data: $(this).serialize(), // Serializar los datos del formulario (incluirá el hidden input)
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#addProfesorModal').modal('hide'); // Ocultar el modal
                            $('#addProfesorForm')[0].reset(); // Limpiar el formulario
                            selectedGrados = []; // Limpiar la lista de grados seleccionados
                            renderSelectedGrados(); // Actualizar la lista visible
                            cargarProfesores(); // Recargar la tabla de profesores
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error al agregar profesor: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });

            // Manejar el envío del formulario para editar profesor
            $('#editProfesorForm').on('submit', function(e) {
                e.preventDefault(); // Evitar el envío normal del formulario

                // Asegurarse de que el campo hidden de grados seleccionados esté actualizado
                $('#editGradosSeleccionadosInput').val(JSON.stringify(selectedGradosEdit.map(g => g.GradoID)));

                $.ajax({
                    url: 'update_profesor.php',
                    type: 'POST',
                    data: $(this).serialize(), // Serializar los datos del formulario (incluirá el hidden input)
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#editProfesorModal').modal('hide'); // Ocultar el modal
                            cargarProfesores(); // Recargar la tabla de profesores
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error al editar profesor: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });

            // Manejar clic en el botón "Eliminar"
            $('#profesoresTableBody').on('click', '.delete-profesor-btn', function() {
                var profesorId = $(this).data('id');
                
                if (confirm('¿Está seguro de que desea eliminar a este profesor? Esta acción es irreversible.')) {
                    $.ajax({
                        url: 'delete_profesor.php',
                        type: 'POST',
                        data: { id: profesorId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                cargarProfesores(); // Recargar la tabla de profesores
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('Error al eliminar profesor: ' + textStatus + ' - ' + errorThrown);
                        }
                    });
                }
            });

            // Manejar clic en el botón "Generar PDF"
            $('#profesoresTableBody').on('click', '.generate-pdf-btn', function() {
                var profesorId = $(this).data('id');
                window.open('generate_profesor_pdf.php?id=' + profesorId, '_blank');
            });
        });
    </script>
</body>
</html>
