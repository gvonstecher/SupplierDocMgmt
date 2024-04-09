<?php

require 'env.php';

$site_config = array(
					'base_domain'    =>ADMIN_DOMAIN,
					'base_protocol'	 => HTTP_PROTOCOL,
					'debug_mode'     =>true,
					'mysql_server'	 =>'mysql:host='. DB_HOST .';dbname='. DB_DB.';',
					'mysql_user'       =>DB_USER,
				 	'mysql_pswd'       =>DB_PASS,
				 	'mysql_db'         =>DB_DB,	
					'modules'        =>'modules/',
					'library'        =>'library/',
					'cookie_domain'  =>COOKIE_DOMAIN,
					'cookie_path'    =>COOKIE_PATH,
					'images_folder'  =>HTTP_PROTOCOL . '://' . IMAGES_DOMAIN,
					);



## Si se envía el parámetro de URL "mostrar_errores=1" el sistema va a mostrar los errores
if(isset($_GET['mostrar_errores'])){
	$site_config['debug_mode'] = true;
}

## Debug function
# Seteo de reporte de errores
if($site_config['debug_mode'])
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}
#################

try {
	$db = new PDO($site_config['mysql_server'], $site_config['mysql_user'], $site_config['mysql_pswd']);
}catch (PDOException $e) {
	if($site_config['debug_mode']){
		exit('Falló la conexión: ' . $e->getMessage());
	}
}

## Conexion a peticion para la DB de Actividad Fisica

function connectionToAFDB(){
	global $site_config;

	try {
		$dbAF = new PDO($site_config['sql_server_af'], $site_config['sql_user'], $site_config['sql_pswd']);
	}catch (PDOException $e) {
		if($site_config['debug_mode']){
			exit('Falló la conexión: ' . $e->getMessage());
		}
	}

	return $dbAF;

}

## Funcion de autoload para objectos ##
function autoload($archivo){
	global $site_config;
	require_once ($site_config['library'].'class.'.$archivo.'.php');
}

spl_autoload_register('autoload');


function print_debug($var,$dump=false)
{
	echo '<pre>';
	if($dump) var_dump($var);
	else print_r($var);
	echo '</pre>';
}