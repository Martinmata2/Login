<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\RolF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\RolD;
use Clases\Catalogos\BasedatosInterface;

//Definiciones para estandarizar valores
define("ROL_ELIMINADO", 1);
define("ROL_ACTIVO", 1);
define("ROL_INACTIVO", 0);
define("ROL_SUCCESS", 200);
define("ROL_ERROR", 400);
define("ROL_DATOS_VALIDOS",200);
define("ROL_DATOS_INVALIDOS",400);
class Rol extends Query implements BasedatosInterface
{
   
    /**
     *
     * @var RolF
     */
    protected $Rol;
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
    public function __construct( $base_datos = BD_GENERAL)
    {
        $this->Rol = new RolF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Rol->table();
        
        parent::__construct($base_datos);
        
        //Inicar tabla
        $this->Rol->create();
        $this->Rol->update();
    }

    public function borrar($id, $campo="RolID",  $usuario = 0)
    {
        if($this->Rol->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>ROL_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    public function obtener($id = 0, $campo = "RolID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".ROL_ELIMINADO);
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

    public function editar($datos, $id,$campo="RolID", $condicion = "0", $usuario = 0)
    {
        if($this->Rol->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Rol->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->Rol->data = new RolD($datos);
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
        $respuesta = $this->consulta("*", $this->Tabla,"RolID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
}

