<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\PermisosF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\PermisosD;
use Clases\Catalogos\BasedatosInterface;

//Definiciones para estandarizar valores
define("PUS_ELIMINADO", 1);
define("PUS_ACTIVO", 1);
define("PUS_INACTIVO", 0);
define("PUS_SUCCESS", 200);
define("PUS_ERROR", 400);
define("PUS_DATOS_VALIDOS",200);
define("PUS_DATOS_INVALIDOS",400);
class Permisos extends Query implements BasedatosInterface
{
   
    /**
     *
     * @var PermisosF
     */
    protected $Permisos;
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
        $this->Permisos = new PermisosF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Permisos->table();
        
        parent::__construct($base_datos);
        
        //Inicar tabla
        $this->Permisos->create();
        $this->Permisos->update();
    }

    public function borrar($id,$campo="PusID",  $usuario=0)
    {
        if($this->Permisos->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>PUS_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    public function obtener($id = 0, $campo = "PusID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".PUS_ELIMINADO);
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

    public function editar($datos, $id,$campo="PusID", $condicion="0", $usuario=0)
    {
        if($this->Permisos->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Permisos->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->Permisos->data = new PermisosD($datos);
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
        $respuesta = $this->consulta("*", $this->Tabla,"PusID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
    
}

