<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\ModulosF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\ModulosD;
use Clases\BasedatosInterface;

//Definiciones para estandarizar valores
define("MOD_ELIMINADO", 1);
define("MOD_ACTIVO", 1);
define("MOD_INACTIVO", 0);
define("MOD_SUCCESS", 200);
define("MOD_ERROR", 400);
define("MOD_DATOS_VALIDOS",200);
define("MOD_DATOS_INVALIDOS",400);
class Modulos extends Query implements BasedatosInterface
{
   
    /**
     *
     * @var ModulosF
     */
    protected $Modulos;
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
        $this->Modulos = new ModulosF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Modulos->table();
        
        parent::__construct($base_datos);
        
        //Inicar tabla
        $this->Modulos->create();
        $this->Modulos->update();
    }

    public function borrar($id,$campo="ModID",  $usuario=0)
    {
        if($this->Modulos->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>MOD_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    public function obtener($id = 0, $campo = "ModID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, $condicion);
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

    public function editar($datos, $id,$campo="ModID", $condicion="0", $usuario=0)
    {
        if($this->Modulos->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Modulos->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->Modulos->data = new ModulosD($datos);
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
        $respuesta = $this->consulta("*", $this->Tabla,"ModID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
}

