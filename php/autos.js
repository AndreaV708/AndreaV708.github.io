document.addEventListener("DOMContentLoaded", function () {
    fetch("php/obtenerAutos.php")
        .then(response => response.json())
        .then(autos => {
            console.log("Autos recibidos:", autos); // Debug: Verifica si los datos llegan

            const carList = document.querySelector('.car-list');
            if (!carList) {
                console.error("No se encontró el contenedor de autos.");
                return;
            }

            carList.innerHTML = ''; // Limpiar antes de agregar los autos

            autos.forEach(auto => {
                carList.innerHTML += `
                    <div class="car-card">
                        <img src="php/${auto.imagen}" alt="${auto.marca} ${auto.modelo}">
                        <h3>${auto.marca} ${auto.modelo}</h3>
                        <p>Año: ${auto.ano}</p>
                        <p>Precio: $${auto.precio} por día</p>
                        <button class="reserve-btn">Reservar</button>
                    </div>
                `;
            });

            document.querySelectorAll('.reserve-btn').forEach(button => {
                button.addEventListener('click', () => {
                    alert('Debes iniciar sesión para reservar un auto.');
                    window.location.href = 'php/login.php';
                });
            });
        })
        .catch(error => console.error("Error al obtener autos:", error));
});
