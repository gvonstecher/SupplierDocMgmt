<?php


$notification = array('class'=>'hide','message'=>'');
$template_vars = array();

$template_path = $site_config['modules'] . $modules_config['vehiculo']['path'] . 'templates/';
$plantilla = new template($template_path . 'vehiculo_edit.html');


$id_admin = $_SESSION['id_admin'];
$id_proveedor = $_SESSION['id_proveedor'];
$id = (isset($_GET['id'])) ? $_GET['id'] : null;
$id_proveedor_planta = (isset($_GET['pp_id'])) ? $_GET['pp_id'] : '';

if($_SERVER['REQUEST_METHOD'] == "POST"){

    if (isset($_POST['borrar'])) {
        $sql = "select id_documentacion from documentaciones where tipo_entidad_documentacion = 3 and id_entidad_asociada_documentacion = {$_POST['id']}";
        $query = $db->query($sql);
        $rs = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($rs as $values){
            $dirname = 'documentos/'.$values['id_documentacion'];
			array_map('unlink', glob("$dirname/*.*"));
			rmdir($dirname);
        }

        $sql = "delete from documentaciones where tipo_entidad_documentacion = 3 and id_entidad_asociada_documentacion = {$_POST['id']}";
    
        if($db->query($sql)){
            
            $sql_delentidad = "delete from vehiculos where id_vehiculo = {$_POST['id']}";
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
	$identificacion = $cleaner->clean_data($_POST['identificacion'],'text',255,0,array('not_empty'=>true));
    $nombre2 = $cleaner->clean_data($_POST['nombre2'],'text',255,0,array('not_empty'=>true));
	$identificacion2 = $cleaner->clean_data($_POST['identificacion2'],'text',255,0,array('not_empty'=>true));

	$without_errors = (!$nombre['error'] && !$identificacion['error']) ? true : false;
    

}else{

	$array_data = array('nombre_vehiculo'=>'',
						'identificacion_vehiculo'=>'',
                        'nombre_vehiculo2'=>'',
						'identificacion_vehiculo2'=>''
                    );
	
	if($id){
        $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = {$id}";

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

    $nombre = array('valor'=>$array_data['nombre_vehiculo'] ,'error'=>true);
    $identificacion = array('valor'=>$array_data['identificacion_vehiculo'],'error'=>true);
    $nombre2 = array('valor'=>$array_data['nombre_vehiculo2'] ,'error'=>true);
    $identificacion2 = array('valor'=>$array_data['identificacion_vehiculo2'],'error'=>true);

    $without_errors = false;

}

if($without_errors){

	$exec_sql = true; // variable de control para saber si se debe o no ejecutar la actualización dependiendo si la subida de imagenes falló

    if($id != 0){
        $sql = "UPDATE vehiculos SET
				        nombre_vehiculo = '{$nombre['valor']}',
				        identificacion_vehiculo = '{$identificacion['valor']}',
                        nombre_vehiculo2 = '{$nombre2['valor']}',
				        identificacion_vehiculo2 = '{$identificacion2['valor']}'";
		$sql .= " WHERE id_vehiculo = {$id}";


		$notification['message'] = 'La entidad fue actualizada';

    } else {

        $sql = "INSERT INTO vehiculos 
                        (id_proveedor_planta,
                        nombre_vehiculo,
                        identificacion_vehiculo,
                        nombre_vehiculo2,
                        identificacion_vehiculo2)
                    VALUES
                        ({$id_proveedor_planta},
                        '{$nombre['valor']}',
						'{$identificacion['valor']}',
                        '{$nombre2['valor']}',
						'{$identificacion2['valor']}')";

        $notification['message'] = 'Se ha creado exitosamente la entidad';
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


/*BUCLE TIPO VEHICULO*/
$tipos_vehiculos = array();
$tipos_vehiculos[1] = 'Automovil';
$tipos_vehiculos[2] = 'Motocicleta';
$tipos_vehiculos[3] = 'Pick up';
$tipos_vehiculos[4] = 'Camion';
$tipos_vehiculos[5] = 'Semirremolque';
$tipos_vehiculos[6] = 'Acoplado';
$tipos_vehiculos[7] = 'Otro';

$plantilla->capturar_bucle('TIPO_VEHICULO');

foreach($tipos_vehiculos as $i => $valor){

	$selected = ($valor == $nombre['valor']) ? 'selected="selected"' : '';
	$plantilla->reemplazar_contenido_bucle(array('id'=>$i,
												 'nombre'=>$valor,
												 'selected'=>$selected));
}
$plantilla->reemplazar_bucle();

$plantilla->capturar_bucle('TIPO_VEHICULO2');

foreach($tipos_vehiculos as $i => $valor){

	$selected = ($valor == $nombre2['valor']) ? 'selected="selected"' : '';
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


$template_vars['titulo_pagina'] = (!empty($id)) ? 'Edicion de Vehiculo ' . $nombre['valor']  : 'Agregar Vehiculo';

$template_vars['id'] = $id;
$template_vars['pp_id'] = $id_proveedor_planta;
$template_vars['identificacion'] = $identificacion['valor'];
$template_vars['identificacion2'] = $identificacion2['valor'];


$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();
