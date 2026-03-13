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
    
    $nombre = trim($datos['nombre'] ?? '');
    if (empty($nombre)) {
        $errores[] = "El nombre del hotel es obligatorio";
    } elseif (strlen($nombre) > 150) {
        $errores[] = "El nombre no puede exceder 150 caracteres";
    }
    
    $ubicacion = trim($datos['ubicacion'] ?? '');
    if (empty($ubicacion)) {
        $errores[] = "La ubicación es obligatoria";
    } elseif (strlen($ubicacion) > 200) {
        $errores[] = "La ubicación no puede exceder 200 caracteres";
    }
    
    $habitaciones = filter_var($datos['habitaciones'] ?? '', FILTER_VALIDATE_INT);
    if ($habitaciones === false || $habitaciones < 1 || $habitaciones > 1000) {
        $errores[] = "Las habitaciones deben ser un número entre 1 y 1000";
    }
    
    $tarifa = filter_var($datos['tarifa'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($tarifa === false || $tarifa < 0) {
        $errores[] = "La tarifa debe ser un número mayor o igual a 0";
    }
    
    if (empty($errores)) {
        $bd = new ConexionBD();
        
        $sql = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) 
                VALUES (?, ?, ?, ?)";
        
        $id_insertado = $bd->ejecutarSeguro($sql, 
            [$nombre, $ubicacion, $habitaciones, $tarifa], 
            "ssid"
        );
        
        if ($id_insertado) {
            header("Location: FormularioHotel.php?ok=1");
            exit();
        } else {
            $errores[] = "Error al guardar en la base de datos";
        }
    }
}

if (!empty($errores)) {
    session_start();
    $_SESSION['errores_hotel'] = $errores;
    $_SESSION['datos_hotel'] = $datos;
    header("Location: FormularioHotel.php?error=1");
    exit();
}
?>