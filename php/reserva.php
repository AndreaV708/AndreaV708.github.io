<?php
session_start();

// Verificar si el usuario ha iniciado sesión y es empleado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'empleado') {
    header('Location: login.php');
    exit();
}

// Incluir el archivo de conexión a la base de datos
include "db.php"; // Conexión a la base de datos

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener reservas pendientes (estado_renta = 'solicitado')
$sql = "SELECT r.id, 
               u.nombre_completo AS cliente, 
               c.modelo AS vehiculo, 
               r.fecha_inicio, 
               r.fecha_fin 
        FROM rentas r
        JOIN usuarios u ON r.usuario = u.nombre_usuario
        JOIN carros c ON r.carro_id = c.id
        WHERE r.estado_renta = 'solicitado'";

$result = $conexion->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta SQL: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas por Aprobar</title>
    <!-- Estilos generales -->
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
    <button onclick="location.href='empleado.php'" class="btn">Volver</button>
        <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
    </div>
</header>

<main class="container1">

<nav class="nav-reservas">
        <button class="nav-button" onclick="location.href='reserva.php'">Reservas</button>
        <button class="nav-button" onclick="location.href='reservaManual.php'">Registrar Reserva Manualmente</button>
        <button class="nav-button" onclick="location.href='reservaAlquilada.php'">Reservas Alquiladas</button>
    </nav>
    <section class="table-container table">
    <h2>Reservas por Aprobar</h2>
    <!-- Mostrar mensaje de éxito si existe -->
    
        <?php unset($_SESSION['mensaje_exito']); ?>

    <div class="reservas-table-container">
        <table class="reservas-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["cliente"]) ?></td>
                        <td><?= htmlspecialchars($row["vehiculo"]) ?></td>
                        <td><?= htmlspecialchars($row["fecha_inicio"]) ?></td>
                        <td><?= htmlspecialchars($row["fecha_fin"]) ?></td>
                        <td>
                            <form method="POST" action="aprobar.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-approve">Aprobar</button>
                            </form>
                            <form method="POST" action="cancelar.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-cancel">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-message">No se encontraron reservas pendientes.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
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
