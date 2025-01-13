<?php
include('db.php');

$sql = "SELECT * FROM carros WHERE disponible = 1"; 
$result = $conexion->query($sql);

$autos = [];

while ($row = $result->fetch_assoc()) {
    $autos[] = [
        "imagen" => $row['imagen'],
        "marca" => $row['marca'],
        "modelo" => $row['modelo'],
        "ano" => $row['ano'],
        "precio" => $row['precio']
    ];
}

header('Content-Type: application/json');
echo json_encode($autos);
?>
