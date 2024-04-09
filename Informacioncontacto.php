<?php
include 'Conexion.php';

$mensajeError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogiendo los datos POST
    $usuariosid = isset($_POST["usuariosid"]) ? $_POST["usuariosid"] : '';
    $codigo_postal = isset($_POST["codigo_postal"]) ? $_POST["codigo_postal"] : '';
    $municipio = isset($_POST["municipio"]) ? htmlspecialchars($_POST["municipio"]) : '';
    $estado = isset($_POST["estado"]) ? htmlspecialchars($_POST["estado"]) : '';
    $ciudad = isset($_POST["ciudad"]) ? htmlspecialchars($_POST["ciudad"]) : '';
    $colonia = isset($_POST["colonia"]) ? htmlspecialchars($_POST["colonia"]) : '';
    $calle_principal = isset($_POST["calle_principal"]) ? htmlspecialchars($_POST["calle_principal"]) : '';
    $primer_cruzamiento = isset($_POST["primer_cruzamiento"]) ? htmlspecialchars($_POST["primer_cruzamiento"]) : '';
    $segundo_cruzamiento = isset($_POST["segundo_cruzamiento"]) ? htmlspecialchars($_POST["segundo_cruzamiento"]) : '';
    $referencias = isset($_POST["referencias"]) ? htmlspecialchars($_POST["referencias"]) : '';
    $numero_exterior = isset($_POST["numero_exterior"]) ? $_POST["numero_exterior"] : '';
    $numero_interior = isset($_POST["numero_interior"]) ? $_POST["numero_interior"] : '';

    // Realiza las validaciones necesarias aquí antes de realizar la inserción en la base de datos
    if (empty($usuariosid) || empty($municipio) || empty($estado) || empty($ciudad) || empty($colonia) || empty($calle_principal ) || empty($primer_cruzamiento) || empty($segundo_cruzamiento) || empty($referencias)) {
        $mensajeError = "Por favor, complete todos los campos obligatorios.";
    } elseif (!is_numeric($codigo_postal) || strlen($codigo_postal) !== 5) {
        $mensajeError = "Ingrese un código postal válido de 5 dígitos.";
    } elseif (!is_numeric($numero_exterior) && !empty($numero_exterior)) {
        $mensajeError = "El número exterior debe ser un valor numérico.";
    } elseif (!is_numeric($numero_interior) && !empty($numero_interior)) {
        $mensajeError = "El número interior debe ser un valor numérico.";
    } else {
        // Preparando la consulta SQL
        $query = "INSERT INTO [BD_Agenda].[dbo].[InformacionContacto] 
                  (usuariosid, codigo_postal, municipio, estado, ciudad, colonia, calle_principal, primer_cruzamiento, 
                   segundo_cruzamiento, referencias, numero_exterior, numero_interior) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = array($usuariosid, $codigo_postal, $municipio, $estado, $ciudad, $colonia, $calle_principal, 
                        $primer_cruzamiento, $segundo_cruzamiento, $referencias, 
                        $numero_exterior, $numero_interior);

        // Ejecutando la consulta SQL
        $stmt = sqlsrv_query($conn, $query, $params);

        // Verificando si la inserción fue exitosa
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            // Verificar si el usuario no es ADMIN (ID_tipousuario = 2)
            $queryTipoUsuario = "SELECT tiposusuariosid FROM dbo.Usuarios WHERE ID_usuarios = ?";
            $stmtTipoUsuario = sqlsrv_query($conn, $queryTipoUsuario, array($usuariosid));
        
            if ($stmtTipoUsuario !== false) {
                $rowTipoUsuario = sqlsrv_fetch_array($stmtTipoUsuario, SQLSRV_FETCH_ASSOC);
        
                if ($rowTipoUsuario['tiposusuariosid'] == 2) {
                    // Es ADMIN, redirigir a Login.php
                    header("Location: Login.php");
                    exit();
                }
            }
        
            // No es ADMIN, redirigir a InformacionAcademica_estudiante.php
            header("Location: InformacionAcademica_estudiante.php");
            exit();
        }
        }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Contacto</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <link href="Estilo2.css" rel="stylesheet"/><!-- Vinculación a un archivo CSS externo -->
</head>

<div class="topnav">
    <a class="logo"><img src="Imagenes/Tec.png" alt="Logo"></a>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Información de Contacto
                </div>
                <div class="card-body">
                    <?php
                    if ($mensajeError != "") {
                        echo "<div class='alert alert-danger'>" . $mensajeError . "</div>";
                    }
                    ?>
                    <form action="Informacioncontacto.php" method="post">
                        <input type="hidden" name="usuariosid" value="<?php echo $lastUser['ID_usuarios']; ?>">                    

                        <div class="form-group">
                            <label for="codigo_postal">Código Postal:</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" pattern="\d{5}" required title="Solo se aceptan 5 digitos" maxlength="5" minlength="5">
                        </div>

                        <script>
                            document.getElementById('codigo_postal').addEventListener('input', function (e) {
                                var x = e.target.value.replace(/\D/g, ''); // Remueve todos los caracteres no numéricos
                                e.target.value = x;
                            });
                        </script>

                        <div class="form-group">
                            <label for="municipio">Municipio:</label>
                            <input type="text" name="municipio" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado:</label>
                            <input type="text" name="estado" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="ciudad">Ciudad:</label>
                            <input type="text" name="ciudad" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="colonia">Colonia:</label>
                            <select name="colonia" class="form-control" required></select>
                        </div>

                        <div class="form-group">
                            <label for="calle_principal">Calle Principal:</label>
                            <input type="text" name="calle_principal" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="primer_cruzamiento">Primer Cruzamiento:</label>
                            <input type="text" name="primer_cruzamiento" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="segundo_cruzamiento">Segundo Cruzamiento:</label>
                            <input type="text" name="segundo_cruzamiento" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="referencias">Referencias:</label>
                            <input type="text" name="referencias" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="numero_exterior">Número Exterior:</label>
                            <input type="text" name="numero_exterior" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="numero_interior">Número Interior:</label>
                            <input type="text" name="numero_interior" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let timer;

    $('input[name="codigo_postal"]').on('keyup', function() {
        clearTimeout(timer);  // Limpiamos cualquier temporizador anterior
        
        timer = setTimeout(function() {
            let codigoPostal = $('input[name="codigo_postal"]').val();
            
            if (codigoPostal.length > 0) {
                $.ajax({
                    url: `http://api.geonames.org/postalCodeLookupJSON?postalcode=${codigoPostal}&country=MX&username=valisama`,
                    method: 'GET',
                    success: function(data) {
                        if (data && data.postalcodes.length > 0) {
                        let place = data.postalcodes[0];
                            $('input[name="municipio"]').val(place.adminName2 || '');
                            $('input[name="estado"]').val(place.adminName1 || '');
                            $('input[name="ciudad"]').val(place.adminName3 || '');

                            // Llenar el dropdown de colonia
                            let coloniaDropdown = $('select[name="colonia"]');
                            coloniaDropdown.empty();  // Limpiar opciones anteriores
                            data.postalcodes.forEach(function(place) {
                            coloniaDropdown.append($('<option>', {
                            value: place.placeName,
                            text: place.placeName
                                }));
                            });
                        }
                },
                    error: function() {
                        console.log('Error al obtener información del código postal.');
                    }
                });
            }
        }, 800);  // Retraso de 800 milisegundos
    });
});
</script>
</body>
</html>
