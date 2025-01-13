<?php
// Incluir la conexión a la base de datos
include('db.php');

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../libreria/vendor/autoload.php';

if (isset($_GET['token'])) {
    // Escapar la entrada del usuario para evitar inyecciones SQL
    $token = $conexion->real_escape_string($_GET['token']);

    // Verificar si el token existe y el usuario no está verificado
    $sql = "SELECT * FROM usuarios WHERE token = '$token' AND verificado = 0";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        // Actualizar el estado del usuario a verificado
        $sqlActualizar = "UPDATE usuarios SET verificado = 1, token = NULL WHERE token = '$token'";
        if ($conexion->query($sqlActualizar) === TRUE) {
            echo "¡Tu cuenta ha sido verificada con éxito!";
        } else {
            echo "Error al verificar la cuenta: " . $conexion->error;
        }
    } else {
        // El token no es válido o la cuenta ya está verificada
        echo "Token inválido o la cuenta ya está verificada. <br>";
        echo '<a href="reenviarVerificacion.php">Reenviar correo de verificación</a>';
    }
} else {
    echo "Token no proporcionado.";
}
?>
