<?php
namespace Clases\Login;


use Clases\MySql\Query;
use Clases\BasicInterface;

/**
 * @version v2020_2
 * @author Martin Mata
 */
class Modulos extends Query implements BasicInterface
{
	private $base_datos;
    function __construct($conexion = null, $base_datos = BD_GENERAL)
    {
        parent::__construct($conexion);
        $this->conn->seleccionaBD($base_datos);
        $this->base_datos = $base_datos;
        if($resultado = $this->conn->ejecutar("SHOW TABLES LIKE 'modulos'"))
        {
            if($this->conn->total_filas($resultado) == 0)
                $this->conn->ejecutarDeArchivo($this->tabla());
        }
        
    }
    
    /**
     * Regresa el modulo al que la pagina pertenece
     * {@inheritDoc}
     * @see \clases\BasicInterface::obtener()
     */
    function obtener($id)
    {
    	$modulo = $this->consulta("ArcModulo", "archivos", $this->base_datos, "ArcPath = '$id'");
    	if(count($modulo) == 0)
    		return 0;
    		return $modulo[0]->ArcModulo;
    }    
    
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::agregar()
     */
    public function agregar($datos, $usuario)
    {
    	return $this->insertar("modulos", $datos, $this->base_datos,$usuario);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::borrar()
     */
    public function borrar($datos, $usuario)
    {
    	return $this->borrar("modulos", $datos->ModID, "ModID", $this->base_datos,$usuario);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::editar()
     */
    public function editar($datos,$id,$usuario)
    {
    	return $this->modificar("modulos", $datos, $id, "ModID", $this->base_datos, $usuario);
    }
       
    
    /**
     * 
     * {@inheritDoc}
     * @see \clases\BasicInterface::lista()
     */
    public function lista($seleccionado = "0", $ordenado = "0", $condicion = "0")
    {
        return $this->options("ModID as id, ModNombre as nombre","modulos", 
            $this->base_datos,"id", $seleccionado, $condicion, $ordenado);
    }


    
    /**
     * Regresa lista de pagfinas en modulo
     * @param int $modulo
     * @return number|array
     */
    function paginas($modulo)
    {
        return $this->consulta("ArcID,ArcNombre,ArcPath,ArcIcon", "archivos", $this->base_datos, "ArcModulo = $modulo AND ArcSubModulo = 0", "ArcOrden");
    }

    /**
     * Regresa el sql de la tabla usuarios
     */
    private function tabla()
    {
        $sql = "
			CREATE TABLE IF NOT EXISTS `modulos` (
			  `ModID` int(11) NOT NULL AUTO_INCREMENT,
			  `ModNombre` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
              `ModRol` int(11) NOT NULL,
              `ModOrden` int(3) NOT NULL,
              `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`ModID`)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

			INSERT INTO `modulos` (`ModID`, `ModNombre`,`ModRol`, `ModOrden`, `lastupdate`) VALUES
			(1, 'PROGRAMA', 1, 8,'2017-06-21 18:19:37'),
			(2, 'ADMINISTRADOR', 2,4, '2017-06-21 18:20:06'),
			(3, 'CATALOGOS', 3,2, '2017-06-21 18:20:20'),
			(4, 'PUNTO VENTA', 3,1, '2017-06-21 18:20:28'),
			(5, 'INVENTARIO', 3,3, '2017-06-21 18:20:37'),
            (6, 'REPORTES', 3, 5,'2017-06-21 18:20:37'),
            (7, 'BANCO', 3,7, '2017-06-21 18:20:37'),
            (8, 'WEB', 3,6, '2017-06-21 18:20:37');";
        return $sql;
    }
}