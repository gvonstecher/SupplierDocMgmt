<?php

$notification = array('class'=>'hide','message'=>'');
$image_step_1_uploaded = false; // control si se ya se subió una imagen para el paso 1
$image_step_4_uploaded = false; // control si se ya se subió una imagen para el paso 4
$template_vars = array();

$template_path = $site_config['modules'] . $modules_config['proveedor']['path'] . 'templates/';
$plantilla = new template($template_path . 'proveedor_edit.html');

$id_admin = $_SESSION['id_admin'];
$id_proveedor = $_SESSION['id_proveedor'];

if($_SERVER['REQUEST_METHOD'] == "POST"){
    
	//Busqueda de errores
	$cleaner = new cleaner();

	$nombre = $cleaner->clean_data($_POST['nombre'],'text',255,0,array('not_empty'=>true));
	$cuit = $cleaner->clean_data($_POST['cuit'],'text',255,0,array('not_empty'=>true));
    $tipo_social = $cleaner->clean_data($_POST['tipo_social'],'int',0,0, array('not_empty'=>true));
    $persona_contacto = $cleaner->clean_data($_POST['persona_contacto'],'text',255,0,array('not_empty'=>true));
    $telefono_contacto = $cleaner->clean_data($_POST['telefono_contacto'],'text',255,0,array('not_empty'=>true));
    $mail_contacto = $cleaner->clean_data($_POST['mail_contacto'],'text',255,0,array('not_empty'=>true));
    $domicilio_fiscal = $cleaner->clean_data($_POST['domicilio_fiscal'],'text',255,0,array('not_empty'=>true));
    $domicilio_real = $cleaner->clean_data($_POST['domicilio_real'],'text',255,0,array('not_empty'=>true));
    $nro_cuenta_banco = $cleaner->clean_data($_POST['nro_cuenta_banco'],'text',255,0);
    $cbu_banco = $cleaner->clean_data($_POST['cbu_banco'],'text',255,0);
    $banco = $cleaner->clean_data($_POST['banco'],'text',255,0);
    $condicion_impositiva = $cleaner->clean_data($_POST['condicion_impositiva'],'int',0,0);
    $contribuyente_iibb = $cleaner->clean_data($_POST['contribuyente_iibb'],'int',0,0);
    $exencion_ganancias = $cleaner->clean_data($_POST['exencion_ganancias'],'int',0,0);
    $exencion_ganancias_porcentaje = $cleaner->clean_data(intval($_POST['exencion_ganancias_porcentaje']),'int',0,0);
    $exencion_caba = $cleaner->clean_data($_POST['exencion_caba'],'int',0,0);
    $exencion_caba_porcentaje = $cleaner->clean_data(intval($_POST['exencion_caba_porcentaje']),'int',0,0);
    $exencion_iibb_bsas = $cleaner->clean_data($_POST['exencion_iibb_bsas'],'int',0,0);
    $exencion_iibb_bsas_porcentaje = $cleaner->clean_data(intval($_POST['exencion_iibb_bsas_porcentaje']),'int',0,0);

	$without_errors = (!$nombre['error'] && !$cuit['error'] && !$tipo_social['error'] && !$persona_contacto['error'] && !$telefono_contacto['error'] && !$mail_contacto['error'] && !$domicilio_fiscal['error'] && !$domicilio_real['error'] ) ? true : false;
    

}else{

	$array_data = array('nombre_proveedor'=>'',
						'cuit_proveedor'=>'',
						'tipo_social_proveedor'=>null,
						'persona_contacto_proveedor'=>'',
						'telefono_contacto_proveedor'=>'',
						'mail_contacto_proveedor'=>'',
						'domicilio_fiscal_proveedor'=>'',
						'domicilio_real_proveedor'=>'',
                        'nro_cuenta_banco_proveedor' =>'',
                        'cbu_banco_proveedor' =>'',
                        'banco_proveedor' =>'',
                        'condicion_impositiva_proveedor' =>null,
                        'contribuyente_iibb_proveedor' =>null,
                        'exencion_ganancias_proveedor' =>null,
                        'exencion_ganancias_porcentaje_proveedor' =>0,
                        'exencion_caba_proveedor' =>null,
                        'exencion_caba_porcentaje_proveedor' =>0,
                        'exencion_iibb_bsas_proveedor' =>null,
                        'exencion_iibb_bsas_porcentaje_proveedor' =>0
	);
	
	$sql_proveedor = "SELECT * FROM proveedores WHERE id_proveedor = {$id_proveedor}";

	$query_proveedor = $db->query($sql_proveedor);
	$rs_proveedor = $query_proveedor->fetchAll(PDO::FETCH_ASSOC);

		if(count($rs_proveedor) > 0){

			foreach($rs_proveedor as $values){
				foreach($values as $keyname => $values_data){
					if(array_key_exists($keyname, $array_data) && !is_null($values_data)){
						$array_data[$keyname] = $values_data;
					}
				}
			}
		}
    

	$nombre = array('valor'=>$array_data['nombre_proveedor'] ,'error'=>true);
	$cuit = array('valor'=>$array_data['cuit_proveedor'],'error'=>true);
	$tipo_social = array('valor'=>$array_data['tipo_social_proveedor'],'error'=>true);
	$persona_contacto = array('valor'=>$array_data['persona_contacto_proveedor'],'error'=>true);
	$telefono_contacto = array('valor'=>$array_data['telefono_contacto_proveedor'],'error'=>true);
	$mail_contacto = array('valor'=>$array_data['mail_contacto_proveedor'], 'error'=>true);
	$domicilio_fiscal = array('valor'=>$array_data['domicilio_fiscal_proveedor'], 'error'=>true);
    $domicilio_real = array('valor'=>$array_data['domicilio_real_proveedor'], 'error'=>true);
    $nro_cuenta_banco = array('valor'=>$array_data['nro_cuenta_banco_proveedor'], 'error'=>true);
    $cbu_banco = array('valor'=>$array_data['cbu_banco_proveedor'], 'error'=>true);
    $banco = array('valor'=>$array_data['banco_proveedor'], 'error'=>true);
    $condicion_impositiva = array('valor'=>$array_data['condicion_impositiva_proveedor'], 'error'=>true);
    $contribuyente_iibb = array('valor'=>$array_data['contribuyente_iibb_proveedor'], 'error'=>true);
    $exencion_ganancias = array('valor'=>$array_data['exencion_ganancias_proveedor'], 'error'=>true);
    $exencion_ganancias_porcentaje = array('valor'=>$array_data['exencion_ganancias_porcentaje_proveedor'], 'error'=>true);
    $exencion_caba = array('valor'=>$array_data['exencion_caba_proveedor'], 'error'=>true);
    $exencion_caba_porcentaje = array('valor'=>$array_data['exencion_caba_porcentaje_proveedor'], 'error'=>true);
    $exencion_iibb_bsas = array('valor'=>$array_data['exencion_iibb_bsas_proveedor'], 'error'=>true);
    $exencion_iibb_bsas_porcentaje = array('valor'=>$array_data['exencion_iibb_bsas_porcentaje_proveedor'], 'error'=>true);

	$without_errors = false;

}

