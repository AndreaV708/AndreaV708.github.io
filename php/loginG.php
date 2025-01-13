<?php
// Solo habilitar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();
require_once '../libreria/vendor/autoload.php'; // Biblioteca Google API
include('db.php'); // Archivo de conexión a la base de datos

// Configuración del cliente de Google
$client = new Google_Client();
$client->setClientId('133150055390-k6oro1jabgi0gl03orqr9ooqkr8r96ub.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/AlquilerVehiculo/php/loginG.php');
$client->addScope(['email', 'profile']);
$client->setPrompt('select_account'); // Configurar prompt directamente

try {
    if (isset($_GET['code'])) {
        // Intercambiar el "code" por el token de acceso
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception("Error al obtener el token de Google: " . $token['error_description']);
        }

        $client->setAccessToken($token['access_token']);

        // Obtener información del usuario desde Google
        $google_oauth = new Google_Service_Oauth2($client);
        $google_user_info = $google_oauth->userinfo->get();

        if (empty($google_user_info->email)) {
            throw new Exception("No se pudo obtener el email del usuario.");
        }

        $email = $google_user_info->email;
        $nombre = $google_user_info->name;
        $google_id = $google_user_info->id;
        $rol_default = 'usuario';

        // Verificar la conexión a la base de datos
        if (!$conexion) {
            throw new Exception("Error de conexión a la base de datos.");
        }

        // Buscar si el usuario ya existe en la base de datos
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conexion->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Usuario existe, iniciar sesión
            $fila = $resultado->fetch_assoc();
            $_SESSION['usuario'] = $fila['nombre_usuario'];
            $_SESSION['rol'] = $fila['rol'];
        } else {
            // Usuario no existe, registrarlo
            $nombre_usuario = strtolower(str_replace(' ', '_', $nombre)); // Generar nombre único
            $sql_insert = "INSERT INTO usuarios (nombre_completo, email, nombre_usuario, rol) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conexion->prepare($sql_insert);

            if (!$stmt_insert) {
                throw new Exception("Error al preparar la inserción: " . $conexion->error);
            }

            $stmt_insert->bind_param('ssss', $nombre, $email, $nombre_usuario, $rol_default);
            if ($stmt_insert->execute()) {
                $_SESSION['usuario'] = $nombre_usuario;
                $_SESSION['rol'] = $rol_default;
            } else {
                throw new Exception("Error al insertar usuario: " . $stmt_insert->error);
            }
        }

        // Redirigir según el rol del usuario
        $redirect_url = 'usuario.php';
        if ($_SESSION['rol'] === 'admin') {
            $redirect_url = 'admin.php';
        } elseif ($_SESSION['rol'] === 'empleado') {
            $redirect_url = 'empleado.php';
        }
        header("Location: $redirect_url");
        exit();
    } else {
        // Generar URL de inicio de sesión con Google
        $google_login_url = $client->createAuthUrl();
        header("Location: $google_login_url");
        exit();
    }
} catch (Exception $e) {
    // Mostrar error solo en desarrollo
    echo "Error: " . $e->getMessage();
}
?>
