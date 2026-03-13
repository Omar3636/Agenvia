<?php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();
}

$errores = [];
$datos = $_POST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $origen = trim($datos['origen'] ?? '');
    if (empty($origen)) {
        $errores[] = "El origen es obligatorio";
    } elseif (strlen($origen) > 100) {
        $errores[] = "El origen no puede exceder 100 caracteres";
    }
    
    $destino = trim($datos['destino'] ?? '');
    if (empty($destino)) {
        $errores[] = "El destino es obligatorio";
    } elseif (strlen($destino) > 100) {
        $errores[] = "El destino no puede exceder 100 caracteres";
    }
    
    $fecha = $datos['fecha'] ?? '';
    if (empty($fecha)) {
        $errores[] = "La fecha es obligatoria";
    } else {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
        $hoy = new DateTime('today');
        if (!$fecha_obj || $fecha_obj < $hoy) {
            $errores[] = "La fecha debe ser hoy o futura";
        }
    }
    
    $plazas = filter_var($datos['plazas'] ?? '', FILTER_VALIDATE_INT);
    if ($plazas === false || $plazas < 1 || $plazas > 500) {
        $errores[] = "Las plazas deben ser un número entre 1 y 500";
    }
    
    $precio = filter_var($datos['precio'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($precio === false || $precio < 0) {
        $errores[] = "El precio debe ser un número mayor o igual a 0";
    }
    
    if (empty($errores)) {
        $bd = new ConexionBD();
        
        $sql = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) 
                VALUES (?, ?, ?, ?, ?)";
        
        $id_insertado = $bd->ejecutarSeguro($sql, 
            [$origen, $destino, $fecha, $plazas, $precio], 
            "sssid"
        );
        
        if ($id_insertado) {
            header("Location: FormularioVuelo.php?ok=1");
            exit();
        } else {
            $errores[] = "Error al guardar en la base de datos";
        }
    }
}

if (!empty($errores)) {
    session_start();
    $_SESSION['errores_vuelo'] = $errores;
    $_SESSION['datos_vuelo'] = $datos;
    header("Location: FormularioVuelo.php?error=1");
    exit();
}
?>