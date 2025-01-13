<?php
require '../libreria/vendor/autoload.php'; // Incluye Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

// Verifica si se ha recibido el ID de la factura
if (!isset($_GET['id'])) {
    die("ID de factura no proporcionado.");
}

$factura_id = intval($_GET['id']);

// Consulta los datos de la factura
$sql_factura = "SELECT 
                    f.id AS factura_id, 
                    c.modelo AS vehiculo,
                    r.fecha_inicio, 
                    r.fecha_fin, 
                    r.fecha_devolucion, 
                    f.monto AS monto_base, 
                    r.metodo_pago, 
                    f.detalle_cargos, 
                    f.cargo_extra, 
                    f.total_cargo, 
                    f.retraso, 
                    f.total_pago, 
                    f.metodo_pagoCargo, 
                    f.fecha_generacion,
                    f.estado AS estado_factura
                FROM facturas f
                JOIN rentas r ON f.renta_id = r.id
                JOIN carros c ON r.carro_id = c.id
                WHERE f.id = ?";
$stmt = $conexion->prepare($sql_factura);
if (!$stmt) {
    die("Error en la consulta de la factura: " . $conexion->error);
}
$stmt->bind_param("i", $factura_id);
$stmt->execute();
$resultado_factura = $stmt->get_result();

if ($resultado_factura->num_rows === 0) {
    die("Factura no encontrada.");
}

$factura = $resultado_factura->fetch_assoc();

// Convierte la imagen del logo a Base64
$logoPath = '../ImagenesHome/logo.png';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = 'data:image/png;base64,' . $logoData;
} else {
    die("El archivo del logo no existe en la ruta especificada.");
}

// Configura Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Contenido de la factura
$html = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Factura #{$factura['factura_id']}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            background-color: #555;
            color: white;
            padding: 20px;
        }
        .header img {
            max-width: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            margin: 20px;
            font-size: 14px;
        }
        .info-section p {
            margin: 5px 0;
        }
        .table-container {
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            font-weight: bold;
            background-color: #333;
            color: white;
        }
        .footer {
            text-align: center;
            margin: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class='header'>
        <img src='{$logoBase64}' alt='Logo'>
        <h1>Factura #{$factura['factura_id']}</h1>
    </div>
    <div class='info-section'>
        <p><strong>Vehículo:</strong> {$factura['vehiculo']}</p>
        <p><strong>Fecha Inicio:</strong> {$factura['fecha_inicio']}</p>
        <p><strong>Fecha Fin:</strong> {$factura['fecha_fin']}</p>
        <p><strong>Fecha Devolución:</strong> " . ($factura['fecha_devolucion'] ?: 'No devuelto') . "</p>
        <p><strong>Fecha Generación:</strong> {$factura['fecha_generacion']}</p>
    </div>
    <div class='table-container'>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Monto Inicial</td>
                    <td>\$" . number_format($factura['monto_base'], 2) . "</td>
                </tr>
                <tr>
                    <td>Detalle de Cargos</td>
                    <td>{$factura['detalle_cargos']}</td>
                </tr>
                <tr>
                    <td>Cargo Extra</td>
                    <td>\$" . number_format($factura['cargo_extra'], 2) . "</td>
                </tr>
                <tr>
                    <td>Retraso</td>
                    <td>\$" . number_format($factura['retraso'], 2) . "</td>
                </tr>
                <tr class='total'>
                    <td>Total Pago</td>
                    <td>\$" . number_format($factura['total_pago'], 2) . "</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class='info-section'>
        <p><strong>Método de Pago:</strong> {$factura['metodo_pago']}</p>
        <p><strong>Método de Pago de Cargos:</strong> {$factura['metodo_pagoCargo']}</p>
        <p><strong>Estado del Pago:</strong> {$factura['estado_factura']}</p>
    </div>
    <div class='footer'>
        <p>&copy; 2024 CarGo - Facturación. Todos los derechos reservados.</p>
    </div>
</body>
</html>
";

// Genera el PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Enviar el PDF al navegador para descargarlo
$dompdf->stream("factura_{$factura_id}.pdf", ["Attachment" => true]);
?>
