<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

$id = $_GET['id'];

$sql = "SELECT * FROM carros WHERE id = $id";
$resultado = $conexion->query($sql);
$carro = $resultado->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $conductor = $_POST['conductor'];
    $fecha_actual = date('Y-m-d');

    if ($fecha_inicio < $fecha_actual) {
        echo "La fecha de inicio no puede ser anterior a la fecha actual.";
    } elseif ($fecha_fin < $fecha_actual) {
        echo "La fecha de fin no puede ser anterior a la fecha actual.";
    } elseif (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
        echo "La fecha de inicio no puede ser mayor que la fecha de fin.";
    } else {
        $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400;
        $monto = $carro['precio'] * $dias;
        $usuario_id = $_SESSION['usuario'];

        $sql_renta = "INSERT INTO rentas (usuario, carro_id, fecha_inicio, fecha_fin, conductor) VALUES ('$usuario_id', '$id', '$fecha_inicio', '$fecha_fin', '$conductor')";
        if ($conexion->query($sql_renta) === TRUE) {
            $renta_id = $conexion->insert_id;

            if ($conductor === 'otro') {
                $nombre_conductor = $_POST['nombre_conductor'];
                $cedula_conductor = $_POST['cedula_conductor'];
                $licencia_conductor = $_POST['licencia_conductor'];
                $telefono_conductor = $_POST['telefono_conductor'];

                $sql_conductor = "INSERT INTO conductores (renta_id, nombre, cedula, licencia, telefono) VALUES ('$renta_id', '$nombre_conductor', '$cedula_conductor', '$licencia_conductor', '$telefono_conductor')";
                $conexion->query($sql_conductor);
            }

            $sql_factura = "INSERT INTO facturas (renta_id, monto) VALUES ('$renta_id', '$monto')";
            if ($conexion->query($sql_factura) === TRUE) {
                $sql_update_car = "UPDATE carros SET disponible = 0 WHERE id = $id";
                if ($conexion->query($sql_update_car) === TRUE) {
                    echo "<script>alert('Reserva solicitada con éxito.'); window.location.href='catalogos.php';</script>";
                    exit();
                }
            }            
            
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Vehículo</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <script>
        function calcularPrecio() {
            const precioPorDia = <?php echo $carro['precio']; ?>;
            const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
            const fechaFin = new Date(document.getElementById('fecha_fin').value);
            const diferenciaTiempo = fechaFin - fechaInicio;
            const dias = diferenciaTiempo / (1000 * 60 * 60 * 24);

            if (dias > 0) {
                const precioTotal = precioPorDia * dias;
                document.getElementById('precio_total').innerText = '$' + precioTotal.toFixed(2);
            } else {
                document.getElementById('precio_total').innerText = '$0.00';
            }
        }

        function validarFechas() {
            var fechaInicio = document.getElementById('fecha_inicio').value;
            var fechaFin = document.getElementById('fecha_fin').value;
            var fechaActual = new Date().toISOString().split('T')[0];

            if (fechaInicio < fechaActual) {
                alert('La fecha de inicio no puede ser anterior a la fecha actual.');
                return false;
            }

            if (fechaFin < fechaActual) {
                alert('La fecha de fin no puede ser anterior a la fecha actual.');
                return false;
            }

            if (new Date(fechaInicio) > new Date(fechaFin)) {
                alert('La fecha de inicio no puede ser mayor que la fecha de fin.');
                return false;
            }
            return true;
        }

        function mostrarFormularioConductor() {
            var conductor = document.getElementById('conductor').value;
            var formularioConductor = document.getElementById('formulario-conductor');
            if (conductor === 'otro') {
                formularioConductor.style.display = 'block';
            } else {
                formularioConductor.style.display = 'none';
            }
        }
    </script>
    <script>
        document.getElementById('end-date').addEventListener('change', function() {
            var startDate = document.getElementById('start-date').value;
            var endDate = document.getElementById('end-date').value;

            if (new Date(endDate) < new Date(startDate)) {
                alert('La fecha de fin no puede ser anterior a la fecha de inicio.');
                document.getElementById('end-date').value = '';
            }
        });
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Reservar Vehículo</h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='catalogos.php'" class="btn">Volver al Catálogo</button>
            <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
        </div>
    </header>

    <main class="rent-container">
        <div class="vehicle-details-container">
            <img src="<?php echo $carro['imagen']; ?>" alt="<?php echo $carro['marca']; ?>" class="vehicle-image">
            <div class="vehicle-details">
                <ul>
                    <li><strong>Marca:</strong> <?php echo $carro['marca']; ?></li>
                    <li><strong>Modelo:</strong> <?php echo $carro['modelo']; ?></li>
                    <li><strong>Año:</strong> <?php echo $carro['ano']; ?></li>
                    <li><strong>Capacidad:</strong> <?php echo $carro['asientos']; ?> pasajeros</li>
                    <li><strong>Combustible:</strong> <?php echo $carro['combustible']; ?></li>
                    <li><strong>Transmisión:</strong> <?php echo $carro['transmision']; ?></li>
                    <li><strong>Precio por día:</strong> $<?php echo number_format($carro['precio'], 2); ?></li>
                </ul>
            </div>
        </div>

        <div class="reservation-form">
            <h2>Reserva tu Vehículo</h2>
            <form method="POST" onsubmit="return validarFechas()">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required onchange="calcularPrecio()">
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required onchange="calcularPrecio()">
                </div>
                <div class="form-group">
                    <label for="conductor">¿Quién va a conducir?</label>
                    <select id="conductor" name="conductor" class="form-control" onchange="mostrarFormularioConductor()" required>
                        <option value="titular">Titular</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div id="formulario-conductor" style="display: none;">
                    <div class="form-group">
                        <label for="nombre_conductor">Nombre del Conductor</label>
                        <input type="text" id="nombre_conductor" name="nombre_conductor" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="cedula_conductor">Cédula del Conductor</label>
                        <input type="text" id="cedula_conductor" name="cedula_conductor" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="licencia_conductor">Licencia del Conductor</label>
                        <input type="text" id="licencia_conductor" name="licencia_conductor" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="telefono_conductor">Número de Teléfono del Conductor</label>
                        <input type="text" id="telefono_conductor" name="telefono_conductor" class="form-control">
                    </div>
                </div>
                <p>Total (con IVA): <span id="precio_total">$0.00</span></p>
                <button type="submit" class="btn-confirmar">Confirmar</button>
                <button type="button" onclick="location.href='catalogos.php'" class="btn-cancelar">Cancelar</button>
            </form>
        </div>
    </main>

    <footer class="fixed-footer">
        <div class="footer-text">
            <h2>CarGo!</h2>
            <p>&copy; 2024 | Todos los derechos reservados</p>
        </div>
        <div class="footer-image">
            <img src="../ImagenesHome/piePag.png" alt="Logo Footer">
        </div>
    </footer>
</body>
</html>
