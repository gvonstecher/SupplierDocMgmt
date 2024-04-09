<?php

$proveedor_subsection = (isset($_GET['subsection'])) ? $_GET['subsection'] : 'default';

$template_path = $modules_config['maquinaria']['path'] . 'templates/';

// Agregado de JS especÃ­ficos

// Agregado de CSS especÃ­ficos
	$css_files[] = 'js/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css';
	$css_files[] = 'js/gentelella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css';

// Marco como sección actual


if($_SESSION['tipouser'] != 0 || (empty($_GET['pp_id']))){
	echo "no tiene persmisos para esta pagina";
	exit();
}


switch($proveedor_subsection){

	case 'edit': // Edición y/o nueva promocion
	default: 
		$js_files[] = 'js/gentelella/vendors/jquery.hotkeys/jquery.hotkeys.js';
		$js_files[] = 'js/gentelella/vendors/google-code-prettify/src/prettify.js';
		//$js_files[] = 'js/gentelella/vendors/dropzone/dist/min/dropzone.min.js';
		//$js_files[]= 'js/gentelella/vendors/bootstrap-daterangepicker/build/js/bootstrap-datetimepicker.min.js';
		$js_files[] = 'js/gentelella/vendors/jquery.tagsinput/src/jquery.tagsinput.js';

		//$css_files[] = 'js/gentelella/vendors/dropzone/dist/min/dropzone.min.css';
		$css_files[] = 'js/summernote/summernote-bs4.css';
		$css_files[]= 'js/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css';

		require $site_config['modules'] .$modules_config['maquinaria']['path'] . 'maquinaria_edit.php';
	break;

}

?>