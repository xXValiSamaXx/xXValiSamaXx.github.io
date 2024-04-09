<?php
include 'Conexion.php';

// Llamar al procedimiento almacenado
$sql = "EXEC ActualizarPeriodos";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

echo "Procedimiento almacenado ejecutado con éxito.\n";

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
