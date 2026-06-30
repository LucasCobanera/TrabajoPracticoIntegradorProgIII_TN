<?php

include "conectar.php";

$tipo_doc = $_POST['tipo_doc'];
$documento = $_POST['documento'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$email = $_POST['email'];
$usuario = $_POST['usuario'];
$passwordA = $_POST['passwordA'];
$passwordB = $_POST['passwordB'];

//validacion de contraseñas
if ($passwordA == $passwordB) {
    $password = $passwordA;
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis Tarjetas - Error</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-gray-100 font-sans min-h-screen flex flex-col justify-between">
        <header class="bg-[#004691] text-white text-center py-4 shadow-md">
            <h1 class="text-xl font-semibold">Mis <span class="font-bold">Tarjetas</span></h1>
        </header>
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center border border-gray-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 text-red-500 rounded-full mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">Las contraseñas no coinciden.</p>
                <a href="registro.html" class="inline-block w-full bg-[#004691] hover:bg-blue-800 text-white font-medium py-3 rounded-full transition duration-200">volver al formulario</a>
            </div>
        </main>
        <footer class="bg-gray-50 text-[10px] text-gray-500 text-center p-4 border-t border-gray-200">
            Portal Oficial de Consultas de Liquidaciones Progra3card.
        </footer>
    </body>
    </html>
    <?php
    exit();
}

//validacion de dni o pasaporte
if ($tipo_doc != 'DNI' && $tipo_doc != 'PASAPORTE') {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis Tarjetas - Error</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-gray-100 font-sans min-h-screen flex flex-col justify-between">
        <header class="bg-[#004691] text-white text-center py-4 shadow-md">
            <h1 class="text-xl font-semibold">Mis <span class="font-bold">Tarjetas</span></h1>
        </header>
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center border border-gray-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 text-red-500 rounded-full mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">El tipo de documento debe ser DNI o PASAPORTE.</p>
                <a href="registro.html" class="inline-block w-full bg-[#004691] hover:bg-blue-800 text-white font-medium py-3 rounded-full transition duration-200">volver al formulario</a>
            </div>
        </main>
        <footer class="bg-gray-50 text-[10px] text-gray-500 text-center p-4 border-t border-gray-200">
            Portal Oficial de Consultas de Liquidaciones Progra3card.
        </footer>
    </body>
    </html>
    <?php
    exit();
}

//busqueda de tarjetas con el mismo documento
$sql_check = "SELECT * FROM tarjetas WHERE dni_titular = '" . $documento . "'";
$resultado_check = $conexion->query($sql_check);

if ($resultado_check->num_rows > 0) {

    $sql_update = "UPDATE usuarios SET tipo_doc = '" . $tipo_doc . "', nombre = '" . $nombre . "', apellido = '" . $apellido . "', fecha_nacimiento = '" . $fecha_nacimiento . "', email = '" . $email . "', usuario = '" . $usuario . "', password = '" . $password . "' WHERE documento = '" . $documento . "'";

    if ($conexion->query($sql_update) === TRUE) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mis Tarjetas - Alta Exitosa</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; }
            </style>
        </head>
        <body class="bg-gray-100 font-sans min-h-screen flex flex-col justify-between">
            <header class="bg-[#004691] text-white text-center py-4 shadow-md">
                <h1 class="text-xl font-semibold">Mis <span class="font-bold">Tarjetas</span></h1>
            </header>
            <main class="flex-grow flex items-center justify-center p-6">
                <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center border border-gray-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-50 text-green-500 rounded-full mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Alta exitosa</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed">Tu cuenta ha sido activada con éxito. Ya podés ingresar al portal.</p>
                    <a href="ingreso.html" class="inline-block w-full bg-[#004691] hover:bg-blue-800 text-white font-medium py-3 rounded-full transition duration-200">Ingresar al sistema</a>
                </div>
            </main>
            <footer class="bg-gray-50 text-[10px] text-gray-500 text-center p-4 border-t border-gray-200">
                Portal Oficial de Consultas de Liquidaciones Progra3card.
            </footer>
        </body>
        </html>
        <?php
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mis Tarjetas - Error</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; }
            </style>
        </head>
        <body class="bg-gray-100 font-sans min-h-screen flex flex-col justify-between">
            <header class="bg-[#004691] text-white text-center py-4 shadow-md">
                <h1 class="text-xl font-semibold">Mis <span class="font-bold">Tarjetas</span></h1>
            </header>
            <main class="flex-grow flex items-center justify-center p-6">
                <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center border border-gray-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 text-red-500 rounded-full mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed">Error al activar la cuenta: <?php echo htmlspecialchars($conexion->error); ?></p>
                    <a href="registro.html" class="inline-block w-full bg-[#004691] hover:bg-blue-800 text-white font-medium py-3 rounded-full transition duration-200">volver al formulario</a>
                </div>
            </main>
            <footer class="bg-gray-50 text-[10px] text-gray-500 text-center p-4 border-t border-gray-200">
                Portal Oficial de Consultas de Liquidaciones Progra3card.
            </footer>
        </body>
        </html>
        <?php
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis Tarjetas - Error</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-gray-100 font-sans min-h-screen flex flex-col justify-between">
        <header class="bg-[#004691] text-white text-center py-4 shadow-md">
            <h1 class="text-xl font-semibold">Mis <span class="font-bold">Tarjetas</span></h1>
        </header>
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center border border-gray-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 text-red-500 rounded-full mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">El documento ingresado no posee una tarjeta registrada por la administración.</p>
                <a href="registro.html" class="inline-block w-full bg-[#004691] hover:bg-blue-800 text-white font-medium py-3 rounded-full transition duration-200">volver al formulario</a>
            </div>
        </main>
        <footer class="bg-gray-50 text-[10px] text-gray-500 text-center p-4 border-t border-gray-200">
            Portal Oficial de Consultas de Liquidaciones Progra3card.
        </footer>
    </body>
    </html>
    <?php
}

$conexion->close();

?>