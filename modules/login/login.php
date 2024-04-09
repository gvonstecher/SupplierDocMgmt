<?php

$template_path = $site_config['modules'] . $modules_config['login']['path'] . 'templates/';
$plantilla = new template($template_path . 'login.html');


$user_get = (isset($_GET['user']) and $_GET['user'] !== '') ? $_GET['user'] : '';

if($_SERVER['REQUEST_METHOD'] == "POST"){

	$_POST['planta'] = (isset($_POST['planta']) and $_POST['planta'] !== '') ? $_POST['planta'] : '';

	$cleaner = new cleaner();

	$cleaner->set_error('El usuario no puede estar vacío','vacio');
	$cleaner->set_error('El usuario no puede estar vacío','size');

	$user = $cleaner->clean_data($_POST['usuario'],'text',0,4,array('not_empty'=>true,'remove_space'=>true));

	$cleaner->set_error("La contraseña no puede estar vacía",'vacio');
	$cleaner->set_error("La contraseña no puede estar vacía",'size');

	$password = $cleaner->clean_data($_POST['password'],'text',0,3,array('not_empty'=>true,'remove_space'=>true));

	$cleaner->set_error("Debe seleccionar una planta",'vacio');
	$cleaner->set_error("Debe seleccionar una planta",'size');
	
	$planta = $cleaner->clean_data($_POST['planta'],'int',0,1,array('not_empty'=>true,'remove_space'=>true));

	$has_error = ($user['error'] or $password['error'] or $planta['error']) ? true : false;

}else{

	$has_error = true;

	$user = array('valor'=>$user_get,'error'=>false,'error_msj'=>'');
	$password = array('valor'=>'','error'=>false,'error_msj'=>'');
	$planta = array('valor'=>'','error'=>false,'error_msj'=>'');

}

if(!$has_error){

	
	## Realizo el login
	$login_user = $db->quote($user['valor']);

	$sql = "SELECT admin.*, proveedores.id_proveedor FROM admin left join proveedores on proveedores.id_admin = admin.id_admin WHERE username_admin={$login_user} or mail_admin = {$login_user}";
	$query = $db->query($sql);
	$rs = array();

	$rs = $query->fetch(PDO::FETCH_ASSOC);

	if($query->rowCount() > 0){

		$password_hash = sha1($password['valor']);

		if($password_hash === $rs['password_admin']){

			if(intval($rs['tipouser_admin']) === 0){

				$sql_p= "select p.id_proveedor from proveedores p where p.id_admin = {$rs['id_admin']}";
				$query_p = $db->query($sql_p);
				$rs_p = $query_p->fetch(PDO::FETCH_ASSOC);

				$sql_pp = "select pp.id_proveedor_planta from proveedores p inner join proveedores_plantas pp on pp.id_proveedor = p.id_proveedor where p.id_admin = {$rs['id_admin']} and pp.id_planta = {$planta['valor']}";
				$query_pp = $db->query($sql_pp);
				$rs_pp = $query_pp->fetch(PDO::FETCH_ASSOC);

				if($query_pp->rowCount() > 0){
					$loginclass::start_login($user['valor']);
					$_SESSION['id_admin'] = $rs['id_admin'];
					$_SESSION['nombre_usuario'] = $rs['username_admin'];
					$_SESSION['tipouser'] = intval($rs['tipouser_admin']);
					$_SESSION['id_proveedor'] = $rs_p['id_proveedor'];
					header('location:index.php?s=proveedor_planta&id='.$rs_pp['id_proveedor_planta']);
					
				} else {
					//no esta habilitado para esta planta
					$planta['error'] = true;
					$planta['error_msj'] = "El proveedor no está habilitado para esta planta.";

					$sql_pp = "INSERT INTO proveedores_plantas (id_proveedor, id_planta) VALUES ({$rs_p['id_proveedor']},{$planta['valor']})";
					$exec_sql = $db->exec($sql_pp);
					$inserted_id = $db->lastInsertId();

					$loginclass::start_login($user['valor']);
					$_SESSION['id_admin'] = $rs['id_admin'];
					$_SESSION['nombre_usuario'] = $rs['username_admin'];
					$_SESSION['tipouser'] = intval($rs['tipouser_admin']);
					$_SESSION['id_proveedor'] = $rs_p['id_proveedor'];
					header('location:index.php?s=proveedor_planta&id='.$inserted_id);



				}
			} else if(intval($rs['tipouser_admin']) === 2){ 
				
				$loginclass::start_login($user['valor']);
				$_SESSION['id_admin'] = $rs['id_admin'];
				$_SESSION['nombre_usuario'] = $rs['username_admin'];
				$_SESSION['tipouser'] = intval($rs['tipouser_admin']);
				header('location:index.php?s=planta&id='.$planta['valor']);
			}

				

		}else{
			$password['error'] = true;
			$password['error_msj'] = "La contraseña es incorrecta.";
		}

	}else{

		$user['error'] = true;
		$user['error_msj'] = 'El usuario no existe';

	}

}

$sql_plantas = "select * from plantas";
$query_plantas = $db->query($sql_plantas);
$rs_plantas = $query_plantas->fetchAll(PDO::FETCH_ASSOC);

$plantilla->capturar_bucle('PLANTA');

foreach($rs_plantas as $rs_planta){

    $plantilla->reemplazar_contenido_bucle(array(
        'id'=>$rs_planta['id_planta'],
        'nombre'=>$rs_planta['nombre_planta'],
    ));
}
$plantilla->reemplazar_bucle();



$plantilla->asignar_variables(array('email'=>$user['valor'],
									'password'=>$password['valor'],
									'error_usuario_class'=>($user['error'])? 'is-invalid' : '',
									'error_usuario'=>$user['error_msj'],
									'error_password_class'=>($password['error'])? 'is-invalid' : '',
									'error_password'=>$password['error_msj'],
									'error_planta_class'=>($planta['error'])? 'is-invalid' : '',
									'error_planta'=>$planta['error_msj']
									));

$body = $plantilla->procesar_plantilla();