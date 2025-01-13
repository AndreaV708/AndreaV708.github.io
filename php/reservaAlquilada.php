<?php
session_start();

// Verificar si el usuario ha iniciado sesión y si es un empleado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas Alquiladas</title>
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
        <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
    </div>
</header>
<main class="rent-container">
    <nav class="nav-reservas">
        <button class="nav-button" onclick="location.href='reserva.php'">Reservas</button>
        <button class="nav-button" onclick="location.href='reservaFinalizada.php'">Reservas Finalizadas</button>
        <button class="nav-button" onclick="location.href='estadoVehiculo.php'">Actualizar estado del Vehiculo</button>
    </nav>
    <section class="table-container table">
        <h2>Reservas Alquiladas</h2>
        <table class="reservas-table">
            <thead>
            <tr>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Garantía</th>
                <th>Método de Pago</th>
                <th>Estado de la Reserva</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody id="reservation-table">
            <?php
            include 'db.php';

            if ($conexion->connect_error) {
                die("Error de conexión: " . $conexion->connect_error);
            }

            // Consulta para obtener las reservas con estado 'alquilado' y el nombre del conductor
            $sql = "SELECT r.id, 
                           u.nombre_completo AS cliente, 
                           CONCAT(c.marca, ' ', c.modelo) AS vehiculo,
                           r.fecha_inicio, 
                           r.fecha_fin, 
                           r.garantia, 
                           r.metodo_pago, 
                           r.estado_renta AS estado,
                           COALESCE(con.nombre, 'Titular') AS conductor
                    FROM rentas r
                    JOIN usuarios u ON r.usuario = u.nombre_usuario
                    JOIN carros c ON r.carro_id = c.id
                    LEFT JOIN conductores con ON r.id = con.renta_id
                    WHERE r.estado_renta = 'alquilado'";

            $result = $conexion->query($sql);

            if (!$result) {
                die("Error en la consulta SQL: " . $conexion->error);
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["cliente"]) . "</td>
                            <td>" . htmlspecialchars($row["vehiculo"]) . "</td>
                            <td>" . htmlspecialchars($row["fecha_inicio"]) . "</td>
                            <td>" . htmlspecialchars($row["fecha_fin"]) . "</td>
                            <td>" . htmlspecialchars($row["garantia"]) . "</td>
                            <td>" . htmlspecialchars($row["metodo_pago"]) . "</td>
                            <td>" . htmlspecialchars($row["estado"]) . "</td>
                            <td><button class='btn-approve' onclick=\"location.href='Devolucion.php?id=" . $row['id'] . "'\">Registrar Devolución</button></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No hay reservas alquiladas</td></tr>";
            }

            $conexion->close();
            ?>
            </tbody>
        </table>
    </section>
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
