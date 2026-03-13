<?php

$tiempoSesion = 3600;

session_set_cookie_params([
    'lifetime' => $tiempoSesion,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

$tiempoMaximoInactividad = 1800;
if (isset($_SESSION['ULTIMA_ACTIVIDAD'])) {
    $tiempoInactivo = time() - $_SESSION['ULTIMA_ACTIVIDAD'];
    if ($tiempoInactivo > $tiempoMaximoInactividad) {
        session_unset();
        session_destroy();
        header("Location: login.php?expirado=1");
        exit();
    }
}

$_SESSION['ULTIMA_ACTIVIDAD'] = time();
if (isset($_SESSION['USER_AGENT']) && $_SESSION['USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php?seguridad=1");
    exit();
}

if (!isset($_SESSION['CONTADOR_PETICIONES'])) {
    $_SESSION['CONTADOR_PETICIONES'] = 0;
}
$_SESSION['CONTADOR_PETICIONES']++;

if ($_SESSION['CONTADOR_PETICIONES'] % 10 == 0) {
    session_regenerate_id(true);
}
?>