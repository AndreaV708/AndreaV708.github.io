<?php
session_start();

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Inicializar variables
$errorCreate = '';
$successCreate = '';
$errorUpdate = '';
$successUpdate = '';
$errorDelete = '';
$successDelete = '';

// Crear carro
if (isset($_POST['create'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $ano = $_POST['ano'];
    $asientos = $_POST['asientos'];
    $placa = $_POST['placa'];
    $combustible = $_POST['combustible'];
    $transmision = $_POST['transmision'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio']; // Obtener el precio
    $disponible = $_POST['disponible']; // Obtener el valor del menú desplegable

    // Manejar la carga de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $uploads_dir = 'uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        $imagen = $uploads_dir . '/' . basename($_FILES['imagen']['name']);
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen)) {
            echo "Error al mover el archivo subido.";
        }
    } else {
        $errorCreate = 'No se ha seleccionado ninguna imagen o el archivo es demasiado grande.';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $asientos = $_POST['asientos'];
        $precio = $_POST['precio'];
        $placa = $_POST['placa'];

        if (!is_numeric($asientos)) {
            $errorCreate = "El número de asientos debe ser un número.";
        } elseif ($asientos <= 0) {
            $errorCreate = "No se puede ingresar un número negativo o cero en el campo de asientos.";
        } elseif (!is_numeric($precio)) {
            $errorCreate = "El precio debe ser un número.";
        } elseif ($precio <= 0) {
            $errorCreate = "El precio no puede ser negativo o cero.";
        } else {
            // Verificar si la placa ya existe
            $sql_verificar = "SELECT * FROM carros WHERE placa = ?";
            $stmt_verificar = $conexion->prepare($sql_verificar);
            $stmt_verificar->bind_param("s", $placa);
            $stmt_verificar->execute();
            $resultado_verificar = $stmt_verificar->get_result();

            if ($resultado_verificar->num_rows > 0) {
                $errorCreate = 'Placa ya registrada.';
            } else {
                // Insertar el nuevo carro en la base de datos
                $sql = "INSERT INTO carros (marca, modelo, ano, asientos, placa, combustible, transmision, descripcion, precio, imagen, disponible) VALUES ('$marca', '$modelo', '$ano', '$asientos', '$placa', '$combustible', '$transmision', '$descripcion', '$precio', '$imagen', '$disponible')";

                if ($conexion->query($sql) === TRUE) {
                    $successCreate = 'Carro creado exitosamente.';
                } else {
                    $errorCreate = 'Error al crear el carro: ' . mysqli_error($conexion);
                }
            }
        }
    }
}

// Inicializar la variable $carro
$carro = null;

// Modificar carro
if (isset($_POST['fetch_update'])) {
    $id = $_POST['id'];

    // Obtener datos del carro
    $sql = "SELECT * FROM carros WHERE id='$id'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $carro = $resultado->fetch_assoc();
    } else {
        $errorUpdate = 'No se encontró ningún carro con el ID proporcionado.';
    }
}

