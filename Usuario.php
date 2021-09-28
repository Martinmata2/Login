<?php
namespace clases\Login;

/**
 *
 * @version v2020_2
 * @author Martin Mata
 */
use clases\MySql\Conexion;
use clases\MySql\Query;
use clases\BasicInterface;
use clases\Utilidades\AjustesEspeciales;

class Usuario extends Query implements BasicInterface
{

    protected $base_datos;

    /**
     * Inicia Objeto Usuario
     *
     * @param Conexion $conexion
     * @param string $base_datos
     */
    function __construct($conexion = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($conexion);
        $this->conn->ejecutar("CREATE DATABASE IF NOT EXISTS $base_datos DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish2_ci");
        $this->conn->seleccionaBD($base_datos);
        if ($resultado = $this->conn->ejecutar("SHOW TABLES LIKE 'usuarios'")) {
            if ($this->conn->total_filas($resultado) == 0)
                $this->conn->ejecutarDeArchivo($this->tabla());
        }
    }

    /**
     * Regresa lista de usuarios
     *
     * {@inheritdoc}
     * @see \clases\BasicInterface::lista()
     */
    public function lista($seleccionado = "0", $ordenado = "0", $condicion = "0")
    {
        return $this->options("UsuID as id, UsuNombre as nombre", "usuarios", $this->base_datos, $seleccionado, $condicion, $ordenado);
    }

    /**
     * Elimina (Inactivo) Usuario
     *
     * {@inheritdoc}
     * @see \clases\BasicInterface::borrar()
     */
    public function borrar($datos, $usuario)
    {
        return $this->modificar("usuarios", array(
            "UsuActivo" => 0
        ), $datos->UsuID, "UsuID", $this->base_datos, $usuario);
    }

    /**
     * Obtiene Usuario
     *
     * {@inheritdoc}
     * @see \clases\BasicInterface::obtener()
     */
    public function obtener($id)
    {
        $resultado = $this->consulta("*", "usuarios", $this->base_datos, "UsuID = $id");
        if (\count($resultado) > 0)
            return $resultado[0];
        else
            return 0;
    }

    /**
     * Modificar Usuario
     *
     * {@inheritdoc}
     * @see \clases\BasicInterface::editar()
     */
    public function editar($datos, $id, $usuario)
    {
        return $this->modificar("usuarios", $datos, $id, "UsuID", $this->base_datos, $usuario);
    }

    /**
     * Agrega Usuario
     *
     * {@inheritdoc}
     * @see \clases\BasicInterface::agregar()
     */
    public function agregar($datos, $usuario)
    {
        return $this->insertar("usuarios", $datos, $this->base_datos, $usuario);
    }
    
    /**
     * Clona permisos y permisos especiales de usuario 
     * @param \stdClass $datos
     * @param int $clone
     * @param int $usuario
     * @return number
     */
    public function clonar($datos,$clone, $usuario)
    {        
        $PERMISOS = new Permisos();
        $resultado = $this->agregar($datos, $usuario);
        $permisos = new \stdClass();
        $permisos->PusPermisos = $PERMISOS->obtener($clone);
        $permisos->PusUsuario = $resultado;
        $permisos->PusID = null;
        $PERMISOS->agregar($permisos, $usuario);
        $ESPECIALES = new AjustesEspeciales();
        $especiales = $ESPECIALES->obtenerTodos($clone);
        foreach ($especiales as $value) 
        {
            $value->AjuID = null;
            $value->AjuUsuario = $resultado;
            $ESPECIALES->agregar($value, $usuario);
        }
        return $resultado;
    }

    /**
     * Ultima modificacion del catalogo de Usuarios
     *
     * @return string
     */
    public function ultima_modificacion()
    {
        if (isset($_SESSION["CLI_EMPRESA"])) {
            $last = $this->consulta("date(lastupdate)as lastupdate", "usuarios", $this->base_datos, "UsuEmpresa = '" . $_SESSION["CLI_EMPRESA"] . "'", "lastupdate");
        } else {
            $last = $this->consulta("date(lastupdate)as lastupdate", "usuarios", $this->base_datos, "0", "lastupdate");
        }
        return $last[0]->lastupdate;
    }
    
    /**
     * Verifica el permiso del usuario 
     * @param string $permiso
     * @param string $usuario
     * @param string $clave
     * @return number
     */
    public function permiso($permiso,$usuario,$clave)
    {
        $id = $this->consulta("UsuID","usuarios", $this->base_datos,"UsuUsuario = '$usuario' AND UsuClave = '$clave'")[0]->UsuID;
        $especial = new AjustesEspeciales(null, $this->base_datos);
        
        if($especial->obtener($permiso, $id))
        {            
            return 1;
        }
        else 
        {            
            return 0;
        }
        
    }

    /**
     * Regresa el sql de la tabla usuarios
     */
    private function tabla()
    {
        $sql = "
        	CREATE TABLE IF NOT EXISTS `usuarios` (
          `UsuID` int(11) NOT NULL AUTO_INCREMENT,
          `UsuCliente` int(11) NOT NULL COMMENT 'id del Cliente',
          `UsuNombre` tinytext NOT NULL,
          `UsuUsuario` varchar(48) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuClave` varchar(48) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuRol` tinyint(4) UNSIGNED NOT NULL,
          `UsuEmail` tinytext NOT NULL,
          `UsuToken` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuBd` varchar(40) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuCodigo` tinytext COLLATE utf8_spanish2_ci NOT NULL,
          `UsuActivo` tinyint(1) NOT NULL,
		  `UsuEmpresa` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `updated` tinyint(1) NOT NULL,
		  `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `UsuClavePAC` tinytext COLLATE utf8_spanish2_ci NOT NULL,
		  `UsuUsuarioPAC` tinytext COLLATE utf8_spanish2_ci NOT NULL,
			PRIMARY KEY (`UsuID`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
            
        ALTER TABLE `usuarios` ADD UNIQUE KEY `UsuUsuario` (`UsuUsuario`);
		INSERT INTO `usuarios` (`UsuID`, `UsuCliente`, `UsuNombre`, `UsuUsuario`, `UsuClave`, `UsuRol`,
			`UsuEmail`, `UsuToken`, `UsuBd`, `UsuCodigo`, `UsuActivo`, `UsuEmpresa`, `updated`)
			VALUES	(1, 1, 'ROOT', '3482a7f8ec5b6e9d19fe48be6459c97a', '0b10b29cf24c04543d4faa2e48d3cd6c', 1,
			'', 'abc', '" . $this->base_datos . "', '100000000001', 1, '', 1),
		    (2, 2, 'EMPRESA', 'b7c1bd35920a87029e6afdc30d5e8f70', '0b10b29cf24c04543d4faa2e48d3cd6c', 1,
			'', 'abc', '" . $this->base_datos . "', '100000000002', 1, '', 1);";

        return $sql;
    }
}

