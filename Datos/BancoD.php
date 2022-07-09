<?php
namespace Clases\Login\Datos;

class BancoD
{
    /**
* @var int
*
*/public $CueID;
/**
* @var string
*
*/
public $CueNombre;
/**
* @var string
*
*/
public $CueDetalle;
/**
* @var string
*
*/
public $CueSaldo;
/**
* @var string
*
*/
public $CueNumero;
/**
* @var string
*
*/
public $CueUsuarios;
 
    public function __construct($datos = null)
    {
        try
        {
            if ($datos == null)
            {
                $this->CueID = 0;
$this->CueNombre = "";
$this->CueDetalle = "";
$this->CueSaldo = "";
$this->CueNumero = "";
$this->CueUsuarios = "";

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