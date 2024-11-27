<?php
// Configuración de la conexión a SQL Server
$serverName = "HOMERO_JPC";
$connectionOptions = [
    "Database" => "Proyecto_Integrador1",
    "UID" => "sa",
    "PWD" => "12345678",
    "CharacterSet" => "UTF-8"
];

// Conexión a la base de datos
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conn === false) {
    die("Error al conectar con la base de datos: " . print_r(sqlsrv_errors(), true));
}



// Iniciar una transacción
sqlsrv_begin_transaction($conn);

try {
    // Recuperar los datos del formulario
    $requiredFields = [
        'no_de_oi', 'no_registros_asignados', 'fecha_elaboracion_oi', 'magnitud',
        'fecha_recepcion', 'fecha_termino', 'vendedor', 'elaboro_oi', 'nombre_cliente',
        'direccion', 'atencion', 'actividad_realizar', 'lugar', 'telefono', 'contacto_contacto',
        'correo', 'servicio', 'fecha_entrega', 'certificado_informe', 'tipo_servicio', 'equipo',
        'marca_modelo', 'codigo_fabricante', 'serie', 'identificacion', 'intervalo', 'resolucion',
        'grado_clase_escala', 'accesorios', 'observaciones', 'material', 'no_parte', 'no_plano',
        'no_cotas', 'no_piezas'
    ];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es obligatorio.");
        }
    }

    // Asignar variables
    $no_orden_interna = $_POST['no_de_oi'];
    $no_registros_asignados = $_POST['no_registros_asignados'];
    $fecha_elaboracion =$_POST['fecha_elaboracion_oi'];
    $magnitud = $_POST['magnitud'];
    $fecha_recepcion = $_POST['fecha_recepcion'];
    $fecha_termino = $_POST['fecha_termino'];

    $fecha_recepcion_obj = DateTime::createFromFormat('Y-m-d', $_POST['fecha_recepcion']);
    $fecha_termino_obj = DateTime::createFromFormat('Y-m-d', $_POST['fecha_termino']);
    $fecha_elaboracion_obj = DateTime::createFromFormat('Y-m-d', $_POST['fecha_elaboracion_oi']);

    // Obtener el formato 'Y-m-d' para la base de datos
    $fecha_recepcion_sql = $fecha_recepcion_obj->format('Y-m-d');
    $fecha_termino_sql = $fecha_termino_obj->format('Y-m-d');
    $fecha_elaboracion_sql = $fecha_elaboracion_obj->format('Y-m-d');

    $vendedor = $_POST['vendedor'];
    $elaboro_oi = $_POST['elaboro_oi'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $direccion = $_POST['direccion'];
    $atencion = $_POST['atencion'];
    $actividad_realizada = $_POST['actividad_realizar'];
    $lugar_calibracion = $_POST['lugar'];
    $telefono = $_POST['telefono'];
    $contacto = $_POST['contacto_contacto'];
    $correo = $_POST['correo'];
    $servicio = $_POST['servicio'];
    $fecha_entrega = $_POST['fecha_entrega'];

    $fecha_entrega_obj = DateTime::createFromFormat('Y-m-d', $_POST['fecha_entrega']);
    $fecha_entrega_sql = $fecha_entrega_obj->format('Y-m-d');

    $certificado_informe = $_POST['certificado_informe'];
    $tipo_servicio = $_POST['tipo_servicio'];
    $equipo = $_POST['equipo'];
    $marca_modelo = $_POST['marca_modelo'];
    $codigo_fabricante = $_POST['codigo_fabricante'];
    $serie = $_POST['serie'];
    $identificacion = $_POST['identificacion'];
    $intervalo = $_POST['intervalo'];
    $resolucion = $_POST['resolucion'];
    $grado_clase_escala = $_POST['grado_clase_escala'];
    $accesorios = $_POST['accesorios'];
    $observaciones_equipo = $_POST['observaciones'];
    $material = $_POST['material'];
    $numero_partes = $_POST['no_parte'];
    $numero_plano = $_POST['no_plano'];
    $numero_cotas = $_POST['no_cotas'];
    $numero_pieza = $_POST['no_piezas'];

    // Insertar en Orden_datos CONVERT(DATE, ?, 103)
    $queryOrden = "INSERT INTO Orden_datos (No_orden_interna, No_registros_asignados, Fecha_elaboracion, Magnitud, Fecha_recepcion, Fecha_termino_servicio, Vendedor, Elaboro_oi) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $paramsOrden = [$no_orden_interna, $no_registros_asignados, $fecha_elaboracion_sql, $magnitud, $fecha_recepcion_sql, $fecha_termino_sql, $vendedor, $elaboro_oi];
    $resultOrden = sqlsrv_query($conn, $queryOrden, $paramsOrden);
    if ($resultOrden === false) {
        throw new Exception("Error al insertar en Orden_datos: " . print_r(sqlsrv_errors(), true));
    }

    // Generar Id_cliente e insertar en Cliente
    $queryClienteMax = "SELECT ISNULL(MAX(Id_cliente), 100) + 1 AS new_id FROM Cliente";
    $resultCliente = sqlsrv_query($conn, $queryClienteMax);
    if ($resultCliente === false) {
        throw new Exception("Error al obtener el nuevo Id_cliente: " . print_r(sqlsrv_errors(), true));
    }
    $rowCliente = sqlsrv_fetch_array($resultCliente, SQLSRV_FETCH_ASSOC);
    $id_cliente = $rowCliente['new_id'];

    $queryCliente = "INSERT INTO Cliente (Id_cliente, Nombre, Direccion, Atencion, No_orden_interna) 
                     VALUES (?, ?, ?, ?, ?)";
    $paramsCliente = [$id_cliente, $nombre_cliente, $direccion, $atencion, $no_orden_interna];
    $resultClienteInsert = sqlsrv_query($conn, $queryCliente, $paramsCliente);
    if ($resultClienteInsert === false) {
        throw new Exception("Error al insertar en Cliente: " . print_r(sqlsrv_errors(), true));
    }

    // Continuar con las demás tablas...
        // Insertar en Datos_empresa
        $queryEmpresa = "INSERT INTO Datos_empresa (Id_cliente, Telefono, Contacto, Correo, Servicio, Fecha_entrega) 
        VALUES (?, ?, ?, ?, ?, ?)";
$paramsEmpresa = [$id_cliente, $telefono, $contacto, $correo, $servicio, $fecha_entrega_sql];
$stmtEmpresa = sqlsrv_query($conn, $queryEmpresa, $paramsEmpresa);

if ($stmtEmpresa === false) {
throw new Exception("Error al insertar en Datos_empresa: " . print_r(sqlsrv_errors(), true));
}

// Generar Numero_ingreso e insertar en Datos_equipo
$queryEquipoMax = "SELECT ISNULL(MAX(Numero_ingreso), 1000) + 1 AS new_ingreso FROM Datos_equipo";
$resultEquipo = sqlsrv_query($conn, $queryEquipoMax);

if ($resultEquipo === false) {
throw new Exception("Error al obtener nuevo Numero_ingreso: " . print_r(sqlsrv_errors(), true));
}

// Generar Id_factura de manera automática
$queryFacturaMax = "SELECT ISNULL(MAX(Id_factura), 0) + 1 AS new_id FROM Datos_factura";
$resultFactura = sqlsrv_query($conn, $queryFacturaMax);

if ($resultFactura === false) {
    throw new Exception("Error al obtener el nuevo Id_factura: " . print_r(sqlsrv_errors(), true));
}

$rowFactura = sqlsrv_fetch_array($resultFactura, SQLSRV_FETCH_ASSOC);
$id_factura = $rowFactura['new_id'];

// Insertar en Datos_factura
$queryFactura = "INSERT INTO Datos_factura (Id_factura, Id_cliente, Actividad_realizada, Lugar_calibracion) 
                 VALUES (?, ?, ?, ?)";
$paramsFactura = [$id_factura, $id_cliente, $actividad_realizada, $lugar_calibracion]; // Asegúrate de definir estos valores antes de la inserción
$stmtFactura = sqlsrv_query($conn, $queryFactura, $paramsFactura);

if ($stmtFactura === false) {
    throw new Exception("Error al insertar en Datos_factura: " . print_r(sqlsrv_errors(), true));
}


$rowEquipo = sqlsrv_fetch_array($resultEquipo, SQLSRV_FETCH_ASSOC);
$numero_ingreso = $rowEquipo['new_ingreso'];

$queryEquipo = "INSERT INTO Datos_equipo (No_orden_interna, Numero_ingreso, Certificado_informe, Tipo_servicio, Equipo, Marca_modelo, Codigo_fabricante, Serie, Identificacion, Intervalo, Resolucion, Grado_clase_escala, Accesorios, Observaciones, Material, Numero_partes, Numero_plano, Numero_cotas, Numero_piezas) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$paramsEquipo = [$no_orden_interna, $numero_ingreso, $certificado_informe, $tipo_servicio, $equipo, $marca_modelo, $codigo_fabricante, $serie, $identificacion, $intervalo, $resolucion, $grado_clase_escala, $accesorios, $observaciones_equipo, $material, $numero_partes, $numero_plano, $numero_cotas, $numero_pieza];
$stmtEquipo = sqlsrv_query($conn, $queryEquipo, $paramsEquipo);

if ($stmtEquipo === false) {
throw new Exception("Error al insertar en Datos_equipo: " . print_r(sqlsrv_errors(), true));
}


    // Confirmar la transacción
    sqlsrv_commit($conn);
    echo "Todos los datos se han guardado correctamente.";
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    sqlsrv_rollback($conn);
    echo "Error al guardar los datos: " . $e->getMessage();
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
