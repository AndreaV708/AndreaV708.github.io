<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener el nombre de usuario de la sesión
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Administración</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css">
    <link rel="stylesheet" href="../Css/admin.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
    <div class="logo">
    <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Bienvenido <?php echo htmlspecialchars($usuario); ?></h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='catalogos.php'" class="btn">Catálogo</button>
            <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
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
    <!-- Banner -->
    <section class="banner">
        <h2>Opciones de Administración</h2>
    </section>
    
    <!-- Opciones de Administración -->
    <section class="admin-options-container">
        <div class="admin-option">
            <h3>Gestionar Carros</h3>
            <div class="buttons">
                <a href="gestioncarros.php" class="btn">Ir a Gestión de Carros</a>
            </div>
        </div>
        <div class="admin-option">
            <h3>Gestionar Usuarios</h3>
            <div class="buttons">
                <a href="gestionusuarios.php" class="btn">Ir a Gestión de Usuarios</a>
            </div>
        </div>
        <div class="admin-option">
            <h3>Reportes</h3>
            <div class="buttons">
                <a href="reportes.php" class="btn">Ir a Reportes</a>
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