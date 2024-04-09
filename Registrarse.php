<?php
include 'Conexion.php';

$tiposUsuarios = array();
$mensajeError = "";

$query = "SELECT ID_tiposusuarios, tipo FROM dbo.Tiposusuarios";
$stmt = sqlsrv_query($conn, $query);

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $tiposUsuarios[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $tiposusuarioid = isset($_POST['tiposusuarioid']) ? $_POST['tiposusuarioid'] : '';

    if (empty($nombre) || empty($contrasena) || empty($tiposusuarioid)) {
        $mensajeError = "Por favor, complete todos los campos.";
    } else {
        // Verificar si el usuario ya existe
        $checkQuery = "SELECT * FROM dbo.Usuarios WHERE nombre = ?";
        $checkStmt = sqlsrv_query($conn, $checkQuery, array($nombre));

        if (sqlsrv_fetch($checkStmt)) {
            $mensajeError = "El nombre de usuario ya existe.";
        } else {
            // Hash de la contraseña antes de guardarla en la base de datos
            $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

            $query = "INSERT INTO dbo.Usuarios (nombre, contrasenas, tiposusuariosid) VALUES (?, ?, ?)";
            $params = array($nombre, $hashContrasena, $tiposusuarioid);
            $stmt = sqlsrv_query($conn, $query, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                header("Location: Informacionpersonal.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrarse</title>
    <link href="Estilo2.css" rel="stylesheet"/><!-- Vinculación a un archivo CSS externo -->
</head>

<div class="topnav">
    <a class="logo"><img src="Imagenes/Tec.png" alt="Logo"></a>
</div>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">Registrarse</div>
                <div class="card-body">
                    <?php
                    if ($mensajeError != "") {
                        echo "<div class='alert alert-danger'>" . $mensajeError . "</div>";
                    }
                    ?>
                    <form action="Registrarse.php" method="post">
                        <div class="form-group">
                            <label for="nombre">Nombre Usuario:</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="contrasena">Contraseña:</label>
                            <input type="password" name="contrasena" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="tiposusuarioid">Tipo de Usuario:</label>
                            <select name="tiposusuarioid" class="form-control">
                                <?php
                                foreach ($tiposUsuarios as $tipoUsuario) {
                                    echo "<option value='" . $tipoUsuario['ID_tiposusuarios'] . "'>" . $tipoUsuario['tipo'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" value="Registrarse" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS y Popper.js -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
