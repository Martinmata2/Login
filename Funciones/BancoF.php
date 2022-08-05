<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\PermisosBD;
use Clases\Login\Datos\BancoD;

class BancoF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var BancoD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new BancoD($datos);
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
        return "cuentas";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "CREATE TABLE IF NOT EXISTS `cuentas` (
          `CueID` int(11) NOT NULL AUTO_INCREMENT,
          `CueNombre` tinytext CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
          `CueDetalle` tinytext CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
          `CueSaldo` decimal(10,2) NOT NULL,
          `CueNumero` varchar(20) NOT NULL,
          `CueUsuarios` varchar(40) NOT NULL,
          PRIMARY KEY (`CueID`)
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

