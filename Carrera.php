<?php
    include 'conexion.php';
    
    // READ Carreras
    $query = "SELECT * FROM Carrera";
    $stmt = sqlsrv_query($conn, $query);
    $carreras = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $carreras[] = $row;
    }

// UPDATE Carrera
    if (isset($_POST['update'])) {
        $ID_carrera = $_POST['ID_carrera'];
        $nombre = $_POST['nombre'];
        $perfil_carrera = $_POST['perfil_carrera'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];

        $query = "UPDATE Carrera SET nombre = ?, perfil_carrera = ?, duracion = ?, descripcion = ? WHERE ID_carrera = ?";
        $params = array($nombre, $perfil_carrera, $duracion, $descripcion, $ID_carrera);
        $stmt = sqlsrv_prepare($conn, $query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        sqlsrv_execute($stmt);
        $success_message_Update = "Carrera Editada con éxito";

        header("Location: Carrera.php");
        exit();
    }

// DELETE Carrera
if (isset($_POST['delete'])) {
    $ID_carrera = $_POST['ID_carrera'];

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);

    // Actualizar los registros de estudiantes que tienen esta carrera asociada
    $queryUpdateEstudiantes = "UPDATE InformacionAcademica_estudiante SET carreraid = NULL WHERE carreraid = ?";
    $paramsUpdateEstudiantes = array($ID_carrera);
    $stmtUpdateEstudiantes = sqlsrv_prepare($conn, $queryUpdateEstudiantes, $paramsUpdateEstudiantes);

    if ($stmtUpdateEstudiantes === false || sqlsrv_execute($stmtUpdateEstudiantes) === false) {
        echo "Error al actualizar InformacionAcademica_estudiante:<br>";
        die(print_r(sqlsrv_errors(), true));
        sqlsrv_rollback($conn); // Revertir la transacción en caso de error
    }

    // Eliminar la carrera
    $queryDeleteCarrera = "DELETE FROM Carrera WHERE ID_carrera = ?";
    $paramsDeleteCarrera = array($ID_carrera);
    $stmtDeleteCarrera = sqlsrv_prepare($conn, $queryDeleteCarrera, $paramsDeleteCarrera);

    if ($stmtDeleteCarrera === false || sqlsrv_execute($stmtDeleteCarrera) === false) {
        echo "Error al eliminar Carrera:<br>";
        die(print_r(sqlsrv_errors(), true));
        sqlsrv_rollback($conn); // Revertir la transacción en caso de error
    } else {
        sqlsrv_commit($conn); // Confirmar la transacción si todo sale bien
        $success_message_Delete = "Carrera Eliminada con éxito";
        header("Location: Carrera.php");
        exit();
    }
}

