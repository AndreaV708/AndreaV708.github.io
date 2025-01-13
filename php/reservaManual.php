<?php
session_start();

// Verificar si el usuario ha iniciado sesión y si es un empleado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

include "db.php";
$mensaje = "";

// Obtener usuarios con reservas confirmadas y detalles del vehículo
$query_usuarios = "SELECT DISTINCT u.nombre_completo, u.email, u.nombre_usuario, 
                  r.id AS reserva_id, r.fecha_inicio, r.fecha_fin, r.carro_id, 
                  c.marca, c.modelo, c.placa, c.precio, c.imagen, c.ano, 
                  c.combustible, c.transmision, c.descripcion, r.garantia, r.conductor
                  FROM rentas r
                  JOIN usuarios u ON r.usuario = u.nombre_usuario 
                  JOIN carros c ON r.carro_id = c.id
                  WHERE r.estado_renta = 'confirmada'";

$result_usuarios = $conexion->query($query_usuarios);
if (!$result_usuarios) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Obtener datos de los conductores
$query_conductores = "SELECT renta_id, nombre, cedula, licencia, telefono FROM conductores";
$result_conductores = $conexion->query($query_conductores);
$conductores = [];
while ($row = $result_conductores->fetch_assoc()) {
    $conductores[$row['renta_id']] = $row;
}

