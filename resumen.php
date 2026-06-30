<?php
session_start();
if (!isset($_SESSION['documento'])) {
    header("Location: ingreso.html");
    exit();
}

include "conectar.php";

$documento = $_SESSION['documento'];
$nombreCompleto = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];

//obtener datos de la tarjeta del usuario activo
$sqlTarjeta = "SELECT num_cuenta, numero_tarjeta, banco_emisor, estado, saldo FROM tarjetas WHERE dni_titular = '" . $documento . "'";
$resTarjeta = $conexion->query($sqlTarjeta);

$tarjeta = null;
$liquidaciones = [];

if ($resTarjeta->num_rows > 0) {
    $tarjeta = $resTarjeta->fetch_assoc();
    $num_cuenta = $tarjeta['num_cuenta'];

    //obtener liquidaciones
    $sqlLiq = "SELECT l.id_liquidacion, l.periodo, l.fecha_vencimiento, l.total_a_pagar, l.pago_minimo 
               FROM liquidaciones l 
               INNER JOIN tarjetas t ON l.num_cuenta = t.num_cuenta 
               WHERE t.num_cuenta = " . $num_cuenta . " 
               ORDER BY l.periodo DESC";

    $resLiq = $conexion->query($sqlLiq);
    //guardar cada liquidacion dentro del array
    while ($fila = $resLiq->fetch_assoc()) {
        $liquidaciones[] = $fila;
    }
}
$conexion->close();

