<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\PermisosBD;
use Clases\Login\Datos\ModulosD;

class ModulosF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var ModulosD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new ModulosD($datos);
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
        return "modulos";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "CREATE TABLE IF NOT EXISTS `modulos` (
          `ModID` int(11) NOT NULL AUTO_INCREMENT,
          `ModNombre` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `ModRol` int(11) NOT NULL,
          `ModOrden` int(3) NOT NULL,
          `lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`ModID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
        INSERT INTO `modulos` (`ModID`, `ModNombre`, `ModRol`, `ModOrden`, `lastupdate`) VALUES
        (1, 'PROGRAMA', 1, 8, '2017-06-21 23:19:37'),
        (2, 'ADMINISTRADOR', 2, 4, '2017-06-21 23:20:06'),
        (3, 'CATALOGOS', 3, 2, '2017-06-21 23:20:20'),
        (4, 'PUNTO VENTA', 3, 1, '2017-06-21 23:20:28'),
        (5, 'INVENTARIO', 3, 3, '2017-06-21 23:20:37'),
        (6, 'REPORTES', 3, 5, '2017-06-21 23:20:37'),
        (7, 'BANCO', 3, 7, '2017-06-21 23:20:37'),
        (8, 'WEB', 3, 6, '2017-06-21 23:20:37');";
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
        $update = "UPDATE `modulos` SET `ModRol` = '4' WHERE `modulos`.`ModID` = 3; 
                UPDATE `modulos` SET `ModRol` = '4' WHERE `modulos`.`ModID` = 4; 
                UPDATE `modulos` SET `ModRol` = '4' WHERE `modulos`.`ModID` = 5; 
                UPDATE `modulos` SET `ModRol` = '3' WHERE `modulos`.`ModID` = 7;";                
        return $update;
    }
}

