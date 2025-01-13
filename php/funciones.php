<?php
require_once 'db.php';

/* Reservas - APROBAR O CANCELAR */
// Función para aprobar una reserva
function aprobarReserva($conexion, $id) {
    $stmt = $conexion->prepare("UPDATE rentas SET estado_renta = 'aprobado' WHERE id = ?");
    $stmt->execute([$id]);
}

// Función para cancelar una reserva
function cancelarReserva($conexion, $id) {
    $stmt = $conexion->prepare("UPDATE rentas SET estado_renta = 'cancelado' WHERE id = ?");
    $stmt->execute([$id]);
}

//------------------------------------------------------------------------------------------------------------------------------------//

/* Reservas Finalizadas */

// Obtener datos de la reserva por ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("SELECT * FROM rentas WHERE id = ?");
    $stmt->execute([$id]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Procesar el formulario de devolución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['devolucionReserva'])) {
    $id = $_POST['id'];
    $cedula = $_POST['cedula'];
    $estadoVehiculo = $_POST['estadoVehiculo'];
    $cargoExtra = $_POST['cargoExtra'];

    // Actualizar la reserva con los datos de devolución
    $stmt = $conexion->prepare("UPDATE rentas SET estado_renta = 'finalizada', documento_garantia = ?, cargo_extra = ?, estado_vehiculo = ? WHERE id = ?");
    $stmt->execute([$cedula, $cargoExtra, $estadoVehiculo, $id]);

    echo "<script>alert('Devolución registrada correctamente.'); window.location.href='reservaConfirmada.php';</script>";
}

//------------------------------------------------------------------------------------------------------------------------------------//

/* RESERVA MANUAL */

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registroReservaManual'])) {
    // Obtener datos del formulario
    $cliente = trim($_POST['name']);
    $email = trim($_POST['email']);
    $id_doc = trim($_POST['id_doc']);
    $telefono = trim($_POST['phone']);
    $licencia = trim($_POST['license']);
    $vehiculo = trim($_POST['vehicle']);
    $fecha_inicio = trim($_POST['start_date']);
    $fecha_fin = trim($_POST['end_date']);
    $metodo_pago = trim($_POST['payment_method']);
    $total = trim($_POST['total']);

    // Validar campos
    if (empty($cliente) || empty($email) || empty($id_doc) || empty($vehiculo) || empty($fecha_inicio) || empty($fecha_fin) || empty($metodo_pago) || empty($total)) {
        $message = "Todos los campos son obligatorios.";
    } else {
        try {
            // Insertar los datos
            $stmt = $conexion->prepare("INSERT INTO rentas (usuario, carro_id, fecha_inicio, fecha_fin, estado_renta, estado_pago) 
                                   VALUES (?, ?, ?, ?, 'pendiente', 'sin pago')");
            $stmt->execute([$cliente, $vehiculo, $fecha_inicio, $fecha_fin]);

            $message = "Reserva registrada exitosamente.";
        } catch (PDOException $e) {
            $message = "Error al registrar la reserva: " . $e->getMessage();
        }
    }
}

//------------------------------------------------------------------------------------------------------------------------------------//

/* FUNCIONES AUXILIARES */

// Obtener reserva por ID
function obtenerReservaPorID($conexion, $id) {
    $stmt = $conexion->prepare("SELECT * FROM rentas WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Confirmar una reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmarReserva'])) {
    $id = $_POST['id'];
    $metodoPago = $_POST['metodoPago'];
    $licencia = $_POST['licencia'];

    if (empty($metodoPago) || empty($licencia)) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }

    try {
        $stmt = $conexion->prepare("UPDATE rentas SET estado_pago = ?, estado_renta = 'confirmada', fecha_fin = NOW() WHERE id = ?");
        $stmt->execute([$metodoPago, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Reserva confirmada correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>
