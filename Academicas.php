<?php
session_start(); // Iniciar la sesión una vez al principio del script

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];

        // Disponible para uso en JavaScript
        echo "<script>var userId = " . json_encode($_SESSION['user_id']) . ";</script>";
    } else {
        // Considera redirigir al usuario a una página de inicio de sesión o mostrar un mensaje
        header("Location: login.php");
        exit;
    }
} else {
    // Usuario no autenticado, redirigir o manejar de acuerdo a la lógica de tu aplicación
    header("Location: login.php");
    exit;
}

include 'Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id']; // Ya hemos verificado esto arriba
    $materiaID = $_POST['materiaID'];
    $tiposactividadesID = $_POST['tiposactividadesID'];
    $fecha = $_POST['fecha'];    
    $descripcion = $_POST['descripcion'];

    $query = "INSERT INTO ActividadesAcademicas (usuariosID, materiaID, tiposactividadesID, descripcion, fecha) VALUES (?, ?, ?, ?, ?)";
    $params = array($userId, $materiaID, $tiposactividadesID, $descripcion, $fecha);
    $stmt = sqlsrv_prepare($conn, $query, $params);

    if($stmt === false) {
        // Considera usar una función de manejo de errores personalizada
        error_log(print_r(sqlsrv_errors(), true));
        // Redirige o muestra un mensaje de error si es necesario

        exit;
    }

    if(!sqlsrv_execute($stmt)) {
        // Manejo de errores
        error_log(print_r(sqlsrv_errors(), true));
        // Redirige o muestra un mensaje de error si es necesario
        exit;
    }
    
    // Redirecciona para evitar la re-submisión del formulario
    header("Location: Academicas.php?success=1");
    exit; 
}

// Obtener Materia
$query = "SELECT * FROM Materia";
$stmt = sqlsrv_query($conn, $query);
$materias = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $materias[$row['ID_materia']] = $row['nombre']; // Guardamos el nombre de la materia
}

// Obtener ID_tiposactividades y reindexar el array
$query = "SELECT * FROM Tipos_Actividades";
$stmt = sqlsrv_query($conn, $query);
$tiposActividades = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $tiposActividades[$row['ID_tiposactividades']] = $row;
}

// Obtener tipos de actividades
$query = "SELECT * FROM Tipos_Actividades";
$stmt = sqlsrv_query($conn, $query);
$tiposActividades = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $tiposActividades[$row['ID_tiposactividades']] = $row['nombre'];
}

if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Preparar la consulta para obtener la carreraid del estudiante
    $query = "SELECT carreraid FROM InformacionAcademica_estudiante WHERE usuariosid = ?";
    $params = array($userId);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener el resultado de la consulta
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $carreraId = $row['carreraid'];

        // Preparar la consulta para obtener las materias de la carrera del estudiante
        $query = "SELECT m.ID_materia, m.nombre 
                FROM Materia m 
                INNER JOIN carrera_materia cm ON m.ID_materia = cm.materiaid 
                WHERE cm.carreraid = ?";
        $params = array($carreraId);
        $stmt2 = sqlsrv_query($conn, $query, $params);

        if ($stmt2 === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Obtener los resultados de la consulta
        $carrera_materias = array();
        while ($materia = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            $carrera_materias[$materia['ID_materia']] = $materia['nombre']; // Guardamos el nombre de la materia con su ID
        }

    } else {
        // Manejar el caso en que no se encuentra la carrera
        $carrera_materias = null;
    }

    // No olvides liberar los recursos de la declaración una vez que hayas terminado
    sqlsrv_free_stmt($stmt);
    if (isset($stmt2)) {
        sqlsrv_free_stmt($stmt2);
    }
}

// Preparar la consulta SQL para obtener solo las actividades del usuario que ha iniciado sesión
$query = "SELECT * FROM ActividadesAcademicas WHERE usuariosID = ?";
$params = array($userId);
$stmt = sqlsrv_prepare($conn, $query, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Ejecutar la consulta
if(sqlsrv_execute($stmt)) {
    // Inicializar el arreglo de actividades
    $actividadesDelUsuario = [];

    // Obtener las actividades del usuario
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $actividadesDelUsuario[] = $row;
    }

    // Puedes utilizar $actividadesDelUsuario para mostrar las actividades en la página
} else {
    die(print_r(sqlsrv_errors(), true));
}

