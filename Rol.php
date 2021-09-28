<?php
namespace clases\Login;
use clases\MYSQL\Query;
use clases\BasicInterface;


/**
 * @version v2020_2
 * @author Martin Mata
 */
class Rol extends Query implements BasicInterface
{
	private $base_datos;
	
    function __construct($conexion = null, $base_datos = BD_GENERAL)
    {
        parent::__construct($conexion);
        $this->conn->seleccionaBD($base_datos);
        $this->base_datos = $base_datos;
        if($resultado = $this->conn->ejecutar("SHOW TABLES LIKE 'rol'"))
        {
            if($this->conn->total_filas($resultado) == 0)
                $this->conn->ejecutarDeArchivo($this->tabla());
        }
       
    }

    /**
     * Obtiene especifico rol
     * {@inheritDoc}
     * @see \clases\BasicInterface::obtener()
     */
    function obtener($id)
    {
    	$rol = $this->consulta("*", "rol", $this->base_datos, "RolID = $id");
    	if(count($rol) == 0)
    		return 0;
    		return $rol[0];
    }
    /**
     * Agrega rol a la base de datos
     * {@inheritDoc}
     * @see \clases\BasicInterface::agregar()
     */
    public function agregar($datos,$usuario)
    {
    	return $this->insertar("rol", $datos, $this->base_datos,$usuario);
    }
    
    /**
     * Elimina Rol
     * {@inheritDoc}
     * @see \clases\BasicInterface::borrar()
     */
    public function borrar($datos,  $usuario)
    {
    	return $this->borrar("rol", $datos->RolID, "RolID", $this->base_datos,$usuario);
    }
    
   /**
    * Modifica Rol
    * {@inheritDoc}
    * @see \clases\BasicInterface::editar()
    */
    public function editar($datos,$id, $usuario)
    {
    	return $this->modificar("rol", $datos, $id, "RolID", $this->base_datos, $usuario);
    }
        
    
    /**
     * Regresa lista de Rol
     * {@inheritDoc}
     * @see \clases\BasicInterface::lista()
     */
    public function lista($seleccionado = "0", $ordenado = "0", $condicion = "0")
    {
        return $this->options("RolID as id, RolNombre as nombre","rol", 
            $this->base_datos,"id", $seleccionado, $condicion, $ordenado);
    }

    
    /**
     * Regresa el sql de la tabla usuarios
     */
    private function tabla()
    {
        $sql = "
			CREATE TABLE `rol` (
			  `RolID` int(11) NOT NULL AUTO_INCREMENT,
			  `RolNombre` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
              `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`RolID`)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

		INSERT INTO `rol` (`RolID`, `RolNombre`, `lastupdate`) VALUES
		(1, 'PROGRAMADOR', '2017-06-21 18:09:40'),
		(2, 'ADMINISTRADOR', '2017-06-21 18:09:50'),
		(3, 'SUPERVISOR', '2017-06-21 18:10:00'),
		(4, 'USUARIO', '2017-06-21 18:10:10');";
        return $sql;
    }
}