<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include('db.php'); // Conexión a la base de datos

// Obtener los vehículos cuyas reservas han finalizado y que NO están disponibles
$sql = "SELECT 
            c.id AS carro_id, 
            c.marca, 
            c.modelo, 
            c.ano, 
            c.imagen, 
            c.disponible,
            r.fecha_fin, 
            r.estado_renta
        FROM carros c
        JOIN rentas r ON c.id = r.carro_id
        WHERE r.estado_renta = 'finalizado' AND c.disponible = 0
        ORDER BY r.fecha_fin DESC";

$resultado = $conexion->query($sql);

// Verificar si hubo errores en la consulta
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Vehículos</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css"> <!-- Sección de estilos mejorada -->
</head>
<body>
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Estado de Vehículos</h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='reserva.php'" class="btn">Volver al Panel</button>
            <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
        </div>
    </header>
    <main class="container1">

    <main class="rentas-container">
        <h2>Vehículos con Reservas Finalizadas</h2>
        <div class="vehi-container">
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="vehi-card">
                        <img src="<?= htmlspecialchars($fila['imagen']); ?>" 
                             alt="<?= htmlspecialchars($fila['marca'] . ' ' . $fila['modelo']); ?>">
                        <h3><?= htmlspecialchars($fila['marca'] . ' ' . $fila['modelo']); ?></h3>
                        <p><strong>Año:</strong> <?= htmlspecialchars($fila['ano']); ?></p>
                        <p><strong>Disponibilidad:</strong> 
                            <?= $fila['disponible'] == 1 ? '<span class="disponible">Disponible</span>' : 
                                                           '<span class="no-disponible">No Disponible</span>'; ?>
                        </p>
                        <button onclick="location.href='cambiarEstado.php?id=<?= urlencode($fila['carro_id']); ?>'" 
                                class="btn-estado">Cambiar Estado</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-message">No hay vehículos en espera de actualización de estado.</p>
            <?php endif; ?>
        </div>
    </main>
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