// Función para agrupar actividades por periodo de tiempo y contarlas
function agruparActividadesConContador($actividadesDelUsuario, $inicio, $fin) {
    $actividadesFiltradas = array_filter($actividadesDelUsuario, function($actividad) use ($inicio, $fin) {
        $fecha = strtotime($actividad['fecha']->format('Y-m-d H:i:s'));  // Añadido ->format('Y-m-d H:i:s')
        return $fecha >= $inicio && $fecha <= $fin;
    });

    return [
        'contador' => count($actividadesFiltradas),
        'actividades' => $actividadesFiltradas
    ];
}

// Agrupar actividades por periodo de tiempo y contarlas
$inicioEstaSemana = strtotime('monday this week');
$finEstaSemana = strtotime('sunday this week') + 86399;
$actividadesEstaSemana = agruparActividadesConContador($actividadesDelUsuario, $inicioEstaSemana, $finEstaSemana);

$inicioProximaSemana = strtotime('monday next week');
$finProximaSemana = strtotime('sunday next week') + 86399;
$actividadesProximaSemana = agruparActividadesConContador($actividadesDelUsuario, $inicioProximaSemana, $finProximaSemana);

$inicioProximoMes = strtotime('first day of next month');
$finProximoMes = strtotime('last day of next month') + 86399;
$actividadesProximoMes = agruparActividadesConContador($actividadesDelUsuario, $inicioProximoMes, $finProximoMes);

// Combinar los resultados en un solo arreglo
$resultados = [
    'Para esta semana' => $actividadesEstaSemana,
    'Para la próxima semana' => $actividadesProximaSemana,
    'Para el próximo mes' => $actividadesProximoMes
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <title>Actividades Académicas</title>
</head>
<body>

<!-- Botones actividades -->
<div class="container mt-4">
    <div class="mb-3">
        <button type="button" class="btn btn-primary" id="btnTareas" data-id="1">Tareas</button>
        <button type="button" class="btn btn-primary" id="btnProyectos" data-id="2">Proyectos</button>
        <button type="button" class="btn btn-primary" id="btnExamenes" data-id="3">Examenes</button>
        <a href="Index.php" class="btn btn-secondary">Volver al inicio</a>
    </div>

<!-- Tabla de Actividades Académicas -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Para esta semana</th>
                <th>Para la próxima semana</th>
                <th>Para el próximo mes</th>
                <th>Acciones</th> <!-- Columna adicional para acciones -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultados as $index => $resultado): ?>
                <?php if (isset($resultado['actividades']) && count($resultado['actividades']) > 0): ?>
                    <?php foreach ($resultado['actividades'] as $actividad): ?>
                        <tr class="actividad-row" data-tipoactividad="<?= htmlspecialchars($tiposActividades[$actividad['tiposactividadesID']]) ?>">
                            <td><?= count($resultado['actividades']) ?></td>
                            <?php foreach (['Para esta semana', 'Para la próxima semana', 'Para el próximo mes'] as $periodo): ?>
                                <td>
                                    <?php if ($periodo === $index): ?>
                                        <!-- Mostrar Materia, Descripción y Fecha solo si es el período correspondiente -->
                                        <ul>
                                            <li><strong>Materia:</strong> <?= htmlspecialchars($materias[$actividad['materiaID']]) ?></li>
                                            <li><strong>Descripción:</strong> <?= htmlspecialchars($actividad['descripcion']) ?></li>
                                            <li><strong>Fecha:</strong> <?= htmlspecialchars($actividad['fecha']->format('Y-m-d')) ?></li>
                                            <li><strong>Tipo actividad:</strong> <?= htmlspecialchars($tiposActividades[$actividad['tiposactividadesID']]) ?></li>
                                        </ul>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <!-- Botón Editar que solo se muestra inicialmente -->
                                <button type="button" class="btn btn-sm btn-primary edit-button">...</button>
                        
                                <!-- Botones ocultos que se mostrarán al hacer clic en Editar -->
                                <div class="action-buttons d-none">
                                <form action="DeleteActividad.php" method="POST">
                                    <input type="hidden" name="id_actividad" value="<?= $actividad['ID_actividadesacademicas'] ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro que desea eliminar esta materia?');">Eliminar</button>                                </form>
                                </form>
                                <form action="UpdateActividad.php" method="POST">
                                <input type="hidden" name="id_actividad" value="<?= $actividad['ID_actividadesacademicas'] ?>">
                                <button type="submit" name="update" class="btn btn-sm btn-success">Editar</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

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

