<?php
namespace Clases\Login\Datos;

class PermisosD
{
    /**
* @var int
*
*/public $PusID;
/**
* @var int
*
*/public $PusUsuario;
/**
* @var string
*
*/
public $PusPermisos;
/**
* @var int
*
*/public $updated;
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
                $this->PusID = 0;
$this->PusUsuario = 0;
$this->PusPermisos = "";
$this->updated = 0;
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