$banco_colores = [
    'Banco Nación' => 'from-blue-700 to-cyan-500',
    'Banco Provincia' => 'from-green-600 to-teal-500',
    'Banco Galicia' => 'from-orange-500 to-amber-500',
    'Banco Santander' => 'from-red-600 to-rose-500',
    'Banco BBVA' => 'from-blue-800 to-indigo-900',
    'Banco Macro' => 'from-blue-600 to-indigo-500'
];
$gradient_card = $tarjeta ? ($banco_colores[$tarjeta['banco_emisor']] ?? 'from-gray-700 to-gray-900') : 'from-gray-700 to-gray-900';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Cuenta - Mis Tarjetas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col justify-between text-slate-800">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">💳</span>
                    <span class="text-xl font-extrabold text-[#004691] tracking-tight">Mis <span
                            class="text-blue-500">Tarjetas</span></span>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-slate-400 font-medium">Bienvenido,</p>
                        <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($nombreCompleto); ?></p>
                    </div>
                    <a href="logout.php"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition duration-150">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (!$tarjeta): ?>
            <!-- Caso: Sin Tarjeta Asignada -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center max-w-xl mx-auto mt-12">
                <div class="text-amber-500 text-5xl mb-4">⚠️</div>
                <h2 class="text-2xl font-extrabold text-slate-800 mb-2">Sin Tarjeta Vinculada</h2>
                <p class="text-slate-500 mb-6">No posees una tarjeta de crédito Progra3card asociada a tu DNI actualmente.
                    Contactate con la administración para más información.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Columna Izquierda: Tarjeta de Crédito Visual y Estado -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Tarjeta Visual -->
                    <div
                        class="bg-gradient-to-br <?php echo $gradient_card; ?> text-white rounded-2xl p-6 shadow-lg relative overflow-hidden h-52 flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest opacity-80">
                                    <?php echo htmlspecialchars($tarjeta['banco_emisor']); ?>
                                </p>
                                <h3 class="text-lg font-bold mt-1">Progra3card</h3>
                            </div>
                            <div class="text-right">
                                <span
                                    class="bg-white/20 text-white text-[10px] uppercase font-extrabold px-2.5 py-1 rounded-full border border-white/10">
                                    <?php echo htmlspecialchars($tarjeta['estado']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Número enmascarado inline -->
                        <div class="my-4">
                            <p class="text-xl font-mono tracking-widest text-center">
                                <?php echo "•••• •••• •••• " . substr($tarjeta['numero_tarjeta'], -4); ?>
                            </p>
                        </div>

                        <div class="flex justify-between items-end text-xs">
                            <div>
                                <p class="text-[9px] uppercase tracking-wider opacity-60">Titular</p>
                                <p class="font-bold tracking-wide"><?php echo htmlspecialchars($nombreCompleto); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] uppercase tracking-wider opacity-60">Nro Cuenta</p>
                                <p class="font-bold font-mono"><?php echo $tarjeta['num_cuenta']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Saldo Actual -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo Financiado Actual
                        </h4>
                        <p class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo "$ " . $tarjeta['saldo']; ?></p>
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Límite disponible:</span>
                            <span class="font-semibold text-green-600">Sujeto a aprobación</span>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Liquidación Actual e Historial -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Liquidación Destacada -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-2 h-full bg-[#004691]"></div>

                        <div class="sm:flex sm:justify-between sm:items-center">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Última Liquidación Emitida</h3>
                                <p class="text-xs text-slate-400">Resumen mensual consolidado de tu tarjeta</p>
                            </div>
                            <?php if (count($liquidaciones) > 0): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#004691] border border-blue-100 mt-2 sm:mt-0">
                                    Período <?php echo htmlspecialchars($liquidaciones[0]['periodo']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (count($liquidaciones) == 0): ?>
                            <div class="mt-6 text-center py-8 text-slate-400">
                                <p class="text-sm">No hay liquidaciones emitidas para esta cuenta.</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mt-6">
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <span class="text-xs text-slate-400 block font-medium">Total a Pagar</span>
                                    <span
                                        class="text-xl sm:text-2xl font-black text-red-600 block mt-1"><?php echo "$ " . $liquidaciones[0]['total_a_pagar']; ?></span>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <span class="text-xs text-slate-400 block font-medium">Pago Mínimo</span>
                                    <span
                                        class="text-lg sm:text-xl font-bold text-slate-700 block mt-1"><?php echo "$ " . $liquidaciones[0]['pago_minimo']; ?></span>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                                    <span class="text-xs text-slate-400 block font-medium">Vencimiento</span>
                                    <span class="text-sm sm:text-base font-bold text-slate-700 block mt-2 font-mono">
                                        <?php echo htmlspecialchars($liquidaciones[0]['fecha_vencimiento']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Historial de Liquidaciones -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="text-base font-bold text-slate-800">Historial de Resúmenes</h3>
                            <p class="text-xs text-slate-400">Consulta de períodos anteriores de tu tarjeta</p>
                        </div>

                        <?php
                        // Contamos si hay historial (elementos después de la posición 0)
                        $tieneHistorial = count($liquidaciones) > 1;
                        if (!$tieneHistorial):
                            ?>
                            <div class="text-center py-12 text-slate-400">
                                <p class="text-sm">No existen liquidaciones anteriores en tu historial.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                            <th class="p-4 pl-6">Período</th>
                                            <th class="p-4">Vencimiento</th>
                                            <th class="p-4 text-right">Pago Mínimo</th>
                                            <th class="p-4 text-right">Total a Pagar</th>
                                            <th class="p-4 pr-6">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                                        <?php
                                        $index = 0;
                                        foreach ($liquidaciones as $liq):
                                            // Saltamos la primera liquidación ya que se muestra en la sección destacada
                                            if ($index > 0):
                                                ?>
                                                <tr class="hover:bg-slate-50/50 transition">
                                                    <td class="p-4 pl-6 font-bold text-slate-700">Período
                                                        <?php echo htmlspecialchars($liq['periodo']); ?>
                                                    </td>
                                                    <td class="p-4 font-mono text-slate-500">
                                                        <?php echo htmlspecialchars($liq['fecha_vencimiento']); ?>
                                                    </td>
                                                    <td class="p-4 text-right font-semibold text-slate-600">
                                                        <?php echo "$ " . $liq['pago_minimo']; ?>
                                                    </td>
                                                    <td class="p-4 text-right font-extrabold text-slate-800">
                                                        <?php echo "$ " . $liq['total_a_pagar']; ?>
                                                    </td>
                                                    <td class="p-4 pr-6">
                                                        <button
                                                            onclick="alert('Funcionalidad de impresión de PDF no requerida para esta entrega.')"
                                                            class="text-blue-600 hover:text-blue-800 font-semibold text-xs flex items-center space-x-1">
                                                            <span>Descargar</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            endif;
                                            $index++;
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-6 text-center text-xs text-slate-400">
        <p class="max-w-7xl mx-auto px-4">
            Portal Oficial de Consultas de Liquidaciones Progra3card. © 2026. Todos los derechos reservados.
        </p>
    </footer>

</body>

</html>