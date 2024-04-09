<?php
require '../../library/configuration.php';
require_once "../../library/class.phpmailer.php";


function mailDocumentacionPresentada($db,$idProveedorPlanta){

	$sql_datos = "select plantas.nombre_planta, proveedores.nombre_proveedor, proveedores.cuit_proveedor, admin.mail_admin from proveedores_plantas
					inner join proveedores on proveedores.id_proveedor = proveedores_plantas.id_proveedor
					inner join plantas on plantas.id_planta = proveedores_plantas.id_planta
					inner join admin on proveedores.id_admin = admin.id_admin
					where id_proveedor_planta = {$idProveedorPlanta}";
	$query_datos = $db->query($sql_datos);
	$rs_datos = $query_datos->fetch(PDO::FETCH_ASSOC);

	$mail = new PHPMailer(true);
				try {
					//Server settings
					
					$mail->isSMTP();                                            //Send using SMTP
					$mail->Host       = 'smtp.proveedoreshz.com';                     //Set the SMTP server to send through
					$mail->SMTPAuth = true;                             //Enable SMTP authentication
					$mail->Username   = 'noreply@proveedoreshz.com';                     //SMTP username
					$mail->Password   = 'xKLnj02CIcF8';                               //SMTP password
					//$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
					$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
				
					//Recipients
					$mail->setFrom($mail->Username, 'Proveedores HZ');
					$mail->addAddress($rs_datos['mail_admin'], $rs_datos['nombre_proveedor']);     //Add a recipient
					$mail->addBCC(MAIL_ADMIN_MAIL, MAIL_ADMIN_NAME);

					//Content
					$mail->isHTML(true);                                  //Set email format to HTML
					$mail->Subject = mb_convert_encoding("Su documentación ha sido recibida", 'ISO-8859-1', 'UTF-8');
					$mail->Body    = mb_convert_encoding("La documentación del Proveedor <b>{$rs_datos['nombre_proveedor']}</b> para la planta <b>{$rs_datos['nombre_planta']}</b> ha sido recepcionada correctamente. Su estado actual es: EN PROCESO DE REVISION. Cualquier duda podrá chequear información ingresando a <a href='".ADMIN_DOMAIN."' target='_blank'>".ADMIN_DOMAIN."</a>", 'ISO-8859-1', 'UTF-8');
					$mail->AltBody = mb_convert_encoding("La documentación del Proveedor “{$rs_datos['nombre_proveedor']}” para la planta “{$rs_datos['nombre_planta']}” ha sido recepcionada correctamente. Su estado actual es: EN PROCESO DE REVISION. Cualquier duda podrá chequear información ingresando a ".ADMIN_DOMAIN, 'ISO-8859-1', 'UTF-8');

					$mail->send();
			
				} catch (Exception $e) {
					echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
				}
}