// Verificar si $carro está definido antes de acceder a sus elementos
if (isset($carro) && $carro !== null) {
    // Tu código para manejar el carro encontrado
    // Por ejemplo:
    echo "Marca: " . $carro['marca'];
} else {
    echo "No se encontró ningún carro.";
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $ano = $_POST['ano'];
    $asientos = $_POST['asientos'];
    $placa = $_POST['placa'];
    $combustible = $_POST['combustible'];
    $transmision = $_POST['transmision'];
    $descripcion = $_POST['descripcion'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Prevenir inyección SQL
    $marca = $conexion->real_escape_string($marca);
    $modelo = $conexion->real_escape_string($modelo);
    $ano = $conexion->real_escape_string($ano);
    $asientos = $conexion->real_escape_string($asientos);
    $placa = $conexion->real_escape_string($placa);
    $combustible = $conexion->real_escape_string($combustible);
    $transmision = $conexion->real_escape_string($transmision);
    $descripcion = $conexion->real_escape_string($descripcion);

    // Actualizar el carro en la base de datos
    $sql = "UPDATE carros SET marca='$marca', modelo='$modelo', ano='$ano', asientos='$asientos', placa='$placa', combustible='$combustible', transmision='$transmision', descripcion='$descripcion', disponible='$disponible' WHERE id='$id'";

    if ($conexion->query($sql) === TRUE) {
        $successUpdate = 'Carro actualizado exitosamente.';
    } else {
        $errorUpdate = 'Error al actualizar el carro: ' . mysqli_error($conexion);
    }
}

// Eliminar carro
if (isset($_POST['fetch_delete'])) {
    $id = $_POST['id'];

    // Obtener datos del carro
    $sql = "SELECT * FROM carros WHERE id='$id'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $carro = $resultado->fetch_assoc();
    } else {
        $carro = null;
        $errorDelete = 'Carro no encontrado.';
    }

    // Verificar si el carro fue encontrado antes de acceder a sus datos
    if ($carro !== null) {
        // Aquí puedes acceder a los datos del carro
    } else {
        // Manejar el caso en que el carro no fue encontrado
        // No usar echo directamente, almacenar el mensaje en una variable
        $errorDelete = 'Carro no encontrado.';
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Eliminar filas relacionadas en la tabla facturas
    $sqlDeleteFacturas = "DELETE FROM facturas WHERE renta_id IN (SELECT id FROM rentas WHERE carro_id = '$id')";
    if ($conexion->query($sqlDeleteFacturas) === TRUE) {
        // Eliminar filas relacionadas en la tabla rentas
        $sqlDeleteRentas = "DELETE FROM rentas WHERE carro_id = '$id'";
        if ($conexion->query($sqlDeleteRentas) === TRUE) {
            // Ahora eliminar el carro
            $sqlDeleteCarro = "DELETE FROM carros WHERE id = '$id'";
            if ($conexion->query($sqlDeleteCarro) === TRUE) {
                $successDelete = 'Carro eliminado exitosamente.';
            } else {
                $errorDelete = 'Error al eliminar el carro: ' . mysqli_error($conexion);
            }
        } else {
            $errorDelete = 'Error al eliminar las rentas relacionadas: ' . mysqli_error($conexion);
        }
    } else {
        $errorDelete = 'Error al eliminar las facturas relacionadas: ' . mysqli_error($conexion);
    }
}

// Obtener todos los carros
$sql = "SELECT * FROM carros";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Gestión de Carros</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css">
    <link rel="stylesheet" href="../Css/admin.css">    
    <script>
        function validarFormulario() {
            var asientos = document.getElementById('asientos').value;
            var precio = document.getElementById('precio').value;

            if (!/^\d+$/.test(asientos) || asientos <= 0) {
                alert('El número de asientos debe ser un número positivo.');
                return false;
            }

            if (!/^\d+(\.\d{1,2})?$/.test(precio) || precio <= 0) {
                alert('El precio debe ser un número positivo.');
                return false;
            }

            return true;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modelosPorMarca = {
                'Toyota': ['Corolla', 'Camry', 'RAV4', 'Highlander', 'Prius'],
                'Ford': ['F-150', 'Mustang', 'Explorer', 'Escape', 'Fusion'],
                'Chevrolet': ['Silverado', 'Equinox', 'Malibu', 'Tahoe', 'Camaro'],
                'Honda': ['Civic', 'Accord', 'CR-V', 'Pilot', 'Fit'],
                'Nissan': ['Altima', 'Sentra', 'Rogue', 'Murano', 'Maxima'],
                'BMW': ['Serie 3', 'Serie 5', 'X5', 'X3', 'Serie 7'],
                'Mercedes-Benz': ['Clase C', 'Clase E', 'Clase S', 'GLC', 'GLE'],
                'Volkswagen': ['Golf', 'Passat', 'Tiguan', 'Jetta', 'Atlas'],
                'Audi': ['A3', 'A4', 'Q5', 'Q7', 'A6'],
                'Hyundai': ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Kona']
            };

            const marcaSelect = document.getElementById('marca');
            const modeloSelect = document.getElementById('modelo');

            marcaSelect.addEventListener('change', function() {
                const marca = marcaSelect.value;
                const modelos = modelosPorMarca[marca] || [];

                modeloSelect.innerHTML = '';
                modelos.forEach(function(modelo) {
                    const option = document.createElement('option');
                    option.value = modelo;
                    option.textContent = modelo;
                    modeloSelect.appendChild(option);
                });
            });

            // Trigger change event to populate models on page load
            marcaSelect.dispatchEvent(new Event('change'));
        });
    </script>
