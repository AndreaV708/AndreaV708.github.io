<?php
session_start();

// Verificar si el usuario ha iniciado sesión y es empleado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'empleado') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Catálogo Empleado</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>BIENVENIDOS EMPLEADO</h1>
        </div>
        <div class="nav-buttons">
            <?php if (isset($_SESSION['usuario'])): ?>
                <button onclick="location.href='reserva.php'" class="btn">Panel de Empleado</button>
                <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
            <?php else: ?>
                <button onclick="location.href='login.php'" class="btn">Iniciar Sesión</button>
                <button onclick="location.href='crearCuenta.php'" class="btn">Registrar</button>
            <?php endif; ?>
        </div>
    </header>

    <!-- Carrusel -->
    <section class="carousel1">
        <div class="carousel-track">
            <div class="carousel-slide">
                <img src="../ImagenesCatalogos/FondoCatalogo.png" alt="Fondo Catalogo 1">
            </div>
            <div class="carousel-slide">
                <img src="../ImagenesCatalogos/kilometraje.jpg" alt="Fondo Catalogo 2">
            </div>
            <div class="carousel-slide">
                <img src="../ImagenesCatalogos/autoCarretera.jpg" alt="Fondo Catalogo 3">
            </div>
        </div>
    </section>
    
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
