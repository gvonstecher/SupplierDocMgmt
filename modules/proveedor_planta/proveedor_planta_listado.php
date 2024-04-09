<?php

$template_path =  $site_config['modules'] . $modules_config['proveedor_planta']['path'] . 'templates/';

$plantilla = new template($template_path . 'proveedor_planta_listado.html');
$id_proveedor_planta = (isset($_GET['id'])) ? $_GET['id'] : $_SESSION['id_proveedor_planta'];

$sql_proveedor = "SELECT * FROM 
                        proveedores 
                    INNER JOIN 
                        proveedores_plantas
                        ON proveedores.id_proveedor = proveedores_plantas.id_proveedor
                    INNER JOIN
                        plantas
                        ON plantas.id_planta = proveedores_plantas.id_planta
                    INNER JOIN
                        admin
                        ON admin.id_admin = proveedores.id_admin
                    WHERE  proveedores_plantas.id_proveedor_planta = ".$id_proveedor_planta;

$query_proveedor = $db->query($sql_proveedor);
$rs_proveedor = $query_proveedor->fetch(PDO::FETCH_ASSOC);

$id_proveedor = $rs_proveedor['id_proveedor'];

$template_vars['id_proveedor_planta'] = $id_proveedor_planta;
$template_vars['tipouser'] = $_SESSION['tipouser'];
$template_vars['nombre_planta'] = $rs_proveedor['nombre_planta'];

$template_vars['id_proveedor'] = $rs_proveedor['id_proveedor'];
$template_vars['nombre_proveedor'] = $rs_proveedor['nombre_proveedor'];
$template_vars['cuit_proveedor'] =$rs_proveedor['cuit_proveedor'];


switch($rs_proveedor['tipo_social_proveedor']){
    case 1:
        $template_vars['tipo_social_proveedor'] = 'S.A.';
    break;
    case 2:
        $template_vars['tipo_social_proveedor'] = 'S.R.L.';
    break;
    case 3:
        $template_vars['tipo_social_proveedor'] = 'S.A.S.';
    break;
    case 4:
        $template_vars['tipo_social_proveedor'] = 'Autónomo';
    break;
}

$template_vars['persona_contacto_proveedor'] = $rs_proveedor['persona_contacto_proveedor'];
$template_vars['telefono_contacto_proveedor'] = $rs_proveedor['telefono_contacto_proveedor'];
$template_vars['mail_contacto_proveedor'] = $rs_proveedor['mail_admin'];
$template_vars['domicilio_fiscal_proveedor'] = $rs_proveedor['domicilio_fiscal_proveedor'];
$template_vars['domicilio_real_proveedor'] = $rs_proveedor['domicilio_real_proveedor'];
$template_vars['nro_cuenta_banco_proveedor'] = $rs_proveedor['nro_cuenta_banco_proveedor'];
$template_vars['cbu_banco_proveedor'] = $rs_proveedor['cbu_banco_proveedor'];
$template_vars['banco_proveedor'] = $rs_proveedor['banco_proveedor'];


if($rs_proveedor['condicion_impositiva_proveedor'] == 0){
    $template_vars['condicion_impositiva_proveedor'] = 'Exento';
} else {
    $template_vars['condicion_impositiva_proveedor'] = 'Responsable Inscripto';
}


if($rs_proveedor['contribuyente_iibb_proveedor'] == 0){
    $template_vars['contribuyente_iibb_proveedor'] = 'Exento';
} else if ($rs_proveedor['contribuyente_iibb_proveedor'] == 1){
    $template_vars['contribuyente_iibb_proveedor'] = 'Local';
} else {
    $template_vars['contribuyente_iibb_proveedor'] = 'Convenio';
}


if($rs_proveedor['exencion_ganancias_proveedor'] == 0){
    $template_vars['exencion_ganancias_proveedor'] = 'Si';
} else {
    $template_vars['exencion_ganancias_proveedor'] = 'No';
}
$template_vars['exencion_ganancias_porcentaje_proveedor'] = '- '.$rs_proveedor['exencion_ganancias_porcentaje_proveedor'].'%';


