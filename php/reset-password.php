<?php
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Inicializar variable de error
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nuevaContraseña = $_POST['password'];
    $confirmarContraseña = $_POST['confirm_password'];

    // Verificar si las contraseñas coinciden
    if ($nuevaContraseña !== $confirmarContraseña) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Prevenir inyección SQL
        $token = $conexion->real_escape_string($token);
        $nuevaContraseñaHash = password_hash($nuevaContraseña, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $sql = "UPDATE usuarios SET contraseña = '$nuevaContraseñaHash' WHERE token = '$token'";

        if ($conexion->query($sql) === TRUE) {
            // Eliminar el token después de usarlo
            $sql = "UPDATE usuarios SET token = NULL WHERE token = '$token'";
            $conexion->query($sql);

            header('Location: login.php');
            exit();
        } else {
            $error = 'Error al restablecer la contraseña: ' . mysqli_error($conexion);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Restablecer Contraseña</title>
    <link rel="stylesheet" href="../Css/Login_CrearCuenta.css">
</head>
<body>
    <div class="reset-box">
        
        <h1>Restablecer Contraseña</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="reset-password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>" required>
            <input type="password" name="password" placeholder="Nueva Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            <button type="submit">Restablecer Contraseña</button>
        </form>
    </div>
    <script src="../Js/funciones.js"></script>
</body>
</html>