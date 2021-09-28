<?php
namespace clases\Login;
use clases\MYySql\Query;
use clases\BasicInterface;

/**
 * Maneja permisos de la aplicacion
 * @version v2020_2
 * @author Martin Mata
 */
class Permisos extends Query implements BasicInterface
{
	private $base_datos;
	function __construct($conexion = null, $base_datos = BD_GENERAL)
	{
	    $this->base_datos = $base_datos;
		parent::__construct($conexion);
		$this->conn->seleccionaBD($base_datos);
		if($resultado = $this->conn->ejecutar("SHOW TABLES LIKE 'permisosusuarios'"))
		{
			if($this->conn->total_filas($resultado) == 0)
				$this->conn->ejecutarDeArchivo($this->tabla());
		}
		
	}
		
	/**
	 * Obtiene el permiso
	 * {@inheritDoc}
	 * @see \clases\BasicInterface::obtener()
	 */
	public function obtener($id)
	{
		$permiso = $this->consulta("*", "permisosusuarios", $this->base_datos, "PusUsuario = $id");		
		if(count($permiso) == 0)
			return 0;
		return $permiso[0]->PusPermisos;
	}	
	
	/**
	 * Agrega permiso a BD
	 * {@inheritDoc}
	 * @see \clases\BasicInterface::agregar()
	 */
	public function agregar($datos, $usuario)
	{
		return $this->reemplazar("permisosusuarios", $datos, $this->base_datos);
	}
		
	
	/**
	 * borra permiso 
	 * {@inheritDoc}
	 * @see \clases\BasicInterface::borrar()
	 */
	public function borrar($datos,  $usuario)
	{
		return $this->borrar("permisosusuarios", $datos->PusID, "PusID", $this->base_datos,$usuario);
	}
		
	
	/**
	 * Modifica permiso
	 * {@inheritDoc}
	 * @see \clases\BasicInterface::editar()
	 */
	public function editar($datos,$id, $usuario)
	{
		return $this->modificar("permisosusuarios", $datos, $id, "PusID", $this->base_datos, $usuario);
	}
		
	
	
	/**
	 * Mostrar lista de paginas para asignar permisos
	 * @param number $usuario
	 */
	public function mostrar($rol = 0)
	{
		//$modulo = "0";
		$html = "<div class='grid-x grid-margin-x fluid'>";
		$secciones = $this->consulta("*","modulos", $this->base_datos, "ModRol >= $rol", "ModOrden asc");
		$count = 0;		
		foreach ($secciones as $seccion)
		{
			if($count++ > 2)
			{
				$count = 0;
				$end = "end";
				$html .= "<hr>";
			}
			else $end = "";
		    $html .= "<div class='large-4 medium-4 cell $end'>";
		    $html .= "    <h5> ".$seccion->ModNombre."  </h5>";
		      $html .= "  <ul class='tree1' style='margin-left:0.5rem;'>";
		    $permisos = $this->consulta("*", "archivos", $this->base_datos, "ArcModulo = ".$seccion->ModID, "ArcOrden");
		          $html .= "<li style='vertical-align: middle;'>
		                  <input type='checkbox' value='0' />
				          <label><b> -Todos- </b><br/></label>";
		          $html .= "  <ul style='display: table'>";
		    foreach ($permisos as $permiso) 
		    {
		          $html .= "      <li style='vertical-align: middle;'>
		                          <input type='checkbox' id='id".$permiso->ArcID."' value='".$permiso->ArcID."' />
		                              <label  style='font-size: medium !important;'> - ".$permiso->ArcNombre."</label>
		                      </li>";                       
		        
		    }
		        
		    $html .= "        </ul>";
		    $html .= "      </li>";
		    $html .= "    </ul>";
		    $html .= "</div>";
		   
		}
		$html .= "</div>";
		return $html;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \clases\BasicInterface::lista()
	 */
	public function lista($seleccionado, $ordenado, $condicion)
	{
	    //TODO
	    return 0;
		//return $this->options("UsuID as id, UsuNombre as nombre","usuarios", $this->base_datos,"id", $seleccionado);
	}


	/**
	 * Obtiene pagina correspondiente al permiso
	 * @param int $id
	 * @return string|number
	 */
	function obtener_pagina($id)
	{
		$resultado = $this->consulta("ArcPath", "archivos", $this->base_datos, "ArcID = $id");
		if(count($resultado)>0)
			return $resultado[0]->ArcPath;
		else
			return 0;
	}

	/**
	 * Obtiene permisos
	 * @param int $id
	 * @return string|number
	 */
	function usuario_permisos($id)
	{
		$resultado = $this->consulta("*", "permisosusuarios", $this->base_datos, "PusUsuario = $id");
		if(count($resultado)>0)
			return $resultado[0]->PusPermisos;
		else
			return 0;
	}
	
	/**
	 * verifica que se tenga permiso asignado a la pagina
	 * @param string $pagina
	 * @param int $usuario
	 * @return number
	 */
	function verificar_permiso($pagina,$usuario)
	{
		$result = $this->consulta("ArcID", "archivos", $this->base_datos,"ArcPath like '%$pagina%'");
		if(count($result) > 0)
		{
			$results = $this->consulta("*", "permisosusuarios", $this->base_datos,
					"PusUsuario = $usuario" );
			if(\count($results)>0)
			{
				$permisos = $results[0]->PusPermisos;
				$permisos = \explode(',', $permisos);
				if( \in_array($result[0]->ArcID, $permisos))
					return 1;
				else return 0;
			}
			else
				return 0;// (isset($results[0]->PusID)?1:0);
		}
		else
		{
			echo "el archivo $pagina no se encontro";
			return 0;
		}
		
	}
	
	/**
	 * Regresa el sql de la tabla usuarios
	 */
	private function tabla()
	{
		$sql = "			
			
			CREATE TABLE IF NOT EXISTS `permisosusuarios` (
			  `PusID` int(11) NOT NULL AUTO_INCREMENT,
			  `PusUsuario` int(11) NOT NULL,
			  `PusPermisos` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
			  `updated` int(1) NOT NULL,
		      `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`PusID`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
		    ALTER TABLE `permisosusuarios`
	           ADD UNIQUE KEY `PusUsuario` (`PusUsuario`);

            INSERT INTO `permisosusuarios` (`PusID`, `PusUsuario`, `PusPermisos`, `updated`, `lastupdate`) 
            VALUES (1, 2, '1,2,3,4,5,16,17,11,14,8,6,12,9,10,13,18,7,15,19,20,21,22', 0, '2018-05-11 23:46:12'),
                   (2, 1, '1,2,3,4,5,16,17,11,14,8,6,12,9,10,13,18,7,15,19,20,21,22', 0, '2018-05-11 23:46:12');
		    ";
	
		return $sql;
	}
}