if($rs_proveedor['exencion_caba_proveedor'] == 0){
    $template_vars['exencion_caba_proveedor'] = 'Si';
} else {
    $template_vars['exencion_caba_proveedor'] = 'No';
}
$template_vars['exencion_caba_porcentaje_proveedor'] = '- '.$rs_proveedor['exencion_caba_porcentaje_proveedor'].'%';


if($rs_proveedor['exencion_iibb_bsas_proveedor'] == 0){
    $template_vars['exencion_iibb_bsas_proveedor'] = 'Si';
} else {
    $template_vars['exencion_iibb_bsas_proveedor'] = 'No';
}
$template_vars['exencion_iibb_bsas_porcentaje_proveedor'] = '- '.$rs_proveedor['exencion_iibb_bsas_porcentaje_proveedor'].'%';



//traigo personal 
$sql_personal = "select * from personal where id_proveedor_planta = {$id_proveedor_planta}";
$query_personal = $db->query($sql_personal);
$rs_personal = $query_personal->fetchAll(PDO::FETCH_ASSOC);

$plantilla->capturar_bucle('PERSONAL');
foreach($rs_personal as $valores){

    if($valores['vinculo_personal'] == 0){
        $vinculo_desc= 'Contratado';
        $tipo_entidad='2';
    } else {
        $vinculo_desc = 'Relación de Dependencia';
        $tipo_entidad='1';
    }

    $plantilla->reemplazar_contenido_bucle(array(
        'id'=>$valores['id_personal'],
        'nombre'=>$valores['nombre_personal'],
        'dni'=>$valores['dni_personal'],
        'vinculo'=>$vinculo_desc,
        'tipo_entidad'=>$tipo_entidad
    ));
}
$plantilla->reemplazar_bucle();


//traigo vehiculo
$sql_vehiculo = "select * from vehiculos where id_proveedor_planta = {$id_proveedor_planta}";
$query_vehiculo = $db->query($sql_vehiculo);
$rs_vehiculo = $query_vehiculo->fetchAll(PDO::FETCH_ASSOC);

$plantilla->capturar_bucle('VEHICULO');
foreach($rs_vehiculo as $valores){
    $plantilla->reemplazar_contenido_bucle(array(
        'id'=>$valores['id_vehiculo'],
        'nombre'=>$valores['nombre_vehiculo'],
        'identificacion'=>$valores['identificacion_vehiculo'],
        'tipo_entidad'=>'3'
    ));

    $plantilla->capturar_bucle('VEHICULO2',true);
    if(!empty($valores['nombre_vehiculo2'])){
        $plantilla->reemplazar_contenido_bucle(array(
            'nombre2'=>$valores['nombre_vehiculo2'],
            'identificacion2'=>$valores['identificacion_vehiculo2'],
        ),true);
    }
    $plantilla->reemplazar_bucle(true);
    
}
$plantilla->reemplazar_bucle();


//traigo maquinaria
$sql_maquinaria = "select * from maquinarias where id_proveedor_planta = {$id_proveedor_planta}";
$query_maquinaria = $db->query($sql_maquinaria);
$rs_maquinaria = $query_maquinaria->fetchAll(PDO::FETCH_ASSOC);

$plantilla->capturar_bucle('MAQUINARIA');
foreach($rs_maquinaria as $valores){

    $plantilla->reemplazar_contenido_bucle(array(
        'id'=>$valores['id_maquinaria'],
        'nombre'=>$valores['nombre_maquinaria'],
        'identificacion'=>$valores['identificacion_maquinaria'],
        'tipo_entidad'=>'4'
    ));
}
$plantilla->reemplazar_bucle();


