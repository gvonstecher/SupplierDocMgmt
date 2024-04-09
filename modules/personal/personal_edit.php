<?php
$notification = array('class'=>'hide','message'=>'');
$template_vars = array();

$template_path = $site_config['modules'] . $modules_config['personal']['path'] . 'templates/';
$plantilla = new template($template_path . 'personal_edit.html');


$id_admin = $_SESSION['id_admin'];
$id_proveedor = $_SESSION['id_proveedor'];
$id = (isset($_GET['id'])) ? $_GET['id'] : null;
$id_proveedor_planta = (isset($_GET['pp_id'])) ? $_GET['pp_id'] : '';

if($_SERVER['REQUEST_METHOD'] == "POST"){

    if (isset($_POST['borrar'])) {
        $sql = "select id_documentacion from documentaciones where tipo_entidad_documentacion in (1,2) and id_entidad_asociada_documentacion = {$_POST['id']}";
        $query = $db->query($sql);
        $rs = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($rs as $values){
            $dirname = '../../documentos/'.$values['id_documentacion'];
			array_map('unlink', glob("$dirname/*.*"));
			rmdir($dirname);
        }

        $sql = "delete from documentaciones where tipo_entidad_documentacion in (1,2) and id_entidad_asociada_documentacion = {$_POST['id']}";
    
        if($db->query($sql)){
            
            $sql_delentidad = "delete from personal where id_personal = {$_POST['id']}";
            if($db->query($sql_delentidad)){
                header('location:?s=proveedor_planta&id='.$id_proveedor_planta);
            }
        }else{
            echo 'Hubo un error al eliminar los documentos';
        }
    }

	//Busqueda de errores
	$cleaner = new cleaner();

	$nombre = $cleaner->clean_data($_POST['nombre'],'text',255,0,array('not_empty'=>true));
	$dni = $cleaner->clean_data($_POST['dni'],'text',255,0,array('not_empty'=>true));
    $vinculo = $cleaner->clean_data($_POST['vinculo'],'int',0,0, array('not_empty'=>true));

	$without_errors = (!$nombre['error'] && !$dni['error'] && !$vinculo['error']) ? true : false;
    

}else{

	$array_data = array('nombre_personal'=>'',
						'dni_personal'=>'',
						'vinculo_personal'=>null
                    );
	
	if($id){
        $sql = "SELECT * FROM personal WHERE id_personal = {$id}";

        $query = $db->query($sql);
        $rs = $query->fetchAll(PDO::FETCH_ASSOC);

            if(count($rs) > 0){

                foreach($rs as $values){
                    foreach($values as $keyname => $values_data){
                        if(array_key_exists($keyname, $array_data) && !is_null($values_data)){
                            $array_data[$keyname] = $values_data;
                        }
                    }
                }
            }
    }

    $nombre = array('valor'=>$array_data['nombre_personal'] ,'error'=>true);
    $dni = array('valor'=>$array_data['dni_personal'],'error'=>true);
    $vinculo = array('valor'=>$array_data['vinculo_personal'],'error'=>true);

    $without_errors = false;

}

if($without_errors){

	$exec_sql = true; // variable de control para saber si se debe o no ejecutar la actualización dependiendo si la subida de imagenes falló

    if($id != 0){
        $sql = "UPDATE personal SET
				        nombre_personal = '{$nombre['valor']}',
				        dni_personal = '{$dni['valor']}',
				        vinculo_personal = {$vinculo['valor']}";
		$sql .= " WHERE id_personal = {$id}";


		$notification['message'] = 'La entidad fue actualizada';

    } else {

        $sql = "INSERT INTO personal 
                        (id_proveedor_planta,
                        nombre_personal,
                        dni_personal,
                        vinculo_personal)
                    VALUES
                        ({$id_proveedor_planta},
                        '{$nombre['valor']}',
						'{$dni['valor']}',
						{$vinculo['valor']})";

        $notification['message'] = 'Se ha agregado exitosamente a la entidad';
    }

	$exec_sql = $db->exec($sql);

	if($exec_sql){

			$notification['class'] = '';
            header('location:index.php?s=proveedor_planta&id='.$id_proveedor_planta);

	}else{
			print_r($db->errorInfo());
			$notification['message'] = '¡Atención! Hubo un error al intentar guardar los datos';
			$notification['class'] = 'alert-danger';

	}

}

$template_vars['titulo_pagina'] = (!empty($id)) ? 'Edicion de Personal ' . $nombre['valor']  : 'Agregar Personal';

$template_vars['id'] = $id;
$template_vars['pp_id'] = $id_proveedor_planta;
$template_vars['nombre'] = $nombre['valor'];
$template_vars['dni'] = $dni['valor'];
$template_vars['vinculo'] = $vinculo['valor'];

if($vinculo['valor'] == 0){
    $template_vars['vinculo_contratado'] = 'checked="checked"';
    $template_vars['vinculo_dependencia'] = '';
} else {
    $template_vars['vinculo_contratado'] = '';
    $template_vars['vinculo_dependencia'] = 'checked="checked"';
}


/*BUCLE TIPO SOCIAL*/
$tipos_vinculos = array();
$tipos_vinculos[0] = 'Contratado';
$tipos_vinculos[1] = 'Relación de Dependencia';

$plantilla->capturar_bucle('VINCULOS');

foreach($tipos_vinculos as $i => $valor){

	$selected = ($i == $vinculo['valor']) ? 'selected="selected"' : '';
	$plantilla->reemplazar_contenido_bucle(array('id'=>$i,
												 'nombre'=>$valor,
												 'selected'=>$selected));
}

$plantilla->reemplazar_bucle();

$plantilla->capturar_bucle('EDIT');
if($id){
    $plantilla->reemplazar_contenido_bucle(array());
}
$plantilla->reemplazar_bucle();


$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();
