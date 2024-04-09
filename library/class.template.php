<?php
/*
.-------------------------------------------------------------------.
| CLASS TEMPLATE v3.2 (2014-09)                                     |
| ------------------------------------------------------------------|
| Objeto de manipulación de templates (plantillas) para el output   |
| de código PHP.                                                    |
|-------------------------------------------------------------------|
| (c) Leandro E. Renedo - Para Titansol                             |
.-------------------------------------------------------------------.
*/
class template
{

    ////////////////////////////////////
    /// PROPIEDADES                  //
    ///////////////////////////////////

    private $template_file; // Contiene el nombre del template
    private $template_content;  // Guarda el contenido del template
    private $template_vars = array(); // Guarda las variables del template
    private $nombre_bucle; // Nombre del bucle capturado
    private $bucle_capturado; // Contenido inicial del bucle capturado
    private $bucle_procesado; // Contenido final del bucle capturado (con sus variables reemplazadas)
    private $bucle_interno; // Nombre del bucle interno
    private $bucle_interno_capturado; // Contenido inicial del bucle interno
    private $bucle_interno_procesado; // Contenido final del bucle interno (con sus variables procesdas)



    /**
     * Creación del objeto. Realizar carga del contenido del template
     * @access public
     * @param string $template El nombre del archivo de plantilla (el nombre puede contener una ruta ej: 'midirectorio/miplantilla.html')
     * @return void
    */
    public function __construct($template)
    {
        $this->cargar_template($template);
    }

    /**
     * Permite un carga de un nuevo archivo de plantilla si el objeto ya existe. Revisa si hay un antigua plantilla
     * cargada y la elimina de ser positivo.
     * @access public
     * @param string $template El nombre del archivo de plantilla (el nombre puede contener una ruta ej: 'midirectorio/miplantilla.html')
     * @return void
    */
    public function cargar_template($template)
    {

        $this->limpiar_plantilla();
        $this->template_file = $template;
        $this->revisar_template();
        //ob_start(); // Abro el buffer de salida
    }


     /**
     * Chequea que la plantilla a cargar exista en la ruta especificada. De caso contrario devuelve un error critico
     * y detiene la ejecución del script.
     * @access private
     * @return void
    */
    private function revisar_template()
    {

        // Chequea si $template viene con extensión, sino agrega el .html por defecto (legacy)
        // $nombre_template = explode('.', $this->template_file);
        $nombre_template = substr($this->template_file,strlen($this->template_file)-5, strlen($this->template_file));

        if($nombre_template != '.html'){
            $this->template_file = $this->template_file .'.html';
        }

        if(is_file($this->template_file))
        {
            $this->template_content = file_get_contents($this->template_file);
        }else exit('El archivo de plantilla ::'.$this->template_file.':: especificado no existe');
    }

    /**
     * Limpia tanto el archivo de plantilla guardado como el contenido obtenido y las variables asignadas
     * @access private
     * @return void
    */
    private function limpiar_plantilla()
    {
         if(!empty($this->template_file)){
            unset ($this->template_file);
            unset ($this->template_content);
            unset ($this->template_vars);
         }

    }

     /**
     * Asigna a la variable interna 'variables' las variables ingresadas por el usuario que serán usadas para cambiar
     * los tags de la plantilla por contenido verdadero.
     * @access public
     * @param array $vars or string Las variables con el contenido para reemplezar tags (también puede ser un string)
     * @return void
    */
    public function asignar_variables($variables)
    {
        $this->template_vars = $variables;
    }

    /**
     * Captura el contenido dentro de los tags especiales de bucle. El contenido dentro de estos tags( {::BUCLE::} )
     * se podrán usar indefinidamente en estructuras de bucle a fin de evitar la utilización de una nueva plantilla
     * para estos fines
     * @access public
     * @param string $nombre_bucle El nombre del tag especial de bucle
     * @param boolenan $es_bucle_interno Especifica si hay que buscar un bucle dentro de un bucle
     * @return void
    */
    public function capturar_bucle($bucle,$es_bucle_interno=false)
    {

        unset($coincidencias);

        if($es_bucle_interno)
        {

            $this->bucle_interno_capturado = '';
            $this->bucle_interno_procesado = '';

            $this->bucle_interno = $bucle;
            $patron = '/{::'.$this->bucle_interno.'::}([\d\D]*?)\{::'.$this->bucle_interno.'::}/';
            preg_match($patron,$this->bucle_capturado,$coincidencias);

            $this->bucle_interno_capturado = $coincidencias[1];
        }
        else
        {

            $this->bucle_capturado = '';
            $this->bucle_procesado = '';

            $this->nombre_bucle = $bucle;

            $patron = '/{::'.$this->nombre_bucle.'::}([\d\D]*?)\{::'.$this->nombre_bucle.'::}/';
            preg_match($patron,$this->template_content,$coincidencias);

            $this->bucle_capturado = $coincidencias[1];

        }


    }