// ADD Carrera
    if (isset($_POST['add'])) {
        $nombre = $_POST['nombre'];
        $perfil_carrera = $_POST['perfil_carrera'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];

        $query = "INSERT INTO Carrera (nombre, perfil_carrera, duracion, descripcion) VALUES (?, ?, ?, ?)";
        $params = array($nombre, $perfil_carrera, $duracion, $descripcion);
        $stmt = sqlsrv_prepare($conn, $query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        sqlsrv_execute($stmt);
        $success_message_Add = "Carrera Añadida con éxito";

        header("Location: Carrera.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta tags requeridas por Bootstrap -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Gestión de Carreras</h2>
    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCarreraModal">Agregar Carrera</button>
        <a href="Admin.php" class="btn btn-secondary">Volver a Administración</a>
    </div>

<!-- Tabla de Carreras -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Perfil de Carrera</th>
                <th>Duración</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($carreras as $carrera): ?>
            <form action="Carrera.php" method="post">
                <tr>
                    <td><input type="hidden" name="ID_carrera" value="<?= $carrera['ID_carrera'] ?>"><?= $carrera['ID_carrera']?> </td>
                    <td>
                        <input type="text" class="form-control" name="nombre" value="<?= $carrera['nombre'] ?>" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required disabled>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="perfil_carrera" value="<?= $carrera['perfil_carrera'] ?>" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required disabled>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="duracion" value="<?= $carrera['duracion'] ?>" pattern="^(?:1[0-8]|[7-9])$" required disabled>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="descripcion" value="<?= $carrera['descripcion'] ?>" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required disabled>
                    <td>
                        <!-- Botón Editar -->
                        <button type="button" class="btn btn-sm btn-primary edit-button">Editar</button>

                        <!-- Botones de acción, inicialmente ocultos -->
                        <div class="action-buttons d-none" style="min-width: 200px"> 
                            <input type="hidden" name="ID_carrera" value="<?= $carrera['ID_carrera'] ?>">
                            <button type="submit" name="update" class="btn btn-sm btn-success">Guardar</button>
                            <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro que desea eliminar esta materia?');">Eliminar</button>
                        </div>
                    </td>
                </tr>
            </form>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para añadir nueva carrera -->
    <div class="modal fade" id="addCarreraModal" tabindex="-1" role="dialog" aria-labelledby="addCarreraModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCarreraModalLabel">Añadir Nueva Carrera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="Carrera.php" method="POST">
    <div class="modal-body">
        <!-- Contenedor de mensajes -->
        <div id="messageContainer" style="display: none;" class="alert"></div>

        <div class="form-group">
            <label for="nombreCarrera">Nombre de la Carrera</label>
            <input type="text" class="form-control" id="nombreCarrera" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required>
            <small>Solo se permiten caracteres alfabéticos y espacios.</small>
        </div>

        <div class="form-group">
            <label for="perfilCarrera">Perfil de Carrera</label>
            <input type="text" class="form-control" id="perfilCarrera" name="perfil_carrera" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required>
            <small>Solo se permiten caracteres alfabéticos y espacios.</small>
        </div>

        <div class="form-group">
            <label for="tipoCarrera">Tipo de Carrera</label>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="escolarizada" name="tipoCarrera" value="escolarizada" onclick="mostrarDuracion()">
                <label class="form-check-label" for="escolarizada">Escolarizada</label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="mixta" name="tipoCarrera" value="mixta" onclick="mostrarDuracion()">
                <label class="form-check-label" for="mixta">Mixta</label>
            </div>
        </div>

        <div class="form-group" id="seccionDuracion" style="display:none;">
            <label for="duracionCarrera">Duración</label>
            <select class="form-control" id="duracionCarrera" name="duracion" required>
                <?php
                    // Generar opciones numéricas del 7 al 12 (por defecto para escolarizada)
                    for ($i = 7; $i <= 12; $i++) {
                        // Usar la condición ternaria para marcar la opción "9" como seleccionada por defecto
                        $selected = ($i == 9) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="descripcionCarrera">Descripción</label>
            <input type="text" class="form-control" id="descripcionCarrera" name="descripcion" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required>
            <small>Solo se permiten caracteres alfabéticos y espacios.</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" name="add" class="btn btn-primary">Añadir Carrera</button>
    </div>
</form>

<!-- Vista de agregar -->
<script>
    function mostrarDuracion() {
        var seccionDuracion = document.getElementById("seccionDuracion");
        var escolarizadaRadio = document.getElementById("escolarizada");
        var mixtaRadio = document.getElementById("mixta");
        var duracionCarrera = document.getElementById("duracionCarrera");

        if (escolarizadaRadio.checked) {
            duracionCarrera.innerHTML = "";
            for (var i = 7; i <= 12; i++) {
                var selected = (i == 9) ? 'selected' : '';
                duracionCarrera.innerHTML += "<option value='" + i + "' " + selected + ">" + i + "</option>";
            }
        } else if (mixtaRadio.checked) {
            duracionCarrera.innerHTML = "";
            for (var i = 12; i <= 18; i++) {
                var selected = (i == 14) ? 'selected' : '';
                duracionCarrera.innerHTML += "<option value='" + i + "' " + selected + ">" + i + "</option>";
            }
        }

        seccionDuracion.style.display = "block";
    }
</script>

<!-- Botones Tabla -->
<script>
    $(document).ready(function() {
        $('.edit-button').click(function() {
            // Habilitar los campos de entrada en la fila
            $(this).closest('tr').find('input, select').removeAttr('disabled');

            // Ocultar el botón de editar
            $(this).hide();

            // Mostrar los botones de acción
            $(this).closest('tr').find('.action-buttons').removeClass('d-none');
        });
    });
</script>

</body>
</html>
