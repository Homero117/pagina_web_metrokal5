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
    $fecha_elaboracion = $_POST['fecha_elaboracion_oi'];
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

    //Obtener id_cliente y numero_ingreso
// Consulta para obtener el id_cliente y numero_ingreso desde las tablas correspondientes
$query = "
    SELECT c.Id_cliente, d.Numero_ingreso
    FROM Cliente c
    JOIN Datos_equipo d ON d.No_orden_interna = c.No_orden_interna
    WHERE c.No_orden_interna = ?";
    
// Preparar y ejecutar la consulta
$stmt = sqlsrv_prepare($conn, $query, array(&$no_orden_interna));
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sqlsrv_result = sqlsrv_execute($stmt);
if ($sqlsrv_result === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Obtener los resultados
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if ($row) {
    $id_cliente = $row['Id_cliente'];
    $numero_ingreso = $row['Numero_ingreso'];
    
    // Ahora puedes utilizar estos valores en tus consultas de actualización
    echo "ID Cliente: " . $id_cliente . "<br>";
    echo "Número Ingreso: " . $numero_ingreso . "<br>";
} else {
    echo "No se encontró el cliente para esta orden.";
}


    // Comprobar si el número de orden ya existe
    $queryOrdenExistente = "SELECT COUNT(*) AS count FROM Orden_datos WHERE No_orden_interna = ?";
    $paramsOrdenExistente = [$no_orden_interna];
    $resultOrdenExistente = sqlsrv_query($conn, $queryOrdenExistente, $paramsOrdenExistente);
    if ($resultOrdenExistente === false) {
        throw new Exception("Error al verificar la existencia de la orden: " . print_r(sqlsrv_errors(), true));
    }

    $rowOrdenExistente = sqlsrv_fetch_array($resultOrdenExistente, SQLSRV_FETCH_ASSOC);
    $existeOrden = $rowOrdenExistente['count'] > 0;

    // Si la orden ya existe, actualizarla
    if ($existeOrden) {
        // Actualizar Orden_datos
        $queryOrdenUpdate = "UPDATE Orden_datos SET 
                             No_registros_asignados = ?, Fecha_elaboracion = ?, Magnitud = ?, 
                             Fecha_recepcion = ?, Fecha_termino_servicio = ?, Vendedor = ?, Elaboro_oi = ? 
                             WHERE No_orden_interna = ?";
        $paramsOrdenUpdate = [$no_registros_asignados, $fecha_elaboracion_sql, $magnitud, 
                              $fecha_recepcion_sql, $fecha_termino_sql, $vendedor, $elaboro_oi, $no_orden_interna];
        $resultOrdenUpdate = sqlsrv_query($conn, $queryOrdenUpdate, $paramsOrdenUpdate);
        if ($resultOrdenUpdate === false) {
            throw new Exception("Error al actualizar en Orden_datos: " . print_r(sqlsrv_errors(), true));
        }
    } else {
        // Si no existe la orden, insertar un nuevo registro en Orden_datos
        $queryOrden = "INSERT INTO Orden_datos (No_orden_interna, No_registros_asignados, Fecha_elaboracion, Magnitud, Fecha_recepcion, Fecha_termino_servicio, Vendedor, Elaboro_oi) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $paramsOrden = [$no_orden_interna, $no_registros_asignados, $fecha_elaboracion_sql, $magnitud, $fecha_recepcion_sql, $fecha_termino_sql, $vendedor, $elaboro_oi];
        $resultOrden = sqlsrv_query($conn, $queryOrden, $paramsOrden);
        if ($resultOrden === false) {
            throw new Exception("Error al insertar en Orden_datos: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Insertar o actualizar el cliente
    $queryClienteExistente = "SELECT COUNT(*) AS count FROM Cliente WHERE No_orden_interna = ?";
    $paramsClienteExistente = [$no_orden_interna];
    $resultClienteExistente = sqlsrv_query($conn, $queryClienteExistente, $paramsClienteExistente);
    if ($resultClienteExistente === false) {
        throw new Exception("Error al verificar la existencia del cliente: " . print_r(sqlsrv_errors(), true));
    }

    $rowClienteExistente = sqlsrv_fetch_array($resultClienteExistente, SQLSRV_FETCH_ASSOC);
    $existeCliente = $rowClienteExistente['count'] > 0;

    if ($existeCliente) {
        // Actualizar Cliente
        $queryClienteUpdate = "UPDATE Cliente SET Nombre = ?, Direccion = ?, Atencion = ? WHERE No_orden_interna = ?";
        $paramsClienteUpdate = [$nombre_cliente, $direccion, $atencion, $no_orden_interna];
        $resultClienteUpdate = sqlsrv_query($conn, $queryClienteUpdate, $paramsClienteUpdate);
        if ($resultClienteUpdate === false) {
            throw new Exception("Error al actualizar en Cliente: " . print_r(sqlsrv_errors(), true));
        }
    } else {
        // Insertar nuevo cliente
        $queryClienteMax = "SELECT ISNULL(MAX(Id_cliente), 100) + 1 AS new_id FROM Cliente";
        $resultCliente = sqlsrv_query($conn, $queryClienteMax);
        if ($resultCliente === false) {
            throw new Exception("Error al obtener el nuevo Id_cliente: " . print_r(sqlsrv_errors(), true));
        }
        $rowCliente = sqlsrv_fetch_array($resultCliente, SQLSRV_FETCH_ASSOC);
        $id_cliente = $rowCliente['new_id'];

        $queryClienteInsert = "INSERT INTO Cliente (Id_cliente, No_orden_interna, Nombre, Direccion, Atencion) 
                               VALUES (?, ?, ?, ?, ?)";
        $paramsClienteInsert = [$id_cliente, $no_orden_interna, $nombre_cliente, $direccion, $atencion];
        $resultClienteInsert = sqlsrv_query($conn, $queryClienteInsert, $paramsClienteInsert);
        if ($resultClienteInsert === false) {
            throw new Exception("Error al insertar en Cliente: " . print_r(sqlsrv_errors(), true));
        }
    }

    /*
    // Insertar el resto de los detalles del formulario
    $queryInsert = "INSERT INTO Detalles_orden (No_orden_interna, Actividad_realizada, Lugar_calibracion, 
                 Telefono, Contacto, Correo, Servicio, Fecha_entrega, Certificado_informe, Tipo_servicio, 
                 Equipo, Marca_modelo, Codigo_fabricante, Serie, Identificacion, Intervalo, Resolucion, 
                 Grado_clase_escala, Accesorios, Observaciones_equipo, Material, No_parte, No_plano, 
                 No_cotas, No_piezas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $paramsInsert = [$no_orden_interna, $actividad_realizada, $lugar_calibracion, $telefono, $contacto, $correo, 
                     $servicio, $fecha_entrega_sql, $certificado_informe, $tipo_servicio, $equipo, $marca_modelo, 
                     $codigo_fabricante, $serie, $identificacion, $intervalo, $resolucion, $grado_clase_escala, 
                     $accesorios, $observaciones_equipo, $material, $numero_partes, $numero_plano, $numero_cotas, 
                     $numero_pieza];

    $resultInsert = sqlsrv_query($conn, $queryInsert, $paramsInsert);
    if ($resultInsert === false) {
        throw new Exception("Error al insertar los detalles de la orden: " . print_r(sqlsrv_errors(), true));
    }*/

    // Actualizar datos de la factura
    $queryUpdateFactura = "UPDATE Datos_factura 
    SET Actividad_realizada = ?, Lugar_calibracion = ? 
    WHERE Id_cliente = ?";

    $paramsUpdateFactura = [
    $actividad_realizada, $lugar_calibracion, $id_cliente
    ];

    $resultUpdateFactura = sqlsrv_query($conn, $queryUpdateFactura, $paramsUpdateFactura);
    if ($resultUpdateFactura === false) {
    throw new Exception("Error al actualizar la factura: " . print_r(sqlsrv_errors(), true));
    }

    // Actualizar datos de la empresa
    $queryUpdateEmpresa = "UPDATE Datos_empresa 
    SET Telefono = ?, Contacto = ?, Correo = ?, Servicio = ?, Fecha_entrega = ? 
    WHERE Id_cliente = ?";

    $paramsUpdateEmpresa = [
    $telefono, $contacto, $correo, $servicio, $fecha_entrega_sql, $id_cliente
    ];

    $resultUpdateEmpresa = sqlsrv_query($conn, $queryUpdateEmpresa, $paramsUpdateEmpresa);
    if ($resultUpdateEmpresa === false) {
    throw new Exception("Error al actualizar los datos de la empresa: " . print_r(sqlsrv_errors(), true));
    }

    // Actualizar datos del equipo
    $queryUpdateEquipo = "UPDATE Datos_equipo 
    SET Numero_ingreso = ?, Certificado_informe = ?, Tipo_servicio = ?, 
        Equipo = ?, Marca_modelo = ?, Codigo_fabricante = ?, 
        Serie = ?, Identificacion = ?, Intervalo = ?, Resolucion = ?, 
        Grado_clase_escala = ?, Accesorios = ?, Observaciones = ?, 
        Material = ?, Numero_partes = ?, Numero_plano = ?, 
        Numero_cotas = ?, Numero_piezas = ? 
    WHERE No_orden_interna = ?";

    $paramsUpdateEquipo = [
    $numero_ingreso, $certificado_informe, $tipo_servicio, $equipo, $marca_modelo, 
    $codigo_fabricante, $serie, $identificacion, $intervalo, $resolucion, $grado_clase_escala, 
    $accesorios, $observaciones_equipo, $material, $numero_partes, $numero_plano, 
    $numero_cotas, $numero_pieza, $no_orden_interna
    ];

    $resultUpdateEquipo = sqlsrv_query($conn, $queryUpdateEquipo, $paramsUpdateEquipo);
    if ($resultUpdateEquipo === false) {
    throw new Exception("Error al actualizar los datos del equipo: " . print_r(sqlsrv_errors(), true));
    }


    // Confirmar la transacción
    sqlsrv_commit($conn);
    echo "Datos guardados correctamente.";
    
    // Redirigir a la página principal con un mensaje de éxito
    header("Location: CentroOrdenes.php?mensaje=orden_actualizada");
    exit();
} catch (Exception $e) {
    // Si ocurre algún error, deshacer la transacción
    sqlsrv_rollback($conn);
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
