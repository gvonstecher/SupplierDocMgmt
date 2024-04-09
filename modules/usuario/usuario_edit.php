<?php
$notification = array('class'=>'hide','message'=>'');
$template_vars = array();
$without_errors = true;

$template_path = $site_config['modules'] . $modules_config['usuario']['path'] . 'templates/';
$plantilla = new template($template_path . 'usuario_edit.html');

$error_msj = '';

$id = (isset($_GET['id'])) ? $_GET['id'] : null;
$tipouser = (isset($_GET['tipouser'])) ? $_GET['tipouser'] : "0";

if(!empty($id)){
    $nombre_accion = 'Creacion';

    switch($tipouser){
        case '1':
            $tipouser_nombre = "Seguridad e Higiene";
        break;
        case '2':
            $tipouser_nombre = "Administrador";
        break;
        case '0':
        default:
            $tipouser_nombre = "Proveedor";
        break;
    }

} else{
    $nombre_accion = 'Edicion';
}


if($_SERVER['REQUEST_METHOD'] == "POST"){

    if (isset($_POST['borrar'])) {

        $sql = "delete from admin where id_admin = {$_POST['id']}";
        if($db->query($sql)){
            if($tipouser == 0){
                $sql_proveedor = "select p.id_proveedor, pp.id_proveedor_planta from proveedores p left join proveedores_plantas pp on p.id_proveedor = pp.id_proveedor where p.id_admin = {$_POST['id']}";
                $query_proveedor = $db->query($sql_proveedor);
                $rs_proveedor = $query_proveedor->fetchAll(PDO::FETCH_ASSOC);

                foreach($rs_proveedor as $proveedor){

                    //busco documentos del proveedor
                    $sql_documentacion = "select id_documentacion from documentaciones where tipo_entidad_documentacion =0 and id_entidad_asociada_documentacion = {$proveedor['id_proveedor_planta']}";
                    $query_documentacion = $db->query($sql_documentacion);
                    $rs_documentacion = $query_documentacion->fetchAll(PDO::FETCH_ASSOC);

                    foreach($rs_documentacion as $documento){

                        $dirname = 'documentos/'.$documento['id_documentacion'];
                        array_map('unlink', glob("$dirname/*.*"));
                        rmdir($dirname);
                        $sql = "delete from documentaciones where id_documentacion = {$documento['id_documentacion']}";
                        $db->query($sql);

                    }

                    
                    //bucleo personal
                    $sql_personal = "select id_personal from personal where id_proveedor_planta = {$proveedor['id_proveedor_planta']}";
                    $query_personal = $db->query($sql_personal);
                    $rs_personal = $query_personal->fetchAll(PDO::FETCH_ASSOC);

                    foreach($rs_personal as $personal){

                        $sql_documentacion = "select id_documentacion from documentaciones where tipo_entidad_documentacion in (1,2) and id_entidad_asociada_documentacion = {$personal['id_personal']}";
                        $query_documentacion = $db->query($sql_documentacion);
                        $rs_documentacion = $query_documentacion->fetchAll(PDO::FETCH_ASSOC);

                        foreach($rs_documentacion as $documento){

                            $dirname = 'documentos/'.$documento['id_documentacion'];
                            array_map('unlink', glob("$dirname/*.*"));
                            rmdir($dirname);
                            $sql = "delete from documentaciones where id_documentacion = {$documento['id_documentacion']}";
                            $db->query($sql);

                        }

                        $sql = "delete from personal where id_personal = {$personal['id_personal']}";
                        $db->query($sql);

                    }

                    //bucleo vehiculo
                    $sql_vehiculos = "select id_vehiculo from vehiculos where id_proveedor_planta = {$proveedor['id_proveedor_planta']}";
                    $query_vehiculos = $db->query($sql_vehiculos);
                    $rs_vehiculos = $query_vehiculos->fetchAll(PDO::FETCH_ASSOC);

                    foreach($rs_vehiculos as $vehiculo){

                        $sql_documentacion = "select id_documentacion from documentaciones where tipo_entidad_documentacion =3 and id_entidad_asociada_documentacion = {$vehiculo['id_vehiculo']}";
                        $query_documentacion = $db->query($sql_documentacion);
                        $rs_documentacion = $query_documentacion->fetchAll(PDO::FETCH_ASSOC);

                        foreach($rs_documentacion as $documento){

                            $dirname = 'documentos/'.$documento['id_documentacion'];
                            array_map('unlink', glob("$dirname/*.*"));
                            rmdir($dirname);
                            $sql = "delete from documentaciones where id_documentacion = {$documento['id_documentacion']}";
                            $db->query($sql);

                        }

                        $sql = "delete from vehiculo where id_vehiculo = {$vehiculo['id_vehiculo']}";
                        $db->query($sql);

                    }

                    //bucleo vehiculo
                    $sql_maquinarias = "select id_maquinaria from maquinarias where id_proveedor_planta = {$proveedor['id_proveedor_planta']}";
                    $query_maquinarias = $db->query($sql_maquinarias);
                    $rs_maquinarias = $query_maquinarias->fetchAll(PDO::FETCH_ASSOC);

                    foreach($rs_maquinarias as $maquinaria){

                        $sql_documentacion = "select id_documentacion from documentaciones where tipo_entidad_documentacion =4 and id_entidad_asociada_documentacion = {$maquinaria['id_maquinaria']}";
                        $query_documentacion = $db->query($sql_documentacion);
                        $rs_documentacion = $query_documentacion->fetchAll(PDO::FETCH_ASSOC);

                        foreach($rs_documentacion as $documento){

                            $dirname = 'documentos/'.$documento['id_documentacion'];
                            array_map('unlink', glob("$dirname/*.*"));
                            rmdir($dirname);
                            $sql = "delete from documentaciones where id_documentacion = {$documento['id_documentacion']}";
                            $db->query($sql);

                        }

                        $sql = "delete from maquinarias where id_maquinaria = {$maquinaria['id_maquinaria']}";
                        $db->query($sql);

                    }
                }

            }
        }

        header('location:?s=usuario&tipouser='.$tipouser);

    } else {

        //Busqueda de errores
	    $cleaner = new cleaner();

	    $username = $cleaner->clean_data($_POST['username'],'text',255,0,array('not_empty'=>true));
        $mail = $cleaner->clean_data($_POST['mail'],'email',255,0,array('not_empty'=>true));
        $password = $cleaner->clean_data($_POST['password'],'text',255,0,array('not_empty'=>true));
        $password2 = $cleaner->clean_data($_POST['password2'],'text',255,0,array('not_empty'=>true));
        $tipouser = $_POST['tipouser'];

        if((strcmp($password['valor'], $password2['valor']) !== 0)){
            $without_errors = false;
            $error_msj = 'Las contraseñas no coinciden';
        } else if(empty($id)){
            $sql_check = "select * from admin where mail_admin = '{$mail['valor']}'";
            $query_check = $db->query($sql_check);
            $rs_check = $query_check->fetch(PDO::FETCH_ASSOC);
            if(!empty($rs_check)){
                $without_errors = false;
                $error_msj = 'Ya existe un usuario con ese correo electronico';
            } 
        } else {
            $without_errors = (!$username['error'] && !$mail['error']) ? true : false;
        }
	    
    

    }

}else{

    $username = array('valor'=>'' ,'error'=>true);
	$mail = array('valor'=>'','error'=>true);
	$password = array('valor'=>'','error'=>true);
	$password2 = array('valor'=>'','error'=>true);
	
	if($id){
        $sql = "SELECT * FROM admin WHERE id_admin = {$id}";

        $query = $db->query($sql);
        $rs = $query->fetch(PDO::FETCH_ASSOC);

        $username = array('valor'=>$rs['username_admin'] ,'error'=>true);
        $mail = array('valor'=>$rs['mail_admin'] ,'error'=>true);
        $tipouser = $rs['tipouser_admin'];

        $sql_pp = "select id_proveedor_planta, id_planta 
                    from proveedores p
                    left join proveedores_plantas pp 
                        on pp.id_proveedor = p.id_proveedor
                    where p.id_admin = {$rs['id_admin']}";
    }


    $without_errors = false;

}

