<?php
namespace Clases\Login;

use Clases\Login\Datos\UsuarioD;
use Clases\Utilidades\Encode;
use Clases\Utilidades\Validar;
use Clases\Login\Usuario;

define("LOGIN_SUCCESS",200);
define("LOGIN_ERROR", 400);
define("LOGIN_INACTIVO",0);
define("LOGIN_ACTIVO", 1);

/**
 * Control de accesos al sistema
 * @author Marti
 *
 */
class Acceso extends Usuario
{

    private $cookieoptions = array();
   
    public function __construct($base_datos = BD_GENERAL)
    {      
        parent::__construct ($base_datos);
        $this->cookieoptions = array("expiry"=>Cookie::AWeek,"path"=>"/","domain"=>false,"secure"=>false, "httponly"=>false,"globalremove"=>true);
    }
    
    /**
     * 
     * @param string $usuario
     * @param string $clave
     * @return UsuarioD|number
     */
   public function login($usuario, $clave)
   {
       $usuario = $this->conexion->real_escape_string($usuario);
       $clave = $this->conexion->real_escape_string($clave);              
       $login = $this->consulta("*", $this->Tabla, "UsuUsuario = '$usuario' AND UsuClave = '$clave' AND  UsuActivo = ".USU_ACTIVO);
       if ($login !== 0 && count($login) > 0) 
       {          
           $this->Usuario->data = new UsuarioD($login[0]);               
           $this->resetValores();
           $this->sessionIniciar();
           $this->recuerdame();                                
           return $login[0];//this->Usuario->data;
           
       } 
       else
           return 0;
   }
   
   
   /**
    * Asigna valores en session
    *
    * 
    * @return boolean
    */
   private function resetValores()
   {             
       @session_start();      
       $_SESSION["USR_ROL"] = $this->Usuario->data->UsuRol;
       $_SESSION["USR_NOMBRE"] = $this->Usuario->data->UsuNombre;
       $_SESSION["USR_ID"] = $this->Usuario->data->UsuID;     
       $_SESSION["USR_BD"] = BD_GENERAL;       
       $_SESSION["USR_USUARIO_PAC"] = $this->Usuario->data->UsuUsuarioPAC;
       $_SESSION["USR_CLAVE_PAC"] = $this->Usuario->data->UsuClavePAC;
       if(!isset($_SESSION["CSRF"]))
           $_SESSION["CSRF"] = session_id();
       /**
        * *** Aqui se pueden agregar otros valores necesarios en session ******
        */
       return true;
   }
   
   /**
    * Activar session deb base de datos
    * @return boolean
    */
   private function sessionIniciar()
   {              
       return ($this->modificar($this->Tabla, array("session_id"=>session_id()), $this->Usuario->data->UsuID, "UsuID") !== 0);
   }
   
   private function recuerdame()
   {
       $token = $this->crearllave(24);              
       $this->modificar($this->Tabla, array("token"=>$token), $this->Usuario->data->UsuID, "UsuID");     
      
           Cookie::Remove("auth",$this->cookieoptions);
           Cookie::Remove("usrtoken",$this->cookieoptions);
           Cookie::Set("auth",session_id(), $this->cookieoptions);
           Cookie::Set("usrtoken",$token,$this->cookieoptions);
       
   }
   
   /**
    * Destruye la session
    *
    * @param int $id
    *            
    */
   public function sesionDestruir($id)
   {       
       @session_destroy();
       
       $this->modificar($this->Tabla, array(
           "session_id" => 0, "token"=>0
       ), $id, "UsuID");
       Cookie::Remove("auth",$this->cookieoptions);
       Cookie::Remove("usrtoken",$this->cookieoptions);
       unset($_SESSION["CSRF"]);
   }
      
   
   /**
   * Verifica si El Cliente tiene iniciada Session
   *
   * @return bool
   */
   public function estaLogueado()
   {        
       @session_start();
        if (isset($_SESSION["USR_ID"]))
        {
           return true;  //estan logieados de otra computadora.           
        }
        elseif (Cookie::Exists('auth') && Cookie::Get('auth') !== false)
        {
            $credenciales = $this->consulta("*", $this->Tabla, "token = '" . Cookie::Get("usrtoken") . "'");
            if($credenciales !== 0 && count($credenciales) > 0)
            {
                $this->Usuario->data = new UsuarioD($credenciales[0]);
                $this->resetValores();
                $this->sessionIniciar();
                //$this->recuerdame();
                return true;
            }
            else return false;
        }
        else
            return false;
        return false;
   }
   
   
  /**
   * Crea una cadena aleatoria con un numero de caracteres
   *
   * @param int $length
   * @return string clave de $lenth caracteres
   */
   public function crearllave($length = 16)
   {
       if ($length <= 0) {
           return false;
       }
       $code = "";
       $chars = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
       srand((double) microtime() * 1000000);
       for ($i = 0; $i < $length; $i ++) {
           $code = $code . substr($chars, rand() % strlen($chars), 1);
       }
       return $code;
   }
     
}

