<?php
// Incluir la conexión a la base de datos
include('db.php');

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../libreria/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar el correo electrónico enviado por el usuario
    $email = $conexion->real_escape_string(trim($_POST['email']));

    // Verificar si el correo electrónico existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $nombreCompleto = $fila['nombre_completo'];
        $token = $fila['token'];

        // Verificar si la cuenta ya está verificada
        if ($fila['verificado'] == 1) {
            echo "Tu cuenta ya está verificada. Puedes iniciar sesión.";
        } else {
            // Enviar el correo de verificación
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'nixon2000paulhs@gmail.com'; // Tu correo Gmail
                $mail->Password = 'pzst iicr iocx hrwg'; // Contraseña de aplicación de Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Configuración para ignorar errores de SSL
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
                $mail->Subject = 'Reenviar Verificación - CarGo';
                $mail->Body = "Hola $nombreCompleto,<br><br>Por favor, verifica tu cuenta haciendo clic en el siguiente enlace:<br>
                               <a href='http://localhost/AlquilerVehiculo/php/verificar.php?token=$token'>Verificar mi cuenta</a><br><br>Saludos,<br>CarGo";

                $mail->send();
                echo "Correo de verificación reenviado. Revisa tu bandeja de entrada.";
            } catch (Exception $e) {
                echo "Error al enviar el correo de verificación: " . $mail->ErrorInfo;
            }
        }
    } else {
        echo "El correo electrónico no está registrado.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