if($without_errors){
    
    // variable de control para saber si se debe o no ejecutar la actualización dependiendo si la subida de imagenes falló

    if($id != 0){
        $sql = "UPDATE admin SET
				        username_admin = '{$username['valor']}',
                        mail_admin = '{$mail['valor']}'";
        
        if(!empty($password['valor'])){
            $pass_sql = sha1($password['valor']);
            $sql .= ", password_admin = '{$pass_sql}'";
        }
		$sql .= " WHERE id_admin = {$id}";

		$notification['message'] = 'La entidad fue actualizada';

    } else {
        $pass_sql = sha1($password['valor']);
        $sql = "INSERT INTO admin 
                        (username_admin,
                        mail_admin,
                        password_admin,
                        tipouser_admin)
                    VALUES
                        ('{$username['valor']}',
						'{$mail['valor']}',
                        '{$pass_sql}',
                        '{$tipouser}'
                        )";


        $notification['message'] = 'Se ha creado exitosamente la entidad';
    }

	$exec_sql = $db->exec($sql);

	if($exec_sql){

            if($tipouser == 0){
                $id_admin = $db->lastInsertId();
                $sql_prov = "INSERT INTO proveedores (id_admin) VALUES ({$id_admin})";
                $exec_sql = $db->exec($sql_prov);
                $id_prov = $db->lastInsertId();

                foreach ($_POST['plantas'] as $planta){
                    $sql_pp = "INSERT INTO proveedores_plantas (id_proveedor, id_planta) VALUES ({$id_prov},{$planta})";
                    $exec_sql = $db->exec($sql_pp);
                }
            }

			$notification['class'] = '';
            header('location:index.php?s=usuario&tipouser='.$tipouser);

	}else{
			print_r($db->errorInfo());
			$notification['message'] = '¡Atención! Hubo un error al intentar guardar los datos';
			$notification['class'] = 'alert-danger';

	}

}

