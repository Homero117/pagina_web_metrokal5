<?php
// Configuración de la base de datos
$serverName = "HOMERO_JPC"; // Escapa la barra invertida
$connectionInfo = array(
    "Database" => "Proyecto_Integrador1",
    "UID" => "sa",
    "PWD" => "12345678",
    "CharacterSet" => "UTF-8"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Verificar si se recibió el número de orden interna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['no_orden_interna'])) {
    $no_orden_interna = intval($_POST['no_orden_interna']); // Sanitizar el valor recibido

    // Consulta SQL para obtener los datos de la orden
    $query = "SELECT 
        OI.No_orden_interna,
        OI.No_registros_asignados,
        OI.Fecha_elaboracion,
        OI.Magnitud,
        OI.Fecha_recepcion,
        OI.Fecha_termino_servicio,
        OI.Vendedor,
        OI.Elaboro_oi,
        C.Nombre AS Nombre_cliente,
        C.Direccion,
        C.Atencion,
        F.Actividad_realizada,
        F.Lugar_calibracion,
        O.Observaciones_generales,
        E.Numero_ingreso,
        E.Certificado_informe,
        E.Tipo_servicio,
        E.Equipo,
        E.Marca_modelo,
        E.Codigo_fabricante,
        E.Serie,
        E.Identificacion,
        E.Intervalo,
        E.Resolucion,
        E.Grado_clase_escala,
        E.Accesorios,
        E.Observaciones AS Observaciones_equipo,
        E.Material,
        E.Numero_partes,
        E.Numero_plano,
        E.Numero_cotas,
        E.Numero_piezas
    FROM 
        Orden_datos OI
    JOIN 
        Cliente C ON OI.No_orden_interna = C.No_orden_interna
    JOIN 
        Datos_factura F ON C.Id_cliente = F.Id_cliente
    JOIN 
        Observaciones O ON OI.No_orden_interna = O.No_orden_interna
    JOIN 
        Datos_equipo E ON OI.No_orden_interna = E.No_orden_interna
    WHERE 
        OI.No_orden_interna = ?"; // Consulta con parámetro

    // Preparar y ejecutar la consulta
    $params = array($no_orden_interna); // Parámetro para la consulta
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener el resultado
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row) {
        // Aquí puedes procesar los datos obtenidos, como mostrarlos en un formulario de edición
        /*echo "<pre>";
        print_r($row);
        echo "</pre>";*/
    } else {
        echo "No se encontró la orden con el número proporcionado.";
    }
} else {
    echo "No se recibió un número de orden válido.";
}

// Cerrar conexión
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden Interna</title>
    <style>
        /* Estilo global */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .flex-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .flex-item {
        flex: 1; /* Ambos ocupan el mismo espacio */
        min-width: 0; /* Para evitar problemas de overflow */
    }

        .title {
            text-align: center;
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h3 {
            font-size: 1.5em;
            color: #0056b3;
            border-left: 5px solid #0056b3;
            padding-left: 10px;
            margin-bottom: 15px;
        }

        .list {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }

        .list-item {
            flex: 1 1 50%;
            padding: 10px;
            font-size: 1em;
            color: #555;
        }

        .list-item strong {
            color: #333;
        }

        .clearfix {
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px 15px;
            text-align: left;
            font-size: 0.9em;
        }

        th {
            background-color: #0056b3;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Orden Interna</div>

        <?php 
                // Comprobar si el valor es un objeto DateTime y luego formatearlo
                $fechaRecepcion = (is_a($row['Fecha_recepcion'], 'DateTime')) ? $row['Fecha_recepcion']->format('Y-m-d') : $row['Fecha_recepcion'];
                $fechaTerminoServicio = (is_a($row['Fecha_termino_servicio'], 'DateTime')) ? $row['Fecha_termino_servicio']->format('Y-m-d') : $row['Fecha_termino_servicio'];
                $fechaElaboracion = (is_a($row['Fecha_elaboracion'], 'DateTime')) ? $row['Fecha_elaboracion']->format('Y-m-d') : $row['Fecha_elaboracion'];
                //echo htmlspecialchars($row['Magnitud'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="flex-container">
        <!-- Datos Generales -->
        <div class="section flex-item">
            <h3>Datos Generales</h3>
            <div class="list">
                <div class="list-item">
                    <strong>Magnitud:</strong> <?php echo htmlspecialchars($row['Magnitud'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Fecha de Recepción:</strong> <?php echo $fechaRecepcion; ?>
                </div>
                <div class="list-item">
                    <strong>Fecha de Término de Servicio:</strong> <?php echo $fechaTerminoServicio; ?>
                </div>
                <div class="list-item">
                    <strong>Vendedor:</strong> <?php echo htmlspecialchars($row['Vendedor'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Elaboró OI:</strong> <?php echo htmlspecialchars($row['Elaboro_oi'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Número de OI:</strong> <?php echo htmlspecialchars($row['No_orden_interna'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Fecha de Elaboración:</strong> <?php echo $fechaElaboracion; ?>
                </div>
            </div>
        </div>

        <!-- Datos del Cliente -->
        <div class="section flex-item">
            <h3>Datos del Cliente</h3>
            <div class="list">
                <div class="list-item">
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($row['Nombre_cliente'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Dirección:</strong> <?php echo htmlspecialchars($row['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Atención:</strong> <?php echo htmlspecialchars($row['Atencion'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="list-item">
                    <strong>Lugar de Calibración:</strong> <?php echo htmlspecialchars($row['Lugar_calibracion'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        </div>
    </div>

        <!-- Observaciones Generales -->
        <div class="section">
            <h3>Observaciones Generales</h3>
            <p><?php echo htmlspecialchars($row['Observaciones_generales'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <!-- Datos de Equipos -->
        <div class="section">
            <h3>Datos de Equipos</h3>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Número de Ingreso</th>
                        <th>Certificado/Informe</th>
                        <th>Tipo de Servicio</th>
                        <th>Equipo</th>
                        <th>Marca/Modelo</th>
                        <th>Código de Fabricante</th>
                        <th>Serie</th>
                        <th>Identificación</th>
                        <th>Intervalo</th>
                        <th>Resolución</th>
                        <th>Grado/Clase/Escala</th>
                        <th>Accesorios</th>
                        <th>Observaciones del Equipo</th>
                        <th>Material</th>
                        <th>Número de Piezas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><?php echo htmlspecialchars($row['Numero_ingreso'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Certificado_informe'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Tipo_servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Equipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Marca_modelo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Codigo_fabricante'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Serie'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Identificacion'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Intervalo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Resolucion'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Grado_clase_escala'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Accesorios'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Observaciones_equipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Material'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['Numero_piezas'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
