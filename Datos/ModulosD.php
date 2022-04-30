<?php
namespace Clases\Login\Datos;

class ModulosD
{
    /**
* @var int
*
*/public $ModID;
/**
* @var string
*
*/
public $ModNombre;
/**
* @var int
*
*/public $ModRol;
/**
* @var int
*
*/public $ModOrden;
/**
* @var string
*
*/
public $lastupdate;
 
    public function __construct($datos = null)
    {
        try
        {
            if ($datos == null)
            {
                $this->ModID = 0;
$this->ModNombre = "";
$this->ModRol = 0;
$this->ModOrden = 0;
$this->lastupdate = "";

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