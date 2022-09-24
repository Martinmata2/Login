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
        $sql = "CREATE TABLE IF NOT EXISTS `archivos` (
          `ArcID` int(11) NOT NULL AUTO_INCREMENT,
          `ArcNombre` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `ArcPath` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `ArcIcon` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `ArcModulo` int(11) NOT NULL,
          `ArcOrden` int(11) NOT NULL,
          `ArcSubModulo` int(11) NOT NULL,
          `lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`ArcID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

        INSERT INTO `archivos` (`ArcID`, `ArcNombre`, `ArcPath`, `ArcIcon`, `ArcModulo`, `ArcOrden`, `ArcSubModulo`, `lastupdate`) VALUES
        
        (2, 'Usuarios', 'Administrador/Lista/Usuarios.php', 'user-1', 2, 1, 0, '2017-03-23 16:57:00'),             
        (3, 'Descuentos', 'Catalogos/Lista/Descuentos.php', 'tag-1', 3, 8, 0, '2017-07-14 01:18:51'),
        (4, 'Ajustes', 'Administrador/Ajustes.php', 'settings-1', 2, 5, 0, '2017-06-30 15:23:45'),
        (5, 'Claves', 'Administrador/Lista/Claves.php', 'key-1', 2, 6, 0, '2017-07-04 02:20:19'),
        (6, 'Productos', 'Catalogos/Lista/Productos.php', 'shopping-bag-1', 3, 1, 0, '2017-06-25 13:02:09'),
        (7, 'Clientes', 'Catalogos/Lista/Clientes.php', 'user-1', 3, 2, 0, '2017-06-22 05:03:11'),
        (8, 'Proveedores', 'Catalogos/Lista/Proveedores.php', 'portfolio-grid-1', 3, 3, 0, '2017-07-01 08:10:19'),
        (9, 'Producto Nuevo', 'Catalogos/Agregar/Productos.php', 'add-1', 3, 4, 0, '2017-06-30 15:18:03'),
        (10, 'Cliente Nuevo', 'Catalogos/Agregar/Clientes.php', 'user-1', 3, 5, 0, '2017-06-30 15:21:17'),
        (11, 'Proveedor Nuevo', 'Catalogos/Agregar/Proveedor.php', 'portfolio-grid-1', 3, 6, 0, '2017-07-01 08:24:07'),
        (12, 'Ventas', 'Pos/Agregar/Venta.php', 'shopping-cart-1', 4, 1, 0, '2017-06-25 12:38:13'),
        (13, 'Lista de Ventas', 'Pos/Lista/Ventas.php', 'cart-1', 4, 2, 0, '2017-07-04 05:36:14'),
        (14, 'Creditos', 'Pos/Lista/Creditos.php', 'credit-card-1', 4, 3, 0, '2018-06-29 23:16:14'),
        (15, 'Historial', 'Pos/Lista/Historial.php', 'dollar-sign-1', 4, 4, 0, '2018-06-30 04:02:41'),
        (16, 'Compras', 'Inventario/Agregar/Compras.php', 'add-1', 5, 1, 0, '2018-02-10 16:11:31'),
        (17, 'Lista de Compras', 'Inventario/Lista/Compras.php', 'list-details-1', 5, 2, 0, '2018-06-28 07:49:33'),
        (18, 'Movimientos Productos', 'Inventario/Agregar/Salidas.php', 'minus-1', 5, 3, 0, '2018-02-10 16:14:42'),
        (19, 'Lista de Salidas', 'Inventario/Lista/Salidas.php', 'checked-window-1', 5, 4, 0, '2018-06-28 07:50:51'),
        (20, 'Ajustes inventario', 'Inventario/Agregar/Ajustes.php', 'checkmark-1', 5, 5, 0, '2018-07-01 07:24:47'),
        (21, 'Cortes', 'Reportes/Monetario.php', 'money-box-1', 6, 1, 0, '2018-07-02 23:46:47'),
        (22, 'General', 'Reportes/Pizarron.php', 'pie-chart-1', 6, 2, 0, '2018-07-05 09:34:12'),
        (23, 'Lista de Ajustes', 'Inventario/Lista/Ajustes.php', 'bookmark-1', 5, 6, 0, '2018-07-02 23:46:47'),
        (24, 'Prestamos', 'Banco/Prestamos.php', 'credit-card-1', 7, 2, 0, '2018-07-02 23:46:47'),
        (25, 'Productos', 'Reportes/Venta_Productos.php', 'cart-1', 6, 3, 0, '2018-07-02 23:46:47'),
        (26, 'Bitacora', 'Inventario/Lista/Bitacora.php', 'first-aid-kit-1', 5, 4, 0, '2018-07-02 23:46:47'),        
        (27, 'Banco', 'Banco/Inicio.php', 'bank-cards-1', 7, 1, 0, '2018-07-02 23:46:47'),       
        (28, 'Traspasos', 'Inventario/Traspasos.php', 'fi-fast-forward', 5, 6, 0, '2020-06-07 07:48:03'),        
        (29, 'Presupuestos', 'Pos/Agregar/Presupuesto.php', 'checkmark-1', 4, 6, 0, '2020-06-07 07:48:03'),
        (30, 'Lista de Presupuestos', 'Pos/Lista/Presupuestos.php', 'list-details-1', 4, 7, 0, '2020-06-07 07:48:03'),
        (31, 'Medicos', 'Catalogos/Lista/Medicos.php', 'first-aid-kit-1', 3, 7, 0, '2020-06-07 07:48:03'), 
        (32, 'Conteo', 'Caja/Efectivo.php', 'fi-torsos-all', 4, 2, 0, '2017-03-22 19:01:59')       ;";
        
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

