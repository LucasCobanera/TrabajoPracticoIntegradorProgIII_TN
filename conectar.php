<?php
$server = "localhost";
$usuario = "root";
$password = "root";
$baseDatos = "mi_banco_db";

$conexion = new mysqli($server, $usuario, $password, $baseDatos);


if ($conexion->connect_error) {
    die("Fallo al conectar: " . $conexion->connect_error);
}
?>