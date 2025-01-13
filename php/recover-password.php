<?php
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');
require '../libreria/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Prevenir inyección SQL
    $email = $conexion->real_escape_string($email);

    // Verificar si el correo electrónico existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        // Generar un token único
        $token = bin2hex(random_bytes(50));

        // Guardar el token en la base de datos con una fecha de expiración
        $sql = "UPDATE usuarios SET token = '$token', token_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = '$email'";
        $conexion->query($sql);

        // Enviar el correo electrónico
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
                    'allow_self_signed' => true
                )
            );
        
            $mail->setFrom('nixon2000paulhs@gmail.com', 'CarGo');
            $mail->addAddress($email);
        
            $mail->isHTML(true);
            $mail->Subject = 'Restablecer tu contraseña';
            $mail->Body    = "Haz clic en el siguiente enlace para restablecer tu contraseña: 
                              <a href='http://localhost/AlquilerVehiculo/php/reset-password.php?token=$token'>Restablecer contraseña</a>";
        
            $mail->send();
            $mensaje = 'Se ha enviado un enlace para restablecer tu contraseña a tu correo electrónico.';
        } catch (Exception $e) {
            $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }
           
    } else {
        $error = 'Correo electrónico no encontrado.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarGo - Recuperar Contraseña</title>
    <link rel="stylesheet" href="../Css/Login_CrearCuenta.css">
</head>
<body>
    <div class="background">
        <div class="left-bg"></div>
        <div class="right-bg"></div>
    </div>
    <div class="verification-container">
        <h1>Recuperar Contraseña</h1>
        <?php if (isset($mensaje)) { echo '<p class="success">'. $mensaje .'</p>'; } ?>
        <?php if (isset($error)) { echo '<p class="error">'. $error .'</p>'; } ?>
        <form action="recover-password.php" method="POST">
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <button type="submit">Enviar</button>
        </form>
        <a href="login.php">Volver</a>
    </div>
</body>
</html>