<!-- Modal para actividades -->
<div class="modal fade" id="modalActividad" tabindex="-1" role="dialog" aria-labelledby="modalActividadLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalActividadLabel">Nueva Actividad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formModalActividad">
                    <input type="hidden" id="tiposactividadesIDInput" name="tiposactividadesID">
                    <div class="form-group">
                        <label for="materiaIDInput">Materia</label>
                        <select class="form-control" id="materiaIDInput" name="materiaID" required>
                            <?php
                                if (!empty($carrera_materias)) {
                                    foreach ($carrera_materias as $idMateria => $nombreMateria) {
                                        echo "<option value='$idMateria'>$nombreMateria</option>";
                                    }
                                } else {
                                    echo "<option>No hay materias disponibles para tu carrera</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="descripcionInput">Descripción</label>
                        <input type="text" class="form-control" id="descripcionInput" name="descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="fechaInput">Fecha</label>
                        <input type="date" class="form-control" id="fechaInput" name="fecha" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formModalActividad').addEventListener('submit', function(event) {
        var form = this;
        // Verifica si todos los campos requeridos están llenos
        if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
</script>

<script>
$(document).ready(function () {
    // Función para mostrar el modal con el formulario para un nuevo tipo de actividad
    function abrirModalParaNuevaActividad(tipoActividadTexto, idTipoActividad) {
        $('#modalActividadLabel').text(`Nueva ${tipoActividadTexto}`);
        $('#tiposactividadesIDInput').val(idTipoActividad);
        $('#modalActividad').modal('show');
    }

    // Función para filtrar la tabla por tipo de actividad
    function filterTableByTipoActividad(tipoActividad) {
        var tipoActividadNombre = {
            1: "Tareas",
            2: "Proyectos",
            3: "Examenes"
        };

        var tipoActividadSeleccionada = tipoActividadNombre[tipoActividad];

        $('.actividad-row').each(function() {
            var filaTipoActividad = $(this).data('tipoactividad');
            if (filaTipoActividad === tipoActividadSeleccionada) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Manejadores de clic para los botones de filtro
    $('#btnTareas').click(function() {
        filterTableByTipoActividad(1);
    });

    $('#btnProyectos').click(function() {
        filterTableByTipoActividad(2);
    });

    $('#btnExamenes').click(function() {
        filterTableByTipoActividad(3);
    });

    // Manejadores de doble clic para cada botón
    $('#btnTareas').on('click', function() {
        abrirModalParaNuevaActividad('Tarea', 1);
    });
    $('#btnProyectos').on('click', function() {
        abrirModalParaNuevaActividad('Proyecto', 2);
    });
    $('#btnExamenes').on('click', function() {
        abrirModalParaNuevaActividad('Examen', 3);
    });

    // Manejador de envío del formulario dentro del modal
    $('#formModalActividad').submit(function (event) {
        event.preventDefault(); // Evitar la recarga de la página

        // Obtener datos del formulario
        var tipoActividad = $('#tiposactividadesIDInput').val();
        var materiaID = $('#materiaIDInput').val();
        var descripcion = $('#descripcionInput').val();
        var fecha = $('#fechaInput').val();

        // Log de datos para verificar qué se está enviando
        console.log("Tipo de Actividad:", tipoActividad);
        console.log("Materia ID:", materiaID);
        console.log("Descripción:", descripcion);
        console.log("Fecha:", fecha);

        // Realizar la petición AJAX para insertar en la base de datos
        $.ajax({
            type: 'POST',
            url: 'Academicas.php',
            data: {
                materiaID: materiaID,
                tiposactividadesID: tipoActividad,
                usuariosID: userId, 
                tipoActividad: tipoActividad,
                descripcion: descripcion,
                fecha: fecha
            },
            success: function (response) {
                console.log(response);
                $('#modalActividad').modal('hide'); // Ocultar el modal después de la inserción
                alert("Actividad guardada exitosamente");
                location.reload(true);
            },
            error: function (error) {
                console.error(error);
                alert("Hubo un error al guardar la actividad");
            }
        });
    });
});

</script>

</body>
</html>
