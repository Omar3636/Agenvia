<?php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();
}

$errores = $_SESSION['errores_hotel'] ?? [];
$datosPrevios = $_SESSION['datos_hotel'] ?? [];
unset($_SESSION['errores_hotel'], $_SESSION['datos_hotel']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Hoteles</title>
    <style>
    <style>
        .menu {padding: 15px; margin-bottom: 20px;}
        .menu a {padding: 10px 20px; margin-right: 10px; border-radius: 5px;}
        .menu a:hover {background: gray;}
        .formulario {max-width:500px; margin:20px auto; padding:20px; border:1px solid gray;}
        .campo {margin-bottom:15px;}
        label {display:block; font-weight:bold; margin-bottom:5px;}
        input {width:100%; padding:8px; border:1px solid gray; border-radius:3px;}
        .error {color:red; font-size:12px; margin-top:5px; display:none;}
        .error-visible {display:block;}
        .error-servidor {background:pink; color:red; padding:10px; margin-bottom:15px; border-radius:3px;}
        button {background:green; color:white; padding:10px 20px; border:none; cursor:pointer;}
        .tabla {margin-top:30px;}
        table {width:100%; border-collapse:collapse;}
        th, td {border:1px solid gray; padding:8px; text-align:left;}
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
        <?php endif; ?>
    </div>

    <h1>Agregar Hoteles</h1>
    
    <?php if (!empty($errores)): ?>
        <div class="error-servidor">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['ok'])): ?>
        <div style="background:green; color:black; padding:10px; margin-bottom:15px; border-radius:3px;">
            Hotel guardado correctamente
        </div>
    <?php endif; ?>

    <div class="formulario">
        <h2>Agregar Nuevo Hotel</h2>
        <form id="formHotel" method="POST" action="GuardarHotel.php" onsubmit="return validarFormularioHotel()">
            <div class="campo">
                <label for="nombre">Nombre del hotel:</label>
                <input type="text" id="nombre" name="nombre" maxlength="150" 
                       value="<?php echo htmlspecialchars($datosPrevios['nombre'] ?? ''); ?>">
                <div id="error-nombre" class="error">El nombre es obligatorio</div>
            </div>
            
            <div class="campo">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" maxlength="200"
                       value="<?php echo htmlspecialchars($datosPrevios['ubicacion'] ?? ''); ?>">
                <div id="error-ubicacion" class="error">La ubicación es obligatoria</div>
            </div>
            
            <div class="campo">
                <label for="habitaciones">Habitaciones disponibles:</label>
                <input type="number" id="habitaciones" name="habitaciones" min="1" max="1000"
                       value="<?php echo htmlspecialchars($datosPrevios['habitaciones'] ?? '10'); ?>">
                <div id="error-habitaciones" class="error">Las habitaciones deben ser entre 1 y 1000</div>
            </div>
            
            <div class="campo">
                <label for="tarifa">Tarifa por noche:</label>
                <input type="number" id="tarifa" name="tarifa" min="0" step="0.01"
                       value="<?php echo htmlspecialchars($datosPrevios['tarifa'] ?? '50'); ?>">
                <div id="error-tarifa" class="error">La tarifa debe ser mayor o igual a 0</div>
            </div>
            
            <button type="submit">Guardar Hotel</button>
        </form>
    </div>

    <div class="tabla">
        <h2>Hoteles Registrados</h2>
        <?php
        $bd = new ConexionBD();
        $resultado = $bd->consultaSegura("SELECT * FROM HOTEL ORDER BY nombre");
        
        if ($resultado && $resultado->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Ubicación</th><th>Habitaciones</th><th>Tarifa noche</th></tr>";
            
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['id_hotel'] . "</td>";
                echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($fila['ubicacion']) . "</td>";
                echo "<td>" . $fila['habitaciones_disponibles'] . "</td>";
                echo "<td>$" . number_format($fila['tarifa_noche'], 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay hoteles registrados.</p>";
        }
        ?>
    </div>

    <script>
    function validarFormularioHotel() {
        let valido = true;
        
        document.querySelectorAll('.error').forEach(e => e.classList.remove('error-visible'));
        
        const nombre = document.getElementById('nombre').value.trim();
        if (nombre === '') {
            document.getElementById('error-nombre').classList.add('error-visible');
            valido = false;
        }
        
        const ubicacion = document.getElementById('ubicacion').value.trim();
        if (ubicacion === '') {
            document.getElementById('error-ubicacion').classList.add('error-visible');
            valido = false;
        }
        
        const habitaciones = document.getElementById('habitaciones').value;
        if (habitaciones === '' || habitaciones < 1 || habitaciones > 1000) {
            document.getElementById('error-habitaciones').classList.add('error-visible');
            valido = false;
        }
        
        const tarifa = document.getElementById('tarifa').value;
        if (tarifa === '' || tarifa < 0) {
            document.getElementById('error-tarifa').classList.add('error-visible');
            valido = false;
        }
        
        return valido;
    }
    </script>
</body>

</html>
