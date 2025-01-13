<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Obtener el nombre de usuario y el rol desde la sesión
$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

// Si es empleado y presionó "Reservar"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rol === 'empleado' && isset($_POST['carro_id']) && isset($_POST['reserva_id'])) {
    $nuevo_carro_id = intval($_POST['carro_id']);
    $reserva_id = intval($_POST['reserva_id']);

    // Verificar si la reserva existe
    $query_reserva = "SELECT carro_id FROM rentas WHERE id = ?";
    $stmt_reserva = $conexion->prepare($query_reserva);
    $stmt_reserva->bind_param("i", $reserva_id);
    $stmt_reserva->execute();
    $result_reserva = $stmt_reserva->get_result();

    if ($result_reserva->num_rows > 0) {
        $row_reserva = $result_reserva->fetch_assoc();
        $carro_anterior_id = intval($row_reserva['carro_id']);

        // Actualizar el carro anterior a "disponible"
        $query_update_carro_anterior = "UPDATE carros SET disponible = 1 WHERE id = ?";
        $stmt_update_carro_anterior = $conexion->prepare($query_update_carro_anterior);
        $stmt_update_carro_anterior->bind_param("i", $carro_anterior_id);
        $stmt_update_carro_anterior->execute();

        // Actualizar el nuevo carro a "ocupado"
        $query_update_nuevo_carro = "UPDATE carros SET disponible = 0 WHERE id = ?";
        $stmt_update_nuevo_carro = $conexion->prepare($query_update_nuevo_carro);
        $stmt_update_nuevo_carro->bind_param("i", $nuevo_carro_id);
        $stmt_update_nuevo_carro->execute();

        // Actualizar la reserva con el nuevo carro
        $query_update_reserva = "UPDATE rentas SET carro_id = ? WHERE id = ?";
        $stmt_update_reserva = $conexion->prepare($query_update_reserva);
        $stmt_update_reserva->bind_param("ii", $nuevo_carro_id, $reserva_id);
        $stmt_update_reserva->execute();

        // Redirigir de vuelta a reservaManual.php
        header('Location: reservaManual.php');
        exit();
    } else {
        echo "<script>alert('No se encontró la reserva especificada.');</script>";
    }
}

// Construir la consulta SQL con los filtros
$sql = "SELECT * FROM carros WHERE 1=1";

if (isset($_GET['marca']) && $_GET['marca'] != '') {
    $marca = $conexion->real_escape_string($_GET['marca']);
    $sql .= " AND marca = '$marca'";
}
if (isset($_GET['ano']) && $_GET['ano'] != '') {
    $ano = intval($_GET['ano']);
    $sql .= " AND ano = $ano";
}
if (isset($_GET['asientos']) && $_GET['asientos'] != '') {
    $asientos = intval($_GET['asientos']);
    $sql .= " AND asientos = $asientos";
}
if (isset($_GET['transmision']) && $_GET['transmision'] != '') {
    $transmision = $conexion->real_escape_string($_GET['transmision']);
    $sql .= " AND transmision = '$transmision'";
}
if (isset($_GET['disponible']) && $_GET['disponible'] != '') {
    $disponible = intval($_GET['disponible']);
    $sql .= " AND disponible = $disponible";
}
if (isset($_GET['ordenar_precio']) && $_GET['ordenar_precio'] != '') {
    $ordenar_precio = $_GET['ordenar_precio'];
    if ($ordenar_precio == 'menor_mayor') {
        $sql .= " ORDER BY precio ASC";
    } elseif ($ordenar_precio == 'mayor_menor') {
        $sql .= " ORDER BY precio DESC";
    }
}

$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Catálogos</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="../ImagenesHome/logo.png" alt="Logo">
        <h1>Catálogos de Vehículos</h1>
    </div>
    <div class="nav-buttons">
        <?php if ($rol === 'admin'): ?>
            <button onclick="location.href='admin.php'" class="btn">Atrás</button>
        <?php elseif ($rol === 'empleado'): ?>
            <button onclick="location.href='reservaManual.php'" class="btn">Atrás</button>
        <?php else: ?>
            <button onclick="location.href='consultas.php'" class="btn">Consultas</button>
        <?php endif; ?>
        <button onclick="location.href='logout.php'" class="btn">Cerrar Sesión</button>
    </div>
