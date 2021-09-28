<?php
namespace Clases\Login;
use clases\MySql\Query;
use Clases\BasicInterface;

/**
 * @version v2020_2
 * @author Martin Mata
 */
class Archivos extends Query implements BasicInterface
{
	private $base_datos;
    function __construct($conexion = null, $base_datos = BD_GENERAL)
    {
        parent::__construct($conexion);
        $this->conn->seleccionaBD($base_datos);
        $this->base_datos = $base_datos;
        if($resultado = $this->conn->ejecutar("SHOW TABLES LIKE 'archivos'"))
        {
            if($this->conn->total_filas($resultado) == 0)
                $this->conn->ejecutarDeArchivo($this->tabla());
        }
       
    }

    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::obtener()
     */
    function obtener($id)
    {
    	$archivo = $this->consulta("*", "archivos", $this->base_datos, "ArcID = $id");
    	if(count($archivo) == 0)
    		return 0;
    	return $archivo[0];
    }
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::agregar()
     */
    public function agregar($datos,  $usuario)
    {
    	return $this->insertar("archivos", $datos, $this->base_datos,$usuario);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::borrar()
     */
    public function borrar($datos, $usuario)
    {
    	return $this->borrar("archivos", $datos->ArcID, "ArcID", $this->base_datos,$usuario);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::editar()
     */
    public function editar($datos,$id, $usuario)
    {
    	return $this->modificar("archivos", $datos, $id, "ArcID", $this->base_datos, $usuario);
    }
     

    /**
     * Regresa lista de archivos
     * {@inheritDoc}
     * @see \clases\BasicInterface::lista()
     */
    public function lista($seleccionado = "0", $ordenado = "0", $condicion = "0")
    {
        return $this->options("ArcID as id, ArcNombre as nombre","archivos", 
            $this->base_datos,"id", $seleccionado, $condicion, $ordenado);
    }      


    /**
     * Regresa el sql de la tabla archivos
     */
    private function tabla()
    {
        $sql = "
			CREATE TABLE IF NOT EXISTS `archivos` (
			  `ArcID` int(11) NOT NULL AUTO_INCREMENT,
			  `ArcNombre` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
              `ArcPath` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
              `ArcIcon` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
              `ArcModulo` int(11) NOT NULL,
			  `ArcOrden` int(11) NOT NULL,
              `ArcSubModulo` int(11) NOT NULL, 
              `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`ArcID`) 
			)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;                                    
        INSERT INTO `archivos` (`ArcID`, `ArcNombre`, `ArcPath`, `ArcIcon`, `ArcModulo`, `ArcOrden`, `ArcSubModulo`, `lastupdate`) VALUES
		(1, 'Rols', 'administracion/programa/rols.php', 'fi-torsos-all', 1, 1, 0, '2017-03-22 13:01:59'),
        (2, 'Modulos', 'administracion/programa/modulos.php', 'fi-info', 1, 2, 0, '2017-03-22 13:02:29'),
        (3, 'Archivos', 'administracion/programa/archivos.php', 'fi-folder', 1, 3, 0, '2017-03-22 13:02:59'),
        (4, 'Usuarios', 'administracion/catalogos/usuarios.php', 'fi-torsos-male-female', 2, 1, 0, '2017-03-23 10:57:00'),
        (5, 'Permisos', 'administracion/permisos.php', 'fi-unlock', 2, 2, 0, '2017-03-23 10:58:35'),
        (6, 'Mis Datos', 'administracion/catalogos/mis_datos.php?id=1', 'fi-torso', 2, 3, 0, '2017-07-13 20:18:51'),        
        (8, 'Ajustes', 'administracion/ajustes.php', 'fi-wrench', 2, 5, 0, '2017-06-30 10:23:45'),
        (7, 'Descuentos', 'pos/Descuentos.php', 'fi-clipboard-notes', 2, 6, 0, '2017-07-13 20:18:51'),
        (9, 'Claves', 'administracion/claves.php', 'fi-widget', 2, 6, 0, '2017-07-03 21:20:19'),
        (10, 'Productos', 'catalogos/productos.php', 'fi-shopping-cart', 3, 1, 0, '2017-06-25 08:02:09'),
        (11, 'Clientes', 'catalogos/clientes.php', 'fi-torsos-all', 3, 2, 0, '2017-06-22 00:03:11'),
        (12, 'Proveedores', 'catalogos/proveedores.php', 'fi-torsos-all', 3, 3, 0, '2017-07-01 03:10:19'),
        (13, 'Producto Nuevo', 'catalogos/Agregar/Producto.php', 'fi-plus', 3, 4, 0, '2017-06-30 10:18:03'),
        (14, 'Cliente Nuevo', 'catalogos/Agregar/Cliente.php', 'fi-torso', 3, 5, 0, '2017-06-30 10:21:17'),
        (15, 'Proveedor Nuevo', 'catalogos/Agregar/Proveedor.php', 'fi-torso-business', 3, 6, 0, '2017-07-01 03:24:07'),
        (16, 'Ventas', 'pos/Agregar/Venta.php', 'fi-shopping-cart', 4, 1, 0, '2017-06-25 07:38:13'),
        (17, 'Lista de Ventas', 'pos/ventas.php', 'fi-indent-less', 4, 2, 0, '2017-07-04 00:36:14'),
        (18, 'Creditos', 'pos/creditos.php', 'fi-dollar', 4, 3, 0, '2018-06-29 18:16:14'),
        (19, 'Historial', 'pos/Pagos/Historial.php', 'fi-graph-bar', 4, 4, 0, '2018-06-29 23:02:41'),
        (20, 'Compras', 'inventario/Agregar/Compras.php', 'fi-plus', 5, 1, 0, '2018-02-10 10:11:31'),
        (21, 'Lista de Compras', 'inventario/compras.php', 'fi-indent-less', 5, 2, 0, '2018-06-28 02:49:33'),
        (22, 'Movimientos Productos', 'inventario/Agregar/Salidas.php', 'fi-minus', 5, 3, 0, '2018-02-10 10:14:42'),
        (23, 'Lista de Salidas', 'inventario/salidas.php', 'fi-indent-less', 5, 4, 0, '2018-06-28 02:50:51'),        
        (24, 'Ajustes inventario', 'inventario/Ajustar.php', 'fi-checkbox', 5, 5, 0, '2018-07-01 02:24:47'),
        (25, 'Cortes', 'reportes/Corte_Caja.php', 'fi-address-book', 6, 1, 0, '2018-07-02 18:46:47'),
        (26, 'General', 'reportes/Pizarron.php', 'fi-graph-bar', 6, 2, 0, '2018-07-05 04:34:12'),
        (27, 'Lista de Ajustes', 'inventario/ajustes.php', 'fi-indent-less', 5, 6, 0, '2018-07-02 18:46:47'),
        (28, 'Prestamos', 'banco/Prestamos.php', 'fi-dollar', 7, 2, 0, '2018-07-02 18:46:47'),
        (29, 'Productos', 'reportes/Productos.php', 'fi-graph-horizontal', 6, 3, 0, '2018-07-02 18:46:47'),
        (30, 'Bitacora', 'pos/Bitacora.php', 'fi-first-aid', 5, 4, 0, '2018-07-02 18:46:47'),
        (31, 'Movimientos', 'caja/Movimientos.php', 'fi-list-bullet', 5, 6, 0, '2018-07-02 18:46:47'),
        (32, 'Banco', 'banco/Inicio.php', 'fi-calendar', 7, 1, 0, '2018-07-02 18:46:47'),
        (33, 'Sincronizar', 'web/Sincronizar.php', 'fi-cloud', 8, 1, 0, '2020-06-07 02:48:03'),
        (34, 'Traspasos', 'inventario/Traspasos.php', 'fi-fast-forward', 5, 6, 0, '2020-06-07 02:48:03'),
        (35, 'Ventas Cerradas', 'pos/Venta-Cerrada.php', 'fi-alert', 4, 5, 0, '2020-06-07 02:48:03'),
        (36, 'Presupuestos', 'pos/Agregar/Presupuesto.php', 'fi-fast-forward', 4, 6, 0, '2020-06-07 02:48:03'),
        (37, 'Lista de Presupuestos', 'pos/Presupuestos.php', 'fi-list-bullet', 4, 7, 0, '2020-06-07 02:48:03'),
        (38, 'Medicos', 'catalogos/Medicos.php', 'fi-first-aid', 3, 7, 0, '2020-06-07 02:48:03');
        ";
        //removi de los programas mis datos y usuario web para futuro desarrollo        
        //(6, 'Mis Datos', 'catalogos/Agregar/Propio.php?id=1', '', 2, 3, 0, '2017-07-13 20:18:51'),
        //(7, 'Usuario WEB', 'administracion/catalogos/usuarios_web.php', '', 2, 4, 0, '2017-07-13 20:18:51'),
        return $sql;
    }
}