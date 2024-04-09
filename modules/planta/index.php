
<?php

$planta_subsection = (isset($_GET['subsection'])) ? $_GET['subsection'] : 'default';
$template_path = $modules_config['planta']['path'] . 'templates/';

// Agregado de JS especÃ­ficos

// Agregado de CSS especÃ­ficos
	$css_files[] = 'js/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css';
	$css_files[] = 'js/gentelella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css';

// Marco como sección actual


if($_SESSION['tipouser'] != 2){
	echo "no tiene persmisos para esta pagina";
	exit();
}

switch($planta_subsection){

	default :
		$css_files[] = 'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css';
		$css_files[] = 'plugins/datatables-responsive/css/responsive.bootstrap4.min.css';
		$css_files[] = 'plugins/datatables-buttons/css/buttons.bootstrap4.min.css';

		$js_files[] = 'plugins/datatables/jquery.dataTables.min.js';
		$js_files[] = 'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js';
		$js_files[] = 'plugins/datatables-responsive/js/dataTables.responsive.min.js';
		$js_files[] = 'plugins/datatables-responsive/js/responsive.bootstrap4.min.js';
		$js_files[] = 'plugins/datatables-buttons/js/dataTables.buttons.min.js';
		$js_files[] = 'plugins/datatables-buttons/js/buttons.bootstrap4.min.js';		
		
		
		$js_files[] = $site_config['modules'] .$modules_config['planta']['path'] . 'templates/js/planta_listado.js';

		require $site_config['modules'] . $modules_config['planta']['path'] . 'planta_listado.php';
	break;

}

?>