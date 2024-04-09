<?php
$serverName = "PC_VALISAMA";
$connectionOptions = array(
    "Database" => "BD_Agenda",
    "Uid" => "", 
    "PWD" => "",
    "CharacterSet" => "UTF-8"
);

// Establece la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>