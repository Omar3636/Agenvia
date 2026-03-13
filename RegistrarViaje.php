<?php
// RegistrarViaje.php
require_once 'ConfigurarSesion.php';
require_once 'ConexionBD.php';

if (!isset($_SESSION['USUARIO'])) {
    header("Location: Login.php");
    exit();
}

// Recuperar errores si existen
$errores = $_SESSION['errores_reserva'] ?? [];
$datosPrevios = $_SESSION['datos_reserva'] ?? [];
unset($_SESSION['errores_reserva'], $_SESSION['datos_reserva']);

// Obtener listados para los selects
$bd = new ConexionBD();
$vuelos = $bd->consultaSegura("SELECT id_vuelo, origen, destino, fecha, precio FROM VUELO ORDER BY fecha");
$hoteles = $bd->consultaSegura("SELECT id_hotel, nombre, ubicacion, tarifa_noche FROM HOTEL ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Viaje - Agencia de Viajes</title>
    <style>
        .menu {padding:15px; margin-bottom:20px;}
        .menu a {padding:10px 20px; margin-right:10px; text-decoration:none;}
        .formulario {max-width:600px; margin:20px auto; padding:20px; border:1px solid gray;}
        .campo {margin-bottom:15px;}
        label {display:block; font-weight:bold; margin-bottom:5px;}
        input, select {width:100%; padding:8px; border:1px solid gray; border-radius:3px; box-sizing:border-box;}
        .error {color:red; font-size:12px; margin-top:5px; display:none;}
        .error-visible {display:block;}
        .error-servidor {background:pink; color:red; padding:10px; margin-bottom:15px; border-radius:3px;}
        .exito {background:lightgreen; color:black; padding:10px; margin-bottom:15px; border-radius:3px;}
        button {background:green; color:white; padding:10px 20px; border:none; cursor:pointer;}
        button:hover {background:darkgreen;}
        .tabla {margin-top:30px; overflow-x:auto;}
        table {width:100%; border-collapse:collapse;}
        th, td {border:1px solid gray; padding:8px; text-align:left;}
        th {background:#f2f2f2;}
        .info {color:gray; font-size:12px; margin-top:5px;}
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

    <h1>Registrar Nueva Reserva de Viaje</h1>
    
    <!-- Mostrar errores del servidor -->
    <?php if (!empty($errores)): ?>
        <div class="error-servidor">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Mensaje de éxito -->
    <?php if (isset($_GET['ok'])): ?>
        <div class="exito">
            Reserva registrada correctamente
        </div>
    <?php endif; ?>

    <!-- FORMULARIO PARA REGISTRAR RESERVA -->
    <div class="formulario">
        <h2>Completa los datos de tu reserva</h2>
        <form id="formReserva" method="POST" action="GuardarReserva.php" onsubmit="return validarFormularioReserva()">
            
            <div class="campo">
                <label for="id_cliente">Cliente:</label>
                <input type="text" id="id_cliente" name="id_cliente" maxlength="50" 
                       value="<?php echo htmlspecialchars($datosPrevios['id_cliente'] ?? $_SESSION['USUARIO']); ?>"
                       placeholder="Tu identificador">
                <div id="error-cliente" class="error">El cliente es obligatorio</div>
            </div>
            
            <div class="campo">
                <label for="id_vuelo">Seleccionar Vuelo:</label>
                <select id="id_vuelo" name="id_vuelo">
                    <option value="">-- Selecciona un vuelo --</option>
                    <?php 
                    if ($vuelos && $vuelos->num_rows > 0) {
                        while ($vuelo = $vuelos->fetch_assoc()) {
                            $selected = (isset($datosPrevios['id_vuelo']) && $datosPrevios['id_vuelo'] == $vuelo['id_vuelo']) ? 'selected' : '';
                            echo "<option value='" . $vuelo['id_vuelo'] . "' $selected>";
                            echo htmlspecialchars($vuelo['origen'] . " → " . $vuelo['destino'] . " (" . date('d/m/Y', strtotime($vuelo['fecha'])) . ") - $" . $vuelo['precio']);
                            echo "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay vuelos disponibles</option>";
                    }
                    ?>
                </select>
                <div id="error-vuelo" class="error">Debes seleccionar un vuelo</div>
            </div>
            
            <div class="campo">
                <label for="id_hotel">Seleccionar Hotel (opcional):</label>
                <select id="id_hotel" name="id_hotel">
                    <option value="">-- Sin hotel --</option>
                    <?php 
                    if ($hoteles && $hoteles->num_rows > 0) {
                        while ($hotel = $hoteles->fetch_assoc()) {
                            $selected = (isset($datosPrevios['id_hotel']) && $datosPrevios['id_hotel'] == $hotel['id_hotel']) ? 'selected' : '';
                            echo "<option value='" . $hotel['id_hotel'] . "' $selected>";
                            echo htmlspecialchars($hotel['nombre'] . " - " . $hotel['ubicacion'] . " ($" . $hotel['tarifa_noche'] . "/noche)");
                            echo "</option>";
                        }
                    }
                    ?>
                </select>
                <div class="info">Puedes reservar solo vuelo o vuelo + hotel</div>
            </div>
            
            <div class="campo">
                <label for="fecha_reserva">Fecha de reserva:</label>
                <input type="date" id="fecha_reserva" name="fecha_reserva" 
                       value="<?php echo htmlspecialchars($datosPrevios['fecha_reserva'] ?? date('Y-m-d')); ?>">
                <div id="error-fecha" class="error">La fecha es obligatoria</div>
            </div>
            
            <button type="submit">Registrar Reserva</button>
        </form>
    </div>

    <!-- LISTADO DE RESERVAS EXISTENTES -->
    <div class="tabla">
        <h2>Mis Reservas Registradas</h2>
        <?php
        // Consultar reservas del usuario actual
        $sql_reservas = "SELECT r.*, 
                                v.origen, v.destino, v.fecha as fecha_vuelo, v.precio as precio_vuelo,
                                h.nombre as nombre_hotel, h.ubicacion, h.tarifa_noche
                         FROM RESERVA r
                         LEFT JOIN VUELO v ON r.id_vuelo = v.id_vuelo
                         LEFT JOIN HOTEL h ON r.id_hotel = h.id_hotel
                         WHERE r.id_cliente = ?
                         ORDER BY r.fecha_reserva DESC";
        
        $resultado = $bd->consultaSegura($sql_reservas, [$_SESSION['USUARIO']], "s");
        
        if ($resultado && $resultado->num_rows > 0) {
            echo "<table>";
            echo "<tr>
                    <th>ID</th>
                    <th>Fecha Reserva</th>
                    <th>Vuelo</th>
                    <th>Precio</th>
                    <th>Hotel</th>
                    <th>Tarifa</th>
                  </tr>";
            
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['id_reserva'] . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($fila['fecha_reserva'])) . "</td>";
                
                // Vuelo
                if ($fila['id_vuelo']) {
                    echo "<td>" . htmlspecialchars($fila['origen'] . " → " . $fila['destino']) . "<br><small>" . date('d/m/Y', strtotime($fila['fecha_vuelo'])) . "</small></td>";
                    echo "<td>$" . number_format($fila['precio_vuelo'], 2) . "</td>";
                } else {
                    echo "<td>Sin vuelo</td><td>-</td>";
                }
                
                // Hotel
                if ($fila['id_hotel']) {
                    echo "<td>" . htmlspecialchars($fila['nombre_hotel']) . "<br><small>" . $fila['ubicacion'] . "</small></td>";
                    echo "<td>$" . number_format($fila['tarifa_noche'], 2) . "</td>";
                } else {
                    echo "<td>Sin hotel</td><td>-</td>";
                }
                
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No tienes reservas registradas.</p>";
        }
        ?>
    </div>

    <script>
    function validarFormularioReserva() {
        let valido = true;
        
        // Limpiar errores anteriores
        document.querySelectorAll('.error').forEach(e => e.classList.remove('error-visible'));
        
        // Validar cliente
        const cliente = document.getElementById('id_cliente').value.trim();
        if (cliente === '') {
            document.getElementById('error-cliente').classList.add('error-visible');
            valido = false;
        }
        
        // Validar vuelo (es obligatorio)
        const vuelo = document.getElementById('id_vuelo').value;
        if (vuelo === '') {
            document.getElementById('error-vuelo').classList.add('error-visible');
            valido = false;
        }
        
        // Validar fecha de reserva
        const fecha = document.getElementById('fecha_reserva').value;
        if (fecha === '') {
            document.getElementById('error-fecha').classList.add('error-visible');
            valido = false;
        }
        
        return valido;
    }
    </script>
</body>
</html>