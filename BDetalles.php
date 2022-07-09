<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\BDetallesF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\BDetallesD;
use Clases\Catalogos\BasedatosInterface;
use Clases\GridInterface;

//Definiciones para estandarizar valores
define("CBN_ELIMINADO", 1);
define("CBN_ACTIVO", 1);
define("CBN_INACTIVO", 0);
define("CBN_SUCCESS", 200);
define("CBN_ERROR", 400);
define("CBN_DATOS_VALIDOS",200);
define("CBN_DATOS_INVALIDOS",400);
class BDetalles extends Query implements BasedatosInterface, GridInterface
{
   
    /**
     *
     * @var BDetallesF
     */
    protected $BDetalles;
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
        $this->BDetalles = new BDetallesF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->BDetalles->table();
        
        parent::__construct($base_datos);
        
        //Inicar tabla
        $this->BDetalles->create();
        $this->BDetalles->update();
    }

    public function borrar($id,$campo="CdeID",  $usuario=0)
    {
        if($this->BDetalles->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>CBN_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    public function obtener($id = 0, $campo = "CdeID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".CBN_ELIMINADO);
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

    public function editar($datos, $id,$campo="CdeID", $condicion="0", $usuario=0)
    {
        if($this->BDetalles->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->BDetalles->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->BDetalles->data = new BDetallesD($datos);
            if($this->validar() === true)
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
        $respuesta = $this->consulta("*", $this->Tabla,"CdeID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
    public function grid($arguments = null)
    {
        return array(
            array("title"=>"ID",                    "name"=>"CdeID",                "width"=>"10",                                          "hidden"=>true,             "export"=>false),
            array("title"=>"Cuentas",               "name"=>"CdeCuenta",            "width"=>"10",                                          "hidden"=>true,             "export"=>false),
            array("title"=>"Fecha / Cuenta",        "name"=>"CdeFecha",             "width"=>"80",
                "link"=>$arguments["link"],         "linkoptions"=>"class='box'" ),
            array("title"=>"Saldo / Modificar",     "name"=>"CdeSaldo",             "width"=>"80",              "align"=>"right",
                "formatter"=>"currency","formatoptions"=>array("prefix" => "$",	"suffix" => '',	"thousandsSeparator" => ",",
                    "decimalSeparator" => ".",
                    "decimalPlaces" => 2),"on_data_display"=>array("saldo_privado2"),                           "hidden"=>$arguments["mostrar"],
                "link"=>"modificar.php?cuenta={CdeCuenta}&movimiento={CdeID}",      "linkoptions"=>"class='box'" ),
            array("title"=>"Monto",                 "name"=>"CdeMonto",             "width"=>"80",              "align"=>"right",
                "formatter"=>"currency","formatoptions"=>array("prefix" => "$",	"suffix" => '',	"thousandsSeparator" => ",",
                    "decimalSeparator" => ".",
                    "decimalPlaces" => 2)),
            array("title"=>"Descripcion",           "name"=>"CdeDescripcion",       "width"=>"180",             "editable"=>$arguments["editar"]),
            //array("title"=>"Usuario","name"=>"UsuNombre","width"=>"80"),
            array("title"=>"Usuario",               "name"=>"CdeUsuario",           "width"=>"80",              "editable"=>$arguments["editar"],           "align"=>"center",      "type"=>"select",
                "edittype"=>"select",            "formatter"=>"select",
                "editoptions"=>array("value"=>$arguments["usuarios"]),              "editable"=>$arguments["edit"],                                         "op"=>"eq", 
                "searchoptions"=>array("value"=>$arguments["usuarios"]),            "stype"=> "select"),
            array("title"=>"Tipo",                  "name"=>"CdeTipo",              "width"=>"10",              "hidden"=>true,               "export"=>false),
            array("title"=>"Imprimir",              "name"=>"print",                "width"=>"40",              "editable"=>false,              "align"=>"left",                    "search"=>false,
                "default"=>$arguments["imprimir"])
        );
    }

    public function forma($arguments = null)
    {}

    public function modal($arguments = null)
    {}

}

