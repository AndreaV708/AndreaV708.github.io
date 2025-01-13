<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Devolución</title>
    <link rel="stylesheet" href="../Css/catalogos.css">
    <link rel="stylesheet" href="../Css/reserva.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../ImagenesHome/logo.png" alt="Logo">
            <h2>Registrar Devolución</h2>
        </div>
        <div class="nav-buttons">
            <button onclick="location.href='reserva.php'" class="btn">Regresar</button>
        </div>
    </header>

    <main class="container1">
        <div class="devolucion-container">
            <form id="devolucionForm" method="POST" action="procesarDevolucion.php">
                <?php
                include "db.php";

                if (isset($_GET["id"])) {
                    $reservaId = intval($_GET["id"]);
                    $sql = "SELECT 
                                r.id AS reserva_id,
                                r.fecha_inicio,
                                r.fecha_fin,
                                r.usuario AS cliente,
                                c.modelo AS vehiculo,
                                c.marca,
                                c.precio AS precio_alquiler
                            FROM rentas r
                            JOIN carros c ON r.carro_id = c.id
                            WHERE r.id = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("i", $reservaId);
                    $stmt->execute();
                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows > 0) {
                        $reserva = $resultado->fetch_assoc();
                    } else {
                        echo "<p>Reserva no encontrada.</p>";
                        exit;
                    }
                } else {
                    echo "<p>ID de reserva no proporcionado.</p>";
                    exit;
                }
                ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($reserva['reserva_id']) ?>">
                <input type="hidden" name="precioAlquiler" value="<?= htmlspecialchars($reserva['precio_alquiler']) ?>">

                <div class="form-group">
                    <label for="cliente">Cliente:</label>
                    <input type="text" id="cliente" value="<?= htmlspecialchars($reserva['cliente']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="vehiculo">Vehículo:</label>
                    <input type="text" id="vehiculo" value="<?= htmlspecialchars($reserva['marca'] . ' ' . $reserva['vehiculo']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="fechaInicio">Fecha de Inicio de Reserva:</label>
                    <input type="text" id="fechaInicio" value="<?= htmlspecialchars($reserva['fecha_inicio']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="fechaFin">Fecha de Fin de Reserva:</label>
                    <input type="text" id="fechaFin" value="<?= htmlspecialchars($reserva['fecha_fin']) ?>" readonly>
                </div>

                <div class="form-group">
    <label for="fechaDevolucion">Fecha de Devolución:</label>
    <input type="date" id="fechaDevolucion" name="fechaDevolucion" required>
</div>

<div id="retrasoRecargo" class="form-group" style="display:none;">
    <label>Recargo por demora:</label>
    <p id="recargoTexto"></p>
</div>

<!-- Campo oculto para almacenar el valor de retraso y enviarlo a PHP -->
<input type="hidden" id="retrasoMonto" name="retraso">

                <div class="form-group">
                    <label for="estadoVehiculo">Estado del Vehículo:</label>
                    <textarea id="estadoVehiculo" name="estadoVehiculo" placeholder="Describa el estado del vehículo" required></textarea>
                </div>

                <div class="form-group">
                    <label>Estado de la devolución (cada check tiene un costo extra):</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="extras[]" value="Infracciones-50"> Infracciones ($50)</label>
                        <label><input type="checkbox" name="extras[]" value="SinGasolina-30"> Sin Gasolina ($30)</label>
                        <label><input type="checkbox" name="extras[]" value="DañosLlantas-70"> Daños en Llantas ($70)</label>
                        <label><input type="checkbox" name="extras[]" value="DañosCarroceria-100"> Daños en la Carrocería ($100)</label>
                        <label><input type="checkbox" name="extras[]" value="DañosVidrios-80"> Daños en Vidrios ($80)</label>
                        <label><input type="checkbox" name="extras[]" value="FaltaAccesorios-40"> Falta de Accesorios ($40)</label>
                        <label><input type="checkbox" name="extras[]" value="Otros-50"> Otros ($50)</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cargoExtra">Cargo Extra Manual (opcional):</label>
                    <input type="number" id="cargoExtra" name="cargoExtra" placeholder="Monto adicional" value="0" min="0">
                </div>

                <div class="form-group">
                    <label for="totalCargo">Total a Cobrar:</label>
                    <input type="text" id="totalCargo" name="totalCargo" value="0" readonly>
                </div>

                <div class="form-group">
                    <label for="metodoPago">Método de Pago:</label>
                    <select id="metodoPago" name="metodoPago" required>
                        <option value="" disabled selected>Seleccione un método</option>
                        <option value="Tarjeta">Tarjeta de Crédito</option>
                        <option value="Transferencia">Transferencia Bancaria</option>
                        <option value="Efectivo">Efectivo</option>
                    </select>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-primary">Registrar Devolución</button>
                </div>
            </form>
        </div>
    </main>
    <script>
        document.getElementById("fechaDevolucion").addEventListener("input", function () {
    calcularTotal();
});

document.getElementById("cargoExtra").addEventListener("input", function () {
    calcularTotal();
});

function calcularTotal() {
    const fechaInicio = new Date(document.getElementById("fechaInicio").value);
    const fechaFin = new Date(document.getElementById("fechaFin").value);
    const fechaDevolucion = new Date(document.getElementById("fechaDevolucion").value);
    const precioAlquiler = parseFloat(document.querySelector('input[name="precioAlquiler"]').value) || 0; // Monto de la renta

    const recargoContainer = document.getElementById("retrasoRecargo");
    const recargoTexto = document.getElementById("recargoTexto");

    let recargo = 0;
    let diasRetraso = 0;

    if (fechaDevolucion > fechaFin) {
        diasRetraso = Math.ceil((fechaDevolucion - fechaFin) / (1000 * 60 * 60 * 24));
        recargo = diasRetraso * precioAlquiler;
        recargoContainer.style.display = "block";
        recargoTexto.textContent = `Recargo por ${diasRetraso} día(s) de retraso: $${recargo.toFixed(2)}`;
    } else {
        recargoContainer.style.display = "none";
    }

    let totalCargos = recargo;
    const checkboxes = document.querySelectorAll('input[name="extras[]"]:checked');
    checkboxes.forEach(checkbox => {
        const value = parseFloat(checkbox.value.split('-')[1]) || 0;
        totalCargos += value;
    });

    const cargoExtra = parseFloat(document.getElementById('cargoExtra').value) || 0;
    if (cargoExtra >= 0) {
        totalCargos += cargoExtra;
    }

    // Incluir el monto original de la renta en el total final
    const totalFinal = precioAlquiler + totalCargos;

    document.getElementById('totalCargo').value = totalFinal.toFixed(2);

    // Guardar el valor de retraso en el campo oculto antes de enviar el formulario
    document.getElementById('retrasoMonto').value = recargo.toFixed(2);
}

$("#devolucionForm").on("submit", function (event) {
    event.preventDefault();
    $.ajax({
        url: "procesarDevolucion.php",
        type: "POST",
        data: $(this).serialize(),
        success: function (response) {
            alert("Devolución registrada con éxito.");
            window.location.href = "reservaAlquilada.php";
        },
        error: function () {
            alert("Error al registrar la devolución.");
        }
    });
});

</script>
    <footer class="fixed-footer">
        <div class="footer-text">
            <h2>CarGo!</h2>
            <p>&copy; 2024 | Todos los derechos reservados</p>
        </div>
    </footer>
</body>
</html>
