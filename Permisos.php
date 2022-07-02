<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\PermisosF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\PermisosD;
use Clases\Catalogos\BasedatosInterface;
use Clases\GridInterface;

//Definiciones para estandarizar valores
define("PUS_ELIMINADO", 1);
define("PUS_ACTIVO", 1);
define("PUS_INACTIVO", 0);
define("PUS_SUCCESS", 200);
define("PUS_ERROR", 400);
define("PUS_DATOS_VALIDOS",200);
define("PUS_DATOS_INVALIDOS",400);
class Permisos extends Query implements BasedatosInterface, GridInterface
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
                if($this->Unico("PusUsuario", $datos->PusUsuario))
                    return $this->insertar($this->Tabla, $datos);
                else 
                    return $this->editar($datos, $datos->PusUsuario, "PusUsuario");
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
    public function grid($arguments = null)
    {}

    public function forma($arguments = null)
    {
        $html = "<div class='row py-2'>";
        $MODULOS = new Modulos();       
        $ARCHIVOS = new Archivos();        
        $secciones = $MODULOS->obtener(0,0,"ModRol >= ".$_SESSION["USR_ROL"]);        
        foreach ($secciones as $seccion)
        {
            $html .= "
                        <div class='col-lg-4 py-2'>
                            <p class='lead fw-normal text-muted mb-0'>".$seccion->ModNombre."</p>
                            <ul class='tree1'>
                                <li><input type='checkbox' value='0' /><label> -Todos-</label>
                                    <ul>";
            $archivos = $ARCHIVOS->obtener(0,0,"ArcModulo = $seccion->ModID");            
            if(!empty($archivos))
                foreach ($archivos as $archivo)
                {
                    $html .="<li><input type='checkbox' id='id_".$archivo->ArcID."' value='".$archivo->ArcID."' /> - ".$archivo->ArcNombre."</li>";
                }
            $html .= "         </ul>
                            </li>
                        </ul>
                    </div>";
        }
        $html .="<div id='permisos_seleccionados'>Permisos selecionados</div>";
        $html .="</div>";
        return $html;
    }

    public function modal($arguments = null)
    {}

    
}

