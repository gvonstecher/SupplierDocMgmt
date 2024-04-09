<?php

$template_path = $site_config['modules'] . $modules_config['registro']['path'] . 'templates/';
$plantilla = new template($template_path . 'registro.html');
$estado = '';


if($_SERVER['REQUEST_METHOD'] == "POST"){

	$_POST['planta'] = (isset($_POST['planta']) and $_POST['planta'] !== '') ? $_POST['planta'] : '';

	$cleaner = new cleaner();

	$cleaner->set_error('El usuario no puede estar vacío','vacio');
	$cleaner->set_error('El usuario no puede estar vacío','size');

	$username = $cleaner->clean_data($_POST['username'],'text',0,4,array('not_empty'=>true,'remove_space'=>true));

	$cleaner->set_error('El mail no puede estar vacio','vacio');
	$cleaner->set_error('El mail no puede estar vacío','size');

	$mail = $cleaner->clean_data($_POST['mail'],'email',0,4,array('not_empty'=>true,'remove_space'=>true));
	
	$cleaner->set_error("La contraseña no puede estar vacía",'vacio');
	$cleaner->set_error("La contraseña no puede estar vacía",'size');

	$password = $cleaner->clean_data($_POST['password'],'text',0,3,array('not_empty'=>true,'remove_space'=>true));
	$password2 = $cleaner->clean_data($_POST['password2'],'text',0,3,array('not_empty'=>true,'remove_space'=>true));

	$cleaner->set_error("Debe seleccionar una planta",'vacio');
	$cleaner->set_error("Debe seleccionar una planta",'size');
	
	$planta = $cleaner->clean_data($_POST['planta'],'int',0,1,array('not_empty'=>true,'remove_space'=>true));

	$has_error = ($username['error'] or $mail['error'] or $password['error'] or $password2['error'] or $planta['error']) ? true : false;

}else{

	$has_error = true;

	$username = array('valor'=>'','error'=>false,'error_msj'=>'');
	$mail = array('valor'=>'','error'=>false,'error_msj'=>'');
	$password = array('valor'=>'','error'=>false,'error_msj'=>'');
	$password2 = array('valor'=>'','error'=>false,'error_msj'=>'');
	$planta = array('valor'=>'','error'=>false,'error_msj'=>'');

}

if(!$has_error){

	$sql = "SELECT mail_admin from admin where mail_admin = '{$mail['valor']}'";
	$query = $db->query($sql);
	$rs = array();

	$rs = $query->fetch(PDO::FETCH_ASSOC);

	if($query->rowCount() > 0){

		$mail['error'] = true;
		$estado = "Ya existe un usuario con esa direccion de correo";

	} else {


		$password_hash = sha1($password['valor']);

		$sql_insert = "INSERT INTO admin 
                        (username_admin,
                        mail_admin,
                        password_admin,
                        tipouser_admin)
                    VALUES
                        ('{$username['valor']}',
						'{$mail['valor']}',
                        '{$password_hash}',
                        '0'
                        )";

		$exec_sql = $db->exec($sql_insert);
		if($exec_sql){

			$id_admin = $db->lastInsertId();
            $sql_prov = "INSERT INTO proveedores (id_admin) VALUES ({$id_admin})";
            $exec_sql = $db->exec($sql_prov);
            $id_prov = $db->lastInsertId();

			$sql_plantas = "select id_planta from plantas";
			$query_plantas = $db->query($sql_plantas);
			$rs_plantas = $query_plantas->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($rs_plantas as $rs_planta){
				$sql_pp = "INSERT INTO proveedores_plantas (id_proveedor, id_planta) VALUES ({$id_prov},{$rs_planta['id_planta']})";
				$exec_sql = $db->exec($sql_pp);
			}
		}

		$estado = "El usuario ha sido creado";
	}
}

$sql_plantas = "select * from plantas";
$query_plantas = $db->query($sql_plantas);
$rs_plantas = $query_plantas->fetchAll(PDO::FETCH_ASSOC);

$plantilla->capturar_bucle('PLANTA');

foreach($rs_plantas as $rs_planta){

	if($planta['valor']== $rs_planta['id_planta']){
		$selected = 'selected';
	} else {
		$selected = '';
	}
    $plantilla->reemplazar_contenido_bucle(array(
        'id'=>$rs_planta['id_planta'],
        'nombre'=>$rs_planta['nombre_planta'],
		'selected'=>$selected
    ));
}
$plantilla->reemplazar_bucle();



$plantilla->asignar_variables(array('mail'=>$mail['valor'],
									'username'=>$username['valor'],
									'error_usuario_class'=>($username['error'])? 'is-invalid' : '',
									'error_usuario'=>$username['error_msj'],
									'error_password_class'=>($password['error'])? 'is-invalid' : '',
									'error_password'=>$password['error_msj'],
									'error_password2_class'=>($password2['error'])? 'is-invalid' : '',
									'error_password2'=>$password2['error_msj'],
									'error_planta_class'=>($planta['error'])? 'is-invalid' : '',
									'error_planta'=>$planta['error_msj'],
									'mensaje'=>$estado
									));

$body = $plantilla->procesar_plantilla();