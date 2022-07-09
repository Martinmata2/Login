<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\BancoF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\BancoD;
use Clases\Catalogos\BasedatosInterface;
use Clases\GridInterface;
use Clases\Catalogos\Movimiento;
use Clases\Catalogos\ListaInterface;

//Definiciones para estandarizar valores
define("BAN_ENTRADA_EFECTIVO", 7);
define("BAN_SALIDA_EFECTIVO", 5);
define("BAN_ELIMINADO", 1);
define("BAN_ACTIVO", 1);
define("BAN_INACTIVO", 0);
define("BAN_SUCCESS", 200);
define("BAN_ERROR", 400);
define("BAN_DATOS_VALIDOS",200);
define("BAN_DATOS_INVALIDOS",400);
class Banco extends Query implements BasedatosInterface, GridInterface, ListaInterface
{
   
    /**
     * 
     * @var BDetalles
     */
    public $Detalles;
    /**
     *
     * @var BancoF
     */
    protected $Banco;
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
        $this->Banco = new BancoF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Banco->table();
        
        parent::__construct($base_datos);
        
        $this->Detalles = new BDetalles();
        //Inicar tabla
        $this->Banco->create();
        $this->Banco->update();
    }

    public function borrar($id,$campo="CueID",  $usuario=0)
    {
        if($this->Banco->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>BAN_ELIMINADO), $id, $campo, $usuario);
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
     * @return BancoD
     */
    public function obtener($id = 0, $campo = "CueID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".BAN_ELIMINADO);
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

    public function editar($datos, $id,$campo="CueID", $condicion="0", $usuario=0)
    {
        if($this->Banco->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Banco->isSupervisor($_SESSION["USR_ROL"]))
        {
            $this->Banco->data = new BancoD($datos);
            if($this->validar() === true)
            {               
                $this->conexion->begin_transaction();
                $this->Detalles->conexion = $this->conexion;
                $cuenta = $this->obtener($datos->CdeCuenta);
                $datos->CdeSaldo = $cuenta->CueSaldo;
                $respuesta = $this->Detalles->agregar($datos);
                if($respuesta > 0)
                {                    
                    $saldo = $cuenta->CueSaldo + $datos->CdeMonto;
                    if($this->editar(array("CueSaldo"=>$saldo), $datos->CdeCuenta) > 0)
                    {
                        $this->conexion->commit();
                        return $respuesta;
                    }
                    else 
                    {
                        $this->conexion->rollback();
                        return 0;
                    }
                }
                else 
                {
                    $this->conexion->rollback();
                    return 0;
                }
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
        $respuesta = $this->consulta("*", $this->Tabla,"CueID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
    public function grid($arguments = null)
    {
        return array(
            array("title"=>"ID",                "name"=>"CueID",                    "editable"=>false,                  "width"=>"40",                  "hidden"=>true),
            array("title"=>"Cuenta",            "name"=>"CueNombre",                "editable"=>true,                   "width"=>"80",                  "align"=>"left"),
            array("title"=>"Numero",            "name"=>"CueNumero",                "editable"=>true,                   "width"=>"40"),
            array("title"=>"Detalles",          "name"=>"CueDetalle",               "editable"=>true,                   "width"=>"100"),
            array("title"=>"Saldo",             "name"=>"CueSaldo",                 "editable"=>false,                  "width"=>"100",                 "align"=>"right",
                "formatter"=>"currency","formatoptions"=>array("prefix" => "$",	"suffix" => '',	"thousandsSeparator" => ",",
                    "decimalSeparator" => ".",
                    "decimalPlaces" => 2), "on_data_display"=>array("saldo_privado"),                                    "hidden"=>$arguments["mostrar"]),
            array("title"=>"Permiso",           "name"=>"CueUsuarios",              "editable"=>$arguments["editar"],    "width"=>"40",                  "hidden"=>$arguments["mostrar"])
        );
    }

    public function forma($arguments = null)
    {
        //lo declaramos para usar las constantes de Entrada y Salida   
        $cuentas = $this->listaSelect(0);
        return "
        <form id='Cuentaformulario'>
        	<input id='CSRF' type='hidden' value='".$_SESSION['CSRF']."' />
        	<div class='row gx-5 justify-content-center'>
        		<div class='col-lg-4 col-md-4'>
                    <div class='form-floating mb-3'>
        				<select class='form-select border-danger valida' data-type='length'
        					data-length='1' name='CdeCuenta' id='CdeCuenta'
        					required='required'>
        					<option value=''>Selecciona Cuenta</option>
        					$cuentas
        				</select> <label for='CdeCuenta'>Cuenta</label>
        					    
        			</div>
        			<div class='form-floating mb-3'>
        				<select class='form-select border-danger valida' data-type='length'
        					data-length='1' name='CdeTipo' id='CajMovimiento'
        					required='required'>
        					<option value=''>Selecciona Movimiento</option>
        					<option value='".BAN_ENTRADA_EFECTIVO."'>ENTRADA</option>
        					<option value='".BAN_SALIDA_EFECTIVO."'>SALIDA</option>
        				</select> <label for='CajMovimiento'>Tipo de movimiento</label>
        					    
        			</div>
        			<div class='form-floating mb-3'>
        				<select class='form-select border-danger valida' data-type='length'
        					data-length='1' name='CdeClave' id='CajClave' required='required'>
        				</select> <label for='CajClave'>Razon</label>
        			</div>
        		</div>
        		<div class='col-lg-4 col-md-4'>
        			<div class='form-floating mb-3'>
        				<select class='form-select border-danger valida' data-type='length'
        					data-length='1' name='CdeConcepto' id='CajConcepto'
        					required='required'>
        				</select> <label for='CajConcepto'>Concepto</label>
        			</div>
        			<div class='form-floating mb-3'>
        				<input class='form-control border-danger valida' data-type='length'
        					data-length='1' type='number' name='CdeMonto'
        					required='required' step='0.01' /> <label for='CdeMonto'>Cantidad</label>
        			</div>
        		</div>
        		<div class='col-lg-4 col-md-4 border'>
        			<div id='conceptos'></div>
        		</div>
        		<div class='col-lg-6 col-xl-6'>
        			<!-- Submit Button-->
        			<div class='button-group text-center'>
        				<button class='btn btn-primary' id='submitButtonCuenta'
        					type='submit'>Enviar</button>
        				<button class='btn btn-danger' id='resetButtonCuenta' type='reset'>Limpiar</button>
        			</div>
        		</div>
        	</div>
        </form>
        ";
    }

    public function modal($arguments = null)
    {}
    public function listaJson($seleccionado, $condicion = "0", $ordenado = "0")
    {}

    public function listaSelect($seleccionado, $condicion = "0", $ordenado = "0")
    {
        return $this->options("CueID as id, CueNombre as nombre", $this->Tabla,"id", $seleccionado,$condicion, $ordenado);
    }


}

