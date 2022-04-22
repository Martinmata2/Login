<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\UsuarioF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\UsuarioD;
use Clases\Catalogos\BasedatosInterface;

//Definiciones para estandarizar valores
define("USU_ELIMINADO", 1);
define("USU_ACTIVO", 1);
define("USU_INACTIVO", 0);
define("USU_SUCCESS", 200);
define("USU_ERROR", 400);
define("USU_DATOS_VALIDOS",200);
define("USU_DATOS_INVALIDOS",400);
class Usuario extends Query implements BasedatosInterface
{   
    /**
     *
     * @var UsuarioF
     */
    protected $Usuario;
    /**
     *
     * @var string
     */
    protected $Tabla;
    /**
     *
     * @var string
     */
    public $mensaje;
    public function __construct($base_datos = BD_GENERAL)
    {
        parent::__construct($base_datos);
        
        $this->Usuario = new UsuarioF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Usuario->table();
                
        
        //Inicar tabla
        $this->Usuario->create();
        $this->Usuario->update();
    }

    public function borrar($id, $campo = "UsuID",  $usuario = 0)
    {
        if($this->Usuario->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>USU_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    public function obtener($id = 0, $campo = "UsuID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".USU_ELIMINADO);
            if (\count($resultado) > 0)
                return $resultado;
                else return 0;
        }
        else
        {
            $resultado = $this->consulta("*", $this->Tabla, "$campo = '$id'", $condicion);
            if (\count($resultado) > 0)
                return $resultado[0];
                else
                    return 0;
        }
        return 0;
    }

    public function editar($datos, $id,$campo = "UsuID",$condicion = "0", $usuario = "1")
    {
        if($this->Usuario->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Usuario->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->Usuario->data = new UsuarioD($datos);
            if($this->validar())
            {               
                return $this->insertar($this->Tabla, $datos);
            }
            else return 0;
        }
        else return 0;
    }      
   
    public function Unico($campo, $valor)
    {
        $respuesta = $this->consulta("*", $this->Tabla,"$campo = '$valor'");
        if(count($respuesta) > 0)
            return false;
        else return true;
    }
    
    
    public function existe($id)
    {
        $respuesta = $this->consulta("*", $this->Tabla,"UsuID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
}

