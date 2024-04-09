<?php
include 'Conexion.php';

$mensaje = ""; // Inicializar la variable $mensaje

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioId = isset($_POST["usuarioId"]) ? $_POST["usuarioId"] : '';
    $periodoId = isset($_POST["periodoId"]) ? $_POST["periodoId"] : '';
    $carreraId = isset($_POST["carreraId"]) ? $_POST["carreraId"] : '';
    $numcontrol = isset($_POST["numcontrol"]) ? $_POST["numcontrol"] : '';
    $semestre = isset($_POST["semestre"]) ? $_POST["semestre"] : '';
    $promedio = isset($_POST["promedio"]) ? $_POST["promedio"] : '';
    
    // Realiza las validaciones necesarias aquí antes de realizar la inserción en la base de datos
    if (empty($usuarioId) || empty($periodoId) || empty($carreraId) || empty($numcontrol) || empty($semestre) || empty($promedio)) {
        $mensaje = "Por favor, complete todos los campos obligatorios.";
    } elseif (!is_numeric($semestre) || $semestre < 1 || $semestre > 12) {
        $mensaje = "El semestre debe ser un número válido entre 1 y 12.";
    } elseif (!preg_match('/^\d{2}\.\d$/', $promedio)) {
        $mensaje = "El promedio debe tener el formato correcto, por ejemplo, 90.0.";
    } else {
        // Preparando la consulta SQL
        $query = "INSERT INTO [BD_Agenda].[dbo].[InformacionAcademica_estudiante] ([usuariosid], [periodoid], [carreraId], [numcontrol], [semestre], [promedio]) VALUES (?, ?, ?, ?, ?, ?)";
        $params = array($usuarioId, $periodoId, $carreraId, $numcontrol, $semestre, $promedio);
        
        // Ejecutando la consulta SQL
        $stmt = sqlsrv_query($conn, $query, $params);

        // Verificando si la inserción fue exitosa
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            header("Location: Login.php");
            exit();
        }
    }
}

// Obtener las Usuarios
$query = "SELECT * FROM Usuarios";
$stmt = sqlsrv_query($conn, $query);
$usuarios = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $usuarios[] = $row;
}

// Obtener los Periodos
$query = "SELECT * FROM Periodo";
$stmt = sqlsrv_query($conn, $query);
$periodos = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $periodos[] = $row;
}

// Obtener las Carreras
$query = "SELECT * FROM Carrera";
$stmt = sqlsrv_query($conn, $query);
$carreras = [];
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $carreras[] = $row;
}

// Obtener el ID del último usuario registrado
$lastUserIdQuery = "SELECT TOP 1 ID_usuarios FROM dbo.Usuarios ORDER BY ID_usuarios DESC";
$lastUserIdStmt = sqlsrv_query($conn, $lastUserIdQuery);
$lastUser = sqlsrv_fetch_array($lastUserIdStmt, SQLSRV_FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información Académica</title>
    <link href="Estilo2.css" rel="stylesheet"/><!-- Vinculación a un archivo CSS externo -->
</head>

<div class="topnav">
    <a class="logo"><img src="Imagenes/Tec.png" alt="Logo"></a>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">Información Académica</div>
                <div class="card-body">
                    <?php
                    if ($mensaje != "") {
                        echo "<div class='alert alert-success'>" . $mensaje . "</div>";
                    }
                    ?>
                    <form action="InformacionAcademica_estudiante.php" method="post">
                        <div class="form-group">

                        <input type="hidden" name="usuarioId" value="<?php echo $lastUser['ID_usuarios']; ?>">

                        <label for="periodoid">Periodo:</label>
                        <select name="periodoId" id="periodoId" class="form-control">
                        <?php foreach($periodos as $periodo): ?>
                        <option value="<?= $periodo['ID_periodo'] ?>"><?= $periodo['nombre'] ?></option>
                        <?php endforeach; ?>
                        </select>
                        <br></br>
                        
                        <label for="carreraid">Carrera:</label>
                        <select name="carreraId" id="carreraId" class="form-control">
                            <?php foreach($carreras as $carrera): ?>
                                <option value="<?= $carrera['ID_carrera'] ?>"><?= $carrera['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <br></br>

                        <div class="form-group">
                            <label for="numcontrol">Número de Control:</label>
                            <input type="text" name="numcontrol" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="semestre">Semestre:</label>
                            <select name="semestre" id="semestre" class="form-control">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="promedio">Promedio (2 dígitos y 1 decimal, por ejemplo, 90.0):</label>
                            <input type="text" name="promedio" id="promedio" class="form-control" pattern="^\d{2}\.\d$">
                            <small>Ejemplo válido: 90.0</small>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" value="Agregar" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>