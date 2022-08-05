<?php
namespace Clases\Login\Funciones;

use Clases\FuncionInterface;
use Clases\login\Datos\UsuarioD;
use Clases\PermisosBD;

class UsuarioF extends PermisosBD implements FuncionInterface
{

    /**
     *
     * @var UsuarioD
     */
    public $data;

    

    public function __construct($datos = null, $base_datos = BD_GENERAL)
    {
        $this->base_datos = $base_datos;
        parent::__construct($base_datos);        
        $this->data = new UsuarioD($datos);
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
        return "usuarios";
    }

    /**
     * Codigo sql para Tabla
     *
     * @return string
     */
    private function sql()
    {
        //sql code para generar la tabla
        $sql = "CREATE TABLE IF NOT EXISTS `usuarios` (
          `UsuID` int(11) NOT NULL AUTO_INCREMENT,
          `UsuCliente` int(11) NOT NULL COMMENT 'id del Cliente',
          `UsuNombre` tinytext COLLATE utf8_spanish2_ci NOT NULL,
          `UsuUsuario` varchar(48) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuClave` varchar(48) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuRol` tinyint(4) unsigned NOT NULL,
          `UsuEmail` tinytext COLLATE utf8_spanish2_ci NOT NULL,
          `UsuToken` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuBd` varchar(40) COLLATE utf8_spanish2_ci NOT NULL,
          `UsuCodigo` tinytext COLLATE utf8_spanish2_ci NOT NULL,
          `UsuActivo` tinyint(1) NOT NULL,
          `UsuEmpresa` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
          `updated` tinyint(1) NOT NULL,
          `lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
          `UsuClavePAC` tinytext COLLATE utf8_spanish2_ci NOT NULL,
          `UsuUsuarioPAC` tinytext COLLATE utf8_spanish2_ci NOT NULL,
          `token` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
          `session_id` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
          PRIMARY KEY (`UsuID`),
          UNIQUE KEY `UsuUsuario` (`UsuUsuario`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci";
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
        $update = "
        IF NOT EXISTS( SELECT NULL FROM INFORMATION_SCHEMA.COLUMNS
           WHERE table_name = '".$this->table()."'
             AND table_schema = '".$this->base_datos."'
             AND column_name = 'token')  THEN
         ALTER TABLE `".$this->table()."` ADD `token` varchar(45) COLLATE utf8_spanish2_ci NOT NULL; 
        END IF;
        IF NOT EXISTS( SELECT NULL FROM INFORMATION_SCHEMA.COLUMNS
           WHERE table_name = '".$this->table()."'
             AND table_schema = '".$this->base_datos."'
             AND column_name = 'session_id')  THEN
         ALTER TABLE `".$this->table()."` ADD `session_id` varchar(45) COLLATE utf8_spanish2_ci NOT NULL; 
        END IF;
        ";                
        return $update;
    }
}

