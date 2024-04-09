<?php

function get_pesoideal($estatura,$estructura,$sexo) {

	global $db;

	if ($sexo == "1") { //masculino ajusta las alturas a los datos de la base
		if ((floatval($estatura) % 2) == 0) {
			$estatura= floatval($estatura) + 1;
		}

	}else{ // femenino ajusta las alturas a los datos de la base

		if ((floatval($estatura) % 2) != 0) {
			$estatura = floatval($estatura) + 1;
		}
	}

	$aux = intval($estatura) / 100;

	if (substr($aux,1, strlen($aux)) == "0") {
		$aux=substr(aux,1,strlen(aux)-1);
	}

	$sql = "select * from pesoideal where PSI_estatura=".$aux." and CTX_codigo=".$estructura." and PSI_sexo=".$sexo."";
	$queryPesoIdeal = $db->query($sql);
	$rsPesoIdeal = $queryPesoIdeal->fetchAll(PDO::FETCH_ASSOC);

	return $rsPesoIdeal[0]["PSI_peso"];

}



function get_parte1($edad2) { // 1 kilo por cada 10 años despues de los 20 de edad

	$edad = intval($edad2);
	$kilos = 0;

	if ($edad > 20) {
		$edad = $edad-20;
		$kilos= intval($edad/10);
		$aux=($edad % 10)/10; //Valor proporcional de los kilos (no entero)
		$kilos = $kilos + $aux;
	}

	return $kilos;
}

function get_parte3($sobrepeso2) {//1 kilo por cada 10 años de sobrepeso alcanzado

	$sobrepeso = intval($sobrepeso2);
	$kilos = 0;

	if ($sobrepeso > 0) {
		$kilos = intval($sobrepeso/10);
		$aux=($sobrepeso % 10)/10; //Valor proporcional de los kilos (no entero)
		$kilos=$kilos+$aux;
	}
	return $kilos;
}

function get_parte4($sobrepeso2,$peso) { //2 kilos por cada 10 kilos de sobrepeso sobre 100

	$sobrepeso = intval($sobrepeso2);
	$kilos=0;

	if ($peso>100) {
		$kilos=  intval($sobrepeso/10)*2;
		$aux=($sobrepeso % 10)/20; //Valor proporcional de los kilos (no entero)
		$kilos=$kilos+$aux;
	}

	return $kilos;
}
