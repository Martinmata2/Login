<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\ArchivosF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\ArchivosD;
use Clases\Catalogos\BasedatosInterface;
use Clases\Login\Datos\PermisosD;

//Definiciones para estandarizar valores
define("ARC_ELIMINADO", 1);
define("ARC_ACTIVO", 1);
define("ARC_INACTIVO", 0);
define("ARC_SUCCESS", 200);
define("ARC_ERROR", 400);
define("ARC_DATOS_VALIDOS",200);
define("ARC_DATOS_INVALIDOS",400);
class Archivos extends Query implements BasedatosInterface
{
   
    /**
     *
     * @var ArchivosF
     */
    protected $Archivos;
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
        $this->Archivos = new ArchivosF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Archivos->table();
        
        parent::__construct($base_datos);
        
        //Inicar tabla
        $this->Archivos->create();
        $this->Archivos->update();
    }

    public function borrar($id,$campo="ArcID",  $usuario=0)
    {
        if($this->Archivos->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>ARC_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Clases\Catalogos\BasedatosInterface::obtener()
     * @return ArchivosD
     */
    public function obtener($id = 0, $campo = "ArcID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, $condicion, "ArcOrden asc");
            if (\count($resultado) > 0)
                return $resultado;
                else return 0;
        }
        else
        {
            $resultado = $this->consulta("*", $this->Tabla, "$campo = '$id'", $condicion, "ArcOrden asc");
            if (\count($resultado) > 0)
                return $resultado[0];
                else
                    return 0;
        }
        return 0;
    }

    public function editar($datos, $id,$campo="ArcID", $condicion="0", $usuario=0)
    {
        if($this->Archivos->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Archivos->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->Archivos->data = new ArchivosD($datos);
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
        $respuesta = $this->consulta("*", $this->Tabla,"ArcID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
}

