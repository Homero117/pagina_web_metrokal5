<?php
// Conexión a la base de datos SQL Server
$serverName = "HOMERO_JPC"; // Tu servidor e instancia nombrada
$connectionOptions = [
    "Database" => "Proyecto_Integrador1", // Nombre de tu base de datos
    "Uid" => "sa",                        // Usuario de SQL Server
    "PWD" => "12345678",
    "CharacterSet" => "UTF-8"             // Contraseña de SQL Server
];

// Establecer la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Función para obtener las órdenes recientes (últimos 7 días)
function obtenerOrdenesRecientes() {
    global $conn;

    // Obtener las órdenes realizadas en los últimos 7 días
    $sql = "SELECT o.No_orden_interna, c.Nombre AS Cliente, de.Equipo, o.Fecha_elaboracion, o.Fecha_termino_servicio 
            FROM Orden_datos o
            JOIN Cliente c ON o.No_orden_interna = c.No_orden_interna
            JOIN Datos_equipo de ON o.No_orden_interna = de.No_orden_interna
            WHERE o.Fecha_elaboracion >= DATEADD(DAY, -7, GETDATE())";

    $stmt = sqlsrv_query($conn, $sql);
    return $stmt;
}

// Función para obtener las órdenes en proceso (más de 7 días)
function obtenerOrdenesEnProceso() {
    global $conn;

    // Obtener las órdenes realizadas hace más de 7 días
    $sql = "SELECT o.No_orden_interna, c.Nombre AS Cliente, de.Equipo, o.Fecha_elaboracion, o.Fecha_termino_servicio 
            FROM Orden_datos o
            JOIN Cliente c ON o.No_orden_interna = c.No_orden_interna
            JOIN Datos_equipo de ON o.No_orden_interna = de.No_orden_interna
            WHERE o.Fecha_elaboracion < DATEADD(DAY, -7, GETDATE())";

    $stmt = sqlsrv_query($conn, $sql);
    return $stmt;
}

// Función para obtener las órdenes por entregar (3 días o menos, sin pasar la fecha de entrega)
function obtenerOrdenesPorEntregar() {
    global $conn;

    // Obtener las órdenes que están a 3 días o menos de alcanzar la fecha de término
    // y asegurarse de que la fecha de término no haya pasado
    $sql = "SELECT o.No_orden_interna, c.Nombre AS Cliente, de.Equipo, o.Fecha_elaboracion, o.Fecha_termino_servicio 
            FROM Orden_datos o
            JOIN Cliente c ON o.No_orden_interna = c.No_orden_interna
            JOIN Datos_equipo de ON o.No_orden_interna = de.No_orden_interna
            WHERE o.Fecha_termino_servicio >= GETDATE() 
            AND o.Fecha_termino_servicio <= DATEADD(DAY, 3, GETDATE())";

    $stmt = sqlsrv_query($conn, $sql);
    return $stmt;
}


