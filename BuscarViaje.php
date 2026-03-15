<?php
require_once 'ConfigurarSesion.php';
require_once 'FiltroViaje.php';
require_once 'ConexionBD.php';

$filtro = new FiltroViajes();

if ($_POST) {
    $filtro->setFiltros($_POST);
}

if (isset($_GET['limpiar'])) {
    $filtro->limpiarFiltros();
}

$bd = new ConexionBD();

$sql = "SELECT * FROM VUELO WHERE 1=1";
$params = [];
$tipos = "";

if (!empty($filtro->ciudad)) {
    $sql .= " AND ciudad LIKE ?";
    $params[] = "%" . $filtro->ciudad . "%";
    $tipos .= "s";
}

if (!empty($filtro->fecha_viaje)) {
    $sql .= " AND fecha >= ?";
    $params[] = $filtro->fecha_viaje;
    $tipos .= "s";
}

$resultado = $bd->consultaSegura($sql, $params, $tipos);

$resultados = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $resultados[] = $fila;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Buscador de Viajes</title>
    <style>
        .filtros { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .filtros input, .filtros select { padding: 8px; margin: 5px; border: 1px solid #ddd; }
        .filtros button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .resultado { background: white; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .resultado:hover { background: #f9f9f9; }
        .precio { color: green; font-weight: bold; font-size: 1.2em; }
    </style>
        <style>
        .menu {
            padding: 15px;
            margin-bottom: 20px;
        }
        .menu a {
            padding: 10px 20px;
            margin-right: 10px;
            border-radius: 5px;
        }
        .menu a:hover {
            background: #777;
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
        
        <?php if (isset($_SESSION['USUARIO'])): ?>
            | <a href="PanelUsuario.php">Panel de <?php echo $_SESSION['USUARIO']; ?></a>
            | <a href="Logout.php">Cerrar Sesión</a>
            | <span>(<?php echo $_SESSION['USUARIO']; ?>)</span>
        <?php else: ?>
            | <a href="Login.php">Iniciar Sesión</a>
        <?php endif; ?>
    </div>

    <h1>Buscador de Viajes</h1>
    
    <div class="filtros">
        <form method="POST">
            <input type="text" name="hotel" placeholder="Nombre del hotel" 
                   value="<?php echo $filtro->nombre_hotel; ?>">
            
            <select name="ciudad">
                <option value="">Todas las ciudades</option>
                <?php foreach ($filtro->getCiudadesDisponibles() as $ciudad): ?>
                    <option value="<?php echo $ciudad; ?>" 
                        <?php echo ($filtro->ciudad == $ciudad) ? 'selected' : ''; ?>>
                        <?php echo $ciudad; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="pais">
                <option value="">Todos los países</option>
                <?php foreach ($filtro->getPaisesDisponibles() as $pais): ?>
                    <option value="<?php echo $pais; ?>" 
                        <?php echo ($filtro->pais == $pais) ? 'selected' : ''; ?>>
                        <?php echo $pais; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="date" name="fecha" value="<?php echo $filtro->fecha_viaje; ?>">
            
            <select name="duracion">
                <option value="0">Cualquier duración</option>
                <?php foreach ($filtro->getDuracionesDisponibles() as $duracion): ?>
                    <option value="<?php echo $duracion; ?>" 
                        <?php echo ($filtro->duracion_viaje == $duracion) ? 'selected' : ''; ?>>
                        <?php echo $duracion; ?> días
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Buscar Viajes</button>
            <a href="?limpiar=1" style="margin-left: 10px;">Limpiar filtros</a>
        </form>
    </div>
    
    <?php $filtro->mostrarFiltros(); ?>
    
    <h2>Resultados (<?php echo count($resultados); ?> viajes encontrados)</h2>
    
<?php if (empty($resultados)): ?>
    <div style="background: #fff3cd; padding: 20px; text-align: center;">
        No se encontraron vuelos con esos filtros.
    </div>
<?php else: ?>
    <?php foreach ($resultados as $vuelo): ?>
        <div class="resultado">
            <h3><?php echo $vuelo['origen']; ?> → <?php echo $vuelo['destino']; ?></h3>
            <p>
                <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($vuelo['fecha'])); ?><br>
                <strong>Plazas disponibles:</strong> <?php echo $vuelo['plazas_disponibles']; ?><br>
                <span class="precio">Precio: $<?php echo number_format($vuelo['precio'], 0, ',', '.'); ?></span>
            </p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>