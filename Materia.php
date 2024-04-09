<?php
    include 'conexion.php';
    
// READ Materia
$query = "SELECT * FROM Materia";
$stmt = sqlsrv_query($conn, $query);
$materias = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $materias[] = $row;
}

// READ Periodo
$query = "SELECT * FROM Periodo";
$stmt = sqlsrv_query($conn, $query);
$periodos = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $periodos[] = $row;
}

// READ carreras
$query = "SELECT * FROM Carrera";
$stmt = sqlsrv_query($conn, $query);
$carreras = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $carreras[] = $row;
}

// READ carrera_materia
$query = "SELECT * FROM carrera_materia";
$stmt = sqlsrv_query($conn, $query);
$carrera_materia = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $carrera_materia[] = $row;
}

// UPDATE
if (isset($_POST['update'])) {
    $ID_materia = $_POST['ID_materia'];
    $periodoId = $_POST["periodoId"];
    $carreraId = $_POST["carreraId"];
    $nombre = $_POST['nombre'];

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);

    // Actualizar la tabla Materia
    $queryMateria = "UPDATE Materia SET periodoId = ?, carreraId = ?, nombre = ? WHERE ID_materia = ?";
    $paramsMateria = array($periodoId, $carreraId, $nombre, $ID_materia);
    $stmtMateria = sqlsrv_prepare($conn, $queryMateria, $paramsMateria);

    if ($stmtMateria === false) {
        sqlsrv_rollback($conn);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_execute($stmtMateria);

    // Actualizar la tabla carrera_materia
    $queryCarreraMateria = "UPDATE carrera_materia SET carreraid = ? WHERE materiaid = ?";
    $paramsCarreraMateria = array($carreraId, $ID_materia);
    $stmtCarreraMateria = sqlsrv_prepare($conn, $queryCarreraMateria, $paramsCarreraMateria);

    if ($stmtCarreraMateria === false) {
        sqlsrv_rollback($conn);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_execute($stmtCarreraMateria);

    // Si ambas operaciones son exitosas, se comete la transacción
    sqlsrv_commit($conn);
    $success_message_Update = "Materia Editada con éxito";

    header("Location: Materia.php");
    exit();
}

// DELETE
if (isset($_POST['delete'])) {
    $ID_materia = $_POST['ID_materia'];

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);

    // Eliminar registros dependientes en ActividadesAcademicas
    $queryActividades = "DELETE FROM ActividadesAcademicas WHERE materiaID = ?";
    $paramsActividades = array($ID_materia);
    $stmtActividades = sqlsrv_prepare($conn, $queryActividades, $paramsActividades);

    if ($stmtActividades === false || sqlsrv_execute($stmtActividades) === false) {
        echo "Error al eliminar registros dependientes en ActividadesAcademicas:<br>";
        die(print_r(sqlsrv_errors(), true));
    }

    // Eliminar registros dependientes en carrera_materia
    $queryCarreraMateria = "DELETE FROM carrera_materia WHERE materiaid = ?";
    $paramsCarreraMateria = array($ID_materia);
    $stmtCarreraMateria = sqlsrv_prepare($conn, $queryCarreraMateria, $paramsCarreraMateria);

    if ($stmtCarreraMateria === false || sqlsrv_execute($stmtCarreraMateria) === false) {
        echo "Error al eliminar registros dependientes en carrera_materia:<br>";
        die(print_r(sqlsrv_errors(), true));
        sqlsrv_rollback($conn); // Revertir la transacción en caso de error
    } else {
        // Eliminar la materia después de eliminar los registros dependientes
        $queryMateria = "DELETE FROM Materia WHERE ID_materia = ?";
        $paramsMateria = array($ID_materia);
        $stmtMateria = sqlsrv_prepare($conn, $queryMateria, $paramsMateria);

        if ($stmtMateria === false || sqlsrv_execute($stmtMateria) === false) {
            echo "Error al ejecutar la consulta de eliminación de materia:<br>";
            die(print_r(sqlsrv_errors(), true));
            sqlsrv_rollback($conn); // Revertir la transacción en caso de error
        } else {
            sqlsrv_commit($conn); // Confirmar la transacción si todo sale bien
            echo "Materia eliminada con éxito.<br>";
            header("Location: Materia.php");
            exit();
        }
    }
}