// Manejo de las acciones de los botones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["tipoOrden"])) {
        $tipoOrden = $_POST["tipoOrden"];
        if ($tipoOrden == "recientes") {
            $ordenesRecientes = obtenerOrdenesRecientes();
        } elseif ($tipoOrden == "proceso") {
            $ordenesEnProceso = obtenerOrdenesEnProceso();
        } elseif ($tipoOrden == "entregar") {
            $ordenesPorEntregar = obtenerOrdenesPorEntregar();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Órdenes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            color: #333;
        }
        .header {
            background-color: #0087FF;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            text-align: center;
            padding: 20px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .table th {
            background-color: #0087FF;
            color: white;
        }
        .btn-actions {
            display: flex;
            justify-content: space-around;
        }
        .btn-actions button {
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-actions button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Centro de Órdenes</h1>
</div>

<div class="container">
    <?php if (!isset($ordenesRecientes) && !isset($ordenesEnProceso) && !isset($ordenesPorEntregar)) { ?>
        <button class="btn" name="tipoOrden" value="recientes" onclick="mostrarOrdenes('recientes')">Órdenes Recientes</button>
        <button class="btn" name="tipoOrden" value="proceso" onclick="mostrarOrdenes('proceso')">Órdenes en Proceso</button>
        <button class="btn" name="tipoOrden" value="entregar" onclick="mostrarOrdenes('entregar')">Órdenes por Entregar</button>
    <?php } ?>

    <?php if (isset($ordenesRecientes)) { ?>
        <h2>Órdenes Recientes</h2>
        <table class="table">
            <tr>
                <th>Numero de Orden Interna</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Fecha de Elaboración</th>
                <th>Fecha de Término de Servicio</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = sqlsrv_fetch_array($ordenesRecientes, SQLSRV_FETCH_ASSOC)) { ?>
                <tr>
                    <td><?php echo $row['No_orden_interna']; ?></td>
                    <td><?php echo $row['Cliente']; ?></td>
                    <td><?php echo $row['Equipo']; ?></td>
                    <td><?php echo $row['Fecha_elaboracion']->format('Y-m-d'); ?></td>
                    <td><?php echo $row['Fecha_termino_servicio'] ? $row['Fecha_termino_servicio']->format('Y-m-d') : 'Pendiente'; ?></td>
                    <td class="btn-actions">
                    <form method="POST" action="generar_orden.php">
                    <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                    <button type="submit" style="margin: 10px; padding: 10px 20px;">Generar Orden</button>
                    </form>
                                <form method="POST" action="editar_orden.php" onsubmit="return confirm('¿Estás seguro de que deseas editar esta orden?');">
                                <!-- Campo oculto que envía el identificador de la orden -->
                                <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                                <!-- Botón para confirmar la acción de editar -->
                                <button type="submit" style="
                                    margin: 10px; 
                                    padding: 10px 20px; 
                                    background-color: #4CAF50; 
                                    color: white; 
                                    border: none; 
                                    border-radius: 5px; 
                                    cursor: pointer;
                                ">Editar Orden</button>
                                </form>

                            <form method="POST" action="eliminar_orden.php" onsubmit="return confirm('¿Estás seguro de que deseas borrar esta orden?');">
                            <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                            <button type="submit" style="margin: 10px; padding: 10px 20px;">Borrar Orden</button>
                            </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <?php if (isset($ordenesEnProceso)) { ?>
        <h2>Órdenes en Proceso</h2>
        <table class="table">
            <tr>
                <th>Numero de Orden Interna</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Fecha de Elaboración</th>
                <th>Fecha de Término de Servicio</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = sqlsrv_fetch_array($ordenesEnProceso, SQLSRV_FETCH_ASSOC)) { ?>
                <tr>
                    <td><?php echo $row['No_orden_interna']; ?></td>
                    <td><?php echo $row['Cliente']; ?></td>
                    <td><?php echo $row['Equipo']; ?></td>
                    <td><?php echo $row['Fecha_elaboracion']->format('Y-m-d'); ?></td>
                    <td><?php echo $row['Fecha_termino_servicio']->format('Y-m-d'); ?></td>
                    <td class="btn-actions">
                    <form method="POST" action="generar_orden.php">
                    <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                    <button type="submit" style="margin: 10px; padding: 10px 20px;">Generar Orden</button>
                    </form>
                            <form method="POST" action="editar_orden.php" onsubmit="return confirm('¿Estás seguro de que deseas editar esta orden?');">
                            <!-- Campo oculto que envía el identificador de la orden -->
                            <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                            <!-- Botón para confirmar la acción de editar -->
                            <button type="submit" style="
                                margin: 10px; 
                                padding: 10px 20px; 
                                background-color: #4CAF50; 
                                color: white; 
                                border: none; 
                                border-radius: 5px; 
                                cursor: pointer;
                            ">Editar Orden</button>
                            </form>

                        <form method="POST" action="eliminar_orden.php" onsubmit="return confirm('¿Estás seguro de que deseas borrar esta orden?');">
                            <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                            <button type="submit" style="margin: 10px; padding: 10px 20px;">Borrar Orden</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <?php if (isset($ordenesPorEntregar)) { ?>
        <h2>Órdenes por Entregar</h2>
        <table class="table">
            <tr>
                <th>Numero de Orden Interna</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Fecha de Elaboración</th>
                <th>Fecha de Término de Servicio</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = sqlsrv_fetch_array($ordenesPorEntregar, SQLSRV_FETCH_ASSOC)) { ?>
                <tr>
                    <td><?php echo $row['No_orden_interna']; ?></td>
                    <td><?php echo $row['Cliente']; ?></td>
                    <td><?php echo $row['Equipo']; ?></td>
                    <td><?php echo $row['Fecha_elaboracion']->format('Y-m-d'); ?></td>
                    <td><?php echo $row['Fecha_termino_servicio']->format('Y-m-d'); ?></td>
                    <td class="btn-actions">
                    <form method="POST" action="generar_orden.php">
                    <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                    <button type="submit" style="margin: 10px; padding: 10px 20px;">Generar Orden</button>
                    </form>
                                <form method="POST" action="editar_orden.php" onsubmit="return confirm('¿Estás seguro de que deseas editar esta orden?');">
                                <!-- Campo oculto que envía el identificador de la orden -->
                                <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                                <!-- Botón para confirmar la acción de editar -->
                                <button type="submit" style="
                                    margin: 10px; 
                                    padding: 10px 20px; 
                                    background-color: #4CAF50; 
                                    color: white; 
                                    border: none; 
                                    border-radius: 5px; 
                                    cursor: pointer;
                                ">Editar Orden</button>
                                </form>

                        <form method="POST" action="eliminar_orden.php" onsubmit="return confirm('¿Estás seguro de que deseas borrar esta orden?');">
                            <input type="hidden" name="no_orden_interna" value="<?php echo $row['No_orden_interna']; ?>">
                            <button type="submit" style="margin: 10px; padding: 10px 20px;">Borrar Orden</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</div>

<script>
    // Función para mostrar las ordenes de acuerdo al tipo de boton presionado
    function mostrarOrdenes(tipoOrden) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="tipoOrden" value="' + tipoOrden + '">';
        document.body.appendChild(form);
        form.submit();
    }
</script>

</body>
</html>
