<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

// Obtener el nombre del usuario desde la sesión
$usuario = trim($_SESSION['usuario']);

// 🔄 **Actualizar estados en la base de datos antes de mostrarlos**
$sql_actualizar_estados = "UPDATE rentas r
                           JOIN facturas f ON r.id = f.renta_id
                           SET r.estado_renta = 
                               CASE 
                                   WHEN NOW() > r.fecha_fin AND r.estado_renta != 'finalizado' THEN 'finalizado'
                                   ELSE r.estado_renta
                               END,
                               f.estado = 
                               CASE 
                                   WHEN NOW() > r.fecha_fin AND f.estado != 'Pagado' THEN 'Pendiente'
                                   ELSE f.estado
                               END";
$conexion->query($sql_actualizar_estados);

// **Consulta para obtener todas las reservas activas del usuario (no canceladas)**
$sql_rentas = "SELECT r.id, c.id AS carro_id, c.modelo, c.imagen, 
                      r.fecha_inicio, r.fecha_fin, r.fecha_devolucion, 
                      r.estado_renta, r.estado_pago, r.metodo_pago
               FROM rentas r
               JOIN carros c ON r.carro_id = c.id
               WHERE r.usuario = ? AND r.estado_renta != 'cancelado'";
$stmt_rentas = $conexion->prepare($sql_rentas);
if ($stmt_rentas) {
    $stmt_rentas->bind_param("s", $usuario);
    $stmt_rentas->execute();
    $resultado_rentas = $stmt_rentas->get_result();
} else {
    die("Error en la consulta de rentas: " . $conexion->error);
}

// **Consulta para obtener todas las facturas relacionadas con las rentas activas**
$sql_facturas = "SELECT 
                    f.id AS factura_id, 
                    c.modelo AS vehiculo,
                    r.fecha_inicio, 
                    r.fecha_fin, 
                    r.fecha_devolucion, 
                    f.monto AS monto_base, 
                    r.metodo_pago, 
                    f.detalle_cargos, 
                    f.cargo_extra, 
                    f.total_cargo, 
                    f.retraso,
                    f.total_pago, 
                    f.metodo_pagoCargo, 
                    f.fecha_generacion,
                    f.estado AS estado_factura
                 FROM facturas f
                 JOIN rentas r ON f.renta_id = r.id
                 JOIN carros c ON r.carro_id = c.id
                 WHERE r.usuario = ? AND r.estado_renta != 'cancelado'";
$stmt_facturas = $conexion->prepare($sql_facturas);
if ($stmt_facturas) {
    $stmt_facturas->bind_param("s", $usuario);
    $stmt_facturas->execute();
    $resultado_facturas = $stmt_facturas->get_result();
} else {
    die("Error en la consulta de facturas: " . $conexion->error);
}

$total_facturado = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas y Facturación</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Consultas</h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='catalogos.php'" class="btn">Volver al Catálogo</button>
            <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
        </div>
    </header>

    <main class="container1">
        <!-- Tabla de Reservas -->
        <section class="table-container table">
            <h2>Reservas</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Vehículo</th>
                        <th>Imagen</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Fecha Devolución</th>
                        <th>Estado de Renta</th>
                        <th>Estado de Pago</th>
                        <th>Método de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($renta = $resultado_rentas->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($renta['modelo']) ?></td>
                            <td><img src="<?= htmlspecialchars($renta['imagen']) ?>" alt="Vehículo" width="100"></td>
                            <td><?= htmlspecialchars($renta['fecha_inicio']) ?></td>
                            <td><?= htmlspecialchars($renta['fecha_fin']) ?></td>
                            <td><?= htmlspecialchars($renta['fecha_devolucion'] ?: 'No devuelto') ?></td>
                            <td><?= htmlspecialchars($renta['estado_renta']) ?></td>
                            <td><?= htmlspecialchars($renta['estado_pago']) ?></td>
                            <td><?= htmlspecialchars($renta['metodo_pago']) ?></td>
                            <td>
                                <?php if ($renta['estado_renta'] === 'solicitado' || $renta['estado_renta'] === 'confirmada'): ?>
                                    <button onclick="cancelarReserva(<?= intval($renta['id']) ?>, <?= intval($renta['carro_id']) ?>)" class="btn-cancelar">Cancelar</button>
                                <?php else: ?>
                                    <span>No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Tabla de Facturación -->
        <section class="table-container table">
            <h2>Facturas</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID Factura</th>
                        <th>Vehículo</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Fecha Devolución</th>
                        <th>Monto Inicial</th>
                        <th>Método de Pago</th>
                        <th>Detalle de Cargos</th>
                        <th>Cargo Extra</th>
                        <th>Total Cargo</th>
                        <th>Retraso</th>
                        <th>Método de Pago de Cargos</th>
                        <th>Pago Total</th>
                        <th>Estado del Pago</th>
                        <th>Fecha Generación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($factura = $resultado_facturas->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($factura['factura_id']) ?></td>
                            <td><?= htmlspecialchars($factura['vehiculo']) ?></td>
                            <td><?= htmlspecialchars($factura['fecha_inicio']) ?></td>
                            <td><?= htmlspecialchars($factura['fecha_fin']) ?></td>
                            <td><?= htmlspecialchars($factura['fecha_devolucion'] ?: 'No devuelto') ?></td>
                            <td>$<?= number_format($factura['monto_base'], 2) ?></td>
                            <td><?= htmlspecialchars($factura['metodo_pago']) ?></td>
                            <td><?= htmlspecialchars($factura['detalle_cargos']) ?></td>
                            <td>$<?= number_format($factura['cargo_extra'], 2) ?></td>
                            <td>$<?= number_format($factura['total_cargo'], 2) ?></td>
                            <td>$<?= number_format($factura['retraso'], 2) ?></td>
                            <td><?= htmlspecialchars($factura['metodo_pagoCargo']) ?></td>
                            <td>$<?= number_format($factura['total_pago'], 2) ?></td>
                            <td><?= htmlspecialchars($factura['estado_factura']) ?></td>
                            <td><?= htmlspecialchars($factura['fecha_generacion']) ?></td>
                            <td>
                                <button onclick="generarFactura(<?= $factura['factura_id'] ?>)" class="btn">
                                    <img src="../ImagenesHome/descarga.png" alt="Generar Factura" width="30">
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script>

function cancelarReserva(reservaId, carroId) {
    if (confirm("¿Estás seguro de que deseas cancelar esta reserva? Esto eliminará la reserva y la factura asociada.")) {
        fetch('cancel.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id: reservaId, carro_id: carroId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Reserva cancelada correctamente.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ocurrió un error al procesar la solicitud: ' + error);
        });
    }
}

        function generarFactura(facturaId) {
            window.location.href = `generarFactura.php?id=${facturaId}`;
        }
    </script>
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

</body>
</html>
