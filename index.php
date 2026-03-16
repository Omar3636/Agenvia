<?php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

function mensajeEmergente($texto) {
    echo "<div style='background:#e74c3c; padding:10px; margin:10px 0;'>";
    echo htmlspecialchars($texto);
    echo "</div>";
}

$resultados = [];
$busqueda_realizada = false;

if (isset($_GET['buscar']) && !empty($_GET['destino'])) {
    $busqueda_realizada = true;
    $destino = $_GET['destino'];
    
    $bd = new ConexionBD();
    $sql = "SELECT * FROM VUELO WHERE destino LIKE ? AND fecha >= CURDATE() ORDER BY fecha";
    $params = ["%$destino%"];
    $resultado = $bd->consultaSegura($sql, $params, "s");
    
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $resultados[] = $fila;
        }
    }
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
        .menu a {padding: 10px 20px; margin-right: 10px; border-radius: 5px;}
        .menu a:hover {background: gray;}
        
        .buscador-rapido {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .buscador-rapido input {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .buscador-rapido button {
            padding: 8px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .resultado-item {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .resultado-item:hover {
            background: #f9f9f9;
        }
        .precio {
            color: green;
            font-weight: bold;
        }
        .mensaje-no-resultados {
            background: #fff3cd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
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

    <div class="buscador-rapido">
        <h3>Buscar vuelos por destino</h3>
        <form method="GET">
            <input type="text" name="destino" placeholder="Ej: Guadalajara, Santiago, Cucuta..." 
                   value="<?php echo isset($_GET['destino']) ? htmlspecialchars($_GET['destino']) : ''; ?>">
            <button type="submit" name="buscar" value="1">Buscar</button>
        </form>
    </div>

    <?php if ($busqueda_realizada): ?>
        <h2>Resultados para "<?php echo htmlspecialchars($_GET['destino']); ?>"</h2>
        
        <?php if (empty($resultados)): ?>
            <div class="mensaje-no-resultados">
                No se encontraron vuelos para este destino.
            </div>
        <?php else: ?>
            <?php foreach ($resultados as $vuelo): ?>
                <div class="resultado-item">
                    <strong>✈️ <?php echo $vuelo['origen']; ?> → <?php echo $vuelo['destino']; ?></strong><br>
                    Fecha: <?php echo date('d/m/Y', strtotime($vuelo['fecha'])); ?><br>
                    Plazas: <?php echo $vuelo['plazas_disponibles']; ?><br>
                    <span class="precio">$<?php echo number_format($vuelo['precio'], 0, ',', '.'); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <h3>Newsletter</h3>
    <form action="procesar_formulario.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre"><br>

        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo"><br>

        <input type="submit" value="Enviar">
    </form>

</body>
</html>
