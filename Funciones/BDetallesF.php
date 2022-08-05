<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\PermisosBD;
use Clases\Login\Datos\BDetallesD;

class BDetallesF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var BDetallesD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new BDetallesD($datos);
    }

    public function truncate()
    {
        if ($this->canTruncate($_SESSION["USR_ROL"])) return $this->conexion->query("TRUNCATE TABLE " . $this->table());
    }

    public function create()
    {
        if ($resultado = $this->conexion->query("SHOW TABLES LIKE '" . $this->table() . "'"))
        {
            if ($resultado->num_rows == 0) $this->conexion->multi_query($this->sql());
        }
    }

    public function update()
    {
        if ($resultado = $this->conexion->query("SHOW TABLES LIKE '" . $this->table() . "'"))
        {
            if ($resultado->num_rows > 0)
            {
                if (strlen(trim($this->pendingupdates())) > 10) $this->conexion->multi_query($this->pendingupdates());
            }
        }
    }

    public function delete()
    {
        if ($this->canDrop($_SESSION["USR_ROL"])) return $this->conexion->query("DROP TABLE IF EXISTS " . $this->table());
    }

    public function getData()
    {
        return $this->data;
    }

    public function table()
    {
        return "cuentadetalles";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "CREATE TABLE IF NOT EXISTS `cuentadetalles` (
          `CdeID` int(11) NOT NULL AUTO_INCREMENT,
          `CdeCuenta` int(11) NOT NULL,
          `CdeUsuario` int(11) NOT NULL,
          `CdeMonto` decimal(12,2) NOT NULL,
          `CdeSaldo` decimal(12,2) NOT NULL,
          `CdeFecha` datetime NOT NULL,
          `CdeTipo` tinyint(4) NOT NULL,
          `CdeConcepto` tinytext CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
          `CdeReferencia` int(11) NOT NULL,
          `CdeClave` int(11) NOT NULL,
          `CdeDescripcion` tinytext NOT NULL,
          PRIMARY KEY (`CdeID`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        return $sql;
    }

    /**
     * Codigo sql para actualizaciones pendientes.
     *
     * @return string
     */
    private function pendingupdates()
    {
        //sql code para actualizar tabla
        $update = "";                
        return $update;
    }
}

