<?php

$proveedor_subsection = (isset($_GET['subsection'])) ? $_GET['subsection'] : 'default';
$template_path = $modules_config['proveedor_planta']['path'] . 'templates/';

// Agregado de JS especÃ­ficos

// Agregado de CSS especÃ­ficos
	$css_files[] = 'js/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css';
	$css_files[] = 'js/gentelella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css';

// Marco como sección actual

if(($_SESSION['tipouser'] != 0 )&& ($_SESSION['tipouser'] != 2 )){
	echo "no tiene persmisos para esta pagina";
	exit();
}


if($_SESSION['tipouser'] == 0 ){
	//chequeo de primer login o datos sin carga
	$sql_checkvacio = "SELECT 
							cuit_proveedor, 
							tipo_social_proveedor, 
							persona_contacto_proveedor, 
							telefono_contacto_proveedor,
							mail_contacto_proveedor
							from proveedores
							where id_proveedor = ".$_SESSION['id_proveedor'];

	$query_checkvacio = $db->query($sql_checkvacio);
	$rs_checkvacio = $query_checkvacio->fetch(PDO::FETCH_ASSOC);
	if(empty($rs_checkvacio['cuit_proveedor']) || empty($rs_checkvacio['tipo_social_proveedor']) || empty($rs_checkvacio['persona_contacto_proveedor']) || empty($rs_checkvacio['telefono_contacto_proveedor']) || empty($rs_checkvacio['mail_contacto_proveedor'])){
		header("location:index.php?s=proveedor&subsection=edit",true);
	}
}


switch($proveedor_subsection){

	case 'edit': // Edición y/o nueva promocion
		$js_files[] = 'js/gentelella/vendors/jquery.hotkeys/jquery.hotkeys.js';
		$js_files[] = 'js/gentelella/vendors/google-code-prettify/src/prettify.js';
		$js_files[] = 'js/summernote/summernote-bs4.min.js';
		$js_files[]= 'js/gentelella/vendors/moment/min/moment.min.js';
		$js_files[]= 'js/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js';
		$js_files[]= 'js/es.js';
		$js_files[] = 'js/gentelella/vendors/autocomplete/jquery.easy-autocomplete.min.js';
		$js_files[] = 'js/gentelella/vendors/jquery.tagsinput/src/jquery.tagsinput.js';
		
		$css_files[] = 'js/summernote/summernote-bs4.css';
		$css_files[]= 'js/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css';
		$css_files[] = 'js/gentelella/vendors/autocomplete/easy-autocomplete.min.css';
		$css_files[] = 'js/gentelella/vendors/autocomplete/easy-autocomplete.themes.min.css';
		$css_files[] = 'js/gentelella/vendors/jquery.tagsinput/src/jquery.tagsinput.css';



	break;

	default :
		$css_files[] = 'plugins/select2/css/select2.min.css';
		$css_files[] = 'plugins/dropzone/min/dropzone.min.css';
		$css_files[] = 'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css';
		$css_files[] = 'plugins/datatables-responsive/css/responsive.bootstrap4.min.css';
		$css_files[] = 'plugins/datatables-buttons/css/buttons.bootstrap4.min.css';
		$css_files[] = 'plugins/daterangepicker/daterangepicker.css';
		
		
		$js_files[] = 'plugins/moment/moment.min.js';
		$js_files[] = 'plugins/select2/js/select2.full.min.js';
		$js_files[] = 'plugins/dropzone/min/dropzone.min.js';
		$js_files[] = 'plugins/inputmask/jquery.inputmask.min.js';
		$js_files[] = 'plugins/datatables/jquery.dataTables.min.js';
		$js_files[] = 'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js';
		$js_files[] = 'plugins/datatables-responsive/js/dataTables.responsive.min.js';
		$js_files[] = 'plugins/datatables-responsive/js/responsive.bootstrap4.min.js';
		$js_files[] = 'plugins/datatables-buttons/js/dataTables.buttons.min.js';
		$js_files[] = 'plugins/datatables-buttons/js/buttons.bootstrap4.min.js';
		$js_files[] = 'plugins/daterangepicker/daterangepicker.js';
	
		
		$js_files[] = $site_config['modules'] .$modules_config['proveedor_planta']['path'] . 'templates/js/proveedor_planta_listado.js';

		require $site_config['modules'] . $modules_config['proveedor_planta']['path'] . 'proveedor_planta_listado.php';
	break;

}

?>