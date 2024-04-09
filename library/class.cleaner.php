<?php

/*
.-------------------------------------------------------------------.
| CLASS CLEANER v1.0.7 (2016-06)                                    |
| ------------------------------------------------------------------|
| Objeto de limpieza de diferentes contenidos                       |
|-------------------------------------------------------------------|
| (c) Leandro E. Renedo - Para Titansol                             |
.-------------------------------------------------------------------.
*/

class cleaner{

	////////////////////////////////////
    /// PROPIEDADES                  //
    ///////////////////////////////////

	private $original_content = ''; // Contenido original a limpiar
	private $string_content = ''; // Contenido ya limpiado
	private $error_messages = array(); // Contenedor de mensajes de error
	private $clean_options = array(); // Contenedor de las opciones de limpieza
	private $output = array(); // Contenedor de la salida de la limpieza


	 /**
     * Creación del objeto. Realizar carga por defecto de las variables de error
     * @access public
     * @return boolean true
    */
	public function __construct(){
		$this->error_messages['vacio'] = 'El contenido no puedo estar vacío';
		$this->error_messages['size']  = 'El contenido no posee el largo especificado';
		$this->error_messages['formato'] = 'El contenido no posee el formato correcto';

		return true;
	}

	public function set_error($error,$error_type){

		switch($error_type)
		{
			case 'vacio':
			case 'size':
			case 'formato':
				$contenido_error = $this->clean_data($error,'text',0,5,array('not_empty'=>true));
				if(!$contenido_error['error']) $this->error_messages[$error_type] = $contenido_error['valor'];
			break;
		}

	}

	 /**
     * Realiza la limpieza del string de acuerdo a los parámetros establecidos
     * @access public
     * @param string $data El string a limpiar
     * @param string $data_type El tipo de datos que se va a limpiar (text, email, int, decimal, date)
     * @param int max_lenght Longitud máxima del string (fecha máxima en caso de date)
     * @param int min_lenght Longitud mínima del string (fecha mínima en caso de date)
     * @param array extra_options Opciones extras de la limpieza. Valores:
     *							  only_text : Solo permite texto en la limpieza de texto
	 *							  not_empty: fuerza que data_type text tiene que tener contenido
	 *                            remove_space : remueve los espacios delante y detrás del string
	 * 							  clean_decimals: limpieza de decimales en data_type int
     * 							  decimal_separator: establece el caracter separador de decimales en data_type decimal
     * 							  thousand_separator: establece el caracter separados de miles en data_type decimal
     * 							  max_decimals: establece el máximo de decimales en data_type decimal
     * @return array el caracter limpiado
    */
	public function clean_data($data,$data_type,$max_lenght=0,$min_lenght=0,$extra_options=array()){

		$this->string_content = $data;
		$this->original_content = $data;
		$this->clean_options['max_lenght'] = ($data_type != 'date') ? intval($max_lenght) : $max_lenght;
		$this->clean_options['min_lenght'] = ($data_type != 'date') ? intval($min_lenght) : $min_lenght;
		$this->clean_options['extras'] = $extra_options;

		switch($data_type)
		{
			case 'text':
				$this->clean_text();
				$this->return_output();
			break;

			case 'email':
				$this->clean_email();
				$this->return_output();
			break;

			case 'int':
				$this->clean_int();
				$this->return_output();
			break;

			case 'decimal':
				$this->clean_decimal();
				$this->return_output();
			break;

			case 'date':

			break;

			default:
				// Muestra error porque el data type no es reconocido
			break;

		}

		return $this->output;
	}


