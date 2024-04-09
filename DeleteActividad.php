<?php
include('Conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id_actividad = $_POST['id_actividad'];

    $sql = "DELETE FROM ActividadesAcademicas WHERE ID_actividadesacademicas = ?";

    if ($stmt = sqlsrv_prepare($conn, $sql, array(&$id_actividad))) {
        if (sqlsrv_execute($stmt)) {
            $mensaje = "<p>Registro eliminado con éxito.</p>";
            
            // Redirigir a academicas.php después de 2 segundos
            header("refresh:2;url=academicas.php");
            exit; // Asegúrate de detener la ejecución del script después de la redirección
        } else {
            $mensaje = "<p>Error al eliminar el registro: " . print_r(sqlsrv_errors(), true) . "</p>";
        }

        sqlsrv_free_stmt($stmt);
    } else {
        $mensaje = "<p>Error al preparar la consulta: " . print_r(sqlsrv_errors(), true) . "</p>";
    }

    sqlsrv_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultado de Eliminación de Actividad</title>
</head>
<body>
    <?php if (isset($mensaje)) {
        echo $mensaje;
    } ?>
</body>
</html>
