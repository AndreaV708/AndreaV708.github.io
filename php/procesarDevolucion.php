<?php
include "db.php";

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $renta_id = intval($_POST['id']); // ID de la reserva enviado por el formulario
    $fechaDevolucion = $_POST['fechaDevolucion']; // Fecha de devolución
    $estadoVehiculo = $_POST['estadoVehiculo']; // Estado del vehículo ingresado
    $extras = isset($_POST['extras']) ? $_POST['extras'] : [];
    $cargoExtra = floatval($_POST['cargoExtra']);
    $totalCargo = floatval($_POST['totalCargo']);
    $metodoPago = $_POST['metodoPago'];
    $retraso = isset($_POST['retraso']) ? floatval($_POST['retraso']) : 0; // Se recibe el valor de retraso

    // **Depuración: Verificar si el retraso está llegando correctamente**
    error_log("Retraso recibido en POST: " . $retraso);

    // Validación de campos obligatorios
    if (empty($fechaDevolucion) || empty($estadoVehiculo) || empty($metodoPago)) {
        die("Error: Todos los campos obligatorios deben completarse.");
    }

    // Obtener la información actual de la factura para conservar los datos existentes
    $query = "SELECT cargo_extra, detalle_cargos, metodo_pagoCargo, total_pago, fecha_generacion, estado, monto 
              FROM facturas WHERE renta_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $renta_id);
    $stmt->execute();
    $stmt->bind_result($cargo_extra, $detalle_cargos, $metodo_pago_db, $total_pago, $fecha_generacion, $estado, $monto);
    
    if ($stmt->fetch()) {
        $stmt->close();

        // Procesar los extras seleccionados y agregarlos a los detalles existentes
        $detalleExtras = implode(", ", $extras);
        if (!empty($detalle_cargos)) {
            $detalleExtras .= ", " . $detalle_cargos;
        }

        // Determinar la fecha actual como fecha de generación
        $fecha_generacion = date("Y-m-d H:i:s");

        // **Depuración: Imprimir valores antes de actualizar la factura**
        error_log("Monto Original: $monto, Total Cargo: $totalCargo, Retraso: $retraso");

        // Calcular el total pago sumando monto original + total de cargos + retraso
        $total_pago = $monto + $totalCargo + $retraso;

        // **Actualizar la factura con los datos de la devolución**
        $updateFacturaSql = "UPDATE facturas 
                             SET cargo_extra = ?, 
                                 detalle_cargos = ?, 
                                 total_cargo = ?, 
                                 retraso = ?, 
                                 metodo_pagoCargo = ?, 
                                 total_pago = ?, 
                                 fecha_generacion = ?, 
                                 estado = 'Pagado' 
                             WHERE renta_id = ?";
        $updateFacturaStmt = $conexion->prepare($updateFacturaSql);
        $updateFacturaStmt->bind_param("dsdssdsi", $cargoExtra, $detalleExtras, $totalCargo, $retraso, $metodoPago, $total_pago, $fecha_generacion, $renta_id);

        if ($updateFacturaStmt->execute()) {
            // ✅ **Actualizar la tabla `rentas` con `fecha_devolucion`, `estado_vehiculo` y `estado_renta`**
            $updateRentaSql = "UPDATE rentas 
                               SET fecha_devolucion = ?, 
                                   estado_renta = 'Finalizado', 
                                   estado_vehiculo = ? 
                               WHERE id = ?";
            $updateRentaStmt = $conexion->prepare($updateRentaSql);
            $updateRentaStmt->bind_param("ssi", $fechaDevolucion, $estadoVehiculo, $renta_id);
            $updateRentaStmt->execute();

            echo "Factura actualizada, fecha de devolución guardada, estado del vehículo registrado y renta finalizada correctamente.";
        } else {
            echo "Error al actualizar la factura: " . $updateFacturaStmt->error;
        }

        $updateFacturaStmt->close();
    } else {
        echo "Error: No se encontró información de la renta.";
    }

    $conexion->close();
} else {
    echo "Método no permitido.";
}
?>
