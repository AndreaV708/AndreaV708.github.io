<?php 
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Inicializar variable de error
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['username'];
    $contraseña = $_POST['password'];

    // Prevenir inyección SQL
    $usuario = $conexion->real_escape_string($usuario);

    // Consultar la base de datos
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();

        // Verificar si el usuario está verificado
        if ($fila['verificado'] == 0) {
            $error = 'Tu cuenta no está verificada. <a href="mensajeVerificacion.php?email=' . $fila['email'] . '">Haz clic aquí para reenviar el correo de verificación.</a>';
        } elseif (password_verify($contraseña, $fila['contraseña'])) {
            // Iniciar sesión
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $fila['rol']; // Asumiendo que la columna 'rol' existe en la tabla 'usuarios'

            // Redirigir según el rol del usuario
            if ($fila['rol'] === 'admin') {
                header('Location: admin.php');
            } elseif ($fila['rol'] === 'empleado') {
                header('Location: empleado.php');
            } else {
                header('Location: usuario.php');
            }
            exit();
        } else {
            $error = 'Contraseña incorrecta. Por favor, inténtalo de nuevo';
        }
    } else {
        $error = 'Usuario no encontrado.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Iniciar Sesión</title>
    <link rel="stylesheet" href="../Css/Login_CrearCuenta.css">
</head>
<body>
    <main>
        <div class="background1">
            <div class="left-bg1"></div>
            <div class="right-bg1"></div>
        </div>
        <div class="verification-container">
            <h1>Iniciar Sesión</h1>
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" class="primary-button">Iniciar Sesión</button>
            </form>
            <button class="google" onclick="window.location.href='loginG.php'">
                <img src="../ImagenesHome/google.png" alt="Google Logo">Continuar con Google
            </button>
            <div class="links">
                <a href="recover-password.php">¿Olvidaste tu contraseña?</a>
                <a href="crearCuenta.php">Crea una cuenta</a>
            </div>
        </div>
    </main>
    <script src="../Js/funciones.js"></script>
</body>
</html>
