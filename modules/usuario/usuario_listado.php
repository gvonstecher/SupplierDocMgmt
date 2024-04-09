<?php

$template_path =  $site_config['modules'] . $modules_config['usuario']['path'] . 'templates/';

$tipouser = (isset($_GET['tipouser'])) ? $_GET['tipouser'] : '0';

$plantilla = new template($template_path . 'usuario_listado.html');

switch($tipouser){
    case '1':
        $tipouser_nombre = "Seguridad e Higiene";
        $tipouser_nombre_sing = "Seguridad e Higiene";
    break;
    case '2':
        $tipouser_nombre = "Administradores";
        $tipouser_nombre_sing = "Administrador";
    break;
    case '0':
    default:
        $tipouser_nombre = "Proveedores";
        $tipouser_nombre_sing = "Proveedor";
    break;
}

$sql_usuarios = "select * from admin where tipouser_admin = {$tipouser}";
$query_usuarios = $db->query($sql_usuarios);
$rs_usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);


$template_vars['tipouser'] = $tipouser;
$template_vars['tipouser_nombre'] = $tipouser_nombre;
$template_vars['tipouser_nombre_sing'] = $tipouser_nombre_sing;

$plantilla->capturar_bucle('USUARIOS');
foreach($rs_usuarios as $usuario){

    $plantilla->reemplazar_contenido_bucle(array(
        'id'=>$usuario['id_admin'],
        'nombre'=>$usuario['username_admin'],
        'mail'=>$usuario['mail_admin'],
    ));
}
$plantilla->reemplazar_bucle();

$plantilla->capturar_bucle('NOT_PROVEEDOR');
if($tipouser != 0){
    $plantilla->reemplazar_contenido_bucle(array(
        'tipouser'=>$tipouser,
    ));
}

$plantilla->reemplazar_bucle();


$plantilla->asignar_variables($template_vars);
$body = $plantilla->procesar_plantilla();



?>