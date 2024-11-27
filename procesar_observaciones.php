<?php
// Conexión a la base de datos (para SQL Server)
$serverName = "HOMERO_JPC";
$connectionOptions = array(
    "Database" => "Proyecto_Integrador1",
    "Uid" => "sa",
    "PWD" => "12345678"
);

// Conexión usando SQLSRV
$conexion = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conexion === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Recuperar los datos del formulario
$no_orden_interna = $_POST['no_de_oi1'];
$observaciones_generales = $_POST['observaciones_generales_seccion'];

// Validar que los campos no estén vacíos
if (!empty($no_orden_interna) && !empty($observaciones_generales)) {
    // Preparar la consulta SQL
    $sql = "INSERT INTO Observaciones (No_orden_interna, Observaciones_generales) VALUES (?, ?)";

    // Preparar la sentencia
    $params = array($no_orden_interna, $observaciones_generales);
    
    // Ejecutar la consulta con los parámetros
    $stmt = sqlsrv_query($conexion, $sql, $params);

    // Verificar si la inserción fue exitosa
    if ($stmt === false) {
        echo "Error al guardar las observaciones: " . print_r(sqlsrv_errors(), true);
    } else {
        echo "Observaciones guardadas correctamente.";
    }
} else {
    // Mensaje si algún campo está vacío
    echo "Por favor, completa todos los campos requeridos.";
}

// Cerrar la conexión a la base de datos
sqlsrv_close($conexion);
?>
