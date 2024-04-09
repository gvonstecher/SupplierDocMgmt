<?php

require_once 'library/configuration.php';
require_once 'library/password.php';
require_once $site_config['modules'] . 'modules_config.php';

$seccion = isset($_GET['s']) ? $_GET['s'] : '';

$menu_template = "";
$body = '';

## CARGA DE ARCHIVOS JS
//$js_files[]= 'js/gentelella/vendors/jquery/dist/jquery.min.js';
//$js_files[]= 'js/gentelella/vendors/bootstrap/dist/js/bootstrap.bundle.min.js';
//$js_files[]= 'js/gentelella/vendors/fastclick/lib/fastclick.js';
//$js_files[]= 'js/gentelella/vendors/iCheck/icheck.min.js';
//$js_files[]= 'js/gentelella/js/custom.min.js';

$js_files[]= 'plugins/jquery/jquery.min.js';
$js_files[]= 'plugins/jquery-ui/jquery-ui.min.js';
$js_files[]= 'plugins/bootstrap/js/bootstrap.bundle.min.js';
$js_files[]= 'plugins/@adminlte/js/adminlte.js';



## CARGA DE ARCHIVOS CSS
//$css_files[] = 'js/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css';
//$css_files[] = 'js/gentelella/vendors/font-awesome/css/font-awesome.min.css';
//$css_files[] = 'js/gentelella/css/custom.css';
//$css_files[] = 'js/gentelella/vendors/iCheck/skins/flat/green.css';
$css_files[] = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback';
$css_files[] = 'plugins/fontawesome-free/css/all.min.css';
$css_files[] = 'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css';
$css_files[] = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback';
$css_files[] = 'plugins/@adminlte/css/adminlte.min.css';
$css_files[] = 'css/styles.css';

## Chequeo de Login
$loginclass = 'loginuser';
$loginclass::set_session();

## Envío el cliente a login si no hay registro en la sessión
if(!array_key_exists('id_admin',$_SESSION)){
	
	if($seccion !== 'registro'){
		$seccion = 'login';
	}
	$secciones = array(); // Borro el menú
}else{
	if(($seccion !== 'login')||($seccion !== 'registro') ){
		## Revisión si el usuario esta logeado
		$loginclass::check_login();
		if($_SESSION['id_admin'] === ''){
			$seccion = 'login';
			$secciones = array(); // Borro el menú
		}
	}
}

## Listado de Secciones
switch($seccion){

	case 'proveedor':
		require $site_config['modules'] . $modules_config['proveedor']['path'] . $modules_config['proveedor']['basefile'];
	break;

	case 'planta':
		require $site_config['modules'] . $modules_config['planta']['path'] . $modules_config['planta']['basefile'];
	break;

	case 'proveedor_planta':
		require $site_config['modules'] . $modules_config['proveedor_planta']['path'] . $modules_config['proveedor_planta']['basefile'];
	break;

	case 'personal':
		require $site_config['modules'] . $modules_config['personal']['path'] . $modules_config['personal']['basefile'];
	break;

	case 'vehiculo':
		require $site_config['modules'] . $modules_config['vehiculo']['path'] . $modules_config['vehiculo']['basefile'];
	break;

	case 'maquinaria':
		require $site_config['modules'] . $modules_config['maquinaria']['path'] . $modules_config['maquinaria']['basefile'];
	break;

	case 'usuario':
		require $site_config['modules'] . $modules_config['usuario']['path'] . $modules_config['usuario']['basefile'];
	break;

	case 'login':
		require $site_config['modules'] . $modules_config['login']['path'] . $modules_config['login']['basefile'];
		$secciones = array(); // Borro el menú
	break;

	case 'registro':
		require $site_config['modules'] . $modules_config['registro']['path'] . $modules_config['registro']['basefile'];
		$secciones = array(); // Borro el menú
	break;

    case 'logout':
        session_destroy();
        header('location:index.php');
    break;

	
}


