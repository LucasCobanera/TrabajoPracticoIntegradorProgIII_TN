<?php
include "conectar.php";
session_start();

$tipo_doc = $_POST['tipo_doc'];
$documento = $_POST['documento'];
$usuario = $_POST['usuario'];
$password = $_POST['password'];


$sql = "SELECT * FROM usuarios WHERE documento = '" . $documento . "' AND tipo_doc = '" . $tipo_doc . "' AND usuario = '" . $usuario . "' AND password = '" . $password . "'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows == 1) {
    $fila = $resultado->fetch_assoc();

    // Guardar variables de sesión
    $_SESSION['documento'] = $fila['documento'];
    $_SESSION['tipo_doc'] = $fila['tipo_doc'];
    $_SESSION['nombre'] = $fila['nombre'];
    $_SESSION['apellido'] = $fila['apellido'];
    $_SESSION['email'] = $fila['email'];
    $_SESSION['usuario'] = $fila['usuario'];

    header("Location: resumen.php");
    exit();
} else {
    echo "<h1>Error de Ingreso</h1>";
    echo "Las credenciales ingresadas no son válidas o tu cuenta aún no está activa.";
    echo "<br><a href='ingreso.html'>Intentar de Nuevo</a>";
}

$conexion->close();
?>x