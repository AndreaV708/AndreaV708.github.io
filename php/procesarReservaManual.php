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

// Procesar solicitudes POST (Registrar Datos)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserva_id'])) {
    $errores = [];

    // Validar entrada
    if (empty($_POST['reserva_id'])) {
        $errores[] = "Debe seleccionar una reserva.";
    }
    if (empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
        $errores[] = "Las fechas de inicio y fin son obligatorias.";
    } elseif (strtotime($_POST['fecha_inicio']) > strtotime($_POST['fecha_fin'])) {
        $errores[] = "La fecha de inicio no puede ser mayor a la de finalización.";
    }
    if (empty($_POST['garantia'])) {
        $errores[] = "Debe seleccionar una garantía.";
    }
    if (empty($_POST['metodo_pago'])) {
        $errores[] = "Debe seleccionar un método de pago.";
    }

    if (empty($errores)) {
        $reserva_id = $conexion->real_escape_string($_POST['reserva_id']);
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $garantia = $_POST['garantia'];
        $metodo_pago = $_POST['metodo_pago'];
        $estado_vehiculo = 'alquilado';
        $estado_pago = 'pago inicial completo';

        // Obtener los datos actuales de la reserva
        $query_fechas = "SELECT fecha_inicio, fecha_fin, carro_id FROM rentas WHERE id = ?";
        $stmt_fechas = $conexion->prepare($query_fechas);
        $stmt_fechas->bind_param("i", $reserva_id);
        $stmt_fechas->execute();
        $result_fechas = $stmt_fechas->get_result();
        $row_fechas = $result_fechas->fetch_assoc();

        if ($row_fechas) {
            $carro_id = $row_fechas['carro_id'];

            // Obtener el precio del carro
            $query_precio = "SELECT precio FROM carros WHERE id = ?";
            $stmt_precio = $conexion->prepare($query_precio);
            $stmt_precio->bind_param("i", $carro_id);
            $stmt_precio->execute();
            $result_precio = $stmt_precio->get_result();
            $precio_dia = ($result_precio->num_rows > 0) ? $result_precio->fetch_assoc()['precio'] : 0;

            // Calcular el nuevo monto
            $dias = max(1, (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24));
            $total_pagar = $dias * $precio_dia;

            // Actualizar la reserva
            $sql_update_renta = "UPDATE rentas 
                                 SET fecha_inicio = ?, fecha_fin = ?, estado_renta = ?, garantia = ?, 
                                     metodo_pago = ?, estado_vehiculo = ?, estado_pago = ?
                                 WHERE id = ?";
            $stmt_renta = $conexion->prepare($sql_update_renta);
            $stmt_renta->bind_param("sssssssi", $fecha_inicio, $fecha_fin, $estado_vehiculo, $garantia, 
                                                 $metodo_pago, $estado_vehiculo, $estado_pago, $reserva_id);
            if ($stmt_renta->execute()) {
                // Actualizar el monto en facturas
                $sql_update_factura = "UPDATE facturas SET monto = ?, total_pago = ? WHERE renta_id = ?";
                $stmt_factura = $conexion->prepare($sql_update_factura);
                $stmt_factura->bind_param("ddi", $total_pagar, $total_pagar, $reserva_id);
                $stmt_factura->execute();

                $mensaje = "La reserva se ha registrado exitosamente.";
            } else {
                $errores[] = "Ocurrió un error al registrar la reserva.";
            }
        }
    }

    if (!empty($errores)) {
        $mensaje = implode(", ", $errores);
    }
}

// Procesar solicitudes GET (Cambiar Vehículo)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reserva_id_cambiar']) && isset($_GET['new_car_id'])) {
    $new_car_id = intval($_GET['new_car_id']);
    $reserva_id_cambiar = intval($_GET['reserva_id_cambiar']);

    // Obtener el ID del vehículo anterior asociado a la reserva
    $query_old_car = "SELECT carro_id FROM rentas WHERE id = ?";
    $stmt_old_car = $conexion->prepare($query_old_car);
    $stmt_old_car->bind_param("i", $reserva_id_cambiar);
    $stmt_old_car->execute();
    $result_old_car = $stmt_old_car->get_result();
    $old_car_id = $result_old_car->fetch_assoc()['carro_id'];

    // Actualizar el vehículo anterior a disponible
    $query_update_old_car = "UPDATE carros SET disponible = 1 WHERE id = ?";
    $stmt_update_old_car = $conexion->prepare($query_update_old_car);
    $stmt_update_old_car->bind_param("i", $old_car_id);
    $stmt_update_old_car->execute();

    // Actualizar el nuevo vehículo a no disponible
    $query_update_new_car = "UPDATE carros SET disponible = 0 WHERE id = ?";
    $stmt_update_new_car = $conexion->prepare($query_update_new_car);
    $stmt_update_new_car->bind_param("i", $new_car_id);
    $stmt_update_new_car->execute();

    // Actualizar la reserva con el nuevo vehículo
    $query_update_renta = "UPDATE rentas SET carro_id = ? WHERE id = ?";
    $stmt_update_renta = $conexion->prepare($query_update_renta);
    $stmt_update_renta->bind_param("ii", $new_car_id, $reserva_id_cambiar);
    $stmt_update_renta->execute();

    $mensaje = "El vehículo ha sido cambiado exitosamente.";
}
?>
