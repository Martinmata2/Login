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
                    UPDATE `archivos` SET `ArcPath` = 'Administrador/Lista/Claves.php' WHERE `archivos`.`ArcID` = 9;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Productos.php' WHERE `archivos`.`ArcID` = 10;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Clientes.php' WHERE `archivos`.`ArcID` = 11;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Proveedores.php' WHERE `archivos`.`ArcID` = 12;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Agregar/Productos.php' WHERE `archivos`.`ArcID` = 13;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Agregar/Clientes.php' WHERE `archivos`.`ArcID` = 14;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Agregar/Proveedor.php' WHERE `archivos`.`ArcID` = 15;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Agregar/Venta.php' WHERE `archivos`.`ArcID` = 16;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Ventas.php' WHERE `archivos`.`ArcID` = 17;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Creditos.php' WHERE `archivos`.`ArcID` = 18;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Historial.php' WHERE `archivos`.`ArcID` = 19;            
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Agregar/Compras.php' WHERE `archivos`.`ArcID` = 20;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Compras.php' WHERE `archivos`.`ArcID` = 21;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Agregar/Salidas.php' WHERE `archivos`.`ArcID` = 22;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Bitacora.php' WHERE `archivos`.`ArcID` = 30;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Salidas.php' WHERE `archivos`.`ArcID` = 23;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Agregar/Ajustes.php' WHERE `archivos`.`ArcID` = 24;                    
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Lista/Ajustes.php' WHERE `archivos`.`ArcID` = 27;
                    UPDATE `archivos` SET `ArcPath` = 'Inventario/Traspasos.php' WHERE `archivos`.`ArcID` = 34;                          
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Agregar/Presupuesto.php' WHERE `archivos`.`ArcID` = 36;
                    UPDATE `archivos` SET `ArcPath` = 'Pos/Lista/Presupuestos.php' WHERE `archivos`.`ArcID` = 37;
                    UPDATE `archivos` SET `ArcPath` = 'Catalogos/Lista/Medicos.php' WHERE `archivos`.`ArcID` = 38;
                    UPDATE `archivos` SET `ArcPath` = 'Reportes/Monetario.php' WHERE `archivos`.`ArcID` = 25;
                    UPDATE `archivos` SET `ArcPath` = 'Reportes/Pizarron.php' WHERE `archivos`.`ArcID` = 26;
                    UPDATE `archivos` SET `ArcPath` = 'Reportes/Venta_Productos.php' WHERE `archivos`.`ArcID` = 29;
                    UPDATE `archivos` SET `ArcPath` = 'Reportes/Corte_Caja.php' WHERE `archivos`.`ArcID` = 41;
                    UPDATE `archivos` SET `ArcPath` = 'Banco/Inicio.php' WHERE `archivos`.`ArcID` = 32;
                    UPDATE `archivos` SET `ArcIcon` = 'first-aid-kit-1' WHERE `archivos`.`ArcID` = 38;
                    UPDATE `archivos` SET `ArcIcon` = 'portfolio-grid-1' WHERE `archivos`.`ArcID` = 15;
                    UPDATE `archivos` SET `ArcIcon` = 'user-1' WHERE `archivos`.`ArcID` = 14;
                    UPDATE `archivos` SET `ArcIcon` = 'add-1' WHERE `archivos`.`ArcID` = 13;
                    UPDATE `archivos` SET `ArcIcon` = 'tag-1' WHERE `archivos`.`ArcID` = 7;
                    UPDATE `archivos` SET `ArcIcon` = 'shopping-bag-1' WHERE `archivos`.`ArcID` = 10;
                    UPDATE `archivos` SET `ArcIcon` = 'user-1' WHERE `archivos`.`ArcID` = 11;
                    UPDATE `archivos` SET `ArcIcon` = 'portfolio-grid-1' WHERE `archivos`.`ArcID` = 12;
                    UPDATE `archivos` SET `ArcIcon` = 'shopping-cart-1' WHERE `archivos`.`ArcID` = 16; 
                    UPDATE `archivos` SET `ArcIcon` = 'cart-1' WHERE `archivos`.`ArcID` = 17; 
                    UPDATE `archivos` SET `ArcIcon` = 'credit-card-1' WHERE `archivos`.`ArcID` = 18; 
                    UPDATE `archivos` SET `ArcIcon` = 'dollar-sign-1' WHERE `archivos`.`ArcID` = 19; 
                    UPDATE `archivos` SET `ArcIcon` = 'close-1' WHERE `archivos`.`ArcID` = 35; 
                    UPDATE `archivos` SET `ArcIcon` = 'checkmark-1' WHERE `archivos`.`ArcID` = 36; 
                    UPDATE `archivos` SET `ArcIcon` = 'list-details-1' WHERE `archivos`.`ArcID` = 37;
                    UPDATE `archivos` SET `ArcIcon` = 'add-1' WHERE `archivos`.`ArcID` = 20;
                    UPDATE `archivos` SET `ArcIcon` = 'list-details-1' WHERE `archivos`.`ArcID` = 21;
                    UPDATE `archivos` SET `ArcIcon` = 'minus-1' WHERE `archivos`.`ArcID` = 22;
                    UPDATE `archivos` SET `ArcIcon` = 'checked-window-1' WHERE `archivos`.`ArcID` = 23;
                    UPDATE `archivos` SET `ArcIcon` = 'checkmark-1' WHERE `archivos`.`ArcID` = 24;
                    UPDATE `archivos` SET `ArcIcon` = 'bookmark-1' WHERE `archivos`.`ArcID` = 27;
                    UPDATE `archivos` SET `ArcIcon` = 'first-aid-kit-1' WHERE `archivos`.`ArcID` = 30;
                    UPDATE `archivos` SET `ArcIcon` = 'money-box-1' WHERE `archivos`.`ArcID` = 25;
                    UPDATE `archivos` SET `ArcIcon` = 'pie-chart-1' WHERE `archivos`.`ArcID` = 26;
                    UPDATE `archivos` SET `ArcIcon` = 'cart-1' WHERE `archivos`.`ArcID` = 29;
                    UPDATE `archivos` SET `ArcIcon` = 'dollar-badge-1' WHERE `archivos`.`ArcID` = 41;
                    UPDATE `archivos` SET `ArcIcon` = 'user-1' WHERE `archivos`.`ArcID` = 4;
                    UPDATE `archivos` SET `ArcIcon` = 'key-1' WHERE `archivos`.`ArcID` = 9;
                    UPDATE `archivos` SET `ArcIcon` = 'settings-1' WHERE `archivos`.`ArcID` = 8;
                    UPDATE `archivos` SET `ArcIcon` = 'bank-cards-1' WHERE `archivos`.`ArcID` = 32; 
                    UPDATE `archivos` SET `ArcPath` = 'Banco/Prestamos.php', `ArcIcon` = 'credit-card-1' WHERE `archivos`.`ArcID` = 28;
                    ";          
        
        return $update;
    }
}

