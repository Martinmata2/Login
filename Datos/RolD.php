<?php
namespace Clases\Login\Datos;

class RolD
{
    /**
* @var int
*
*/public $RolID;
/**
* @var string
*
*/
public $RolNombre;
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
                $this->RolID = 0;
$this->RolNombre = "";
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