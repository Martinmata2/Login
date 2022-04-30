<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\ActividadF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\ActividadD;
use Clases\Catalogos\BasedatosInterface;


define("ACT_VENTA",1);
define("ACT_COMPRAS", 2);
define("ACT_DEVOLUCION",3);
define("ACT_EDITAR_COMPRAS", 4);
define("ACT_SALIDA_EFECTIVO",5);
define("ACT_ENTRADA_EFECTIVO",6);
define("ACT_PAGOS",7);
define("ACT_ENTRADA_PRODUCTOS", 8);
define("ACT_SALIDA_PRODUCTOS", 9);
define("ACT_EDITAR_PRODUCTOS", 11);
define("ACT_CREDITOS", 12);
define("ACT_AJUSTE_INVENTARIO", 13);
define("ACT_DESCUENTO", 14);
define("ACT_PRESUPUESTO", 15);
define("ACT_EDITAR_PRESUPUESTO", 16);
define("ACT_DESCUENTO_CREDITO", 17);
define("ACT_COMISION", 18);

//Definiciones para estandarizar valores
define("ACT_ELIMINADO", 1);
define("ACT_ACTIVO", 1);
define("ACT_INACTIVO", 0);
define("ACT_SUCCESS", 200);
define("ACT_ERROR", 400);
define("ACT_DATOS_VALIDOS",200);
define("ACT_DATOS_INVALIDOS",400);

class Actividad extends Query implements BasedatosInterface
{
   
    /**
     *
     * @var ActividadF
     */
    protected $Actividad;
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
        $this->Actividad = new ActividadF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Actividad->table();
        
        parent::__construct($base_datos);
        
        //Inicar tabla
        $this->Actividad->create();
        $this->Actividad->update();
    }

    public function borrar($id,$campo="ActID",  $usuario=0)
    {
        if($this->Actividad->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>ACT_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        //((validacion de campos))
        return true;       
    }

    public function obtener($id = 0, $campo = "ActID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".ACT_ELIMINADO);
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

    public function editar($datos, $id,$campo="ActID", $condicion="0", $usuario=0)
    {
        if($this->Actividad->isUsuario($_SESSION["USR_ROL"]))
        {
            return $this->modificarEspecial($this->Tabla, $datos, $condicion,$usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Actividad->isUsuario($_SESSION["USR_ROL"]))
        {
            $this->Actividad->data = new ActividadD($datos);
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
        $respuesta = $this->consulta("*", $this->Tabla,"ActID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
    
    /**
     * Entradas de dinero para usuario en periodo de tiempo dado
     * @param string $ultimoCorte
     * @param int $usuario
     * @return number
     */
    public function entradas(string $ultimoCorte, int $usuario)
    {
        $resultado = $this->consulta("sum(ActCantidad) as Cantidad","actividad",
            "ActUsuario = $usuario  AND (ActCodigo = ".ACT_VENTA." or ActCodigo = ".ACT_ENTRADA_EFECTIVO." or ActCodigo = ".ACT_PAGOS.")
                 and (ActFecha > '$ultimoCorte')");
        if(count($resultado) > 0)
            return $resultado[0]->Cantidad;
        else return 0.00;
    }
    
    /**
     * 
     * @param string $ultimoCorte
     * @param int $usuario
     * @return number
     */
    public function salidas(string $ultimoCorte, int $usuario)
    {
        $resultado = $this->consulta("sum(ActCantidad) as Cantidad","actividad",
            "ActUsuario = $usuario AND (ActCodigo = ".ACT_COMPRAS." or ActCodigo = ".ACT_DEVOLUCION." or ActCodigo = ".ACT_SALIDA_EFECTIVO.")
                        and (ActFecha > '$ultimoCorte')");
        if(\count($resultado)>0)
            return $resultado[0]->Cantidad;
            else return 0.00;
    }
}

