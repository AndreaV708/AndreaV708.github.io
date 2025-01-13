<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="../Css/Login_CrearCuenta.css">
    <link rel="stylesheet" href="../Css/catalogos.css">
</head>
<body>
    <!-- Fondo con imágenes diagonales -->
    <div class="background">
        <div class="left-bg"></div>
        <div class="right-bg"></div>
    </div>

    <!-- Contenedor principal -->
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h1>Gracias por registrarte en <span>CarGo</span></h1>
        </div>
    </header>
    
    <!-- Contenedor de la verificación -->
    <main class="container1">
        <div class="veri-container">
            <p>Hemos enviado un correo de verificación a tu dirección de correo electrónico. Por favor, revisa tu bandeja de entrada para verificar tu cuenta.</p>
            <p>¿No recibiste el correo? Ingresa tu correo electrónico para reenviar el correo de verificación.</p>

            <form action="reenviarVerificacion.php" method="POST">
                <input type="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                <button type="submit">Reenviar correo de verificación</button>
            </form>

            <a href="login.php">Volver al inicio de sesión</a>
        </main>
</div>

    <!-- Pie de Página -->
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
