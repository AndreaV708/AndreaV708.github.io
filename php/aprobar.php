<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $renta_id = intval($_POST['id']);

    // Actualizar el estado de la renta a "confirmada"
    $sql_update = "UPDATE rentas SET estado_renta = 'confirmada' WHERE id = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param("i", $renta_id);
    $stmt->execute();

    // Establecer el mensaje de éxito en la sesión
    $_SESSION['mensaje_exito'] = "Reserva confirmada exitosamente.";

    header("Location: reserva.php"); // Redirigir de vuelta a la página de reservas
    exit();
}
?>