</head>

<body>
<header>
    <div class="logo">
    <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Gestión de Carros</h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='admin.php'" class="btn">Volver al Panel de Administración</button>
        </div>
    </header>
    <main class="container1">

    <section class="banner">
        <h2>Administrar Carros</h2>
    </section>
    <section class="admin-options-container">
        <div class="admin-option">
            <h3>Crear Carro</h3>
            <?php if ($errorCreate): ?>
                <div class="error-message"><?php echo $errorCreate; ?></div>
            <?php endif; ?>
            <?php if ($successCreate): ?>
                <div class="success-message"><?php echo $successCreate; ?></div>
            <?php endif; ?>
            <form action="gestioncarros.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                <?php include 'formulario_carro.php'; ?>
                <div class="form-group">
                    <label for="imagen">Imagen:</label>
                    <input type="file" name="imagen" id="imagen" required>
                </div>
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
                <button type="submit" name="create">Crear Carro</button>
            </form>
        </div>
        <div class="admin-option">
            <h3>Modificar Carro</h3>
            <?php if ($errorUpdate): ?>
                <div class="error-message"><?php echo $errorUpdate; ?></div>
            <?php endif; ?>
            <?php if ($successUpdate): ?>
                <div class="success-message"><?php echo $successUpdate; ?></div>
            <?php endif; ?>
            <form action="gestioncarros.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="id" placeholder="ID del Carro" required>
                <button type="submit" name="fetch_update">Buscar Carro</button>
            </form>
            <?php if (isset($carro) && isset($_POST['fetch_update']) && empty($errorUpdate)): ?>
                <form action="gestioncarros.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                    <input type="hidden" name="id" value="<?php echo $carro['id']; ?>">
                    <?php include 'formulario_carro.php'; ?>
                    <div class="form-group">
                        <label for="imagen">Imagen:</label>
                        <input type="file" name="imagen" id="imagen">
                    </div>
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" required>
                    <button type="submit" name="update">Modificar Carro</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="admin-option">
            <h3>Eliminar Carro</h3>
            <?php if ($errorDelete): ?>
                <div class="error-message"><?php echo $errorDelete; ?></div>
            <?php endif; ?>
            <?php if ($successDelete): ?>
                <div class="success-message"><?php echo $successDelete; ?></div>
            <?php endif; ?>
            <form action="gestioncarros.php" method="POST">
                <input type="text" name="id" placeholder="ID del Carro" required>
                <button type="submit" name="fetch_delete">Buscar Carro</button>
            </form>
            <?php if (isset($carro) && isset($_POST['fetch_delete']) && empty($errorDelete)): ?>
                <form action="gestioncarros.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $carro['id']; ?>">
                    <p>¿Está seguro que desea eliminar el carro <strong><?php echo $carro['marca'] . ' ' . $carro['modelo']; ?></strong>?</p>
                    <button type="submit" name="delete">Eliminar Carro</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
    <section class="table-container table">
    <table class="reservas-table">
    <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Asientos</th>
                    <th>Placa</th>
                    <th>Combustible</th>
                    <th>Transmisión</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>Disponible</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fila['id']; ?></td>
                        <td><?php echo $fila['marca']; ?></td>
                        <td><?php echo $fila['modelo']; ?></td>
                        <td><?php echo $fila['ano']; ?></td>
                        <td><?php echo $fila['asientos']; ?></td>
                        <td><?php echo $fila['placa']; ?></td>
                        <td><?php echo $fila['combustible']; ?></td>
                        <td><?php echo $fila['transmision']; ?></td>
                        <td><?php echo $fila['descripcion']; ?></td>
                        <td><img src="<?php echo $fila['imagen']; ?>" alt="<?php echo $fila['marca'] . ' ' . $fila['modelo']; ?>" width="100"></td>
                        <td><?php echo $fila['disponible'] ? 'Sí' : 'No'; ?></td>
                    </tr>
                <?php endwhile; ?>
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
<?php if (isset($errorDelete)): ?>
    <div class="error-message"><?php echo $errorDelete; ?></div>
<?php endif; ?>