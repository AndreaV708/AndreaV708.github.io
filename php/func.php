<?php
function obtenerReservaPorID($id) {
    include "db.php"; // Conexión a la base de datos

    $sql = "SELECT r.id, 
                   u.nombre_completo AS cliente, 
                   CONCAT(c.marca, ' ', c.modelo) AS vehiculo,
                   r.fecha_inicio, 
                   r.fecha_fin,
                   r.garantia,
                   r.telefono
            FROM rentas r
            JOIN usuarios u ON r.usuario = u.nombre_usuario
            JOIN carros c ON r.carro_id = c.id  
            WHERE r.id = ?";
            
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Devuelve los datos como un array asociativo
    } else {
        return null; // No se encontró la reserva
    }
}
?>