<?php

/*
.-------------------------------------------------------------------.
| CLASS LOGINUSER v1.0.0 (2016-12)                                  |
| ------------------------------------------------------------------|
| Crear sessión de usuario con un hash verificador y opciones		|
| de sessión										                |
|-------------------------------------------------------------------|
| (c) Leandro E. Renedo - Para Titansol                             |
.-------------------------------------------------------------------.
*/

class loginuser {

	////////////////////////////////////
	/// PROPIEDADES                  //
	///////////////////////////////////
	private static $session_options = array('name'=>'SESSPHPID',
											'cookie_domain'=>'',
											'cookie_path'=>'/',
											'cookie_lifetime'=>0,
											'cookie_secure'=>false,
											'cookie_httponly'=>true);


	/**
	* Setea la opcion especificada antes de inicializar la sesión
	* @access public
	* @param $option string nombre de la opcion a setear
	* @param $valor string|int|boolean valor de la opcion
	* @return void
	*/
	public static function set_option($opcion,$valor){

		if(array_key_exists($opcion, self::$session_options)){
				self::$session_options[$opcion] = $valor;
		}

	}

	/**
	* Establece la session con los valores especificados en $session_options
	* @access public
	* @return void
	*/
	public static function set_session(){

		if(substr(phpversion(),0,1) >= '7'){
			session_start(self::$session_options);
		}else{

			foreach(self::$session_options as $clave=>$valor){
					ini_set("session.".$clave, $valor);
			}

			session_start();
		}

	}

	/**
	* Crea el login del usuario
	* @access public
	* @param $username string El nombre de usuario
	* @return void
	*/
	public static function start_login($username){

		// Creo un valor por defecto que me ate la sessión al usuario
		$user_key = password_hash($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'],PASSWORD_DEFAULT);

		$_SESSION['user'] = $username;
		$_SESSION['key'] = $user_key;

	}

	/**
	* Revisa que la sessión esté creada y que el hash del user coincida con el establecido en el login. De lo contrario, elimina la sessión
	* @access public
	* @return void
	*/
	public static function check_login(){

		$user_key = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];

		if(isset($_SESSION['user'])){
			// Verifico el hash controlador
			if(password_verify($user_key,$_SESSION['key'])){
				session_regenerate_id();
			}else{
				self::destroy_session();
			}

		}else{
			self::destroy_session();
		}
	}

	/**
	* Destruye la session
	* @access public
	* @return void
	*/
	public static function destroy_session(){
		$_SESSION = array();

		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);
		}

		session_destroy();

	}

}