if (isset($_POST['add'])) {
    // Asumiendo que el valor de 'es_reticula' es enviado desde el formulario como '1' o '0'
    $nombre = $_POST['nombre'];
    $periodoId = isset($_POST["periodoId"]) && $_POST["periodoId"] != '' ? $_POST["periodoId"] : null;
    $carreraId = isset($_POST["carreraId"]) && $_POST["carreraId"] != '' ? $_POST["carreraId"] : null;
    $es_reticula = isset($_POST["es_reticula"]) ? $_POST["es_reticula"] : null;

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);

    // Insertar la materia
    $queryMateria = "INSERT INTO Materia (nombre, periodoId, carreraId) VALUES (?, ?, ?); SELECT SCOPE_IDENTITY() as ID_materia;";
    $paramsMateria = array($nombre, $periodoId, $carreraId);
    $stmtMateria = sqlsrv_query($conn, $queryMateria, $paramsMateria);

    if ($stmtMateria === false) {
        // Si ocurre un error, cancela la transacción y muestra el error
        sqlsrv_rollback($conn);
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener el ID de la materia insertada
    sqlsrv_next_result($stmtMateria); // Mover al siguiente resultado para obtener el ID
    $row = sqlsrv_fetch_array($stmtMateria, SQLSRV_FETCH_ASSOC);
    $materiaId = $row['ID_materia'];

    // Insertar en carrera_materia
    $queryCarreraMateria = "INSERT INTO carrera_materia (carreraid, materiaid, es_reticula) VALUES (?, ?, ?)";
    $paramsCarreraMateria = array($carreraId, $materiaId, $es_reticula);
    $stmtCarreraMateria = sqlsrv_query($conn, $queryCarreraMateria, $paramsCarreraMateria);

    if ($stmtCarreraMateria === false) {
        // Si ocurre un error, cancela la transacción y muestra el error
        sqlsrv_rollback($conn);
        die(print_r(sqlsrv_errors(), true));
    }

    // Si todo fue bien, confirma la transacción
    sqlsrv_commit($conn);

    $success_message_Add = "Materia Añadida con éxito";
    header("Location: Materia.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta tags requeridas por Bootstrap -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Enlace al CSS de Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Gestión de Materias</h2>
    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMateriaModal">Agregar Materia</button>
        <a href="Admin.php" class="btn btn-secondary">Volver a Administración</a>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="input-group">
        <input type="text" class="form-control" id="searchInput" placeholder="Buscar materia...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" id="filterButton" type="button">
                        <img src="Imagenes/filtro.png" alt="Buscar" style="width: 20px; height: 20px;"> 
                    </button>
                </div>
        </div>
    </div>

<!-- Tabla de Materias -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Período</th>
                <th>Carrera</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($materias as $materia): ?>
            <?php
                // Encuentra la carrera_materia correspondiente para esta materia
                $esReticula = "generica"; // Valor por defecto
                foreach ($carrera_materia as $cm) {
                    if ($cm['materiaid'] == $materia['ID_materia']) {
                        $esReticula = $cm['es_reticula'] ? "reticula" : "generica";
                        break; // Rompe el ciclo una vez que se encuentra la correspondencia
                    }
                }
            ?>
            <form action="Materia.php" method="post">
                <tr data-es-reticula="<?= $esReticula ?>">
                    <td><input type="hidden" name="ID_materia" value="<?= $materia['ID_materia'] ?>"><?= $materia['ID_materia'] ?></td>
                    <td>
                        <input type="text" class="form-control" name="nombre" value="<?= $materia['nombre'] ?>" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required disabled>
                    </td>
                    <td>
                        <select class="form-control" name="periodoId" disabled>
                            <?php foreach($periodos as $periodo): ?>
                                <option value="<?= $periodo['ID_periodo'] ?>" <?= $periodo['ID_periodo'] == $materia['periodoid'] ? 'selected' : '' ?>><?= $periodo['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="carreraId" disabled>
                            <?php foreach($carreras as $carrera): ?>
                                <option value="<?= $carrera['ID_carrera'] ?>" <?= $carrera['ID_carrera'] == $materia['carreraid'] ? 'selected' : '' ?>><?= $carrera['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <!-- Botón Editar que solo se muestra inicialmente -->
                        <button type="button" class="btn btn-sm btn-primary edit-button">Editar</button>
                        
                        <!-- Botones ocultos que se mostrarán al hacer clic en Editar -->
                        <div class="action-buttons d-none">
                        <input type="hidden" name="ID_actividad" value="<?= $actividad['ID_actividadesacademicas'] ?>">
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

<!-- Modal para añadir nueva materia -->
<div class="modal fade" id="addMateriaModal" tabindex="-1" role="dialog" aria-labelledby="addMateriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMateriaModalLabel">Añadir Nueva Materia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="Materia.php" method="POST">
                <div class="modal-body">
                    <!-- Contenedor de mensajes -->
                    <div id="messageContainer" style="display: none;" class="alert"></div>
                    <div class="form-group">
                        <label for="nombreMateria">Nombre de la Materia</label>
                        <input type="text" class="form-control" id="nombreMateria" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ\s]+" required>
                        <small>Solo se permiten caracteres alfabéticos y espacios.</small>
                    </div>
                    <div class="form-group">
                        <label for="periodoId">Período</label>
                        <select name="periodoId" id="periodoId" class="form-control" required>
                            <?php foreach($periodos as $periodo): ?>
                                <option value="<?= $periodo['ID_periodo'] ?>"><?= $periodo['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="carreraId">Carrera</label>
                        <select class="form-control" id="carreraId" name="carreraId" required>
                            <?php foreach($carreras as $carrera) {
                                echo "<option value=\"{$carrera['ID_carrera']}\">{$carrera['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Materia</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="es_reticula" id="reticula" value="1" required>
                                <label class="form-check-label" for="reticula">Reticula</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="es_reticula" id="generica" value="0" required>
                                <label class="form-check-label" for="generica">Generica</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="add" class="btn btn-primary">Añadir Materia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de filtros de búsqueda -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filtros de Búsqueda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Selecciona tus filtros aquí:</p>
                <div class="form-group">
                    <label for="filterType">Tipo de Filtro:</label>
                    <select class="form-control" id="filterType">
                        <option value="all">Todos</option>
                        <option value="generica">Genérica</option>
                        <option value="reticula">Reticula</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="aplicarFiltro()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Barra de busqueda-->
<script>
document.getElementById('searchInput').addEventListener('keyup', function(event) {
    var searchQuery = event.target.value.toLowerCase();
    var allRows = document.querySelectorAll('.table tbody tr');

    allRows.forEach(function(row) {
        var nombreMateria = row.querySelector('td:nth-child(2) input').value.toLowerCase();

        if (nombreMateria.includes(searchQuery)) {
            row.style.display = ''; // muestra la fila
        } else {
            row.style.display = 'none'; // oculta la fila
        }
    });
});
</script>

<!-- Aplicar los filtros-->
<script>
function aplicarFiltro() {
    var selectedOption = document.getElementById("filterType").value;
    var searchQuery = document.getElementById('searchInput').value.toLowerCase();
    var tableRows = document.querySelectorAll('.table tbody tr');

    tableRows.forEach(function(row) {
        var nombreMateria = row.querySelector('td:nth-child(2) input').value.toLowerCase();
        var esReticula = row.getAttribute("data-es-reticula");

        // Verifica si la fila debe mostrarse basado en el filtro y la búsqueda
        var showRow = (selectedOption === "all" || selectedOption === esReticula) && nombreMateria.includes(searchQuery);

        row.style.display = showRow ? "" : "none";
    });

    // Cierra el modal después de filtrar usando jQuery
    $('#filterModal').modal('hide');
}

// Asegúrate de llamar a aplicarFiltro también cuando se realiza una búsqueda
document.getElementById('searchInput').addEventListener('keyup', aplicarFiltro);
</script>

<script>
    // Cuando se hace clic en el botón de imagen
    $('#filterButton').click(function() {
        $('#filterModal').modal('show'); // Mostrar el modal
    });
</script>

<!-- Mostrar el mensaje-->
<script>
function showMessage(message, isSuccess) {
    var messageContainer = document.getElementById('messageContainer');
    messageContainer.style.display = 'block';
    messageContainer.className = isSuccess ? 'alert alert-success' : 'alert alert-danger';
    messageContainer.textContent = message;
}

document.getElementById('addMateriaModal').addEventListener('submit', function(event) {
    var nombre = document.getElementById('nombreMateria').value;
    // Aquí puedes añadir más validaciones según sea necesario
    if (nombre === '') {
        event.preventDefault(); // Detiene la presentación del formulario
        showMessage('Por favor, rellena todos los campos requeridos.', false);
    } else {
        // Puedes optar por no detener el envío del formulario aquí
        // y dejar que se procese en el servidor
        showMessage('Formulario enviado con éxito!', true);
    }
});
</script>

<!-- Botones-->
<script>
    $(document).ready(function() {
        $('.edit-button').click(function() {
            // Encuentra todos los elementos de entrada en la fila y habilitarlos
            $(this).closest('tr').find('input, select').removeAttr('disabled');
            
            // Oculta el botón de editar
            $(this).hide();
            
            // Muestra los botones de acción
            $(this).closest('tr').find('.action-buttons').removeClass('d-none');
        });
    });
</script>

<!-- Recargar pagina-->
<script>
    // Función para recargar la página
    function reloadPage() {
        location.reload(); // Recarga la página actual
    }
</script>

</body>
</html>
