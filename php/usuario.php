<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener el rol del usuario desde la sesión
$rol = $_SESSION['rol'];

// Redirigir a los usuarios al archivo catalogos.php si su rol es de usuario
if ($rol === 'usuario') {
    header('Location: catalogos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Catálogo Usuario</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="logo">
            <img src="../ImagenesCatalogos/LogoCar.png" alt="Logo">
            <h1>BIENVENIDOS</h1>
        </div>
        <div class="nav-buttons">
            <?php if (isset($_SESSION['usuario'])): ?>
                <button onclick="location.href='consultas.php'" class="btn">Consultas</button>
                <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
            <?php else: ?>
                <button onclick="location.href='login.php'" class="btn">Iniciar Sesión</button>
                <button onclick="location.href='crearCuenta.php'" class="btn">Registrar</button>
            <?php endif; ?>
        </div>
    </header>
    <!-- Carrusel -->
    <section class="carousel">
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
    <!-- Banner -->
    <section class="banner">
        <h2>Stock Vehiculos</h2>
    </section>
    <!-- Catálogo -->
    <section class="catalog-container">
        <!-- Aquí irán los elementos del catálogo, igual que en catalogos.php -->
    </section>
    <footer>
        <p>&copy; 2024 CarGo! | <a href="privacy-policy.html">Política de Privacidad</a></p>
    </footer>
</body>
</html>