<?php 
session_start();

// Verificar si el usuario ha iniciado sesión y si es un empleado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'empleado') {
    // Redirigir al usuario a la página de inicio de sesión si no está autorizado
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas Finalizadas</title>
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
    <main class="container1">

        <nav class="nav-reservas">
            <button class="nav-button" onclick="location.href='reserva.php'">Reservas</button>
            <button class="nav-button" onclick="location.href='reservaAlquilada.php'">Reservas Alquiladas</button>
            <button class="nav-button" onclick="location.href='estadoVehiculo.php'">Actualizar estado del Vehiculo</button>
        </nav>
            <section class="table-container table">
            <h2>Reservas Alquiladas</h2>
                <table class="reservas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Fecha Devolución</th> <!-- Nueva columna -->
                            <th>Estado del Vehículo</th>
                            <th>Monto Inicial</th>
                            <th>Método de Pago</th>
                            <th>Detalle de Cargos</th>
                            <th>Cargo Extra</th>
                            <th>Retraso</th> <!-- Nueva columna -->
                            <th>Total Cargo</th>
                            <th>Método de Pago de Cargos</th>
                            <th>Estado del Pago</th>
                            <th>Total Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "db.php";

                        $sql = "SELECT 
                                    r.id, 
                                    u.nombre_completo AS cliente, 
                                    CONCAT(c.marca, ' ', c.modelo) AS vehiculo,
                                    r.fecha_inicio, 
                                    r.fecha_fin,
                                    r.fecha_devolucion,  /* Nueva columna */
                                    r.estado_vehiculo, 
                                    f.detalle_cargos, 
                                    f.monto AS monto_base, 
                                    f.cargo_extra, 
                                    f.retraso, /* Nueva columna */
                                    f.total_cargo,
                                    f.metodo_pagoCargo AS metodo_pago_cargos,
                                    f.estado AS estado_pago,
                                    (f.monto + f.total_cargo) AS total_pago
                                FROM rentas r
                                JOIN usuarios u ON r.usuario = u.nombre_usuario
                                JOIN carros c ON r.carro_id = c.id
                                JOIN facturas f ON f.renta_id = r.id
                                WHERE r.estado_renta = 'finalizado'";

                        $result = $conexion->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['cliente']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['vehiculo']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['fecha_inicio']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['fecha_fin']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['fecha_devolucion']) . "</td>"; // Nueva columna
                                echo "<td>" . htmlspecialchars($row['estado_vehiculo']) . "</td>";
                                echo "<td>$" . number_format($row['monto_base'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['metodo_pago_cargos']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['detalle_cargos']) . "</td>";
                                echo "<td>$" . number_format($row['cargo_extra'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['retraso']) . "</td>"; // Nueva columna
                                echo "<td>$" . number_format($row['total_cargo'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['metodo_pago_cargos']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['estado_pago']) . "</td>";
                                echo "<td>$" . number_format($row['total_pago'], 2) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='16'>No hay reservas finalizadas.</td></tr>";
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