if($seccion == 'login' || $seccion == 'logout' || $seccion == 'registro'){

	$plantilla = new template('templates/base-login');

} else {

	$plantilla = new template('templates/base');

	/*Armado sidebar */

	$menu = array();

	// Solo si es proveedor
	if($_SESSION['tipouser'] === 0){ 

		if(!array_key_exists('sidebar', $_SESSION)){
			$build_sidebar = true;
		 }else{
			 $build_sidebar = (count($_SESSION['sidebar']) === 0) ? true : false;
		 }
	
		if($build_sidebar){
			$sql_sidebar = "select plantas.id_planta, plantas.nombre_planta, proveedores_plantas.id_proveedor_planta
								from plantas
								inner join proveedores_plantas
								on proveedores_plantas.id_planta = plantas.id_planta
								and proveedores_plantas.id_proveedor = {$_SESSION['id_proveedor']}";
			

			$query_sidebar = $db->query($sql_sidebar);
			$rs_sidebar = $query_sidebar->fetchAll(PDO::FETCH_ASSOC);
										
			foreach($rs_sidebar as $valor){

				$menu['proveedor_planta'][$valor['id_planta']]['nombre_seccion'] = $valor['nombre_planta'];
				$menu['proveedor_planta'][$valor['id_planta']]['url_seccion'] = '?s=proveedor_planta&id='.$valor['id_proveedor_planta'];
			}

			$_SESSION['sidebar'] = $menu;

		} 
		
	} elseif($_SESSION['tipouser'] === 2){ //es superadmin
		
		if(!array_key_exists('sidebar', $_SESSION)){
			$build_sidebar = true;
		}else{
			$build_sidebar = (count($_SESSION['sidebar']) === 0) ? true : false;
		}

		if($build_sidebar){
			$sql_sidebar = "select plantas.id_planta, plantas.nombre_planta
								from plantas
							";

			$query_sidebar = $db->query($sql_sidebar);
			$rs_sidebar = $query_sidebar->fetchAll(PDO::FETCH_ASSOC);
										
			foreach($rs_sidebar as $valor){

				$menu['planta'][$valor['id_planta']]['nombre_seccion'] = $valor['nombre_planta'];
				$menu['planta'][$valor['id_planta']]['url_seccion'] = '?s=planta&id='.$valor['id_planta'];
			}

			$menu['usuario'][0]['nombre_seccion'] = 'Proveedores';
			$menu['usuario'][0]['url_seccion'] = '?s=usuario&tipouser=0';
			$menu['usuario'][1]['nombre_seccion'] = 'SyH';
			$menu['usuario'][1]['url_seccion'] = '?s=usuario&tipouser=1';
			$menu['usuario'][2]['nombre_seccion'] = 'Administradores';
			$menu['usuario'][2]['url_seccion'] = '?s=usuario&tipouser=2';

			$_SESSION['sidebar'] = $menu;
		
		}

	}

	
	foreach($_SESSION['sidebar'] as $nombre_entidad => $menu_entidad){
		$plantilla_sidebar = new template('templates/sidebar');
		$plantilla_sidebar->capturar_bucle('ITEM_MENU');

		foreach($menu_entidad as $id_entidad => $submenu){
			// Se revisa si se tiene que implementar el encodeado en UTF8
			if(mb_check_encoding($submenu['nombre_seccion'],'UTF-8')){
				$nombre_seccion = $submenu['nombre_seccion'];
			}else{
				$nombre_seccion = utf8_encode($submenu['nombre_seccion']);
			}

			$parsed = parse_url($submenu['url_seccion']);
			$selected = ($parsed['query'] == $_SERVER['QUERY_STRING']) ? ' active' : '';

			$plantilla_sidebar->reemplazar_contenido_bucle(array(
				'nombre_seccion'=>$nombre_seccion,
				'url_seccion'=>$submenu['url_seccion'],
				'selected'=>$selected
			));

		}

		$plantilla_sidebar->reemplazar_bucle();

		
		if($nombre_entidad == 'proveedor_planta'){
			$icono_entidad = "fas fa-industry";
			$titulo_entidad = 'Plantas';
		} else if($nombre_entidad === 'planta'){
			$icono_entidad = "fas fa-industry";
			$titulo_entidad = 'Plantas';
		} else if($nombre_entidad === 'usuario'){
			$icono_entidad = "fas fa-users";
			$titulo_entidad = 'Usuarios';
		}


		if($seccion == $nombre_entidad){
			$active = ' active';
			$menu_open = ' menu-open';
		} else {
			$active = '';
			$menu_open = '';
		}


		$plantilla_sidebar->asignar_variables(array(
			'nombre_entidad'=>$nombre_entidad, 
			'titulo_entidad'=>$titulo_entidad,
			'icono_entidad'=>$icono_entidad,
			'active'=>$active,
			'menu_open'=>$menu_open
		));

		$menu_template .= $plantilla_sidebar->procesar_plantilla();
	}
	

	$plantilla->capturar_bucle('JS-FILES');
	foreach($js_files as $key=>$valor){
		$plantilla->reemplazar_contenido_bucle(array('url'=>$valor));
	}
	$plantilla->reemplazar_bucle();

	$plantilla->capturar_bucle('CSS-FILES');
	foreach($css_files as $key=>$valor){
		$plantilla->reemplazar_contenido_bucle(array('url'=>$valor));
	}
	$plantilla->reemplazar_bucle();

}


$usuario ='';
$tipo_usuario ='';
$link_user_edit = '';

if(isset($_SESSION['tipouser']) && ($_SESSION['tipouser'] === 0)){
	$usuario = $_SESSION['nombre_usuario'];
	$tipo_usuario = 'Proveedor';
	$link_user_edit= '?s=proveedor&subsection=edit';
} else if (isset($_SESSION['tipouser']) && ($_SESSION['tipouser'] === 2)){
	$usuario = $_SESSION['nombre_usuario'];
	$tipo_usuario = 'Superadmin';
	$link_user_edit= '#';
}


$plantilla->asignar_variables(array(
	'body'=>$body, 
	'menu'=>$menu_template, 
	'usuario'=>$usuario,
	'tipo_usuario'=>$tipo_usuario,
	'link_user_edit'=>$link_user_edit
));

echo $plantilla->procesar_plantilla();