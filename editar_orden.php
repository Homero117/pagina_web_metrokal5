<?php
// Configuración para la conexión a SQL Server
$serverName = "HOMERO_JPC"; // Cambiar por el nombre del servidor
$database = "Proyecto_Integrador1";
$username = "sa"; // Cambiar por tu usuario
$password = "12345678"; // Cambiar por tu contraseña

try {
    // Conexión usando PDO
    $conexion = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Comprobar si se recibió el número de orden interna
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['no_orden_interna'])) {
        $no_orden_interna = $_POST['no_orden_interna'];

        // Consulta para obtener todos los datos relacionados
        $sql = "
            SELECT 
                o.No_orden_interna,
                o.No_registros_asignados,
                o.Fecha_elaboracion,
                o.Magnitud,
                o.Fecha_recepcion,
                o.Fecha_termino_servicio,
                o.Vendedor,
                o.Elaboro_oi,

                c.Id_cliente,
                c.Nombre AS Cliente_Nombre,
                c.Direccion,
                c.Atencion,

                obs.Observaciones_generales,

                df.Id_factura,
                df.Actividad_realizada,
                df.Lugar_calibracion,

                de.Telefono,
                de.Contacto,
                de.Correo,
                de.Servicio,
                de.Fecha_entrega,

                eq.Numero_ingreso,
                eq.Certificado_informe,
                eq.Tipo_servicio,
                eq.Equipo,
                eq.Marca_modelo,
                eq.Codigo_fabricante,
                eq.Serie,
                eq.Identificacion,
                eq.Intervalo,
                eq.Resolucion,
                eq.Grado_clase_escala,
                eq.Accesorios,
                eq.Observaciones AS Equipo_Observaciones,
                eq.Material,
                eq.Numero_partes,
                eq.Numero_plano,
                eq.Numero_cotas,
                eq.Numero_piezas
            FROM Orden_datos o
            LEFT JOIN Cliente c ON o.No_orden_interna = c.No_orden_interna
            LEFT JOIN Observaciones obs ON o.No_orden_interna = obs.No_orden_interna
            LEFT JOIN Datos_factura df ON c.Id_cliente = df.Id_cliente
            LEFT JOIN Datos_empresa de ON c.Id_cliente = de.Id_cliente
            LEFT JOIN Datos_equipo eq ON o.No_orden_interna = eq.No_orden_interna
            WHERE o.No_orden_interna = :no_orden_interna
        ";

        // Preparar y ejecutar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':no_orden_interna', $no_orden_interna, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener los resultados
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($datos)) {
            echo "No se encontraron datos para la orden proporcionada.";
            exit;
        }

        // Mostrar los datos obtenidos
        echo "<h1>Detalles de la Orden</h1>";
        foreach ($datos as $fila) {
            //echo "<pre>" . print_r($fila, true) . "</pre>"; 
        }
    } else {
        echo "No se recibió ningún número de orden.";
    }
} catch (PDOException $e) {
    die("Error de conexión o consulta: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden Interna - MTK-LAB-FOR-59 Rev.4</title>
    <style>

        .scroll-container {
            width: 100%; /* Ancho total de la página */
            overflow-x: auto; /* Habilitar desplazamiento horizontal */
            white-space: nowrap; /* Evitar que las celdas se envuelvan en nuevas líneas */
        }
        /*body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f6f9;
            color: #333;
        }*/
        body {
            font-family: Arial, sans-serif;
            background-color: #0087FF;
            margin: 0; /* Quita los márgenes predeterminados */
            color: white; /* Hace el texto más legible si es necesario */
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2 {
            text-align: center;
            color: #4a90e2;
        }
        .section {
            border: 1px solid #dde2eb;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            background-color: #ffffff;
        }
        .section label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
            color: #4a4a4a;
        }
        .section input[type="text"], 
        .section input[type="date"], 
        .section textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #000000;
            border-radius: 4px;
            background-color: #f9f9fc;
        }
        
        table, th, td {
            border: 1px solid #dde2eb;
            padding: 12px;
            background-color: #ffffff;
        }
        th {
            background-color: #4a90e2;
            color: #ffffff;
        }
        td input[type="text"] {
            width: 100%;
            border: none;
            background-color: transparent;
            text-align: center;
        }
        .footer-text {
            text-align: center;
            font-size: 12px;
            color: #7b8ca2;
            margin-top: 20px;
        }

        /*----------*/
        .button-container {
    margin-top: 20px;
    text-align: center;
}

.btn-primary, .btn-secondary {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    margin: 5px;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: #007BFF;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #28a745;
    color: white;
}

.btn-secondary:hover {
    background-color: #1e7e34;
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Orden Interna</h1>
        <h2>Referencia: MTK-LAB-FOR-59 - Número de Revisión: 4</h2>

        <form id="mainForm" action="procesar_datos2.php" method="POST">
        <div class="section">
            <label for="magnitud">Magnitud:</label>
            <input type="text" id="magnitud" name="magnitud" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Magnitud']); ?>">
            
            <label for="fecha-recepcion">Fecha de Recepción:</label>
            <input type="date" id="fecha_recepcion" name="fecha_recepcion" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Fecha_recepcion']); ?>">
    
            <label for="fecha-termino">Fecha de término de servicio:</label>
            <input type="date" id="fecha_termino" name="fecha_termino" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Fecha_termino_servicio']); ?>">
    
            <label for="vendedor">Vendedor:</label>
            <input type="text" id="vendedor" name="vendedor" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Vendedor']); ?>">
    
            <label for="elaboro-oi">Elaboró OI:</label>
            <input type="text" id="elaboro_oi" name="elaboro_oi" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Elaboro_oi']); ?>">
        </div>

        <div class="section">
            <label for="no-de-oi">No. de OI:</label>
            <input type="number" id="no_de_oi" name="no_de_oi" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['No_orden_interna']); ?>" readonly>
            
            <label for="no-registros-asignados">No. de registro(s) asignado(s):</label>
            <input type="number" id="no_registros_asignados" name="no_registros_asignados" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['No_registros_asignados']); ?>">
    
            <label for="fecha-elaboracion-oi">Fecha de elaboración de OI:</label>
            <input type="date" id="fecha_elaboracion_oi" name="fecha_elaboracion_oi" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Fecha_elaboracion']); ?>">
        </div>

        <div class="section">
            <h3>Datos del cliente para emitir Certificado/Informe:</h3>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Cliente_Nombre']); ?>">

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Direccion']); ?>">

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Telefono']); ?>">

            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto_contacto" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Contacto']); ?>">

            <label for="correo">Correo:</label>
            <input type="text" id="correo" name="correo" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Correo']); ?>">

            <label for="servicio">Servicio:</label>
            <input type="text" id="servicio" name="servicio" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Servicio']); ?>">

            <label for="fecha_entrega">Fecha de entrega:</label>
            <input type="date" id="fecha_entrega" name="fecha_entrega" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Fecha_entrega']); ?>">

            <label for="atencion">Atención:</label>
            <input type="text" id="atencion" name="atencion" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Atencion']); ?>">
        </div>

        <div class="section">
            <h3>Datos del cliente para facturar:</h3>

            <label for="lugar">Lugar de Calibración/Medición:</label>
            <input type="text" id="lugar" name="lugar" style="width: 1035px;" value="<?php echo htmlspecialchars($fila['Lugar_calibracion']); ?>">

            <label for="actividad_realizar">Actividad a realizar:</label>
            <textarea id="actividad_realizar" name="actividad_realizar" rows="3" style="width: 1035px;"><?php echo htmlspecialchars($fila['Actividad_realizada']); ?></textarea>
        </div>

        <div class="scroll-container">
            <table>
                <tr>
                    <th>certificado/Informe</th>
                    <th>Tipo de servicio</th>
                    <th>Equipo</th>
                    <th>Marca/Modelo</th>
                    <th>Cód. Fabricante</th>
                    <th>Serie</th>
                    <th>Identificación</th>
                    <th>Intervalo</th>
                    <th>Resolución</th>
                    <th>Grado/Clase/Escala</th>
                    <th>Accesorios</th>
                    <th>Observaciones</th>
                    <th>Material</th>
                    <th>No. Parte</th>
                    <th>No. Plano</th>
                    <th>No. de Cotas</th>
                    <th>No. de Piezas</th>
                </tr>
                <tr>
                    <td><input type="text" id="certificado_informe" name="certificado_informe" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Certificado_informe']); ?>"></td>
                    <td><input type="text" id="tipo_servicio" name="tipo_servicio" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Tipo_servicio']); ?>"></td>
                    <td><input type="text" id="equipo" name="equipo" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Equipo']); ?>"></td>
                    <td><input type="text" id="marca_modelo" name="marca_modelo" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Marca_modelo']); ?>"></td>
                    <td><input type="text" id="codigo_fabricante" name="codigo_fabricante" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Codigo_fabricante']); ?>"></td>
                    <td><input type="text" id="serie" name="serie" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Serie']); ?>"></td>
                    <td><input type="text" id="identificacion" name="identificacion" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Identificacion']); ?>"></td>
                    <td><input type="text" id="intervalo" name="intervalo" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Intervalo']); ?>"></td>
                    <td><input type="text" id="resolucion" name="resolucion" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Resolucion']); ?>"></td>
                    <td><input type="text" id="grado_clase_escala" name="grado_clase_escala" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Grado_clase_escala']); ?>"></td>
                    <td><input type="text" id="accesorios" name="accesorios" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Accesorios']); ?>"></td>
                    <td><input type="text" id="observaciones" name="observaciones" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Equipo_Observaciones']); ?>"></td>
                    <td><input type="text" id="material" name="material" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Material']); ?>"></td>
                    <td><input type="number" id="no_parte" name="no_parte" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Numero_partes']); ?>"></td>
                    <td><input type="number" id="no_plano" name="no_plano" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Numero_plano']); ?>"></td>
                    <td><input type="number" id="no_cotas" name="no_cotas" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Numero_cotas']); ?>"></td>
                    <td><input type="number" id="no_piezas" name="no_piezas" style="width: 100px;" value="<?php echo htmlspecialchars($fila['Numero_piezas']); ?>"></td>
                </tr>
            </table>
        </div>
        
        <div class="button-container">
            <!-- Botón para subir todos los datos excepto observaciones -->
            <button type="submit" form="mainForm" class="btn-primary">Subir todos los datos</button>
        </div>
        

        </form>

        <form id="observationsForm" action="procesar_observaciones.php" method="POST">
        <div class="section">
            <h3>Observaciones Generales:</h3>

            <label for="no-de-oi">No. de OI:</label>
            <input type="number" id="no_de_oi1" name="no_de_oi1" style="width: 1035px;">

            <label for="observaciones_generales_seccion">Observaciones Generales:</label>
            <input type="text" id="observaciones_generales_seccion" name="observaciones_generales_seccion" style="width: 1035px;">

            <div class="button-container">    
                <!-- Botón para subir solo las observaciones -->
            <button type="submit" form="observationsForm" class="btn-secondary">Subir observaciones generales</button>
            </div>
        </div>
        </form>

        
        <p class="footer-text">
            Prohibida la reproducción total o parcial de este documento propiedad de METROSMART S.A. de C.V.<br>
            Av. Peñuelas No. 5 Nave 29, Col. Peñuelas; Querétaro, Qro. C.P. 76148, Tel.: (442) 220 7054 , (442) 220 9707. <br>
            www.metrokal.com.mx
        </p>
    </div>
    </body>
</html>