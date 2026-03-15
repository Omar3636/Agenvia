<?php
// GuardarReserva.php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();
}

$errores = [];
$datos = $_POST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validar cliente
    $id_cliente = trim($datos['id_cliente'] ?? '');
    if (empty($id_cliente)) {
        $errores[] = "El cliente es obligatorio";
    } elseif (strlen($id_cliente) > 50) {
        $errores[] = "El cliente no puede exceder 50 caracteres";
    }
    
    // Validar vuelo
    $id_vuelo = filter_var($datos['id_vuelo'] ?? '', FILTER_VALIDATE_INT);
    if ($id_vuelo === false || $id_vuelo <= 0) {
        $errores[] = "Debes seleccionar un vuelo válido";
    }
    
    // Validar hotel (opcional)
    $id_hotel = !empty($datos['id_hotel']) ? filter_var($datos['id_hotel'], FILTER_VALIDATE_INT) : null;
    
    // Validar fecha de reserva
    $fecha_reserva = $datos['fecha_reserva'] ?? '';
    if (empty($fecha_reserva)) {
        $errores[] = "La fecha de reserva es obligatoria";
    } else {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_reserva);
        if (!$fecha_obj) {
            $errores[] = "La fecha no es válida";
        }
    }
    
    // Si no hay errores, insertar en BD
    if (empty($errores)) {
        $bd = new ConexionBD();
        
        $sql = "INSERT INTO RESERVA (id_cliente, fecha_reserva, id_vuelo, id_hotel) 
                VALUES (?, ?, ?, ?)";
        
        $id_insertado = $bd->ejecutarSeguro($sql, 
            [$id_cliente, $fecha_reserva, $id_vuelo, $id_hotel], 
            "ssii"
        );
        
        if ($id_insertado) {
            header("Location: RegistrarViaje.php?ok=1");
            exit();
        } else {
            $errores[] = "Error al guardar en la base de datos";
        }
    }
}

// Si hay errores, guardar en sesión y volver
if (!empty($errores)) {
    $_SESSION['errores_reserva'] = $errores;
    $_SESSION['datos_reserva'] = $datos;
    header("Location: RegistrarViaje.php?error=1");
    exit();
}
?>