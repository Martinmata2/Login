<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\PermisosBD;
use Clases\Login\Datos\RolD;

class RolF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var RolD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new RolD($datos);
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
        return "rol";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "CREATE TABLE IF NOT EXISTS `rol` (
          `RolID` int(11) NOT NULL AUTO_INCREMENT,
          `RolNombre` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`RolID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
        
        INSERT INTO `rol` (`RolID`, `RolNombre`, `lastupdate`) VALUES
        (1, 'PROGRAMADOR', '2017-06-21 23:09:40'),
        (2, 'ADMINISTRADOR', '2017-06-21 23:09:50'),
        (3, 'SUPERVISOR', '2017-06-21 23:10:00'),
        (4, 'USUARIO', '2017-06-21 23:10:10');";
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

