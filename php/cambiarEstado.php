<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include('db.php'); // Conectar a la base de datos

// Obtener el ID del vehículo
$carro_id = $_GET['id'] ?? null;
if (!$carro_id) {
    die("ID de vehículo no proporcionado.");
}

// Obtener detalles del vehículo
$sql = "SELECT c.*, r.estado_vehiculo FROM carros c 
        LEFT JOIN rentas r ON c.id = r.carro_id 
        WHERE c.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $carro_id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 0) {
    die("Vehículo no encontrado.");
}
$carro = $resultado->fetch_assoc();

// Procesar la actualización del estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado = $_POST['estado'] ?? null;
    $descripcion_estado = $_POST['descripcion_estado'] ?? null;

    if ($nuevo_estado !== null) {
        $sql_update = "UPDATE carros SET disponible = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("ii", $nuevo_estado, $carro_id);

        if ($stmt_update->execute()) {
            // ✅ **Actualizar la tabla `rentas` con la descripción del estado del vehículo**
            $updateRentaSql = "UPDATE rentas SET estado_vehiculo = ? WHERE carro_id = ?";
            $updateRentaStmt = $conexion->prepare($updateRentaSql);
            $updateRentaStmt->bind_param("si", $descripcion_estado, $carro_id);
            $updateRentaStmt->execute();

            $_SESSION['mensaje'] = "Estado del vehículo actualizado correctamente.";
            header("Location: estadoVehiculo.php");
            exit();
        } else {
            $error = "Error al actualizar el estado: " . $conexion->error;
        }
    } else {
        $error = "Por favor selecciona un estado válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Estado del Vehículo</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Cambiar Estado del Vehículo</h1>
        </div>
        
        <div class="nav-buttons">
            <button onclick="location.href='estadoVehiculo.php'" class="btn">Regresar</button>
        </div>
    </header>

    <main class="container1">
        <main class="rentas-container">
            <h2>Actualizar Estado</h2>
            <div class="vehi-card">
                <img src="<?= htmlspecialchars($carro['imagen']); ?>" alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?>">
                <h3><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?></h3>
                <p><strong>Año:</strong> <?= htmlspecialchars($carro['ano']); ?></p>
            </div>

            <div class="form-container">
                <form method="POST">
                    <label for="estado">Estado del Vehículo:</label>
                    <select name="estado" id="estado" required>
                        <option value="1" <?= $carro['disponible'] == 1 ? 'selected' : ''; ?>>Disponible</option>
                        <option value="0" <?= $carro['disponible'] == 0 ? 'selected' : ''; ?>>Mantenimiento</option>
                    </select>

                    <!-- Campo oculto para la descripción del estado -->
                    <div id="descripcion-container" class="form-group" style="display: none;">
                        <label for="descripcion_estado">Descripción del Estado:</label>
                        <textarea name="descripcion_estado" id="descripcion_estado" placeholder="Ingrese la descripción del estado del vehículo..." required></textarea>
                    </div>

                    <button type="submit" class="btn-estado">Actualizar Estado</button>
                </form>

                <?php 
                    if (isset($error)) { 
                        echo "<p class='message error'>$error</p>"; 
                    } 
                ?>
            </div>
        </main>
    </main>

    <script>
        $(document).ready(function () {
            $("#estado").change(function () {
                let estadoSeleccionado = $(this).val();
                if (estadoSeleccionado == "0") { // Mantenimiento
                    $("#descripcion-container").show();
                    $("#descripcion_estado").attr("placeholder", "Describa el tipo de mantenimiento necesario...");
                } else if (estadoSeleccionado == "1") { // Disponible
                    $("#descripcion-container").show();
                    $("#descripcion_estado").attr("placeholder", "Describa la condición del vehículo para su uso...");
                } else {
                    $("#descripcion-container").hide();
                }
            });

            // Mostrar la caja de descripción si ya hay un estado seleccionado
            $("#estado").trigger("change");
        });
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