function mailEstadoCambiado($db,$idProveedor, $estado, $estadoDetalle){


	$sql_datos= "select proveedores.nombre_proveedor, admin.mail_admin from proveedores
					inner join admin on proveedores.id_admin = admin.id_admin
					where id_proveedor = {$idProveedor}";
					
	$query_datos = $db->query($sql_datos);
	$rs_datos = $query_datos->fetch(PDO::FETCH_ASSOC);

	$mail = new PHPMailer(true);
				try {
					//Server settings
					
					$mail->isSMTP();                                            //Send using SMTP
					$mail->Host       = 'smtp.proveedoreshz.com';                     //Set the SMTP server to send through
					$mail->SMTPAuth = true;                             //Enable SMTP authentication
					$mail->Username   = 'noreply@proveedoreshz.com';                     //SMTP username
					$mail->Password   = 'xKLnj02CIcF8';                                //SMTP password
					//$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
					$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
				
					//Recipients
					$mail->setFrom($mail->Username, 'Proveedores HZ');
					$mail->addAddress($rs_datos['mail_admin'], $rs_datos['nombre_proveedor']);     //Add a recipient
					$mail->addBCC(MAIL_ADMIN_MAIL, MAIL_ADMIN_NAME);

					//Content
					$mail->isHTML(true);                                  //Set email format to HTML
					$mail->Subject = mb_convert_encoding('El estado de su documentación ha sido modificado', 'ISO-8859-1', 'UTF-8');
					$mail->Body    = mb_convert_encoding("<p>El estado de su documentacion ha cambiado a <b>{$estado}</b>.</p> 
										<p><i>{$estadoDetalle}</i></p>
										<p>Por favor ingrese a <a href='".ADMIN_DOMAIN."' target='_blank'>".ADMIN_DOMAIN."</a> para mas información", 'ISO-8859-1', 'UTF-8');
					$mail->AltBody = "El estado de su documentacion ha cambiado a “{$estado}”. “{$estadoDetalle}”. Por favor ingrese a ".ADMIN_DOMAIN." para mas información";

					$mail->send();
				} catch (Exception $e) {
					echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
				}

}

if($_SERVER['REQUEST_METHOD'] == "POST"){

	switch($_POST['action']){

		case 'busca_tipos_documentos':
			$tipo_entidad = $_POST['tipo_entidad'];
			$id_entidad = $_POST['id_entidad'];

			if($tipo_entidad == 0){
				$id_proveedor = $_POST['id_proveedor'];
				$select_excenciones = "select exencion_ganancias_proveedor,exencion_caba_proveedor,contribuyente_iibb_proveedor, exencion_iibb_bsas_proveedor from proveedores where id_proveedor = {$id_proveedor}";
				$query_excenciones = $db->query($select_excenciones);
				$rs_excenciones = $query_excenciones->fetch(PDO::FETCH_ASSOC);
			}

			//excluyo los documentos que ya se agregaron
			$sql_search = "SELECT * 
						from tipos_documentaciones 
						where tipo_entidad_tipo_documentacion = {$tipo_entidad}
						and id_tipo_documentacion not in (
							select distinct id_tipo_documentacion 
								from documentaciones 
								where id_entidad_asociada_documentacion = {$id_entidad} 
								and tipo_entidad_documentacion = {$tipo_entidad}
						)";

			$query_search = $db->query($sql_search);

			$resultados_array = array();

			while($rs_search = $query_search->fetch(PDO::FETCH_ASSOC)){
				$entro =true;
				
				if($tipo_entidad == 0){
					if(($rs_excenciones['exencion_ganancias_proveedor'] == 1) && $rs_search['id_tipo_documentacion'] == 30 ){
						$entro = false;
					}
	
					if(($rs_excenciones['exencion_caba_proveedor'] == 1) && $rs_search['id_tipo_documentacion'] == 31 ){
						$entro = false;
					}
	
					if(($rs_excenciones['contribuyente_iibb_proveedor'] == 1) && $rs_search['id_tipo_documentacion'] == 32 ){
						$entro = false;
					}

					if(($rs_excenciones['exencion_iibb_bsas_proveedor'] == 1) && $rs_search['id_tipo_documentacion'] == 33 ){
						$entro = false;
					}
				}

				if($entro){
					$resultados_array[] = array(
						'id'=>$rs_search['id_tipo_documentacion'], 
						'nombre'=>$rs_search['nombre_tipo_documentacion'],
						'detalle'=>$rs_search['detalle_tipo_documentacion'],
						'vencimiento'=>$rs_search['vencimiento_tipo_documentacion']
					);
				}
			}

			$resultados_json = json_encode($resultados_array);

			echo $resultados_json;

		break;

        case 'agrega_documento':

            if((empty($_POST["fecha_vto_documentacion"])) || (intval($_POST['ignora_fecha']))){
				$sql = "INSERT INTO documentaciones
						(id_tipo_documentacion, id_entidad_asociada_documentacion, tipo_entidad_documentacion, filename_documentacion) VALUES
						({$_POST["id_tipo_documentacion"]},{$_POST["id_entidad_asociada_documentacion"]},{$_POST["tipo_entidad_documentacion"]}, '{$_FILES['filename_documentacion']['name']}')";
            } else {
                $date = str_replace('/', '-', $_POST["fecha_vto_documentacion"]);
				$mysql_date = date ('Y-m-d H:i:s', strtotime($date));
                $sql = "INSERT INTO documentaciones
						(fecha_vto_documentacion, id_tipo_documentacion, id_entidad_asociada_documentacion, tipo_entidad_documentacion, filename_documentacion) VALUES
						('{$mysql_date}',{$_POST["id_tipo_documentacion"]},{$_POST["id_entidad_asociada_documentacion"]},{$_POST["tipo_entidad_documentacion"]}, '{$_FILES['filename_documentacion']['name']}')";
            }
            

            if($db->query($sql)){
				$inserted_id =  intval($db->lastInsertId());
                $target_path = '../../documentos/'.$inserted_id;

                if (!file_exists($target_path)) {
                    mkdir($target_path, 0777, true);
                    $temp_file = $_FILES['filename_documentacion']['tmp_name'];
                    $target_file =  $target_path. "/". $_FILES['filename_documentacion']['name'];
                    
                    move_uploaded_file($temp_file,$target_file);

                    echo "true";
                }
			}else{
				echo 'false';
			}

        break;

        case 'busca_documentos':

            $sql_search = "SELECT
                                d.id_documentacion,
                                d.filename_documentacion,
                                d.fecha_vto_documentacion,
                                td.nombre_tipo_documentacion
                            from documentaciones d 
                            left join
                                tipos_documentaciones td
                            on 
                                d.id_tipo_documentacion = td.id_tipo_documentacion
                            where tipo_entidad_documentacion = {$_POST['tipo_entidad']} and id_entidad_asociada_documentacion = {$_POST['id_entidad']}";

			$query_search = $db->query($sql_search);
			$resultados_array = array();

			while($rs_search = $query_search->fetch(PDO::FETCH_ASSOC)){
                if(!empty($rs_search['fecha_vto_documentacion'])){
                    $phpdate = strtotime($rs_search['fecha_vto_documentacion']);
                    $fecha_formateada = date( 'd/m/Y', $phpdate );
                } else {
                    $fecha_formateada = "no aplica";
                }
				$resultados_array[] = array(
                    'nombre_documento'=>$rs_search['nombre_tipo_documentacion'],
                    'fecha_vencimiento'=>$fecha_formateada,
                    'url_documento'=>'documentos/'.$rs_search['id_documentacion'].'/'.$rs_search['filename_documentacion'],
                    'id_documento'=>$rs_search['id_documentacion']
                );
            }

            $resultados_json = json_encode($resultados_array);
			echo $resultados_json;

        break;

		case 'borra_documento':
			$id = $_POST['id'];
			$dirname = '../../documentos/'.$id;
			array_map('unlink', glob("$dirname/*.*"));
			rmdir($dirname);
			$sql = "delete from documentaciones where id_documentacion = {$id}";
			if($db->query($sql)){
				echo 'true';
			} else {
				echo 'false';
			}

		break;

		case 'edita_estado':
			$sql= "update proveedores_plantas set estado_proveedor = {$_POST['estado']}, estado_detalle_proveedor = '{$_POST['estado_detalle']}' where id_proveedor_planta = {$_POST['id_proveedor_planta']}";
			if($db->query($sql)){

				switch($_POST['estado']){
					case 1:
						$estado= 'Autorizado';
					break;
					case 2:
						$estado= 'No autorizado';
					break;
					case 3:
						$estado= 'Adeuda Información';
					break;
					case 4:
						$estado= 'Desafectado';
					break;
					case 0:
					default:
						$estado= 'En revisión';
					break;
				}
				mailEstadoCambiado($db,$_POST['id_proveedor'],$estado,$_POST['estado_detalle']);
				echo "true";
			} else {
				echo 'false';
			}
		break;

		case 'presentar_documentacion':
			$sql= "update proveedores_plantas set estado_proveedor = 0 where id_proveedor_planta = {$_POST['id_proveedor_planta']}";
			if($db->query($sql)){
				mailDocumentacionPresentada($db,$_POST['id_proveedor_planta']);
				echo "true";
			} else {
				echo "false";
			}
			
		break;

	}

}