<?php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Vuelos</title>
    <style>
        .menu {padding: 15px; margin-bottom: 20px;}
        .menu a {padding: 10px 20px; margin-right: 10px; border-radius: 5px;}
        .menu a:hover {background: gray;}
        .formulario {max-width:500px; margin:20px auto; padding:20px; border:1px solid gray;}
        .campo {margin-bottom:15px;}
        label {display:block; font-weight:bold; margin-bottom:5px;}
        input, select {width:100%; padding:8px; border:1px solid gray; border-radius:3px;}
        .error {color:red; font-size:12px; margin-top:5px; display:none;}
        .error-visible {display:block;}
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

    <h1>Gestión de Vuelos</h1>

    <?php if (!empty($errores)): ?>
        <div style="background:pink; color:black; padding:15px; margin-bottom:20px; border-radius:5px;">
            <strong>Errores al guardar:</strong>
            <ul style="margin:10px 0 0 0;">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

<?php if (isset($_GET['ok'])): ?>
    <div style="background:green; color:black; padding:10px; margin-bottom:15px; border-radius:3px;">
        Vuelo guardado correctamente
    </div>
<?php endif; ?>
    
    <div class="formulario">
        <h2>Agregar Nuevo Vuelo</h2>
        <form id="formVuelo" method="POST" action="GuardarVuelo.php" onsubmit="return validarFormularioVuelo()">
            <div class="campo">
                <label for="origen">Origen:</label>
                <input type="text" id="origen" name="origen" maxlength="100">
                <div id="error-origen" class="error">El origen es obligatorio</div>
            </div>
            
            <div class="campo">
                <label for="destino">Destino:</label>
                <input type="text" id="destino" name="destino" maxlength="100">
                <div id="error-destino" class="error">El destino es obligatorio</div>
            </div>
            
            <div class="campo">
                <label for="fecha">Fecha del vuelo:</label>
                <input type="date" id="fecha" name="fecha">
                <div id="error-fecha" class="error">La fecha es obligatoria y debe ser futura</div>
            </div>
            
            <div class="campo">
                <label for="plazas">Plazas disponibles:</label>
                <input type="number" id="plazas" name="plazas" min="1" max="500">
                <div id="error-plazas" class="error">Las plazas deben ser entre 1 y 500</div>
            </div>
            
            <div class="campo">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" min="0" step="0.01">
                <div id="error-precio" class="error">El precio debe ser mayor o igual a 0</div>
            </div>
            
            <button type="submit">Guardar Vuelo</button>
        </form>
    </div>

    <div class="tabla">
        <h2>Vuelos Registrados</h2>
        <?php
        $bd = new ConexionBD();
        $resultado = $bd->consultaSegura("SELECT * FROM VUELO ORDER BY fecha DESC");
        
        if ($resultado && $resultado->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Origen</th><th>Destino</th><th>Fecha</th><th>Plazas</th><th>Precio</th></tr>";
            
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['id_vuelo'] . "</td>";
                echo "<td>" . htmlspecialchars($fila['origen']) . "</td>";
                echo "<td>" . htmlspecialchars($fila['destino']) . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($fila['fecha'])) . "</td>";
                echo "<td>" . $fila['plazas_disponibles'] . "</td>";
                echo "<td>$" . number_format($fila['precio'], 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay vuelos registrados.</p>";
        }
        ?>
    </div>

    <script>
    function validarFormularioVuelo() {
        let valido = true;
        
        document.querySelectorAll('.error').forEach(e => e.classList.remove('error-visible'));
        
        const origen = document.getElementById('origen').value.trim();
        if (origen === '') {
            document.getElementById('error-origen').classList.add('error-visible');
            valido = false;
        }
        
        const destino = document.getElementById('destino').value.trim();
        if (destino === '') {
            document.getElementById('error-destino').classList.add('error-visible');
            valido = false;
        }
        
        const fecha = document.getElementById('fecha').value;
        const hoy = new Date().toISOString().split('T')[0];
        if (fecha === '' || fecha < hoy) {
            document.getElementById('error-fecha').classList.add('error-visible');
            valido = false;
        }
        
        const plazas = document.getElementById('plazas').value;
        if (plazas === '' || plazas < 1 || plazas > 500) {
            document.getElementById('error-plazas').classList.add('error-visible');
            valido = false;
        }
        
        const precio = document.getElementById('precio').value;
        if (precio === '' || precio < 0) {
            document.getElementById('error-precio').classList.add('error-visible');
            valido = false;
        }
        
        return valido;
    }
    </script>
</body>

</html>