	private function clean_text(){

		$this->string_content = filter_var($this->string_content, FILTER_SANITIZE_ADD_SLASHES);
		$this->string_content = filter_var($this->string_content,FILTER_SANITIZE_SPECIAL_CHARS);
		$this->string_content = filter_var($this->string_content,FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		$regular_exp_range = '';

		##Fijo los limites para para la expresion regular
		if($this->clean_options['max_lenght'] != 0)
		{
			$regular_exp_range = "{". $this->clean_options['min_lenght'] .",". $this->clean_options['max_lenght'] ."}";
		}else{
			$regular_exp_range = "{". $this->clean_options['min_lenght'] .",}";
		}

		## Fijo las reglas de la expresión regular
		if(array_key_exists('only_text', $this->clean_options['extras']))
		{
			//Solo es texto puro sin números
			$regular_expression = "/^[a-zA-ZñÑ\s\W]*/";
		}else{
		    //Texto con numeros
			$regular_expression = "/^[0-9a-zA-ZñÑ_\s\W]*/";
		}

		## Primero reviso si cumple los requisitos de mínimo y máximo
		$this->string_content = filter_var($this->string_content,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^.".$regular_exp_range."/")));

		## Si se fuerza a que no sea vacio, se revisa.
		if(array_key_exists('not_empty', $this->clean_options['extras'])){
			$this->string_content = (strlen($this->string_content) == 0) ? false : $this->string_content;
		}

		## Si no es false, quiere decir que cumple con los requisitos de longitud
		if(!is_bool($this->string_content))
		{
			$this->string_content = filter_var($this->string_content,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>$regular_expression)));

			## Si se pide, se remueve los espacios delante y detrás del string
			if(!is_bool($this->string_content) and array_key_exists('remove_space',$this->clean_options['extras']))
			{
				$this->string_content = trim($this->string_content);
			}



		}else{

			## Completo los valores de variables para saber exactamente cual fue el error de longuitud
			if(strlen($this->original_content) < $this->clean_options['min_lenght']){
				$this->original_content = 'small'; // mas pequeño que lo especificado
				$this->string_content = true;
			}elseif(strlen($this->original_content) > $this->clean_options['max_lenght']){
				$this->original_content = 'large'; // mas largo que lo especificado
				$this->string_content = true;
			}else{
				$this->original_content = "empty"; // Es vacio
				$this->string_content = true;
			}



		}
	}

