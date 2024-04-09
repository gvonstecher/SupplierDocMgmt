<?php

$template_path =  $site_config['modules'] . $modules_config['planta']['path'] . 'templates/';

$plantilla = new template($template_path . 'planta_listado.html');

$id_planta = (isset($_GET['id'])) ? $_GET['id'] : '1';

$sql_planta = "select * from plantas where id_planta = {$id_planta}";
$query_planta = $db->query($sql_planta);
$rs_planta = $query_planta->fetch(PDO::FETCH_ASSOC);

$template_vars['nombre_planta'] = $rs_planta['nombre_planta'];

$sql_proveedores = "SELECT
                        p.nombre_proveedor,
                        p.cuit_proveedor,
                        pp.estado_proveedor,
                        pp.id_proveedor_planta,
                        a.username_admin,
                        a.mail_admin
                    FROM 
                        proveedores p
                    INNER JOIN 
                        proveedores_plantas pp
                        ON p.id_proveedor = pp.id_proveedor
                    INNER JOIN
                        plantas
                        ON plantas.id_planta = pp.id_planta
                    INNER JOIN
                        admin a
                        ON a.id_admin = p.id_admin
                    WHERE  plantas.id_planta = ".$id_planta;

$query_proveedores = $db->query($sql_proveedores);
$rs_proveedores = $query_proveedores->fetchAll(PDO::FETCH_ASSOC);


$plantilla->capturar_bucle('PROVEEDORES');
foreach($rs_proveedores as $proveedor){

    //'pequeña' query para traer documentos vencidos
    $sql_documentaciones = "select id_documentacion, fecha_vto_documentacion from documentaciones 
                where id_entidad_asociada_documentacion = {$proveedor['id_proveedor_planta']} and fecha_vto_documentacion < CURDATE()
            UNION
            select id_documentacion, fecha_vto_documentacion from documentaciones d 
            left join personal pe on d.id_entidad_asociada_documentacion = pe.id_personal AND d.tipo_entidad_documentacion in (1,2)
            left join proveedores_plantas pp on pp.id_proveedor_planta = pe.id_proveedor_planta
            where pp.id_proveedor_planta = {$proveedor['id_proveedor_planta']} and d.fecha_vto_documentacion < CURDATE()
            UNION
            select id_documentacion, fecha_vto_documentacion from documentaciones d 
            left join vehiculos ve on d.id_entidad_asociada_documentacion = ve.id_vehiculo AND d.tipo_entidad_documentacion =3
            left join proveedores_plantas pp on pp.id_proveedor_planta = ve.id_proveedor_planta
            where pp.id_proveedor_planta = {$proveedor['id_proveedor_planta']} and d.fecha_vto_documentacion < CURDATE()
            UNION
            select id_documentacion, fecha_vto_documentacion from documentaciones d 
            left join maquinarias ma on d.id_entidad_asociada_documentacion = ma.id_maquinaria AND d.tipo_entidad_documentacion =4
            left join proveedores_plantas pp on pp.id_proveedor_planta = ma.id_proveedor_planta
            where pp.id_proveedor_planta = {$proveedor['id_proveedor_planta']} and d.fecha_vto_documentacion < CURDATE()
    ";


    $query_documentaciones = $db->query($sql_documentaciones);
    $rs_documentaciones = $query_documentaciones->fetchAll(PDO::FETCH_ASSOC);

    if(count($rs_documentaciones) > 0){
        $proveedor['estado_proveedor'] = 2;
    }

    switch(intval($proveedor['estado_proveedor'])){
        case 1:
            $estado_clase = 'success';
            $estado_nombre = 'Habilitado';
        break;
        case 2:
            $estado_clase = 'danger';
            $estado_nombre = 'No Habilitado';
        break;
        case 0:
        default:
            $estado_clase = 'warning';
            $estado_nombre = 'En Revisión';
        break;
    }

    $plantilla->reemplazar_contenido_bucle(array(
        'id_pp'=>$proveedor['id_proveedor_planta'],
        'username'=>$proveedor['username_admin'],
        'mail'=>$proveedor['mail_admin'],
        'nombre'=>$proveedor['nombre_proveedor'],
        'cuit'=>$proveedor['cuit_proveedor'],
        'estado_clase'=>$estado_clase,
        'estado_nombre'=>$estado_nombre,
        'estado_desc'=>$proveedor['estado_proveedor']
    ));
}
$plantilla->reemplazar_bucle();


$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();



?>