if($without_errors){

	$exec_sql = true; // variable de control para saber si se debe o no ejecutar la actualización dependiendo si la subida de imagenes falló

	$sql = "UPDATE proveedores SET
				        nombre_proveedor = '{$nombre['valor']}',
				        cuit_proveedor = '{$cuit['valor']}',
				        tipo_social_proveedor = {$tipo_social['valor']},
						persona_contacto_proveedor = '{$persona_contacto['valor']}',
						telefono_contacto_proveedor = '{$telefono_contacto['valor']}',
						mail_contacto_proveedor = '{$mail_contacto['valor']}',
						domicilio_fiscal_proveedor = '{$domicilio_fiscal['valor']}',
                        domicilio_real_proveedor = '{$domicilio_real['valor']}',
                        nro_cuenta_banco_proveedor = '{$nro_cuenta_banco['valor']}',
                        cbu_banco_proveedor = '{$cbu_banco['valor']}',
                        banco_proveedor = '{$banco['valor']}',
                        condicion_impositiva_proveedor = {$condicion_impositiva['valor']},
                        contribuyente_iibb_proveedor = {$contribuyente_iibb['valor']},
                        exencion_ganancias_proveedor = {$exencion_ganancias['valor']},
                        exencion_ganancias_porcentaje_proveedor = {$exencion_ganancias_porcentaje['valor']},
                        exencion_caba_proveedor = {$exencion_caba['valor']},
                        exencion_caba_porcentaje_proveedor = {$exencion_caba_porcentaje['valor']},
                        exencion_iibb_bsas_proveedor = {$exencion_iibb_bsas['valor']},
                        exencion_iibb_bsas_porcentaje_proveedor = {$exencion_iibb_bsas_porcentaje['valor']}";

		$sql .= " WHERE id_proveedor = {$id_proveedor}";


	$exec_sql = $db->exec($sql);

	if($exec_sql){

			$notification['class'] = '';
            $sql_pp = "select id_proveedor_planta from proveedores_plantas where id_proveedor = {$id_proveedor}";
            $query_pp = $db->query($sql_pp);
	        $rs_pp = $query_pp->fetch(PDO::FETCH_ASSOC);

            $url_red='Location:?s=proveedor_planta&id='.$rs_pp['id_proveedor_planta'];
            header($url_red);

	}else{
			print_r($db->errorInfo());
			$notification['message'] = '¡Atención! Hubo un error al intentar guardar los datos';
			$notification['class'] = 'alert-danger';

	}

}


