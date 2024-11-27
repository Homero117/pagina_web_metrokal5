<?php
// Configuración de conexión a SQL Server
$serverName = "HOMERO_JPC"; 
$connectionInfo = array(
    "Database" => "Proyecto_Integrador1", 
    "UID" => "sa", 
    "PWD" => "12345678", 
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Error en la conexión: " . print_r(sqlsrv_errors(), true));
}

$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';

$cliente = [];
$datos = [];
if ($id_cliente) {
    // Consulta para obtener datos del cliente
    $sqlCliente = "SELECT 
                    c.Nombre AS NombreEmpresa, 
                    de.Contacto AS Contacto, 
                    de.Correo AS Correo,     
                    de.Fecha_entrega AS FechaEntrega, 
                    'Calibración' AS Servicio
               FROM Cliente c
               JOIN Datos_empresa de ON c.Id_cliente = de.Id_cliente
               WHERE c.Id_cliente = ?";
    $paramsCliente = array($id_cliente);
    $stmtCliente = sqlsrv_query($conn, $sqlCliente, $paramsCliente);

    if ($stmtCliente === false) {
        die("Error en la consulta del cliente: " . print_r(sqlsrv_errors(), true));
    }

    $cliente = sqlsrv_fetch_array($stmtCliente, SQLSRV_FETCH_ASSOC);

    // Consulta para obtener los datos de los equipos
    $sql = "SELECT 
                ROW_NUMBER() OVER (ORDER BY de.Numero_ingreso) AS RowNum,
                de.Numero_ingreso, 
                de.Equipo, 
                de.Marca_modelo, 
                de.Serie, 
                c.Id_cliente, 
                de.Numero_partes, 
                de.Accesorios, 
                de.Observaciones
            FROM Datos_equipo de
            JOIN Cliente c ON c.No_orden_interna = de.No_orden_interna
            WHERE c.Id_cliente = ?";
    $params = array($id_cliente);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("Error en la consulta de datos: " . print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $datos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción / Entrega de Equipos / ITEMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background-color: #6200ea;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .search-bar {
            margin-bottom: 2rem;
            text-align: center;
        }
        .search-bar input {
            padding: 0.5rem;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 0.5rem;
        }
        .search-bar button {
            padding: 0.5rem 1rem;
            background-color: #6200ea;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #4500c1;
        }
        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .info ul {
            list-style: none;
            padding: 0;
        }
        .info li {
            margin: 0.5rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 0.8rem;
            text-align: left;
            border: 1px solid #ccc;
        }
        table th {
            background-color: #6200ea;
            color: #fff;
        }
        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        footer {
            text-align: center;
            padding: 1rem;
            background-color: #6200ea;
            color: #fff;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Recepción / Entrega de Equipos / ITEMS</h1>
    </header>
    <div class="container">
        <div class="search-bar">
            <form method="GET" action="">
                <label for="id_cliente">Ingrese el ID del Cliente:</label>
                <input type="number" name="id_cliente" id="id_cliente" required>
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="info">
            <div>
                <ul>
                    <li><strong>Nombre de empresa:</strong> <?php echo isset($cliente['NombreEmpresa']) ? $cliente['NombreEmpresa'] : 'N/A'; ?></li>
                    <li><strong>Teléfono:</strong> <?php echo isset($cliente['Telefono']) ? $cliente['Telefono'] : '555-1234'; ?></li>
                    <li><strong>Fecha de entrega:</strong> <?php echo isset($cliente['FechaEntrega']) ? $cliente['FechaEntrega']->format('d-m-Y') : date('d-m-Y'); ?></li>
                </ul>
            </div>
            <div>
                <ul>
                    <li><strong>Contacto:</strong> <?php echo isset($cliente['Contacto']) ? $cliente['Contacto'] : 'N/A'; ?></li>
                    <li><strong>Correo:</strong> <?php echo isset($cliente['Correo']) ? $cliente['Correo'] : 'ejemplo@correo.com'; ?></li>
                    <li><strong>Servicio:</strong> <?php echo isset($cliente['Servicio']) ? $cliente['Servicio'] : 'Calibración'; ?></li>
                </ul>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número de Ingreso</th>
                    <th>Equipo</th>
                    <th>Marca/Modelo</th>
                    <th>Número de Serie</th>
                    <th>ID Cliente</th>
                    <th>Número de Partes</th>
                    <th>Accesorios</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos)): ?>
                    <?php foreach ($datos as $fila): ?>
                        <tr>
                            <td><?php echo $fila['RowNum']; ?></td>
                            <td><?php echo $fila['Numero_ingreso']; ?></td>
                            <td><?php echo $fila['Equipo']; ?></td>
                            <td><?php echo $fila['Marca_modelo']; ?></td>
                            <td><?php echo $fila['Serie']; ?></td>
                            <td><?php echo $fila['Id_cliente']; ?></td>
                            <td><?php echo $fila['Numero_partes']; ?></td>
                            <td><?php echo $fila['Accesorios']; ?></td>
                            <td><?php echo $fila['Observaciones']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No se encontraron datos para el ID del cliente ingresado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>Proyecto Integrador 1 - 2024</p>
    </footer>
</body>
</html>
