<?php
require_once 'ConfigurarSesion.php';

function mensajeEmergente($texto) {
    echo "<div style='background:#e74c3c; padding:10px; margin:10px 0;'>";
    echo htmlspecialchars($texto);
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agencia Viajes</title>
    <style>
        .menu {padding: 15px; margin-bottom: 20px;}
        .menu a {padding: 10px 20px; margin-right: 10px; border-radius: 5px;
        }
        .menu a:hover {background: #777;}
    </style>
</head>
<body>

    <div class="menu">
        <a href="index.php">Inicio</a>
        <a href="BuscarViaje.php">Buscador de Viajes</a>
        <a href="RegistrarViaje.php">Registrar Viaje</a>
        <a href="FormularioVuelo.php">Gestionar Vuelos</a>
        <a href="FormularioHotel.php">Gestionar Hoteles</a>
        <a href="ReporteHoteles.php">Reporte Hoteles</a>
        
        <?php if (isset($_SESSION['USUARIO'])): ?>
            | <a href="PanelUsuario.php">Panel de <?php echo $_SESSION['USUARIO']; ?></a>
            | <a href="Logout.php">Cerrar Sesión</a>
            | <span>(<?php echo $_SESSION['USUARIO']; ?>)</span>
        <?php else: ?>
            | <a href="Login.php">Iniciar Sesión</a>
        <?php endif; ?>
    </div>

    <h1>Agenvia</h1>
    <?php mensajeEmergente("¡Oferta Viaje a México, últimas 24 Horas!"); ?>

    <form action="procesar_formulario.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre"><br>

        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo"><br>

        <input type="submit" value="Enviar">
    </form>
</body>
</html>