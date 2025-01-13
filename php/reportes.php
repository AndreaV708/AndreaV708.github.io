<?php
include('db.php'); // Conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - CarGo!</title>
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="../Css/catalogos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Librería Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> <!-- Librería jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script> <!-- Plugin autoTable -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Librería Font Awesome -->
</head>
<body>
<header>
    <div class="logo">
        <img src="../ImagenesHome/logo.png" alt="Logo">
        <h1>Reportes</h1>
    </div>
    <div class="nav-buttons">
        <button onclick="location.href='admin.php'" class="btn"><i class="fas fa-arrow-left"></i> Volver</button>
    </div>
</header>
<main class="container1">
  <!-- Reporte de Uso de Vehículos -->
<section class="table-container table">
    <h2>📌 Reporte de Uso de Vehículos</h2>
    <table border="1" id="tablaUsoVehiculos">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Total Alquileres</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $marcas = [];
            $alquileres = [];
            $sql = "SELECT c.marca, c.modelo, c.ano, COUNT(r.id) AS total_rentas 
                    FROM rentas r 
                    JOIN carros c ON r.carro_id = c.id 
                    GROUP BY c.id 
                    ORDER BY total_rentas DESC";
            $result = $conexion->query($sql);
            while ($row = $result->fetch_assoc()) {
                $marcas[] = $row['marca'] . " " . $row['modelo'];
                $alquileres[] = (int)$row['total_rentas'];
                echo "<tr>
                        <td><b>{$row['marca']}</b></td>
                        <td><b>{$row['modelo']}</b></td>
                        <td><b>{$row['ano']}</b></td>
                        <td><b>{$row['total_rentas']}</b></td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    <button onclick="toggleGrafico('graficoUsoVehiculos')" class="btn corto"><i class="fas fa-chart-bar"></i> Estadísticas</button>
    <canvas id="graficoUsoVehiculos" style="display:none; width: 100%; max-width: 350px; height: 250px;"></canvas>
    <button onclick="descargarPDF('tablaUsoVehiculos', 'graficoUsoVehiculos', 'ReporteUsoVehiculos')" class="btn corto"><i class="fas fa-download"></i> PDF</button>
</section>
<section class="table-container table">
    <h3>📌 Reporte de Devoluciones</h3>
    <table border="1" id="tablaDevoluciones">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Vehículo</th>
                <th>Fecha de Devolución</th>
                <th>Estado del Vehículo</th>
                <th>Retraso (Valor)</th>
                <th>Cargos Extras</th>
                <th>Detalle de Cargos</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $usuarios = [];
            $retrasos = [];
            $sql = "SELECT r.usuario, c.marca, c.modelo, r.fecha_fin, r.estado_vehiculo, 
                           f.retraso, f.total_cargo, f.detalle_cargos
                    FROM rentas r 
                    JOIN carros c ON r.carro_id = c.id 
                    LEFT JOIN facturas f ON r.id = f.renta_id 
                    WHERE r.estado_renta = 'finalizado'";
            $result = $conexion->query($sql);
            while ($row = $result->fetch_assoc()) {
                $retrasoValor = $row['retraso']; // Mostrar el valor directamente
                $usuarios[] = $row['usuario'];
                $retrasos[] = $retrasoValor;
                echo "<tr>
                        <td>{$row['usuario']}</td>
                        <td>{$row['marca']} {$row['modelo']}</td>
                        <td>{$row['fecha_fin']}</td>
                        <td>{$row['estado_vehiculo']}</td>
                        <td>\$" . number_format($retrasoValor, 2) . "</td>
                        <td>\$" . number_format($row['total_cargo'], 2) . "</td>
                        <td>{$row['detalle_cargos']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    <button onclick="toggleGrafico('graficoRetraso')" class="btn corto"><i class="fas fa-chart-bar"></i>📊 Estadísticas</button>
    <canvas id="graficoRetraso" style="display:none; width: 100%; max-width: 350px; height: 250px;"></canvas>
    <button onclick="descargarPDF('tablaDevoluciones', 'graficoRetraso', 'ReporteDevoluciones')" class="btn corto"><i class="fas fa-download"></i> PDF</button>
</section>
<!-- Reporte de Estado de Vehículos -->
<section class="table-container table">
    <h3>📌 Reporte de Estado de Vehículos</h3>
    <table border="1" id="tablaEstadoVehiculos">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Placa</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT marca, modelo, placa, 
                           CASE WHEN disponible = 1 THEN 'Disponible' ELSE 'No Disponible' END AS estado 
                    FROM carros";
            $result = $conexion->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['marca']}</td>
                        <td>{$row['modelo']}</td>
                        <td>{$row['placa']}</td>
                        <td>{$row['estado']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    <button onclick="descargarPDFSimple('tablaEstadoVehiculos', 'ReporteEstadoVehiculos')" class="btn corto"><i class="fas fa-download"></i> Descargar PDF</button>
</section>
<script>
    function toggleGrafico(id) {
        let canvas = document.getElementById(id);
        canvas.style.display = canvas.style.display === 'none' ? 'block' : 'none';
    }
 // Gráfico de Uso de Vehículos
 new Chart(document.getElementById("graficoUsoVehiculos"), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($marcas); ?>,
            datasets: [{
                label: "Total de Alquileres por Vehículo",
                backgroundColor: "rgba(54, 162, 235, 0.6)",
                borderColor: "rgba(54, 162, 235, 1)",
                data: <?php echo json_encode($alquileres); ?>
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: { font: { size: 14 } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { font: { size: 12 }, stepSize: 1 }
                },
                x: {
                    ticks: { font: { size: 12 } }
                }
            }
        }
    });

    // Gráfico de Retrasos en Devoluciones
    new Chart(document.getElementById("graficoRetraso"), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($usuarios); ?>,
            datasets: [{
                label: "Valor de Retrasos en Devoluciones",
                borderColor: "rgba(255, 99, 132, 1)",
                backgroundColor: "rgba(255, 99, 132, 0.6)",
                data: <?php echo json_encode($retrasos); ?>,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Funcionalidad para descargar en PDF
    function descargarPDF(tablaId, graficoId, nombreArchivo) {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        // Agregar tabla al PDF
        pdf.text("Reporte", 10, 10);
        pdf.autoTable({
            html: `#${tablaId}`,
            startY: 20,
            styles: {
                fontSize: 10,
                cellPadding: 2,
                halign: 'center'
            },
            headStyles: {
                fillColor: [54, 162, 235],
                textColor: [255, 255, 255]
            }
        });

        // Agregar gráfico al PDF
        const canvas = document.getElementById(graficoId);
        const imgData = canvas.toDataURL('image/png');
        pdf.addPage();
        pdf.text("Estadísticas", 10, 10);
        pdf.addImage(imgData, 'PNG', 15, 30, 140, 100);

        // Pie de página con fecha
        const pageCount = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${pageCount}`, pdf.internal.pageSize.width - 30, pdf.internal.pageSize.height - 10);
            pdf.text(`Generado el: ${new Date().toLocaleDateString()}`, 10, pdf.internal.pageSize.height - 10);
        }

        pdf.save(`${nombreArchivo}.pdf`);
    }

    function descargarPDFSimple(tablaId, nombreArchivo) {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        // Encabezado del PDF
        pdf.setFontSize(14);
        pdf.text("Reporte de Estado de Vehículos", 10, 10);

        // Agregar tabla al PDF
        pdf.autoTable({
            html: `#${tablaId}`,
            startY: 20,
            styles: {
                fontSize: 10,
                cellPadding: 2,
                halign: 'center'
            },
            headStyles: {
                fillColor: [54, 162, 235],
                textColor: [255, 255, 255]
            }
        });

        // Pie de página con fecha
        const pageCount = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${pageCount}`, pdf.internal.pageSize.width - 30, pdf.internal.pageSize.height - 10);
            pdf.text(`Generado el: ${new Date().toLocaleDateString()}`, 10, pdf.internal.pageSize.height - 10);
        }

        // Descargar archivo
        pdf.save(`${nombreArchivo}.pdf`);
    }
</script>
</main>
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
