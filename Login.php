<?php
include 'Conexion.php';
session_start(); // Asegurándonos de que la sesión esté iniciada al principio

$mensajeError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '';
    $contrasenas = isset($_POST['contrasenas']) ? $_POST['contrasenas'] : '';

    if (empty($nombre) || empty($contrasenas)) {
        $mensajeError = "Por favor, ingrese nombre de usuario y contraseña.";
    } else {
        $query = "SELECT TOP 1 * FROM dbo.Usuarios WHERE nombre = ?";
        $params = array($nombre);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt !== false) {
            if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $hashContrasena = $row['contrasenas'];

                if (password_verify($contrasenas, $hashContrasena)) {
                    // El inicio de sesión es exitoso
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $row['ID_usuarios']; // Almacenar el ID del usuario en la sesión
        
                    $tiposusuariosid = $row['tiposusuariosid']; // Asumiendo que tiposusuariosid es el campo correcto en tu tabla
                    $queryTipo = "SELECT tipo FROM dbo.Tiposusuarios WHERE ID_tiposusuarios = ?";
                    $stmtTipo = sqlsrv_query($conn, $queryTipo, array($tiposusuariosid));
        
                    if (sqlsrv_fetch($stmtTipo)) {
                        $tipo = sqlsrv_get_field($stmtTipo, 0);
                        $_SESSION['user_type'] = $tipo; // Aquí guardas el valor de 'tipo' en lugar del ID
        
                        if ($tipo == 'Admi') {
                            header("Location: Admin.php");
                            exit();
                        }
                    }

                    // Registra el inicio de sesión en la tabla "Login" con la fecha y hora actual
                    $queryInsertLogin = "INSERT INTO dbo.Login (usuariosid, fecha_hora) VALUES (?, GETDATE())";
                    $paramsInsertLogin = array($idUsuario);
                    $stmtInsertLogin = sqlsrv_query($conn, $queryInsertLogin, $paramsInsertLogin);

                    if ($stmtInsertLogin === false) {
                        // Maneja errores en la inserción si es necesario
                        die(print_r(sqlsrv_errors(), true));
                    }

                    // Si el usuario no es un 'Admi', lo rediriges a Index.php
                    header("Location: Index.php");
                    exit();
                } else {
                    $mensajeError = "Nombre de usuario o contraseña incorrectos.";
                }
            } else {
                $mensajeError = "Nombre de usuario o contraseña incorrectos.";
            }
        } else {
            die(print_r(sqlsrv_errors(), true)); // Maneja errores en la consulta si es necesario
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
                <div class="card-header text-center">Iniciar sesión</div>
                <div class="card-body">
                    <?php
                    if ($mensajeError != "") {
                        echo "<div class='alert alert-danger'>" . $mensajeError . "</div>";
                    }
                    ?>
                    <form action="Login.php" method="post">
                        <div class="form-group">
                            <label for="nombre">Usuario:</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="contrasena">Contraseña:</label>
                            <input type="password" name="contrasenas" class="form-control" required>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" value="Iniciar sesión" class="btn btn-primary">
                        </div>
                    </form>
                    <div class="text-center">
                        <label for="registrarse">No tienes cuenta?</label>
                        <a href="Registrarse.php" class="btn btn-link">Registrate</a>
                    </div>
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
