<?php
$host = 'localhost:3308';
$usuario = 'root';
$contraseña = '';
$base_de_datos = 'cargodb';

$conexion = new mysqli($host, $usuario, $contraseña, $base_de_datos);

if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}
?>