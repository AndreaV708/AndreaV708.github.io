<?php
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../libreria/vendor/autoload.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombreCompleto = $_POST['fullname'];
    $email = $_POST['email'];
    $usuario = $_POST['username'];
    $contraseña = $_POST['password'];
    $confirmarContraseña = $_POST['confirm_password'];

    if ($contraseña !== $confirmarContraseña) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $email = $conexion->real_escape_string($email);
        $usuario = $conexion->real_escape_string($usuario);

        // Verificar si el nombre de usuario ya existe
        $sqlUsuario = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
        $resultadoUsuario = $conexion->query($sqlUsuario);

        if ($resultadoUsuario->num_rows > 0) {
            $error = 'El nombre de usuario ya está registrado.';
        } else {
            // Verificar si el correo ya existe
            $sqlEmail = "SELECT * FROM usuarios WHERE email = '$email'";
            $resultadoEmail = $conexion->query($sqlEmail);

            if ($resultadoEmail->num_rows > 0) {
                $error = 'El correo electrónico ya está registrado.';
            } else {
                // Insertar el nuevo usuario
                $contraseñaHash = password_hash($contraseña, PASSWORD_BCRYPT);
                $rol = 'usuario';
                $token = bin2hex(random_bytes(16));
                $sql = "INSERT INTO usuarios (nombre_completo, email, nombre_usuario, contraseña, rol, token) 
                        VALUES ('$nombreCompleto', '$email', '$usuario', '$contraseñaHash', '$rol', '$token')";

                if ($conexion->query($sql) === TRUE) {
                    // Enviar el correo de verificación
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'nixon2000paulhs@gmail.com';
                        $mail->Password = 'pzst iicr iocx hrwg';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true,
                            ),
                        );

                        $mail->setFrom('nixon2000paulhs@gmail.com', 'CarGo');
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Verifica tu cuenta - CarGo';
                        $mail->Body = "Hola $nombreCompleto,<br><br>Gracias por registrarte en CarGo. 
                                       Por favor, verifica tu cuenta haciendo clic en el siguiente enlace:<br>
                                       <a href='http://localhost/AlquilerVehiculo/php/verificar.php?token=$token'>Verificar mi cuenta</a><br><br>Saludos,<br>CarGo";

                        $mail->send();
                        header('Location: mensajeVerificacion.php');
                        exit();
                    } catch (Exception $e) {
                        $error = 'Error al enviar el correo de verificación: ' . $mail->ErrorInfo;
                    }
                } else {
                    $error = 'Error al registrar el usuario: ' . $conexion->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Crear Cuenta</title>
    <link rel="stylesheet" href="../Css/Login_CrearCuenta.css">
</head>
<body>
    <div class="background">
        <div class="left-bg"></div>
        <div class="right-bg"></div>
    </div>
    <div class="verification-container">
        <h1>Crear Cuenta</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="crearCuenta.php" method="POST">
            <input type="text" name="fullname" placeholder="Nombre Completo" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="text" name="username" placeholder="Nombre de Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <a href="login.php">Volver</a>
    </div>
</body>
</html>