$template_vars['titulo_pagina'] = 'Edicion de Datos de Proveedor';

$template_vars['nombre'] = $nombre['valor'];
$template_vars['cuit'] = $cuit['valor'];
$template_vars['tipo_social'] = $tipo_social['valor'];
$template_vars['persona_contacto'] = $persona_contacto['valor'];
$template_vars['telefono_contacto'] = $telefono_contacto['valor'];
$template_vars['mail_contacto'] = $mail_contacto['valor'];
$template_vars['domicilio_fiscal'] = $domicilio_fiscal['valor'];
$template_vars['domicilio_real'] = $domicilio_real['valor'];
$template_vars['nro_cuenta_banco'] = $nro_cuenta_banco['valor'];
$template_vars['cbu_banco'] = $cbu_banco['valor'];
$template_vars['banco'] = $banco['valor'];


if($condicion_impositiva['valor'] == 1){
    $template_vars['condicion_impositiva_responsable'] = 'checked="checked"';
    $template_vars['condicion_impositiva_exento'] = '';
} else {
    $template_vars['condicion_impositiva_responsable'] = '';
    $template_vars['condicion_impositiva_exento'] = 'checked="checked"';
}


if($contribuyente_iibb['valor'] == 1){
    $template_vars['contribuyente_iibb_local'] = 'checked="checked"';
    $template_vars['contribuyente_iibb_convenio'] = '';
    $template_vars['contribuyente_iibb_exento'] = '';
} else if($contribuyente_iibb['valor'] == 2){
    $template_vars['contribuyente_iibb_local'] = '';
    $template_vars['contribuyente_iibb_convenio'] = 'checked="checked"';
    $template_vars['contribuyente_iibb_exento'] = '';
} else{
    $template_vars['contribuyente_iibb_local'] = '';
    $template_vars['contribuyente_iibb_convenio'] = '';
    $template_vars['contribuyente_iibb_exento'] = 'checked="checked"';
}


if($exencion_ganancias['valor'] == 1){
    $template_vars['exencion_ganancias_noexento'] = 'checked="checked"';
    $template_vars['exencion_ganancias_exento'] = '';
} else {
    $template_vars['exencion_ganancias_noexento'] = '';
    $template_vars['exencion_ganancias_exento'] = 'checked="checked"';
}
$template_vars['exencion_ganancias_porcentaje'] = $exencion_ganancias_porcentaje['valor'];

if($exencion_caba['valor'] == 1){
    $template_vars['exencion_caba_noexento'] = 'checked="checked"';
    $template_vars['exencion_caba_exento'] = '';
} else {
    $template_vars['exencion_caba_noexento'] = '';
    $template_vars['exencion_caba_exento'] = 'checked="checked"';
}
$template_vars['exencion_caba_porcentaje'] = $exencion_caba_porcentaje['valor'];


if($exencion_iibb_bsas['valor'] == 1){
    $template_vars['exencion_iibb_bsas_noexento'] = 'checked="checked"';
    $template_vars['exencion_iibb_bsas_exento'] = '';
} else {
    $template_vars['exencion_iibb_bsas_noexento'] = '';
    $template_vars['exencion_iibb_bsas_exento'] = 'checked="checked"';
}
$template_vars['exencion_iibb_bsas_porcentaje'] = $exencion_iibb_bsas_porcentaje['valor'];



/*BUCLE TIPO SOCIAL*/
$tipos_sociales = array();
$tipos_sociales[1] = 'S.A.';
$tipos_sociales[2] = 'S.R.L.';
$tipos_sociales[3] = 'S.A.S.';
$tipos_sociales[4] = 'AUT.';


$plantilla->capturar_bucle('TIPO_SOCIAL');

foreach($tipos_sociales as $i => $valor){

	$selected = ($i == $tipo_social['valor']) ? 'selected="selected"' : '';
	$plantilla->reemplazar_contenido_bucle(array('id'=>$i,
												 'nombre'=>$valor,
												 'selected'=>$selected));
}

$plantilla->reemplazar_bucle();


$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();