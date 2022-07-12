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
        $this->mensaje = array();
        //Notas
        if(strlen($this->BDetalles->data->CdeDescripcion) < 4)
            $this->mensaje["conceptos"] = "Llenar todos los datos";
        if(count($this->mensaje) > 0)
        {
            $this->mensaje["status"] = CBN_DATOS_INVALIDOS;
            return $this->mensaje;
        }
        else
        {
            $this->mensaje["status"] = CBN_DATOS_VALIDOS;
            return true;
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Clases\Catalogos\BasedatosInterface::obtener()
     * @return BDetallesD
     */
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
        if($this->BDetalles->isSupervisor($_SESSION["USR_ROL"]))
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
                "link"=>$arguments["link2"],      "linkoptions"=>"class='box'" ),
            array("title"=>"Monto",                 "name"=>"CdeMonto",             "width"=>"80",              "align"=>"right",
                "formatter"=>"currency","formatoptions"=>array("prefix" => "$",	"suffix" => '',	"thousandsSeparator" => ",",
                    "decimalSeparator" => ".",
                    "decimalPlaces" => 2)),
            array("title"=>"Descripcion",           "name"=>"CdeDescripcion",       "width"=>"180",             "editable"=>$arguments["editar"]),
            //array("title"=>"Usuario","name"=>"UsuNombre","width"=>"80"),
            array("title"=>"Usuario",               "name"=>"CdeUsuario",           "width"=>"80",              "editable"=>$arguments["editar"],           "align"=>"center",      "type"=>"select",
                "edittype"=>"select",            "formatter"=>"select",
                "editoptions"=>array("value"=>$arguments["usuarios"]),              "editable"=>$arguments["editar"],                                         "op"=>"eq", 
                "searchoptions"=>array("value"=>$arguments["usuarios"]),            "stype"=> "select"),
            array("title"=>"Tipo",                  "name"=>"CdeTipo",              "width"=>"10",              "hidden"=>true,               "export"=>false),
            array("title"=>"Imprimir",              "name"=>"print",                "width"=>"40",              "editable"=>false,              "align"=>"left",                    "search"=>false,
                "default"=>$arguments["imprimir"])
        );
    }

    public function forma($arguments = null)
    {
        return 
        "        
        <form id='modificarForma'>
            <div class='row'>
        		<div class='col-md-3'>
        			<div class='form-floating mb-3'>                               
        				 <input id='CdeID' type='hidden' name='CdeID' />
        				 <input id='CSRF' type='hidden'
        					value='".$_SESSION["CSRF"]."' />          
        				<input class='form-control datepicker' autocomplete='off' type='text' id='CdeFecha' 
                            name='CdeFecha' ".$arguments["readonly"]."/>
        				<label for='CdeFecha'> Fecha</label>
        			</div>
        		</div>
                <div class='col-md-3'>
                    <div class='form-floating mb-3'>
        				<select class='form-select border-danger valida' data-type='length'
        					data-length='1' name='CdeCuenta' id='CdeCuenta'
        					required='required'>
        					<option value=''>Selecciona Cuenta</option>
        					".$arguments["cuentas"]."
        				</select> <label for='CdeCuenta'>Cuenta</label>        					    
        			</div>
                </div>
        		<div class='col-md-3'>
        			<div class='form-floating mb-3'>                        
        				<input class='form-control' type='number' step='0.01' id='monto' readonly /> 
                        <label for='monto'> Cantidad</label>
        			</div>
        		</div>
        		<div class='col-md-3'>
        			<div class='form-floating mb-3'>                
        				<input class='form-control' type='number' step='0.01' ".$arguments["readonly"]." id='CdeMonto' name='CdeMonto' value='0.00'/>
        				<label for='CdeMonto'> Modificaci&oacute;n</label>
        			</div>
        		</div>
                <div class='col-md-3'>
                    <div class='form-floating mb-3'>
        				<select class='form-select border-danger valida' data-type='length'
        					data-length='1' name='Nueva' id='Nueva'
        					required='required'>
        					<option value=''>Selecciona Cuenta</option>
        					".$arguments["cuentas"]."
        				</select> <label for='Nueva'>Cuenta Modificaci&oacute;n</label>        					    
        			</div>
                </div>
                <div class='col-md-3'>
        			<div class='form-floating mb-3'>                        
        				<input class='form-control' type='number' step='0.01' id='CdeSaldo' name='CdeSaldo' readonly /> 
                        <label for='CdeSaldo'> Saldo</label>
        			</div>
        		</div>
        		<div class='col-md-3'>
        			<div class='form-floating mb-3'>                
        				<input class='form-control' type='number' step='0.01' id='diferencia' name='diferencia' readonly value='0.00' />
        				<label for='Diferencia'> Diferencia</label>
        			</div>
        		</div>
        		<div class='col-lg-6 col-xl-6'>
        			<!-- Submit Button-->
        			<div class='button-group text-center'>
        				<button class='btn btn-primary' id='submitButtonModificar'
        					type='submit'>Enviar</button>
        				<button class='btn btn-danger' id='resetButtonModificar' type='reset'>Limpiar</button>
        			</div>
        		</div>
                
            </div>
        </form>   ";
    }

    public function modal($arguments = null)
    {}

}

