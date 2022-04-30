<?php
namespace Clases\Login\Datos;

class ArchivosD
{
    /**
* @var int
*
*/public $ArcID;
/**
* @var string
*
*/
public $ArcNombre;
/**
* @var string
*
*/
public $ArcPath;
/**
* @var string
*
*/
public $ArcIcon;
/**
* @var int
*
*/public $ArcModulo;
/**
* @var int
*
*/public $ArcOrden;
/**
* @var int
*
*/public $ArcSubModulo;
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
                $this->ArcID = 0;
$this->ArcNombre = "";
$this->ArcPath = "";
$this->ArcIcon = "";
$this->ArcModulo = 0;
$this->ArcOrden = 0;
$this->ArcSubModulo = 0;
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