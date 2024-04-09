<?php
include 'Conexion.php';

$mensajeError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $usuariosid = isset($_POST['usuariosid']) ? $_POST['usuariosid'] : '';
    $nombres = isset($_POST['nombres']) ? htmlspecialchars($_POST['nombres']) : '';
    $primerapellido = isset($_POST['primerapellido']) ? htmlspecialchars($_POST['primerapellido']) : '';
    $segundoapellido = isset($_POST['segundoapellido']) ? htmlspecialchars($_POST['segundoapellido']) : '';
    $fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $RFC = isset($_POST['RFC']) ? $_POST['RFC'] : null;

    // Inicializar mensaje de error
    $mensajeError = '';

    // Validar la edad
    if (!empty($fecha_nacimiento)) {
        $fechaNacimiento = new DateTime($fecha_nacimiento);
        $fechaActual = new DateTime();
        $edad = $fechaActual->diff($fechaNacimiento)->y;
        
        if ($edad < 18) {
            $mensajeError = "Debes tener al menos 18 años para registrarte.";
        }
    } else {
        $mensajeError = "Por favor, ingrese su fecha de nacimiento.";
    }

    // Validar otros campos si la edad es adecuada
    if (empty($mensajeError)) {
        if (empty($nombres) || empty($primerapellido) || empty($segundoapellido) || empty($telefono) || empty($email)) {
            $mensajeError = "Por favor, complete todos los campos obligatorios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensajeError = "Ingrese una dirección de correo electrónico válida.";
        } 
    }

    // Procesar la inserción en la base de datos si no hay errores
    if (empty($mensajeError)) {
        $query = "INSERT INTO dbo.InformacionPersonal (usuariosid, nombres, primerapellido, segundoapellido, fecha_nacimiento, telefono, email, RFC) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array($usuariosid, $nombres, $primerapellido, $segundoapellido, $fecha_nacimiento, $telefono, $email, $RFC);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            header("Location: Informacioncontacto.php");
            exit();
        }
    } else {
        // Mostrar mensaje de error si existe
        echo $mensajeError;
    }
}

// Obtener el tipo de usuario recién registrado
$lastUserIdQuery = "SELECT TOP 1 ID_usuarios, tiposusuariosid FROM dbo.Usuarios ORDER BY ID_usuarios DESC";
$lastUserIdStmt = sqlsrv_query($conn, $lastUserIdQuery);
$lastUser = sqlsrv_fetch_array($lastUserIdStmt, SQLSRV_FETCH_ASSOC);
$esEstudiante = ($lastUser['tiposusuariosid'] == 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Información Personal</title>
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
                <div class="card-header text-center">Información Personal</div>
                <div class="card-body">
                    <?php
                    if ($mensajeError != "") {
                        echo "<div class='alert alert-danger'>" . $mensajeError . "</div>";
                    }
                    ?>
                    <form action="Informacionpersonal.php" method="post">
                        <input type="hidden" name="usuariosid" value="<?php echo $lastUser['ID_usuarios']; ?>">

                        <div class="form-group">
                            <label for="nombres">Nombres:</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="primerapellido">Primer Apellido:</label>
                            <input type="text" name="primerapellido" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="segundoapellido">Segundo Apellido:</label>
                            <input type="text" name="segundoapellido" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" pattern="\d{10}" required title="El número debe ser de 10 dígitos y contener solo números" maxlength="10" minlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                        </div>

                        <script>
                            document.getElementById('telefono').addEventListener('input', function (e) {
                                var x = e.target.value.replace(/\D/g, ''); // Remueve todos los caracteres no numéricos
                                e.target.value = x;
                            });
                        </script>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <?php
                        if (!$esEstudiante) {
                            echo '<div class="form-group">';
                            echo '<label for="RFC">RFC:</label>';
                            echo '<input type="text" name="RFC" class="form-control">';
                            echo '</div>';
                        }
                        ?>

                        <div class="form-group text-center">
                            <input type="submit" value="Guardar" class="btn btn-primary">
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