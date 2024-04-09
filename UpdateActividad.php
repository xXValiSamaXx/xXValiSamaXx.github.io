<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Actividad Académica</title>
    <!-- Agregar enlaces a los archivos CSS y JS de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <?php
        include('Conexion.php'); // Asegúrate de que este sea el nombre correcto y la ubicación de tu archivo de conexión

        // Verificar si se ha enviado el formulario de edición
        if(isset($_POST['editar'])) {
            $id_actividad = $_POST['id_actividad'];
            $descripcion = $_POST['descripcion'];
            $fecha = $_POST['fecha'];
        
            // Realizar la actualización del registro
            $sql = "UPDATE ActividadesAcademicas SET descripcion = ?, fecha = ? WHERE ID_actividadesacademicas = ?";
            $params = array($descripcion, $fecha, $id_actividad);
            $stmt = sqlsrv_query($conn, $sql, $params);
        
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                echo "<p>Registro actualizado con éxito.</p>";
                
                // Redirigir a academicas.php después de 2 segundos
                header("refresh:2;url=academicas.php");
                exit; // Asegúrate de detener la ejecución del script después de la redirección
            }
        }   

        // Obtener el ID de la actividad académica a editar (puedes recibirlo por GET o POST)
        $id_actividad = $_POST['id_actividad'];

        // Realiza una consulta para obtener los datos de la actividad académica
        $sql = "SELECT * FROM ActividadesAcademicas WHERE ID_actividadesacademicas = ?";
        $params = array($id_actividad);
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $registro = sqlsrv_fetch_array($stmt);

        if (!$registro) {
            echo "<p>La actividad académica no se encontró.</p>";
        } else {
            // Mostrar el formulario de edición
        ?>
        <h1>Editar Actividad Académica</h1>
        <form action="" method="POST">
            <input type="hidden" name="id_actividad" value="<?php echo $registro['ID_actividadesacademicas']; ?>">
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $registro['descripcion']; ?>">
            </div>
            
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $registro['fecha']->format('Y-m-d'); ?>">
            </div>

            <div class="mb-3">
            <button type="submit" class="btn btn-primary" name="editar">Guardar Cambios</button>
            </div>
        </form>
        <?php
        }
        
        // Cierra la conexión a la base de datos
        sqlsrv_close($conn);
        ?>
    </div>
</body>
</html>
