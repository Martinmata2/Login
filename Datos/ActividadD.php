<?php
namespace Clases\Login\Datos;

class ActividadD
{
    /**
* @var int
*
*/public $ActID;
/**
* @var int
*
*/public $ActUsuario;
/**
* @var string
*
*/
public $ActCantidad;
/**
* @var int
*
*/public $ActCodigo;
/**
* @var string
*
*/
public $ActDescripcion;
/**
* @var string
*
*/
public $ActFecha;
/**
* @var int
*
*/public $ActRelacion;
/**
* @var string
*
*/
public $updated;
 
    public function __construct($datos = null)
    {
        try
        {
            if ($datos == null)
            {               
                $this->ActUsuario = 0;
                $this->ActCantidad = "";
                $this->ActCodigo = 0;
                $this->ActDescripcion = "";
                $this->ActFecha = "";
                $this->ActRelacion = 0;                

            }
            else
            {
                foreach ($datos as $k => $v)
                {
                    $this->$k = $v;
                }
            }
        }
        catch (\Exception $e)
        {}
    }
}