//traigo documentos vencidos
$sql_documentos = "(select 
                        d.fecha_vto_documentacion, 
                        td.nombre_tipo_documentacion, 
                        p.nombre_proveedor as nombre_entidad, 
                        'Proveedor' as tipo_entidad
                    from documentaciones d
                    inner join tipos_documentaciones td
                        on	d.id_tipo_documentacion = td.id_tipo_documentacion
                    inner join proveedores_plantas pp
                        on pp.id_proveedor_planta = d.id_entidad_asociada_documentacion
                    inner join proveedores p
                        on pp.id_proveedor = p.id_proveedor
                    where 
                        d.tipo_entidad_documentacion = 0 and 
                        td.vencimiento_tipo_documentacion = 1 and
                        (d.fecha_vto_documentacion < (CURDATE() + INTERVAL 1 MONTH)) and
                        d.id_entidad_asociada_documentacion = {$id_proveedor_planta})";
                        
foreach($rs_personal as $valores){
    $sql_documentos .= "UNION (select 
		                    d.fecha_vto_documentacion,
		                    td.nombre_tipo_documentacion,
		                    per.nombre_personal as nombre_entidad,
		                    'Personal' as tipo_entidad
	                    from documentaciones d
	                    inner join tipos_documentaciones td
		                    on	d.id_tipo_documentacion = td.id_tipo_documentacion
                        inner join personal per
		                    on d.id_entidad_asociada_documentacion = per.id_personal
                        inner join proveedores_plantas pp
                            on pp.id_proveedor_planta = per.id_proveedor_planta
                        inner join proveedores p
                            on pp.id_proveedor = p.id_proveedor
	                    where 
		                    d.tipo_entidad_documentacion in (1,2) and 
                            td.vencimiento_tipo_documentacion = 1 and
                            (d.fecha_vto_documentacion < (CURDATE() + INTERVAL 1 MONTH)) and
		                    d.id_entidad_asociada_documentacion = {$valores['id_personal']})";
}

foreach($rs_vehiculo as $valores){
    $sql_documentos .= "UNION (select 
		                    d.fecha_vto_documentacion,
		                    td.nombre_tipo_documentacion,
		                    v.nombre_vehiculo as nombre_entidad,
		                    'Vehiculo' as tipo_entidad
	                    from documentaciones d
	                    inner join tipos_documentaciones td
		                    on	d.id_tipo_documentacion = td.id_tipo_documentacion
	                    inner join vehiculos v
		                    on d.id_entidad_asociada_documentacion = v.id_vehiculo
                        inner join proveedores_plantas pp
                            on pp.id_proveedor_planta = v.id_proveedor_planta
                        inner join proveedores p
                            on pp.id_proveedor = p.id_proveedor
	                    where 
		                    d.tipo_entidad_documentacion in (3) and 
                            td.vencimiento_tipo_documentacion = 1 and
                            (d.fecha_vto_documentacion < (CURDATE() + INTERVAL 1 MONTH)) and
		                    d.id_entidad_asociada_documentacion = {$valores['id_vehiculo']})";
}

foreach($rs_maquinaria as $valores){
    $sql_documentos .= "UNION (select 
		                    d.fecha_vto_documentacion,
		                    td.nombre_tipo_documentacion,
		                    m.nombre_maquinaria as nombre_entidad,
		                    'Maquinaria' as tipo_entidad
	                    from documentaciones d
	                    inner join tipos_documentaciones td
		                    on	d.id_tipo_documentacion = td.id_tipo_documentacion
	                    inner join maquinarias m
		                    on d.id_entidad_asociada_documentacion = m.id_maquinaria
                        inner join proveedores_plantas pp
                            on pp.id_proveedor_planta = m.id_proveedor_planta
                        inner join proveedores p
                            on pp.id_proveedor = p.id_proveedor
	                    where 
		                    d.tipo_entidad_documentacion in (4) and 
                            td.vencimiento_tipo_documentacion = 1 and
                            (d.fecha_vto_documentacion < (CURDATE() + INTERVAL 1 MONTH)) and
		                    d.id_entidad_asociada_documentacion = {$valores['id_maquinaria']})";
}

