<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['carro_id'])) {
    $reservaId = intval($_POST['id']);
    $carroId = intval($_POST['carro_id']);

    // Configurar encabezado JSON
    header('Content-Type: application/json');

    $conexion->begin_transaction();

    try {
        // Verificar si la reserva existe antes de eliminar
        $sqlCheckReserva = "SELECT id FROM rentas WHERE id = ?";
        $stmtCheckReserva = $conexion->prepare($sqlCheckReserva);
        $stmtCheckReserva->bind_param("i", $reservaId);
        $stmtCheckReserva->execute();
        $resultCheck = $stmtCheckReserva->get_result();

        if ($resultCheck->num_rows === 0) {
            throw new Exception("La reserva no existe.");
        }

        // Eliminar la factura asociada a la reserva
        $sqlDeleteFactura = "DELETE FROM facturas WHERE renta_id = ?";
        $stmtFactura = $conexion->prepare($sqlDeleteFactura);
        if (!$stmtFactura) {
            throw new Exception("Error en la consulta de eliminación de factura: " . $conexion->error);
        }
        $stmtFactura->bind_param("i", $reservaId);
        if (!$stmtFactura->execute()) {
            throw new Exception("Error al eliminar la factura: " . $stmtFactura->error);
        }

        // Eliminar los conductores asociados a la reserva (si existen)
        $sqlDeleteConductores = "DELETE FROM conductores WHERE renta_id = ?";
        $stmtConductores = $conexion->prepare($sqlDeleteConductores);
        if (!$stmtConductores) {
            throw new Exception("Error en la consulta de eliminación de conductores: " . $conexion->error);
        }
        $stmtConductores->bind_param("i", $reservaId);
        if (!$stmtConductores->execute()) {
            throw new Exception("Error al eliminar conductores: " . $stmtConductores->error);
        }

        // Eliminar la reserva
        $sqlDeleteReserva = "DELETE FROM rentas WHERE id = ?";
        $stmtReserva = $conexion->prepare($sqlDeleteReserva);
        if (!$stmtReserva) {
            throw new Exception("Error en la consulta de eliminación de reserva: " . $conexion->error);
        }
        $stmtReserva->bind_param("i", $reservaId);
        if (!$stmtReserva->execute()) {
            throw new Exception("Error al eliminar la reserva: " . $stmtReserva->error);
        }

        // Marcar el vehículo como disponible
        $sqlUpdateCarro = "UPDATE carros SET disponible = 1 WHERE id = ?";
        $stmtCarro = $conexion->prepare($sqlUpdateCarro);
        if (!$stmtCarro) {
            throw new Exception("Error en la consulta de actualización del carro: " . $conexion->error);
        }
        $stmtCarro->bind_param("i", $carroId);
        if (!$stmtCarro->execute()) {
            throw new Exception("Error al actualizar el estado del vehículo: " . $stmtCarro->error);
        }

        $conexion->commit();

        // Respuesta JSON exitosa
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Solicitud inválida"]);
}
?>
