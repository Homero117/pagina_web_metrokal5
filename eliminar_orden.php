<?php
// Parámetros de conexión a SQL Server
$serverName = "HOMERO_JPC"; // Cambia SERVIDOR\\INSTANCIA por los datos correctos
$connectionOptions = [
    "Database" => "Proyecto_Integrador1", // Cambia por el nombre de tu base de datos
    "UID" => "sa",                  // Cambia por el usuario de tu SQL Server
    "PWD" => "12345678",               // Cambia por la contraseña de tu SQL Server
    "CharacterSet" => "UTF-8"
];

// Establecer la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die("Error al conectar con la base de datos: " . print_r(sqlsrv_errors(), true));
}

// Verificar si se recibió una solicitud POST con el número de orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['no_orden_interna'])) {
    $no_orden_interna = $_POST['no_orden_interna'];

    // Iniciar una transacción para garantizar la integridad de los datos
    sqlsrv_begin_transaction($conn);

    try {
        // Declarar las consultas para eliminar datos en el orden correcto
        $queries = [
            // Eliminar datos relacionados en la tabla Datos_equipo
            "DELETE FROM Datos_equipo WHERE No_orden_interna = ?",
            
            // Eliminar datos relacionados en la tabla Observaciones
            "DELETE FROM Observaciones WHERE No_orden_interna = ?",
            
            // Eliminar datos relacionados en la tabla Datos_factura
            "DELETE FROM Datos_factura WHERE Id_cliente IN (SELECT Id_cliente FROM Cliente WHERE No_orden_interna = ?)",
            
            // Eliminar datos relacionados en la tabla Datos_empresa
            "DELETE FROM Datos_empresa WHERE Id_cliente IN (SELECT Id_cliente FROM Cliente WHERE No_orden_interna = ?)",
            
            // Eliminar datos relacionados en la tabla Cliente
            "DELETE FROM Cliente WHERE No_orden_interna = ?",
            
            // Eliminar la orden principal en la tabla Orden_datos
            "DELETE FROM Orden_datos WHERE No_orden_interna = ?"
        ];

        // Ejecutar cada consulta en orden
        foreach ($queries as $query) {
            $stmt = sqlsrv_prepare($conn, $query, [$no_orden_interna]);
            if (!$stmt || !sqlsrv_execute($stmt)) {
                throw new Exception('Error al ejecutar la consulta: ' . print_r(sqlsrv_errors(), true));
            }
        }

        // Confirmar la transacción si todas las consultas se ejecutaron correctamente
        sqlsrv_commit($conn);

        // Redirigir a la página principal con un mensaje de éxito
        header("Location: CentroOrdenes.php?mensaje=orden_eliminada");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        sqlsrv_rollback($conn);

        // Mostrar el error en caso de fallo
        die("Error al eliminar la orden: " . $e->getMessage());
    }
} else {
    // Si se accede al script sin una solicitud válida, redirigir con un mensaje de error
    header("Location: CentroOrdenes.php?mensaje=error");
    exit();
}

// Cerrar la conexión al finalizar
sqlsrv_close($conn);
?>