$query_documentos = $db->query($sql_documentos);
$rs_documentos = $query_documentos->fetchAll(PDO::FETCH_ASSOC);

$inhabilitado = false;
$plantilla->capturar_bucle('VENCIMIENTO');
if($rs_documentos){
    $plantilla->reemplazar_contenido_bucle(array());
    $plantilla->capturar_bucle('DOCUMENTO',true);

    foreach($rs_documentos as $valores){

        if(strtotime($valores['fecha_vto_documentacion']) > strtotime("today")){
            $fecha_doc = "Vence el ". date ('d/m/Y', strtotime($valores['fecha_vto_documentacion']));
        } else {
            $fecha_doc = "Vencido el ". date ('d/m/Y', strtotime($valores['fecha_vto_documentacion']));
            $inhabilitado =true;
        }
        
        $plantilla->reemplazar_contenido_bucle(array(
            'tipo_entidad'=>$valores['tipo_entidad'],
            'nombre_entidad'=>$valores['nombre_entidad'],
            'nombre_documento'=>$valores['nombre_tipo_documentacion'],
            'fecha_vencimiento'=>$fecha_doc,
            ),true);
    }
    $plantilla->reemplazar_bucle(true);
}

$plantilla->reemplazar_bucle();

if($_SESSION['tipouser'] == 0 ){ //si es proveedor, le muestro el estado

    $plantilla->capturar_bucle('ESTADO_ALERTA');

    if($inhabilitado){
        $rs_proveedor['estado_proveedor'] = 2;
    }

    switch(intval($rs_proveedor['estado_proveedor'])){
        case 1:
            $alerta_clase = 'alert-success';
            $alerta_estado = 'Habilitado';
        break;
        case 2:
            $alerta_clase = 'alert-danger';
            $alerta_estado = 'No Habilitado';
        break;
        case 0:
        default:
            $alerta_clase = 'alert-warning';
            $alerta_estado = 'En Revisión';
        break;
    }

    $plantilla->reemplazar_contenido_bucle(array(
        'alerta_clase'=>$alerta_clase,
        'alerta_estado'=> $alerta_estado,
        'alerta_detalle'=> (!empty($rs_proveedor['estado_detalle_proveedor'])) ? $rs_proveedor['estado_detalle_proveedor']: ''
    ));

    $plantilla->reemplazar_bucle();

    $plantilla->capturar_bucle('ESTADO_EDITABLE');
    $plantilla->reemplazar_bucle();

    $plantilla->capturar_bucle('SOLO_PROVEEDORES');
    $plantilla->reemplazar_contenido_bucle(array());
    $plantilla->reemplazar_bucle();

} else if ($_SESSION['tipouser'] == 2 ){ //si es superadmin, se lo permito editar

    $plantilla->capturar_bucle('ESTADO_EDITABLE');
    $plantilla->reemplazar_contenido_bucle(array(
        'estado_detalle'=>(!empty($rs_proveedor['estado_detalle_proveedor'])) ? $rs_proveedor['estado_detalle_proveedor']: '',
    ));

    $estados_documentacion = array();
    $estados_documentacion[0] = 'En revisión';
    $estados_documentacion[1] = 'Habilitado';
    $estados_documentacion[2] = 'No Habilitado';
    
    $plantilla->capturar_bucle('TIPO_ESTADO',true);
    foreach($estados_documentacion as $i => $valor){

        $selected = ($i == $rs_proveedor['estado_proveedor']) ? 'selected="selected"' : '';
        $plantilla->reemplazar_contenido_bucle(array('valor'=>$i,
                                                    'nombre'=>$valor,
                                                    'selected'=>$selected),true);
    }
    $plantilla->reemplazar_bucle(true);

    $plantilla->reemplazar_bucle();

    $plantilla->capturar_bucle('ESTADO_ALERTA');
    $plantilla->reemplazar_bucle();

    $plantilla->capturar_bucle('SOLO_PROVEEDORES');
    $plantilla->reemplazar_bucle();
}





$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();



?>