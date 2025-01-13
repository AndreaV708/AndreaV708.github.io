<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Obtener el ID del vehículo desde la URL
$id = $_GET['id'];

// Obtener los detalles del vehículo desde la base de datos
$sql = "SELECT * FROM carros WHERE id = $id";
$resultado = $conexion->query($sql);
$carro = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Detalles del Vehículo</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="logo">
            <img src="../ImagenesCatalogos/LogoCar.png" alt="Logo">
            <h1>Detalles del Vehículo</h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='catalogos.php'" class="btn">Volver al Catálogo</button>
            <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
        </div>
    </header>
    <!-- Detalles del vehículo -->
    <section class="vehicle-details">
        <div class="vehicle-image">
            <img src="<?php echo $carro['imagen']; ?>" alt="<?php echo $carro['marca'] . ' ' . $carro['modelo']; ?>">
        </div>
        <div class="vehicle-info">
            <h2><?php echo $carro['marca'] . ' ' . $carro['modelo']; ?></h2>
            <p><strong>Precio:</strong> <?php echo '$' . number_format($carro['precio'], 2); ?></p>
            <p><strong>Año:</strong> <?php echo $carro['ano']; ?></p>
            <p><strong>Número de Asientos:</strong> <?php echo $carro['asientos']; ?></p>
            <p><strong>Número de Placa:</strong> <?php echo $carro['placa']; ?></p>
            <p><strong>Tipo de Combustible:</strong> <?php echo $carro['combustible']; ?></p>
            <p><strong>Transmisión:</strong> <?php echo $carro['transmision']; ?></p>
            <p><strong>Descripción:</strong> <?php echo $carro['descripcion']; ?></p>
            <p><strong>Disponible:</strong> <?php echo $carro['disponible'] ? 'Sí' : 'No'; ?></p>
        </div>
    </section>
    <footer>
        <p>&copy; 2024 CarGo! | <a href="privacy-policy.html">Política de Privacidad</a></p>
    </footer>
</body>
</html>