</header>
<section class="carousel">
    <div class="carousel-slide">
        <img src="../ImagenesCatalogos/autoCarretera.jpg" alt="Fondo Catalogo 3">
    </div>
</section>
<section class="banner">
    <h2>Catálogo de Vehículos</h2>
</section>
<div class="container">
    <section class="filtros">
        <h3>Filtros</h3>
        <form action="catalogos.php" method="GET">
            <div class="form-group">
                <label for="marca">Marca:</label>
                <select name="marca" id="marca" class="form-control">
                    <option value="">Todas</option>
                    <option value="Toyota">Toyota</option>
                    <option value="Ford">Ford</option>
                    <option value="Chevrolet">Chevrolet</option>
                    <option value="Honda">Honda</option>
                    <option value="Nissan">Nissan</option>
                    <option value="BMW">BMW</option>
                    <option value="Mercedes-Benz">Mercedes-Benz</option>
                    <option value="Volkswagen">Volkswagen</option>
                    <option value="Audi">Audi</option>
                    <option value="Hyundai">Hyundai</option>
                </select>
            </div>
            <div class="form-group">
                <label for="ano">Año:</label>
                <input type="number" name="ano" id="ano" class="form-control">
            </div>
            <div class="form-group">
                <label for="asientos">Número de Asientos:</label>
                <input type="number" name="asientos" id="asientos" class="form-control">
            </div>
            <div class="form-group">
                <label for="transmision">Transmisión:</label>
                <select name="transmision" id="transmision" class="form-control">
                    <option value="">Todas</option>
                    <option value="Automática">Automática</option>
                    <option value="Manual">Manual</option>
                </select>
            </div>
            <div class="form-group">
                <label for="disponible">Disponibilidad:</label>
                <select name="disponible" id="disponible" class="form-control">
                    <option value="">Todas</option>
                    <option value="1">Disponible</option>
                    <option value="0">No Disponible</option>
                </select>
            </div>
            <div class="form-group">
                <label for="ordenar_precio">Ordenar por Precio:</label>
                <select name="ordenar_precio" id="ordenar_precio" class="form-control">
                    <option value="">Seleccionar</option>
                    <option value="menor_mayor">De menor a mayor</option>
                    <option value="mayor_menor">De mayor a menor</option>
                </select>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn">Filtrar</button>
            </div>
        </form>
    </section>
    <section class="catalogo">
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <div class="catalog-item <?php echo $fila['disponible'] == 0 ? 'no-disponible' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="<?php echo htmlspecialchars($fila['marca'] . ' ' . $fila['modelo']); ?>" class="car-image">
                    <h3><?php echo htmlspecialchars($fila['marca'] . ' ' . $fila['modelo']); ?></h3>
                    <span class="price"><?php echo '$' . number_format($fila['precio'], 2); ?></span>
                    <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                    <div class="buttons">
                        <?php if ($fila['disponible'] == 1): ?>
                            <?php if ($rol === 'empleado'): ?>
                                <form method="POST" action="catalogos.php">
                                    <input type="hidden" name="carro_id" value="<?php echo $fila['id']; ?>">
                                    <input type="hidden" name="reserva_id" value="<?php echo $_GET['reserva_id'] ?? ''; ?>">
                                    <button type="submit" class="btn btn-rentar disponible">Reservar</button>
                                </form>
                            <?php elseif ($rol === 'admin'): ?>
                                <button class="btn btn-rentar no-disponible" disabled>Disponible</button>
                            <?php else: ?>
                                <a href="rent-car.php?id=<?php echo $fila['id']; ?>" class="btn btn-rentar disponible">Reservar</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-rentar no-disponible" disabled>No disponible</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No se encontraron carros en el catálogo.</p>
        <?php endif; ?>
    </section>
</div>
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
