<?php
require_once 'ConfigurarSesion.php';

$usuarios_validos = [
    'cliente1' => 'contra123',
    'cliente2' => 'contra321'
];

$error = '';

if ($_POST) {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] == $password) {
        
        session_regenerate_id(true);
        
        $_SESSION['USUARIO'] = $usuario;
        $_SESSION['ULTIMA_ACTIVIDAD'] = time();
        $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['COOKIE_CONFIG'] = [
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        
        header("Location: PanelUsuario.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Agencia de Viajes</title>
</head>

<body>
    <h1>Agencia de Viajes - Login</h1>
    
    <div class="login-box">
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['expirado'])): ?>
            <div class="error">Sesión expirada por inactividad.</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['seguridad'])): ?>
            <div class="error">Alerta de seguridad: Se detectó un cambio en tu navegador.</div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Usuario:</label>
            <input type="text" name="usuario" required>
            
            <label>Contraseña:</label>
            <input type="password" name="password" required>
            
            <input type="submit" value="Ingresar">
        </form>
        
        <div style="margin-top: 15px; text-align: center;">
            <small>Usuarios para probar: cliente1/contra123 o cliente2/contra321</small>
        </div>
    </div>
    
    <p><a href="index.php">Volver al inicio</a></p>
</body>
</html>