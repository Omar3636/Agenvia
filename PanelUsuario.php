<?php
require_once 'ConfigurarSesion.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Usuario</title>
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
    
    <h1>Bienvenido, <?php echo $_SESSION['USUARIO']; ?>!</h1>
    
    <div class="seguridad-box">
        <p><strong>Tiempo restante de sesión:</strong> 
            <?php
            $inactivo = time() - $_SESSION['ULTIMA_ACTIVIDAD'];
            $restante = max(0, 1800 - $inactivo);
            echo round($restante / 60) . " minutos";
            ?>
        </p>
    </div>
    
</body>
</html>