$plantilla->capturar_bucle('EDIT');
if($id){
    $plantilla->reemplazar_contenido_bucle(array());
}
$plantilla->reemplazar_bucle();

$plantilla->capturar_bucle('SOLO_EDIT');
if($id){
    
}
$plantilla->reemplazar_bucle();

$plantilla->capturar_bucle('PROVEEDOR');
/*if($tipouser == 0){

    $plantilla->reemplazar_contenido_bucle(array());

    $sql_plantas= 'select * from plantas';
    $query_plantas = $db->query($sql_plantas);
    $rs_plantas = $query_plantas->fetchAll(PDO::FETCH_ASSOC);

    $plantilla->capturar_bucle('PLANTAS_OPT',true);
    foreach($rs_plantas as $planta){

        
        $plantilla->reemplazar_contenido_bucle(array(
            'id'=>$planta['id_planta'],
            'nombre'=>$planta['nombre_planta'],
            'selected'=>$selected
        ),true);
    }
    $plantilla->reemplazar_bucle(true);

}*/
$plantilla->reemplazar_bucle();

$template_vars['titulo_pagina'] = (!empty($id)) ? 'Edicion de Usuario ' . $username['valor']  : 'Agregar Usuario';

$template_vars['id'] = $id;
$template_vars['username'] = $username['valor'];
$template_vars['mail'] = $mail['valor'];
$template_vars['tipouser'] = $tipouser;
$template_vars['error_msj'] = $error_msj;


$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();
