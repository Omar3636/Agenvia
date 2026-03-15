<?php
require_once 'ConfigurarSesion.php';
require_once 'FiltroViaje.php';

// Verificar sesión
if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();
}

$errores = [];
$datos = $_POST;

// VALIDACIONES DEL LADO DEL SERVIDOR
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validar hotel
    $hotel = trim($datos['hotel'] ?? '');
    if (empty($hotel)) {
        $errores[] = "El nombre del hotel es obligatorio";
    } elseif (strlen($hotel) > 150) {
        $errores[] = "El nombre del hotel no puede exceder 150 caracteres";
    }
    
    // Validar ciudad
    $ciudad = trim($datos['ciudad'] ?? '');
    if (empty($ciudad)) {
        $errores[] = "La ciudad es obligatoria";
    } elseif (strlen($ciudad) > 100) {
        $errores[] = "La ciudad no puede exceder 100 caracteres";
    }
    
    // Validar país
    $pais = trim($datos['pais'] ?? '');
    if (empty($pais)) {
        $errores[] = "El país es obligatorio";
    } elseif (strlen($pais) > 100) {
        $errores[] = "El país no puede exceder 100 caracteres";
    }
    
    // Validar fecha
    $fecha = $datos['fecha'] ?? '';
    if (empty($fecha)) {
        $errores[] = "La fecha de viaje es obligatoria";
    } else {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
        $hoy = new DateTime('today');
        if (!$fecha_obj || $fecha_obj < $hoy) {
            $errores[] = "La fecha debe ser hoy o futura";
        }
    }
    
    // Validar duración
    $duracion = filter_var($datos['duracion'] ?? '', FILTER_VALIDATE_INT);
    if ($duracion === false || $duracion < 1 || $duracion > 30) {
        $errores[] = "La duración debe ser un número entre 1 y 30 días";
    }
    
    // Si no hay errores, procesar los datos
    if (empty($errores)) {
        
        // Crear instancia de la clase y asignar valores
        $intencion_viaje = new FiltroViajes();
        $intencion_viaje->nombre_hotel = $hotel;
        $intencion_viaje->ciudad = $ciudad;
        $intencion_viaje->pais = $pais;
        $intencion_viaje->fecha_viaje = $fecha;
        $intencion_viaje->duracion_viaje = $duracion;
        
        // AQUÍ PODRÍAS GUARDAR EN BASE DE DATOS SI LO DESEAS
        // Por ahora solo mostramos confirmación
        
        // Redirigir a la página de confirmación con los datos
        session_start();
        $_SESSION['intencion_confirmada'] = [
            'hotel' => $hotel,
            'ciudad' => $ciudad,
            'pais' => $pais,
            'fecha' => $fecha,
            'duracion' => $duracion
        ];
        
        header("Location: GuardarIntencion.php?confirmacion=1");
        exit();
    }
}

// Si hay errores, guardar en sesión y volver al formulario
if (!empty($errores)) {
    session_start();
    $_SESSION['errores_intencion'] = $errores;
    $_SESSION['datos_intencion'] = $datos;
    header("Location: RegistrarViaje.php?error=1");
    exit();
}

// Si llegamos aquí con confirmacion=1, mostramos los datos guardados
$confirmacion = $_SESSION['intencion_confirmada'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Intención de Viaje</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .menu { 
            padding: 15px; 
            margin-bottom: 20px; 
            background: #f0f0f0;
        }
        .menu a { 
            padding: 10px 20px; 
            margin-right: 10px; 
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        .menu a:hover { 
            background: #777; 
            color: white;
        }
        .confirmacion-box { 
            background: #e8f4fd; 
            padding: 20px; 
            border-radius: 5px;
            max-width: 500px;
            margin: 20px auto;
        }
        .dato-item {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 3px;
        }
        .dato-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .dato-valor {
            color: #007bff;
        }
        .enlaces {
            margin-top: 20px;
            text-align: center;
        }
        .enlaces a {
            margin: 0 10px;
            color: #007bff;
            text-decoration: none;
        }
        .enlaces a:hover {
            text-decoration: underline;
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

    <?php if (isset($_GET['confirmacion']) && $confirmacion): ?>
        <h1 style="text-align: center;">✅ Intención de viaje registrada</h1>

        <div class="confirmacion-box">
            <h2 style="margin-top: 0;">Datos de tu viaje:</h2>
            
            <div class="dato-item">
                <span class="dato-label">Hotel:</span>
                <span class="dato-valor"><?php echo htmlspecialchars($confirmacion['hotel']); ?></span>
            </div>
            
            <div class="dato-item">
                <span class="dato-label">Ciudad:</span>
                <span class="dato-valor"><?php echo htmlspecialchars($confirmacion['ciudad']); ?></span>
            </div>
            
            <div class="dato-item">
                <span class="dato-label">País:</span>
                <span class="dato-valor"><?php echo htmlspecialchars($confirmacion['pais']); ?></span>
            </div>
            
            <div class="dato-item">
                <span class="dato-label">Fecha:</span>
                <span class="dato-valor">
                    <?php 
                    echo date('d/m/Y', strtotime($confirmacion['fecha']));
                    ?>
                </span>
            </div>
            
            <div class="dato-item">
                <span class="dato-label">Duración:</span>
                <span class="dato-valor"><?php echo $confirmacion['duracion']; ?> días</span>
            </div>
        </div>
        
        <?php 
        // Limpiar la sesión después de mostrar
        unset($_SESSION['intencion_confirmada']);
        ?>
        
    <?php else: ?>
        <!-- Si alguien accede directamente sin datos -->
        <div style="text-align: center; padding: 50px;">
            <h2>No hay datos de intención para mostrar</h2>
            <p><a href="RegistrarViaje.php">Registrar una nueva intención de viaje</a></p>
        </div>
    <?php endif; ?>
    
    <div class="enlaces">
        <a href="RegistrarViaje.php">← Registrar otra intención</a>
        <a href="index.php">← Volver al inicio</a>
        <a href="BuscarViaje.php">🔍 Buscar viajes</a>
    </div>
    
</body>
</html>