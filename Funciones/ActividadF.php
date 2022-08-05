<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\PermisosBD;
use Clases\Login\Datos\ActividadD;

class ActividadF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var ActividadD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new ActividadD($datos);
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
        return "actividad";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "CREATE TABLE IF NOT EXISTS `actividad` (
          `ActID` int(11) NOT NULL AUTO_INCREMENT,
          `ActUsuario` int(11) NOT NULL,
          `ActCantidad` decimal(10,2) NOT NULL,
          `ActCodigo` int(11) NOT NULL COMMENT '1:ventas, 2:compras, 3: devolucion de productos 4:Edicion de Compras, 5:Salida de Efectivo, 6:Entrada de Efectivo, 7:Pagos, 8:entrada, 9:salida, 10: edificacion de Producto, 11: devolucion de producto en compra 12:Creditos',
          `ActDescripcion` text COLLATE utf8_spanish2_ci NOT NULL,
          `ActFecha` datetime NOT NULL,
          `ActRelacion` int(11) NOT NULL,
          `updated` tinyint(1) NOT NULL,
          PRIMARY KEY (`ActID`),
          KEY `actividad:fecha` (`ActFecha`),
          KEY `actividad_relacion` (`ActRelacion`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;";
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

