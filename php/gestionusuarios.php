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

// Crear usuario
if (isset($_POST['create'])) {
    $nombreCompleto = $_POST['fullname'];
    $correo = $_POST['email'];
    $usuario = $_POST['username'];
    $contraseña = $_POST['password'];
    $rol = $_POST['rol'];

    // Prevenir inyección SQL
    $correo = $conexion->real_escape_string($correo);
    $usuario = $conexion->real_escape_string($usuario);

    // Verificar si el correo o el nombre de usuario ya existen
    $sqlVerificar = "SELECT * FROM usuarios WHERE email = '$correo' OR nombre_usuario = '$usuario'";
    $resultadoVerificar = $conexion->query($sqlVerificar);

    if ($resultadoVerificar->num_rows > 0) {
        $errorCreate = 'El correo electrónico o el nombre de usuario ya están registrados.';
    } else {
        $contraseñaHash = password_hash($contraseña, PASSWORD_BCRYPT);

        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre_completo, email, nombre_usuario, contraseña, rol) VALUES ('$nombreCompleto', '$correo', '$usuario', '$contraseñaHash', '$rol')";

        if ($conexion->query($sql) === TRUE) {
            $successCreate = 'Usuario creado exitosamente.';
        } else {
            $errorCreate = 'Error al crear el usuario: ' . mysqli_error($conexion);
        }
    }
}

// Modificar usuario
if (isset($_POST['fetch_update'])) {
    $id = $_POST['id'];

    // Obtener datos del usuario
    $sql = "SELECT * FROM usuarios WHERE id='$id'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
    } else {
        $errorUpdate = 'Usuario no encontrado.';
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nombreCompleto = $_POST['fullname'];
    $correo = $_POST['email'];
    $usuario = $_POST['username'];
    $rol = $_POST['rol'];

    // Prevenir inyección SQL
    $correo = $conexion->real_escape_string($correo);
    $usuario = $conexion->real_escape_string($usuario);

    // Actualizar el usuario en la base de datos
    $sql = "UPDATE usuarios SET nombre_completo='$nombreCompleto', email='$correo', nombre_usuario='$usuario', rol='$rol' WHERE id='$id'";

    if ($conexion->query($sql) === TRUE) {
        $successUpdate = 'Usuario modificado exitosamente.';
    } else {
        $errorUpdate = 'Error al modificar el usuario: ' . mysqli_error($conexion);
    }
}

// Eliminar usuario
if (isset($_POST['fetch_delete'])) {
    $id = $_POST['id'];

    // Obtener datos del usuario
    $sql = "SELECT * FROM usuarios WHERE id='$id'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
    } else {
        $errorDelete = 'Usuario no encontrado.';
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Eliminar el usuario de la base de datos
    $sql = "DELETE FROM usuarios WHERE id='$id'";

    if ($conexion->query($sql) === TRUE) {
        $successDelete = 'Usuario eliminado exitosamente.';
    } else {
        $errorDelete = 'Error al eliminar el usuario: ' . mysqli_error($conexion);
    }
}

// Obtener todos los usuarios
$sql = "SELECT * FROM usuarios";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css">
    <link rel="stylesheet" href="../Css/admin.css">    </head>
<body>
    <header>
    <div class="logo">
    <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Gestión de Usuarios</h1>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='admin.php'" class="btn">Volver al Panel de Administración</button>
        </div>
    </header>
    <main class="container1">
    <section class="banner">
        <h2>Administrar Usuarios</h2>
    </section>
    <section class="admin-options-container">
        <div class="admin-option">
        <h3>Crear Usuario</h3>
        <?php if ($errorCreate): ?>
            <div class="error-message"><?php echo $errorCreate; ?></div>
        <?php endif; ?>
        <?php if ($successCreate): ?>
            <div class="success-message"><?php echo $successCreate; ?></div>
        <?php endif; ?>
        <form class="formulario-admin" action="gestionusuarios.php" method="POST">
            <div class="form-group">
                <label for="fullname">Nombre Completo:</label>
                <input type="text" name="fullname" id="fullname" placeholder="Nombre Completo" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" placeholder="Correo Electrónico" required>
            </div>

            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" name="username" id="username" placeholder="Nombre de Usuario" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" placeholder="Contraseña" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="admin">Administrador</option>
                    <option value="empleado">Empleado</option>
                    <option value="usuario">Usuario</option>
                </select>
            </div>

            <button type="submit" name="create">Crear Usuario</button>
        </form>
    </div>

    <div class="admin-option">
        <h3>Modificar Usuario</h3>
        <form class="formulario-admin" action="gestionusuarios.php" method="POST">
            <div class="form-group">
                <label for="id">ID del Usuario:</label>
                <input type="text" name="id" id="id" placeholder="ID del Usuario" required>
            </div>
            <button type="submit" name="fetch_update">Buscar Usuario</button>
        </form>

        <?php if (isset($usuario) && isset($_POST['fetch_update']) && empty($errorUpdate)): ?>
            <form class="formulario-admin" action="gestionusuarios.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

                <div class="form-group">
                    <label for="fullname">Nombre Completo:</label>
                    <input type="text" name="fullname" value="<?php echo $usuario['nombre_completo']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" name="username" value="<?php echo $usuario['nombre_usuario']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select name="rol">
                        <option value="admin" <?php if ($usuario['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
                        <option value="empleado" <?php if ($usuario['rol'] == 'empleado') echo 'selected'; ?>>Empleado</option>
                        <option value="usuario" <?php if ($usuario['rol'] == 'usuario') echo 'selected'; ?>>Usuario</option>
                    </select>
                </div>

                <button type="submit" name="update">Modificar Usuario</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="admin-option">
        <h3>Eliminar Usuario</h3>
        <form class="formulario-admin" action="gestionusuarios.php" method="POST">
            <div class="form-group">
                <label for="id">ID del Usuario:</label>
                <input type="text" name="id" placeholder="ID del Usuario" required>
            </div>
            <button type="submit" name="fetch_delete">Buscar Usuario</button>
        </form>

        <?php if (isset($usuario) && isset($_POST['fetch_delete']) && empty($errorDelete)): ?>
            <form class="formulario-admin" action="gestionusuarios.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                <p>¿Está seguro que desea eliminar al usuario <strong><?php echo $usuario['nombre_completo']; ?></strong>?</p>
                <button type="submit" name="delete">Eliminar Usuario</button>
            </form>
        <?php endif; ?>
    </div>
</section>

    <section class="table-container table">
    <h3>Lista de Usuarios</h3>
    <table class="reservas-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Nombre de Usuario</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody id="reservation-table">
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fila['id']; ?></td>
                        <td><?php echo $fila['nombre_completo']; ?></td>
                        <td><?php echo $fila['email']; ?></td>
                        <td><?php echo $fila['nombre_usuario']; ?></td>
                        <td><?php echo $fila['rol']; ?></td>
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