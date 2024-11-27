<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Equipo</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Datos del Equipo</h1>
    <table>
        <thead>
            <tr>
                <th>No Orden Interna</th>
                <th>Número de Ingreso</th>
                <th>Certificado Informe</th>
                <th>Tipo de Servicio</th>
                <th>Equipo</th>
                <th>Marca/Modelo</th>
                <th>Código Fabricante</th>
                <th>Serie</th>
                <th>Identificación</th>
                <th>Intervalo</th>
                <th>Resolución</th>
                <th>Grado/Clase/Escala</th>
                <th>Accesorios</th>
                <th>Observaciones</th>
                <th>Material</th>
                <th>Número de Partes</th>
                <th>Número de Plano</th>
                <th>Número de Cotas</th>
                <th>Número de Piezas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Datos de conexión
            $serverName = "LAPTOP-34QSTRA9\\SQLEXPRESS"; // Cambia según tu configuración
            $connectionOptions = array(
                "Database" => "Proyecto_Integrador1",
                "Uid" => "sa", // Cambia según tu usuario
                "PWD" => "1234", // Cambia según tu contraseña
                "CharacterSet" => "UTF-8"
            );

            // Conexión al servidor SQL Server
            $conn = sqlsrv_connect($serverName, $connectionOptions);

            // Verificar conexión
            if ($conn === false) {
                die("<p>Error en la conexión: " . print_r(sqlsrv_errors(), true) . "</p>");
            }

            // Consulta SQL
            $sql = "SELECT * FROM Datos_equipo";
            $stmt = sqlsrv_query($conn, $sql);

            // Verificar consulta
            if ($stmt === false) {
                die("<p>Error en la consulta: " . print_r(sqlsrv_errors(), true) . "</p>");
            }

            // Generar filas de la tabla
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlentities($row['No_orden_interna'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Numero_ingreso'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Certificado_informe'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Tipo_servicio'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Equipo'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Marca_modelo'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Codigo_fabricante'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Serie'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Identificacion'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Intervalo'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Resolucion'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Grado_clase_escala'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Accesorios'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Observaciones'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Material'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Numero_partes'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Numero_plano'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Numero_cotas'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlentities($row['Numero_piezas'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "</tr>";
            }

            // Liberar recursos y cerrar conexión
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);
            ?>
        </tbody>
    </table>
</body>
</html>


