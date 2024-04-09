<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Página Web</title>
    <link href="Estilos.css" rel="stylesheet"/><!-- Vinculación a un archivo CSS externo -->
</head>
<body>

<!-- Barra de navegación con logo y botón -->
<div class="topnav">
    <a class="logo" href="#"><img src="Imagenes/Tec.png" alt="Logo"></a>
    <?php
    session_start();

    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Admi') {
        echo '<button class="button" onclick="return confirmLogout();"><a href="Logout.php" style="color: white; text-decoration: none;">Admin</a></button>';
    } else {
        echo '<button class="button"><a href="Login.php" style="color: white; text-decoration: none;">Ingresar</a></button>';
    }
    ?>
    <script>
    function confirmLogout() {
        return window.confirm("¿Estás seguro de que deseas cerrar sesión?");
    }
    </script>
</div>

<div class="card-container">
    <div class="card" id="card7">
        <a href="Carrera.php" class="card-link">
            <button class="image-button7">
                <img src="Imagenes/carreras.jpg" alt="" class="card7">
            </button>
        </a>
        <span class="image-text7">Carreras</span>
    </div>

    <div class="card" id="card8">
        <a href="Materia.php" class="card-link">
            <button class="image-button8">
                <img src="Imagenes/Materias.jpg" alt="" class="card8">
            </button>
        </a>
        <span class="image-text8">Materias</span>
    </div>
</div>

</body>
</html>
