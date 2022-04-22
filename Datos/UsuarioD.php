<?php
namespace Clases\Login\Datos;

class UsuarioD
{
    /**
* @var int
*
*/public $UsuID;
/**
* @var int
*
*/public $UsuCliente;
/**
* @var string
*
*/
public $UsuNombre;
/**
* @var string
*
*/
public $UsuUsuario;
/**
* @var string
*
*/
public $UsuClave;
/**
* @var string
*
*/
public $UsuRol;
/**
* @var string
*
*/
public $UsuEmail;
/**
* @var string
*
*/
public $UsuToken;
/**
* @var string
*
*/
public $UsuBd;
/**
* @var string
*
*/
public $UsuCodigo;
/**
* @var string
*
*/
public $UsuActivo;
/**
* @var string
*
*/
public $UsuEmpresa;
/**
* @var string
*
*/
public $updated;
/**
* @var string
*
*/
public $lastupdate;
/**
* @var string
*
*/
public $UsuClavePAC;
/**
* @var string
*
*/
public $UsuUsuarioPAC;
 
    public function __construct($datos = null)
    {
        try
        {
            if ($datos == null)
            {
                $this->UsuID = 0;
                $this->UsuCliente = 0;
                $this->UsuNombre = "";
                $this->UsuUsuario = "";
                $this->UsuClave = "";
                $this->UsuRol = "";
                $this->UsuEmail = "";
                $this->UsuToken = "";
                $this->UsuBd = "";
                $this->UsuCodigo = "";
                $this->UsuActivo = "";
                $this->UsuEmpresa = "";
                $this->updated = "";
                $this->lastupdate = "";
                $this->UsuClavePAC = "";
                $this->UsuUsuarioPAC = "";

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