	private function clean_email(){

		## Primero reviso si cumple los requisitos de mínimo y máximo
		$this->string_content = filter_var($this->string_content,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^.{1,}$/")));

		## Si no es false, quiere decir que cumple con los requisitos de longitud
		if($this->string_content !== false){
			$this->string_content = filter_var($this->string_content, FILTER_SANITIZE_EMAIL);
			$this->string_content = filter_var($this->string_content, FILTER_VALIDATE_EMAIL);
		}else{
				$this->original_content = "empty"; // Es vacio
			$this->string_content = true;
		}
	}

	private function clean_int(){

		// Si se pide se elimina los decimales del número
		if(isset($this->clean_options['extras']['clean_decimals'])){
			if($this->clean_options['extras']['clean_decimals'] == true){
				$this->string_content = intval($this->string_content);
			}
		}

		$number_limit = array();

		## Creación de los límites para el filtro depende las opciones
		if($this->clean_options['min_lenght'] != 0 ){
			if($this->clean_options['max_lenght'] != 0){
				$number_limit = array("options"=>array("min_range"=>$this->clean_options['min_lenght'],"max_range"=>$this->clean_options['max_lenght']));
			}else{
				$number_limit = array("options"=>array("min_range"=>$this->clean_options['min_lenght']));
			}
		}else{

			if($this->clean_options['max_lenght'] != 0)
			{
				$number_limit = array("options"=>array("min_range"=>"0","max_range"=>$this->clean_options['max_lenght']));
			}
		}

		$this->string_content = filter_var($this->string_content,FILTER_VALIDATE_INT, $number_limit);

		if($this->string_content === false)
		{
			if(intval($this->original_content) < $this->clean_options['min_lenght']){
				$this->original_content = 'small'; // mas pequeño que lo especificado
				$this->string_content = true;
			}elseif(intval($this->original_content) > $this->clean_options['max_lenght']){
				$this->original_content = 'large'; // mas largo que lo especificado
				$this->string_content = true;
			}else{
				$this->original_content = 'empty';
				$this->string_content = true;
			}
		}

	}

	private function clean_decimal(){

		## Revision del largo del número si se preestablecer un mínimo y/o máximo
		if($this->clean_options['min_lenght'] != 0 or $this->clean_options['max_lenght'] != 0){
			$this->clean_options['extras']['clean_decimals'] = true; // establezco la limpieza de decimales para que el chequeo de int funcione correctamente
			$this->clean_int();
		}


		// Si no hay error con el proceso de chequeo, proceso el decimal
		if(!is_bool($this->string_content)){

			$decimal_separator = array('flags' => FILTER_FLAG_ALLOW_THOUSAND);

			if(array_key_exists('decimal_separator', $this->clean_options['extras']))
			{
				$decimal_separator = array('options' => array('decimal'=>$this->clean_options['extras']['decimal_separator']), 'flags' => FILTER_FLAG_ALLOW_THOUSAND);
			}

			$this->string_content = filter_var($this->original_content,FILTER_VALIDATE_FLOAT, $decimal_separator);

			## Reviso si existen los parámetros de separacion de decimales y máximo de decimales
			if($this->string_content !== false)
			{

				if( array_key_exists('decimal_separator', $this->clean_options['extras']) or
					array_key_exists('thousand_separator', $this->clean_options['extras']) or
					array_key_exists('max_decimals', $this->clean_options['extras'])){

					$decimal_separator  = (array_key_exists('decimal_separator', $this->clean_options['extras'])) ? $this->clean_options['extras']['decimal_separator'] : ',';
					$thousand_separator = (array_key_exists('thousand_separator', $this->clean_options['extras'])) ? $this->clean_options['extras']['thousand_separator'] : '.';
					$max_decimals       = (array_key_exists('max_decimals', $this->clean_options['extras'])) ? $this->clean_options['extras']['max_decimals'] : 2;

					$this->string_content = number_format($this->string_content, $max_decimals, $decimal_separator, $thousand_separator);

				}

			}

		}

	}

	private function clean_date(){

		## Establece que formato va a tener la fecha
		if( (array_key_exists('date_format', $this->clean_options['extras'])))
		{
			$date_format = explode('-',$this->clean_options['extras']['date_format']);
		}
		else
		{
			$date_format = array(0=>'dd',
				                 1=>'mm',
				                 2=>'yyyy');
		}

		## Separa la fecha para poder saber el formato de origen
		$date_separator = ((array_key_exists('date_separator', $this->clean_options['extras']))) ? $this->clean_options['extras']['date_separator'] : '-';

		$date_explode = explode($date_separator,$this->string_content);

		if($this->clean_options[''])
		{
			$date_max_explode = '';
			$date_min_explode = '';
		}

		foreach($date_format as $key=>$value){

			switch($value){
				case 'dd':
					$date_day = $date_explode[$key];
				break;

				case 'mm':
					$date_month = $date_explode[$key];
				break;

				case 'yyyy':
					$date_year = $date_explode[$key];
				break;
			}

		}

		## Reviso si la fecha es válida
		if(checkdate($date_month, $date_day, $date_year)){

			# Controlo si la fecha es mayor o menor a lo establecido


			# Devuelvo la fecha de acuerdo al parámetro solicitado


		}else{

		}

	}

	private function return_output()
	{

		$this->output = array();

		if(!is_bool($this->string_content))
		{
			$this->output['valor'] = $this->string_content;
			$this->output['error'] = false;
			$this->output['error_msj'] = '';

		}else{

			switch($this->string_content)
				{
				case true: // El dato no cumple el largo estipulado

					$this->output['valor'] = '';
					$this->output['error'] = true;

					switch($this->original_content){
						case 'small':
						case 'large':
							$this->output['error_msj'] = $this->error_messages['size'];
						break;
						case 'empty':
						defaut:
							$this->output['error_msj'] = $this->error_messages['vacio'];
						break;
					}

				break;

				case false: // El dato no cumple el formato estipulado
					$this->output['valor'] = '';
					$this->output['error'] = true;
					$this->output['error_msj'] = $this->error_messages['formato'];
				break;
				}
		}

	}
}