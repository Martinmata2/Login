<?php
namespace clases\Login;

/**
 *
 * @version v2020_2
 * @author Martin Mata
 */

/**
 * Muestra una forma login para uso individual
 *
 * @author Martin
 *        
 */
class Personal extends Acceso
{

    /**
     * A donde sera redireccionado cuando sea autorizado
     *
     * @var string
     */
    public $redireccion = "";

    public $base_datos;

    /**
     * Muestra la forma login al usuario
     *
     * @param string $action
     *            <code>ajax</code>
     * @param string $css
     *            <code>clase a usar en login form</code>
     * @return string <code>html code para login form</code>
     */
    function inicio($ubicacion, $action = "ajax", $css = "", $base_datos = BD_GENERAL)
    {
        $_SESSION["PROGRAMA_MODE"] = "privada";
        if (! isset($_SESSION["intentos"]))
            $_SESSION["intentos"] = 0;
        $this->base_datos = $base_datos;
        if (! $this->estaLogueado()) {
            return $this->mostrar($ubicacion, $action, $css);
        } elseif (strlen($this->redireccion) > 2) {
            header("Location:$this->redireccion");
            exit(0);
        } else
            return $this->mostrar($ubicacion, $action, $css, true);
    }

    /**
     * Log por medio de ajax
     *
     * @param string $usuario
     * @param string $clave
     * @return number
     */
    public function ajaxCall($usuario, $clave)
    {
        $result = $this->log($usuario, $clave);
        if ($result !== 0)
            return $result->UsuID;
        else
            return 0;
    }

    /**
     * Mostrar login form
     *
     * @param string $ubicacion
     *            lugar donde folder de login esta
     * @param string $action
     * @param string $css
     * @param string $loged
     * @return string
     */
    private function mostrar($ubicacion, $action = "ajax", $css = "", $loged = false)
    {
        $class = "";
        $html = "";
        if ($css == "") {
            $html .= $this->css();
        } else
            $class = "class='$css'";
        $html .= $this->forma($class);

        if ($action == "ajax") {
            $ajax = "
           
            <script src='$ubicacion/js/md5.js' type='text/javascript'></script>
            <script src='$ubicacion/js/sha1.js' type='text/javascript'></script>
           
            <script type='text/javascript'>
            $(document).ready(function()
            {
            ";
            if ($loged) {
                $ajax .= " $('.pantalla').hide();
		        		 $('.log-in-form').hide();";
            }
            $ajax .= "
              		$('#log-in-form').submit(function(event)
            		{
    				   event.preventDefault();
    					$('#entrar').addClass('loading');
    					$('input[type=submit]').attr('disabled', 'disabled');
    					var form_data = {
    						usuario:encripta($('#usuarioLogin').val()),
    						clave: encripta($('#claveLogin').val())
    					};
                        //REQUIERE js/funsiones.js
					   ajax_call('" . $ubicacion . "/clases/LOGIN/ajax/login.php',{form_data,base_datos:'$this->base_datos'},
                       function(result)
                       {
                            if(result > 0)
                       		 {
                   			 	$('input[type=submit]').attr('disabled', false);
                    			$('.log-in-form').hide();
                    			$('.pantalla').hide();";

            if (strlen($this->redireccion) > 2) {
                $ajax .= "window.location.replace('" . $this->redireccion . "');";
            }
            $ajax .= "
             		         }
                		     else
                		     {
            		    		 $('input[type=submit]').attr('disabled', false);
            		             $('#entrar').notify('Usuario o clave equivocado','warning');";
            if ($_SESSION["intentos"] > 5)
                $ajax .= "$('#entrar').notify('Usuario o clave equivocado,<br>El Numero de intentos supero los permitidos<br>consulte el administrador','warning');";
            $ajax .= "
        			         }
                       },
                       function()
                       {
                           $('input[type=submit]').attr('disabled', false);
                           alert(result);
                       });
                  });    
            });

             function encripta(name)
             {
                  	  //REQUIERE js/md5.js
            
                     return hex_md5(hex_sha1(name));
             }
        </script>";
            $html .= $ajax;
        }
        return $html;
    }

    private function timer()
    {
        $html = "
    	var IDLE_TIMEOUT = 960; //seconds = 11 min
    	var _idleSecondsCounter = 0;
    	document.onclick = function() {
    		_idleSecondsCounter = 0;
    	};
    	document.onmousemove = function() {
    		_idleSecondsCounter = 0;
    	};
    	document.onkeypress = function() {
    		_idleSecondsCounter = 0;
    	};
    	window.setInterval(CheckIdleTime, 5000);
  
    	function CheckIdleTime() {
    		_idleSecondsCounter += 5;

    		if (_idleSecondsCounter >= IDLE_TIMEOUT)
    		{
    			$('.pantalla').show();
    			$('.log-in-form').show();
    		}
    	}";
        return $html;
    }

    private function forma($class = "")
    {
        return "
        <div class='pantalla'>
        <form id='log-in-form' class='log-in-form $class' autocomplete='off'>
          <h4 class='text-center'>Entre al sistema</h4>
          <label>Usuario
            <input type='text' placeholder='Usuario' id='usuarioLogin'>
          </label>
          <label>Contrase&ntilde;a
            <input type='password' placeholder='Contrase&ntilde;a' id='claveLogin'>
          </label>          
          <p><input type='submit' id='entrar' class='button expanded' value='Entrar'></input></p>
          <p class='text-center'><a href='#'>Olvidaste tu clave</a></p>
        </form>
        </div> ";
        /*
         * <div class='pantalla'>
         * <div class='login-card'>
         * <p id='mensaje'>
         * <form id='loginForm' method='post' autocomplete='off'>
         * <h4 class='text-center'>Acceso al Sistema</h4>
         * <label>Usuario
         * <input type='text' name='usuarioLogin' id='usuarioLogin' value='' placeholder='Usuario'/>
         * </label>
         * <label>Clave
         * <input type='password' name='claveLogin' id='claveLogin' value='' placeholder='Clave'/>
         * </label>
         * <p><input type='submit' id='login' class='login login-submit button expanded' value='login' />
         * </form>
         * </div></div>";
         */
    }

    function css()
    {
        $css = "<style>
        .pantalla
        {
            position:fixed;
            margin:auto;		
            width: 100%;
            height: 100%;
            background:rgba(5,5,5,1.00);
            z-index:100;
         }
        .log-in-form 
        {              
              border: 1px solid #cacaca;
              padding: 1rem;
              border-radius: 0;
              width:300px;
              margin: auto;
             background-color: #F7F7F7;
        }
       

       
            </style>";
        return $css;
    }
}