    /**
     * Reemplaza el contenido capturado del bucle con las variables/contenido dado. El contenido capturado original
     * no se pierde tras esta operación, por lo que puede seguir siendo utilizado nuevamente.
     * @access public
     * @param array $variables Las variables con el contenido para reemplezar tags (también puede ser un string)
     * @param boolean $es_bucle_interno especifica si se reemplaza contenido de un bucle interno
     * @return void
    */
    public function reemplazar_contenido_bucle($variables,$es_bucle_interno=false)
    {




        if(is_array($variables))
        {

             unset($almacenamiento_temporal);

            $almacenamiento_temporal = ($es_bucle_interno) ? $this->bucle_interno_capturado : $this->bucle_capturado;

            foreach($variables as $clave=>$valor){
                $almacenamiento_temporal = preg_replace('/({:'.$clave.':})/',$valor,$almacenamiento_temporal);
            }

        }else{

            $patron = '/({:[a-zA-Z0-9]+.?[a-zA-Z0-9]*:})/';
            if($es_bucle_interno)
            {
                $almacenamiento_temporal = preg_replace($patron, $variables, $this->bucle_interno_capturado);
            }else{
                $almacenamiento_temporal = preg_replace($patron, $variables, $this->bucle_capturado);
            }

        }

        if($es_bucle_interno){
            $this->bucle_interno_procesado .= $almacenamiento_temporal;
        }else{
            $this->bucle_procesado .= $almacenamiento_temporal;
        }

    }

     /**
     * Reemplaza todo el bloque compredido dentro del tag de bucle por una estructura ya modificada.
     * @access public
     * @param boolean $es_bucle_interno  Especifica si hay que reemplazar un bucle interno
     * @return void
    */
    public function reemplazar_bucle($es_bucle_interno=false)
    {

        $this->limpiar_contenido(true,true);

        if($es_bucle_interno)
        {

            $patron = '/({::'.$this->bucle_interno.'::}[\d\D]*?\{::'.$this->bucle_interno.'::})/';
            //$this->bucle_capturado = preg_replace($patron,$this->bucle_interno_procesado,$this->bucle_capturado);
            $this->bucle_procesado = preg_replace($patron,$this->bucle_interno_procesado,$this->bucle_procesado);
        }
        else
        {
            $patron = '/({::'.$this->nombre_bucle.'::}[\d\D]*?\{::'.$this->nombre_bucle.'::})/';
            $this->template_content = preg_replace($patron,$this->bucle_procesado,$this->template_content);

        }

    }

    /**
     * Limpia posibles tags que hayan quedado huérfanos dentro de la plantilla o, de ser especificado, dentro del
     * contenido de un bucle
     * @access private
     * @param bool $bucle De ser true limpia el contenido del bucle en lugar de toda la plantilla
     * @param bool $es_bucle_interno Indica si el contenido de a limpiar pertenece a un bucle interno
     * @return void
    */
    private function limpiar_contenido($es_bucle=false, $es_bucle_interno=false)
    {
        $patron = '/({:[a-zA-Z0-9]+.?[a-zA-Z0-9]*:})/';

        if($es_bucle){
            $this->bucle_procesado = preg_replace($patron, '', $this->bucle_procesado);
        }elseif($es_bucle_interno){
            $this->bucle_interno_procesado = preg_replace($patron, '', $this->bucle_interno_procesado);
        }else{
            $this->template_content = preg_replace($patron, '', $this->template_content);
        }
    }


    /**
     * Reemplaza las etiquetas de la plantilla por un contenido previamente asignado y devuelve la plantilla
     * con sus tags ya reemplazados.
     * @access public
     * @return string
    */
    public function procesar_plantilla()
    {

        if(is_array($this->template_vars)){
            foreach($this->template_vars as $clave=>$valor){
                $this->template_content = preg_replace('/({:'.$clave.':})/',$valor,$this->template_content);
            }
        }else{
            $patron = '/({:[a-zA-Z0-9]+.?[a-zA-Z0-9]*:})/';
            $this->template_content = preg_replace($patron, $this->template_vars, $this->template_content);
        }

        $this->limpiar_contenido();

        return $this->template_content;
    }

    ###################################
    ## Legacy methods
    ###################################
    public function reemplazarContenidoBucle($variables)
    {
        $this->reemplazar_contenido_bucle($variables,false);
    }

    public function reemplazarBucle(){
        $this->reemplazar_bucle(false);
    }

    public function mostrar()
    {
        $plantilla = $this->procesar_plantilla();
        return $plantilla;
    }

}