
/*Home*/
// Función para mostrar el formulario de login y ocultar encabezado y pie de página
/*function showLogin() {
    document.getElementById('header').style.display = 'none';  // Oculta el encabezado
    document.getElementById('footer').style.display = 'none';  // Oculta el pie de página
    document.getElementById('mainContent').style.display = 'none';  // Oculta el contenido principal
    document.querySelector('#loginBox').style.display = 'block';  // Muestra el formulario de login
}

// Función para redirigir al registro
function redirectCrearCuenta() {
    window.location.href = 'crearCuenta.html';
}*/


/* Login */

// Función para manejar el inicio de sesión con Google
/*function continueWithGoogle() {
    alert('Redirigiendo a la autenticación con Google...');
    // Aquí puedes implementar la lógica de autenticación con Google
}*/
/* Crear Cuenta */
/*function validateForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden. Por favor, verifica e inténtalo de nuevo.');
        return false;
    }
    return true;
}*/
function redirectToRegister() {
    window.location.href = "home.html";
}
const track = document.querySelector('.carousel-track');
const slides = Array.from(track.children);
let currentIndex = 0;

function moveToSlide(index) {
    const slideWidth = slides[0].getBoundingClientRect().width;
    track.style.transform = `translateX(-${index * slideWidth}px)`;
}

setInterval(() => {
    currentIndex = (currentIndex + 1) % slides.length;
    moveToSlide(currentIndex);
}, 10000); // Cambiar cada 10 segundos