// Procesar cambios en los datos del conductor o fechas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];

    // Validar datos básicos
    if (empty($_POST['reserva_id'])) {
        $errores[] = "Debe seleccionar una reserva.";
    }
    if (empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
        $errores[] = "Las fechas de inicio y fin son obligatorias.";
    } elseif (strtotime($_POST['fecha_inicio']) > strtotime($_POST['fecha_fin'])) {
        $errores[] = "La fecha de inicio no puede ser mayor a la fecha de fin.";
    }
    if (empty($_POST['garantia'])) {
        $errores[] = "Debe seleccionar una garantía.";
    }
    if (empty($_POST['metodo_pago'])) {
        $errores[] = "Debe seleccionar un método de pago.";
    }

    $reserva_id = intval($_POST['reserva_id']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $garantia = $_POST['garantia'];
    $metodo_pago = $_POST['metodo_pago'];

    // Lógica para el conductor
    if (isset($_POST['conductor_tipo']) && $_POST['conductor_tipo'] === 'otro') {
        // Conductor "otro"
        $nombre_conductor = trim($_POST['nombre_conductor'] ?? '');
        $cedula_conductor = trim($_POST['cedula_conductor'] ?? '');
        $licencia_conductor = trim($_POST['licencia_conductor'] ?? '');
        $telefono_conductor = trim($_POST['telefono_conductor'] ?? '');

        if (!$nombre_conductor || !$cedula_conductor || !$licencia_conductor || !$telefono_conductor) {
            $errores[] = "Debe completar todos los datos del conductor 'otro'.";
        } else {
            $query_check_conductor = "SELECT renta_id FROM conductores WHERE renta_id = ?";
            $stmt_check_conductor = $conexion->prepare($query_check_conductor);
            $stmt_check_conductor->bind_param("i", $reserva_id);
            $stmt_check_conductor->execute();
            $result_check_conductor = $stmt_check_conductor->get_result();

            if ($result_check_conductor->num_rows > 0) {
                // Actualizar conductor "otro"
                $query_update_conductor = "UPDATE conductores 
                                           SET nombre = ?, cedula = ?, licencia = ?, telefono = ? 
                                           WHERE renta_id = ?";
                $stmt_update_conductor = $conexion->prepare($query_update_conductor);
                $stmt_update_conductor->bind_param("ssssi", $nombre_conductor, $cedula_conductor, $licencia_conductor, $telefono_conductor, $reserva_id);
                if (!$stmt_update_conductor->execute()) {
                    $errores[] = "Error al actualizar los datos del conductor 'otro': " . $stmt_update_conductor->error;
                }
                $stmt_update_conductor->close();
            } else {
                // Insertar conductor "otro"
                $query_insert_conductor = "INSERT INTO conductores (renta_id, nombre, cedula, licencia, telefono) 
                                           VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_conductor = $conexion->prepare($query_insert_conductor);
                $stmt_insert_conductor->bind_param("issss", $reserva_id, $nombre_conductor, $cedula_conductor, $licencia_conductor, $telefono_conductor);
                if (!$stmt_insert_conductor->execute()) {
                    $errores[] = "Error al insertar los datos del conductor 'otro': " . $stmt_insert_conductor->error;
                }
                $stmt_insert_conductor->close();
            }
            $stmt_check_conductor->close();
        }
    } elseif (isset($_POST['conductor_tipo']) && $_POST['conductor_tipo'] === 'titular') {
        // Conductor "titular"
        $query_nombre_titular = "SELECT u.nombre_completo FROM usuarios u 
                                 JOIN rentas r ON r.usuario = u.nombre_usuario 
                                 WHERE r.id = ?";
        $stmt_nombre_titular = $conexion->prepare($query_nombre_titular);
        $stmt_nombre_titular->bind_param("i", $reserva_id);
        $stmt_nombre_titular->execute();
        $result_nombre_titular = $stmt_nombre_titular->get_result();
        $nombre_titular = $result_nombre_titular->fetch_assoc()['nombre_completo'];
        $stmt_nombre_titular->close();

        $cedula_titular = trim($_POST['cedula_titular'] ?? '');
        $licencia_titular = trim($_POST['licencia_titular'] ?? '');
        $telefono_titular = trim($_POST['telefono_titular'] ?? '');

        if (!$cedula_titular || !$licencia_titular || !$telefono_titular) {
            $errores[] = "Debe completar todos los datos del conductor titular.";
        } else {
            $query_check_conductor = "SELECT renta_id FROM conductores WHERE renta_id = ?";
            $stmt_check_conductor = $conexion->prepare($query_check_conductor);
            $stmt_check_conductor->bind_param("i", $reserva_id);
            $stmt_check_conductor->execute();
            $result_check_conductor = $stmt_check_conductor->get_result();

            if ($result_check_conductor->num_rows > 0) {
                // Actualizar conductor titular
                $query_update_conductor = "UPDATE conductores 
                                           SET nombre = ?, cedula = ?, licencia = ?, telefono = ? 
                                           WHERE renta_id = ?";
                $stmt_update_conductor = $conexion->prepare($query_update_conductor);
                $stmt_update_conductor->bind_param("ssssi", $nombre_titular, $cedula_titular, $licencia_titular, $telefono_titular, $reserva_id);
                if (!$stmt_update_conductor->execute()) {
                    $errores[] = "Error al actualizar los datos del conductor titular: " . $stmt_update_conductor->error;
                }
                $stmt_update_conductor->close();
            } else {
                // Insertar conductor titular
                $query_insert_conductor = "INSERT INTO conductores (renta_id, nombre, cedula, licencia, telefono) 
                                           VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_conductor = $conexion->prepare($query_insert_conductor);
                $stmt_insert_conductor->bind_param("issss", $reserva_id, $nombre_titular, $cedula_titular, $licencia_titular, $telefono_titular);
                if (!$stmt_insert_conductor->execute()) {
                    $errores[] = "Error al insertar los datos del conductor titular: " . $stmt_insert_conductor->error;
                }
                $stmt_insert_conductor->close();
            }
            $stmt_check_conductor->close();
        }
    }

    // Actualizar reserva con los nuevos datos
    if (empty($errores)) {
        $query_update_renta = "UPDATE rentas 
                               SET fecha_inicio = ?, fecha_fin = ?, garantia = ?, metodo_pago = ?, 
                                   estado_renta = 'alquilado', estado_pago = 'pago inicial completo' 
                               WHERE id = ?";
        $stmt_update_renta = $conexion->prepare($query_update_renta);
        $stmt_update_renta->bind_param("ssssi", $fecha_inicio, $fecha_fin, $garantia, $metodo_pago, $reserva_id);
        if (!$stmt_update_renta->execute()) {
            $errores[] = "Error al actualizar la reserva: " . $stmt_update_renta->error;
        }
        $stmt_update_renta->close();

        $mensaje = empty($errores) ? "Los datos se han actualizado correctamente." : implode("<br>", $errores);

            // Calcular nuevo monto
        $dias = max(1, (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24));
        $query_car_price = "SELECT precio FROM carros WHERE id = (SELECT carro_id FROM rentas WHERE id = ?)";
        $stmt_car_price = $conexion->prepare($query_car_price);
        $stmt_car_price->bind_param("i", $reserva_id);
        $stmt_car_price->execute();
        $result_car_price = $stmt_car_price->get_result();
        $car_price = $result_car_price->fetch_assoc()['precio'];
        $stmt_car_price->close();

        $nuevo_total_pagar = $dias * $car_price;

        // Actualizar factura
        $query_update_factura = "UPDATE facturas SET monto = ?, total_pago = ? WHERE renta_id = ?";
        $stmt_update_factura = $conexion->prepare($query_update_factura);
        $stmt_update_factura->bind_param("ddi", $nuevo_total_pagar, $nuevo_total_pagar, $reserva_id);
        $stmt_update_factura->execute();
        $stmt_update_factura->close();

        $mensaje = "Los datos se han actualizado correctamente.";    } else {
        $mensaje = implode("<br>", $errores);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Manual</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Bienvenidos al Sistema de Reservas</h1>
        </div>
        <div class="nav-buttons">
        <?php if (isset($_SESSION['usuario'])): ?>
            <button onclick="location.href='reserva.php'" class="btn">Volver</button>
            <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
        <?php else: ?>
            <button onclick="location.href='login.php'" class="btn">Iniciar Sesión</button>
            <button onclick="location.href='crearCuenta.php'" class="btn">Registrar</button>
        <?php endif; ?>
        </div>
    </header>

    <main class="rent-container">
    <?php if ($mensaje && strpos($mensaje, 'exitosamente') !== false): ?>
    <div class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></div>
    <script>
        // Comprobar si ya se realizó la recarga
        if (!sessionStorage.getItem('reservaExitosa')) {
            sessionStorage.setItem('reservaExitosa', 'true'); // Marcar la recarga como realizada
            setTimeout(() => {
                location.reload();
            }, 2000); // Recargar solo una vez después de 2 segundos
        }
    </script>
    <?php elseif ($mensaje): ?>
    <div class="mensaje-error"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="contenedor-alquiler">
        <!-- Formulario principal para registrar datos -->
            <div class="formulario-alquiler">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="reserva_id" id="reservaSeleccionadaId">
                <h2>Datos del Cliente</h2>
                <div class="campo-formulario">
                    <label for="cliente">Seleccionar Cliente:</label>
                    <select name="reserva_id" id="cliente" required onchange="fillDetails(this)">
                        <option value="">Seleccione un cliente</option>
                        <?php while ($row = $result_usuarios->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['reserva_id']); ?>"
                                    data-nombre="<?= htmlspecialchars($row['nombre_completo']); ?>"
                                    data-email="<?= htmlspecialchars($row['email']); ?>"
                                    data-fechainicio="<?= htmlspecialchars($row['fecha_inicio']); ?>"
                                    data-fechafin="<?= htmlspecialchars($row['fecha_fin']); ?>"
                                    data-imagen="<?= htmlspecialchars($row['imagen']); ?>"
                                    data-marca="<?= htmlspecialchars($row['marca']); ?>"
                                    data-modelo="<?= htmlspecialchars($row['modelo']); ?>"
                                    data-ano="<?= htmlspecialchars($row['ano']); ?>"
                                    data-combustible="<?= htmlspecialchars($row['combustible']); ?>"
                                    data-transmision="<?= htmlspecialchars($row['transmision']); ?>"
                                    data-descripcion="<?= htmlspecialchars($row['descripcion']); ?>"
                                    data-placa="<?= htmlspecialchars($row['placa']); ?>"
                                    data-conductor="<?= htmlspecialchars($row['conductor']); ?>"
                                    data-precio="<?= htmlspecialchars($row['precio']); ?>"
                                    <?php if (isset($conductores[$row['reserva_id']])): ?>
                                    data-nombre-conductor="<?= htmlspecialchars($conductores[$row['reserva_id']]['nombre']); ?>"
                                    data-cedula-conductor="<?= htmlspecialchars($conductores[$row['reserva_id']]['cedula']); ?>"
                                    data-licencia-conductor="<?= htmlspecialchars($conductores[$row['reserva_id']]['licencia']); ?>"
                                    data-telefono-conductor="<?= htmlspecialchars($conductores[$row['reserva_id']]['telefono']); ?>"
                                    <?php endif; ?>
>
                                <?= htmlspecialchars($row['nombre_completo']) . " - " . $row['fecha_inicio'] . " a " . $row['fecha_fin']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo-formulario">
                    <label for="nombre">Nombre y Apellido:</label>
                    <input type="text" id="nombre" readonly>
                </div>
                <div class="campo-formulario">
                    <label for="email">Correo Electrónico:</label>
                    <input type="text" id="email" readonly>
                </div>
                <div class="campo-formulario">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required onchange="calcularTotal()">
                </div>
                <div class="campo-formulario">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" required onchange="calcularTotal()">
                </div>

                <div class="campo-formulario">
                    <label for="total_pagar">Total a Pagar:</label>
                    <input type="text" id="total_pagar" readonly>
                </div>

                <div class="campo-formulario">
                    <label for="garantia">Seleccionar Garantía:</label>
                    <select name="garantia" id="garantia" required>
                        <option value="">Seleccione una garantía</option>
                        <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Depósito Bancario">Depósito Bancario</option>
                    </select>
                </div>
<!-- Campos para conductor -->
<div id="datosConductor" style="display: none;">
    <h2>Datos del Conductor</h2>
    <div class="campo-formulario">
        <label for="nombre_conductor">Nombre:</label>
        <input type="text" name="nombre_conductor" id="nombre_conductor" autocomplete="name" required>
    </div>
    <div class="campo-formulario">
        <label for="cedula_conductor">Cédula:</label>
        <input type="text" name="cedula_conductor" id="cedula_conductor" pattern="[0-9]{10}" title="Ingrese solo números con 10 dígitos" autocomplete="off" required>
    </div>
    <div class="campo-formulario">
        <label for="licencia_conductor">Licencia:</label>
        <input type="text" name="licencia_conductor" id="licencia_conductor" pattern="[0-9]{6,10}" title="Ingrese una licencia válida" autocomplete="off" required>
    </div>
    <div class="campo-formulario">
        <label for="telefono_conductor">Teléfono:</label>
        <input type="text" name="telefono_conductor" id="telefono_conductor" pattern="[0-9]{10}" title="Ingrese solo números con 10 dígitos" autocomplete="tel" required>
    </div>
</div>

<!-- Campos para titular -->
<div id="datosTitular" style="display: none;">
    <h2>Datos del Titular</h2>
    <div class="campo-formulario">
        <label for="cedula_titular">Cédula:</label>
        <input type="text" name="cedula_titular" id="cedula_titular" pattern="[0-9]{10}" title="Ingrese solo números con 10 dígitos" autocomplete="off" required>
    </div>
    <div class="campo-formulario">
        <label for="licencia_titular">Licencia:</label>
        <input type="text" name="licencia_titular" id="licencia_titular" pattern="[0-9]{6,10}" title="Ingrese una licencia válida" autocomplete="off" required>
    </div>
    <div class="campo-formulario">
        <label for="telefono_titular">Teléfono:</label>
        <input type="text" name="telefono_titular" id="telefono_titular" pattern="[0-9]{10}" title="Ingrese solo números con 10 dígitos" autocomplete="tel" required>
    </div>
</div>

<!-- Método de pago -->
<div class="campo-formulario">
    <label for="metodo_pago">Método de Pago:</label>
    <select name="metodo_pago" id="metodo_pago" required>
        <option value="">Seleccione un método</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Tarjeta">Tarjeta</option>
    </select>
</div>

                <button type="submit" class="btn-boton" id="btnRegistrarDatos" disabled>Registrar Datos</button>
                </form>
<form method="GET" action="catalogos.php">
    <input type="hidden" name="reserva_id" id="reservaSeleccionadaIdGET">
    <button type="submit" class="btn-cancel" id="btnCambiarVehiculo" disabled>Cambiar Vehículo</button>
</form>

</div>
        <!-- Detalles del Vehículo -->
        <div class="detalles-vehiculo" id="detailsContainer" style="display:none;">
            <h2>Detalles del Vehículo</h2>
            <div class="contenedor-vehiculo">
                <img id="vehicleImage" src="" alt="Vehículo">
                <div class="info-vehiculo">
                    <p><strong>Marca:</strong> <span id="marca"></span></p>
                    <p><strong>Modelo:</strong> <span id="modelo"></span></p>
                    <p><strong>Año:</strong> <span id="ano"></span></p>
                    <p><strong>Combustible:</strong> <span id="combustible"></span></p>
                    <p><strong>Transmisión:</strong> <span id="transmision"></span></p>
                    <p><strong>Placa:</strong> <span id="placa"></span></p>
                    <p><strong>Descripción:</strong> <span id="descripcion"></span></p>
                </div>
            </div>
        </div>
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
    <script>
function fillDetails(select) {
    const selectedOption = select.options[select.selectedIndex];
    const reservaSeleccionadaId = document.getElementById('reservaSeleccionadaId');
    const reservaSeleccionadaIdGET = document.getElementById('reservaSeleccionadaIdGET');
    const btnRegistrarDatos = document.getElementById('btnRegistrarDatos');
    const btnCambiarVehiculo = document.getElementById('btnCambiarVehiculo');
    const detailsContainer = document.getElementById('detailsContainer');
    const datosConductor = document.getElementById('datosConductor');
    const datosTitular = document.getElementById('datosTitular');

    if (!selectedOption || selectedOption.value === "") {
        resetAll([reservaSeleccionadaId, reservaSeleccionadaIdGET], btnRegistrarDatos, btnCambiarVehiculo, detailsContainer, datosConductor, datosTitular);
        return;
    }

    // Asignar valores de la reserva seleccionada
    reservaSeleccionadaId.value = selectedOption.value;
    reservaSeleccionadaIdGET.value = selectedOption.value;

    // Habilitar botones
    toggleButton(btnRegistrarDatos, false);
    toggleButton(btnCambiarVehiculo, false);

    // Llenar detalles del cliente
    setElementValue('nombre', selectedOption.getAttribute('data-nombre'));
    setElementValue('email', selectedOption.getAttribute('data-email'));
    setElementValue('fecha_inicio', selectedOption.getAttribute('data-fechainicio'));
    setElementValue('fecha_fin', selectedOption.getAttribute('data-fechafin'));
    setElementSrc('vehicleImage', selectedOption.getAttribute('data-imagen'));

    // Llenar detalles del vehículo
    setElementText('marca', selectedOption.getAttribute('data-marca'));
    setElementText('modelo', selectedOption.getAttribute('data-modelo'));
    setElementText('ano', selectedOption.getAttribute('data-ano'));
    setElementText('combustible', selectedOption.getAttribute('data-combustible'));
    setElementText('transmision', selectedOption.getAttribute('data-transmision'));
    setElementText('placa', selectedOption.getAttribute('data-placa'));
    setElementText('descripcion', selectedOption.getAttribute('data-descripcion'));

    // Mostrar el contenedor de detalles
    toggleElementDisplay(detailsContainer, true);

    // Actualizar precio y calcular total
    const precioDia = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
    document.getElementById('total_pagar').setAttribute('data-precio', precioDia);
    calcularTotal();

    // Lógica para mostrar campos de conductor o titular
    const conductorTipo = selectedOption.getAttribute('data-conductor');
    if (conductorTipo === 'otro') {
        mostrarCampos(datosConductor);
        ocultarCampos(datosTitular);

        // Llenar los campos del conductor
        setElementValue('nombre_conductor', selectedOption.getAttribute('data-nombre-conductor'));
        setElementValue('cedula_conductor', selectedOption.getAttribute('data-cedula-conductor'));
                setElementValue('licencia_conductor', selectedOption.getAttribute('data-licencia-conductor'));

        setElementValue('telefono_conductor', selectedOption.getAttribute('data-telefono-conductor'));
    } else {
        mostrarCampos(datosTitular);
        ocultarCampos(datosConductor);

        // Reiniciar campos del titular
        resetFields(['cedula_titular', 'licencia_titular', 'telefono_titular']);
    }
}

function calcularTotal() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    const precioDia = parseFloat(document.getElementById('total_pagar').getAttribute('data-precio')) || 0;

    if (fechaInicio && fechaFin) {
        const startDate = new Date(fechaInicio);
        const endDate = new Date(fechaFin);

        if (startDate > endDate) {
            alert("La fecha de inicio no puede ser mayor que la fecha de fin.");
            setElementValue('total_pagar', "Fecha inválida");
            return;
        }

        const dias = Math.max(1, Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)));
        const total = dias * precioDia;
        setElementValue('total_pagar', `$${total.toFixed(2)}`);
    } else {
        setElementValue('total_pagar', "Seleccione fechas válidas");
    }
}

function resetAll(fields, ...elements) {
    fields.forEach((field) => field.value = "");
    elements.forEach((el) => {
        if (el.tagName === 'BUTTON') el.disabled = true;
        if (el.tagName === 'DIV') el.style.display = 'none';
    });
}

function mostrarCampos(contenedor) {
    toggleElementDisplay(contenedor, true);
}

function ocultarCampos(contenedor) {
    toggleElementDisplay(contenedor, false);
}

function toggleElementDisplay(element, show) {
    element.style.display = show ? 'block' : 'none';
    const inputs = element.querySelectorAll('input, select');
    inputs.forEach((input) => {
        input.disabled = !show;
        if (!show) input.value = '';
    });
}

function toggleButton(button, disable) {
    button.disabled = disable;
}

function setElementValue(id, value) {
    const element = document.getElementById(id);
    if (element) element.value = value || '';
}

function setElementSrc(id, src) {
    const element = document.getElementById(id);
    if (element) element.src = src || '';
}

function setElementText(id, text) {
    const element = document.getElementById(id);
    if (element) element.textContent = text || '';
}

function resetFields(ids) {
    ids.forEach((id) => {
        const element = document.getElementById(id);
        if (element) element.value = '';
    });
}
</script>
</body>
</html>
