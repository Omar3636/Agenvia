<?php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();
}

$bd = new ConexionBD();

// Desarrollo de consulta avanzada...
$sql = "SELECT 
            h.id_hotel,
            h.nombre AS nombre_hotel,
            h.ubicacion,
            h.habitaciones_disponibles,
            h.tarifa_noche,
            COUNT(r.id_reserva) AS total_reservas
        FROM HOTEL h
        LEFT JOIN RESERVA r ON h.id_hotel = r.id_hotel
        GROUP BY h.id_hotel, h.nombre, h.ubicacion, h.habitaciones_disponibles, h.tarifa_noche
        HAVING COUNT(r.id_reserva) > 2
        ORDER BY total_reservas DESC";

$resultado = $bd->consultaSegura($sql);

$sql_total = "SELECT COUNT(*) as total FROM RESERVA";
$total_reservas = $bd->consultaSegura($sql_total);
$total = $total_reservas->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoteles con más reservas</title>
    <style>
        .menu {padding:15px; margin-bottom:20px;}
        .menu a {padding:10px 20px; margin-right:10px; text-decoration:none;}
        .reporte {max-width:1000px; margin:20px auto;}
        .resumen {background:#e8f4fd; padding:15px; border-radius:5px; margin-bottom:20px;}
        table {width:100%; border-collapse:collapse; margin-top:20px;}
        th, td {border:1px solid gray; padding:8px; text-align:left;}
        .destacado {font-weight:bold; color:black;}
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
        <?php endif; ?>
    </div>

    <div class="reporte">
        <h1>Hoteles con más de 2 reservas</h1>
        
        <div class="resumen">
            <p><strong>Total de reservas en el sistema:</strong> <?php echo $total; ?></p>
            <p><strong>Hoteles que cumplen el criterio:</strong> <?php echo $resultado->num_rows; ?></p>
        </div>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Ubicación</th>
                        <th>Habitaciones</th>
                        <th>Tarifa/noche</th>
                        <th>Total Reservas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($hotel = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($hotel['nombre_hotel']); ?></strong></td>
                            <td><?php echo htmlspecialchars($hotel['ubicacion']); ?></td>
                            <td><?php echo $hotel['habitaciones_disponibles']; ?></td>
                            <td>$<?php echo number_format($hotel['tarifa_noche'], 2); ?></td>
                            <td class="destacado"><?php echo $hotel['total_reservas']; ?> reservas</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="background:pink; padding:20px; text-align:center; border-radius:5px;">
                <p>No hay hoteles con más de 2 reservas en este momento.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>