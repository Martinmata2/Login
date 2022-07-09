<?php
namespace Clases\Login\Datos;

class BDetallesD
{
    /**
* @var int
*
*/public $CdeID;
/**
* @var int
*
*/public $CdeCuenta;
/**
* @var int
*
*/public $CdeUsuario;
/**
* @var string
*
*/
public $CdeMonto;
/**
* @var string
*
*/
public $CdeSaldo;
/**
* @var string
*
*/
public $CdeFecha;
/**
* @var string
*
*/
public $CdeTipo;
/**
* @var string
*
*/
public $CdeConcepto;
/**
* @var int
*
*/public $CdeReferencia;
/**
* @var int
*
*/public $CdeClave;
/**
* @var string
*
*/
public $CdeDescripcion;
 
    public function __construct($datos = null)
    {
        try
        {
            if ($datos == null)
            {
                $this->CdeID = 0;
$this->CdeCuenta = 0;
$this->CdeUsuario = 0;
$this->CdeMonto = "";
$this->CdeSaldo = "";
$this->CdeFecha = "";
$this->CdeTipo = "";
$this->CdeConcepto = "";
$this->CdeReferencia = 0;
$this->CdeClave = 0;
$this->CdeDescripcion = "";

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