document.addEventListener("DOMContentLoaded", () => {
    const images = [
        {
            src: "ImagenesHome/portada.png",
            title: "Bienvenido a CarGo",
            description: "La mejor experiencia en alquiler de vehículos.",
        },
        {
            src: "ImagenesHome/portada1.jpg",
            title: "Viaja con estilo",
            description: "Encuentra el vehículo perfecto para tu aventura.",
        },
        {
            src: "ImagenesHome/portada2.jpeg",
            title: "Explora nuevos destinos",
            description: "Tu viaje comienza aquí.",
        },
        {
            src: "ImagenesHome/portada3.png",
            title: "Conduce con confianza",
            description: "Vehículos confiables para cada ocasión.",
        },
    ];

    let currentIndex = 0;

    // Referencias a los elementos HTML
    const presentationImage = document.getElementById("presentation-image");
    const presentationTitle = document.getElementById("presentation-title");
    const presentationDescription = document.getElementById("presentation-description");
    const reserveButton = document.getElementById("reserve-button");
    const leftArrow = document.getElementById("left-arrow");
    const rightArrow = document.getElementById("right-arrow");

    // Asignar evento al botón "Reservar Ahora"
    document.addEventListener("DOMContentLoaded", () => {
        const reserveButton = document.getElementById("reserve-button");
        if (reserveButton) {
            reserveButton.addEventListener("click", () => {
                window.location.href = "php/login.php";
            });
        } else {
            console.error("El botón 'Reservar Ahora' no se encontró en el DOM.");
        }
    });
    
    // Función para resetear las animaciones
    function resetAnimations() {
        [presentationImage, presentationTitle, presentationDescription].forEach(el => {
            if (el) {
                el.style.animation = "none";
                el.offsetHeight; // Forzar reflujo para reiniciar animaciones
                el.style.animation = "";
            }
        });
    }

    // Función para actualizar la presentación con animación de transición
    function updatePresentation(index) {
        const currentImage = images[index];

        // Aplicar transición de salida
        presentationImage.style.animation = "fade-out-image 0.5s ease-in-out forwards";
        presentationTitle.style.animation = "fade-out 0.5s ease-in-out forwards";
        presentationDescription.style.animation = "fade-out 0.5s ease-in-out forwards";

        setTimeout(() => {
            // Actualizar contenido después de la transición de salida
            presentationImage.src = currentImage.src;
            presentationTitle.textContent = currentImage.title;
            presentationDescription.textContent = currentImage.description;

            // Aplicar transición de entrada
            resetAnimations();
            presentationImage.style.animation = "fade-in-image 1.5s ease-in-out forwards";
            presentationTitle.style.animation = "slide-in-left 1.5s ease-in-out forwards";
            presentationDescription.style.animation = "slide-in-right 1.5s ease-in-out forwards";
        }, 500); // Tiempo coincide con el tiempo de salida
    }

    // Cambiar a la imagen anterior
    function navigateLeft() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updatePresentation(currentIndex);
    }

    // Cambiar a la siguiente imagen
    function navigateRight() {
        currentIndex = (currentIndex + 1) % images.length;
        updatePresentation(currentIndex);
    }

    // Función para la presentación automática
    function startPresentation() {
        setInterval(() => {
            currentIndex = (currentIndex + 1) % images.length;
            updatePresentation(currentIndex);
        }, 7000); // Cambia cada 7 segundos
    }

    // Eventos de navegación manual
    if (leftArrow && rightArrow) {
        leftArrow.addEventListener("click", navigateLeft);
        rightArrow.addEventListener("click", navigateRight);
    }

    // Inicializar la presentación
    updatePresentation(currentIndex);
    startPresentation();
});
