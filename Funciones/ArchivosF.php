<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\PermisosBD;
use Clases\Login\Datos\ArchivosD;

class ArchivosF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var ArchivosD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new ArchivosD($datos);
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
        return "archivos";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "";
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
        $update = " UPDATE `archivos` SET `ArcPath` = 'Administrador/Lista/Usuarios.php' WHERE `archivos`.`ArcID` = 4;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Descuentos.php', `ArcModulo` = '3', `ArcOrden` = '8' WHERE `archivos`.`ArcID` = 7;
                    UPDATE `archivos` SET `ArcPath` = 'Administrador/Ajustes.php' WHERE `archivos`.`ArcID` = 8;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Agregar/Compras.php' WHERE `archivos`.`ArcID` = 20;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Compras.php' WHERE `archivos`.`ArcID` = 21;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Agregar/Salidas.php' WHERE `archivos`.`ArcID` = 22;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Bitacora.php' WHERE `archivos`.`ArcID` = 30;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Salidas.php' WHERE `archivos`.`ArcID` = 23;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Agregar/Ajustes.php' WHERE `archivos`.`ArcID` = 24;                    
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Ajustes.php' WHERE `archivos`.`ArcID` = 27;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Traspasos.php' WHERE `archivos`.`ArcID` = 34;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Productos.php' WHERE `archivos`.`ArcID` = 10;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Clientes.php' WHERE `archivos`.`ArcID` = 11;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Agregar/Productos.php' WHERE `archivos`.`ArcID` = 13;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Agregar/Clientes.php' WHERE `archivos`.`ArcID` = 14;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Agregar/Proveedor.php' WHERE `archivos`.`ArcID` = 15;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Agregar/Venta.php' WHERE `archivos`.`ArcID` = 16;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Ventas.php' WHERE `archivos`.`ArcID` = 17;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Creditos.php' WHERE `archivos`.`ArcID` = 18;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Historial.php' WHERE `archivos`.`ArcID` = 19;                    
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Agregar/Presupuesto.php' WHERE `archivos`.`ArcID` = 36;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Presupuestos.php' WHERE `archivos`.`ArcID` = 37;";                
        return $update;
    }
}

