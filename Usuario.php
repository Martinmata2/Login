<?php
namespace Clases\Login;

use Clases\MySql\Query;
use Clases\Login\Funciones\UsuarioF;
use Clases\Utilidades\Validar;
use Clases\Login\Datos\UsuarioD;
use Clases\Catalogos\BasedatosInterface;
use Clases\GridInterface;
use Clases\UltimoInterface;

//Definiciones para estandarizar valores
define("USU_ELIMINADO", 1);
define("USU_ACTIVO", 1);
define("USU_INACTIVO", 0);
define("USU_SUCCESS", 200);
define("USU_ERROR", 400);
define("USU_DATOS_VALIDOS",200);
define("USU_DATOS_INVALIDOS",400);
class Usuario extends Query implements BasedatosInterface, GridInterface, UltimoInterface
{   
    /**
     *
     * @var UsuarioF
     */
    protected $Usuario;
    /**
     *
     * @var string
     */
    protected $Tabla;
    /**
     *
     * @var string
     */
    public $mensaje;
    public function __construct($base_datos = BD_GENERAL)
    {
        parent::__construct($base_datos);
        
        $this->Usuario = new UsuarioF(null, $base_datos);
        $this->base_datos = $base_datos;
        $this->mensaje = array();
        $this->Tabla = $this->Usuario->table();
                
        
        //Inicar tabla
        $this->Usuario->create();
        $this->Usuario->update();
    }

    public function borrar($id, $campo = "UsuID",  $usuario = 0)
    {
        if($this->Usuario->isAdmin($_SESSION["USR_ROL"]))
            return $this->modificar($this->Tabla, array("deleted"=>USU_ELIMINADO), $id, $campo, $usuario);
            else return 0;
    }

    public function validar()
    {
        $this->mensaje = array();
        //Nombre
        if(!$this->Unico("UsuNombre", $this->Usuario->data->UsuNombre))
        {            
            $this->mensaje["UsuNombre"] = "Este usuario ya esta registrado";                
        }
        //Usuario
        if(!$this->Unico("UsuUsuario", $this->Usuario->data->UsuUsuario))
        {
            $this->mensaje["UsuUsuario"] = "Este usuario ya esta registrado";            
        }
        
        if(count($this->mensaje) > 0)
        {
            $this->mensaje["status"] = USU_DATOS_INVALIDOS;
            return $this->mensaje;
        }
        else
        {
            $this->mensaje["status"] = USU_DATOS_VALIDOS;
            return true;
        }
    }

    public function obtener($id = 0, $campo = "UsuID", $condicion = "0")
    {
        if($id <= 0)
        {
            $resultado = $this->consulta("*", $this->Tabla, "deleted <> ".USU_ELIMINADO);
            if (\count($resultado) > 0)
                return $resultado;
                else return 0;
        }
        else
        {
            $resultado = $this->consulta("*", $this->Tabla, "$campo = '$id'", $condicion);
            if (\count($resultado) > 0)
                return $resultado[0];
                else
                    return 0;
        }
        return 0;
    }

    public function editar($datos, $id,$campo = "UsuID",$condicion = "0", $usuario = "1")
    {
        if($this->Usuario->isAdmin($_SESSION["USR_ROL"]))
        {
            return $this->modificar($this->Tabla, $datos, "$id", $campo, $usuario);
        }
        else return 0;
    }

    public function agregar($datos)
    {
        if($this->Usuario->isAdmin($_SESSION["USR_ROL"]))
        {
            $this->Usuario->data = new UsuarioD($datos);
            if($this->validar())
            {               
                return $this->insertar($this->Tabla, $datos);
            }
            else return 0;
        }
        else return 0;
    }      
   
    public function Unico($campo, $valor)
    {
        $respuesta = $this->consulta("*", $this->Tabla,"$campo = '$valor'");
        if(count($respuesta) > 0)
            return false;
        else return true;
    }
    
    
    
    public function existe($id)
    {
        $respuesta = $this->consulta("*", $this->Tabla,"UsuID = '$id'");
        if(count($respuesta) > 0)
            return true;
        else return false;
    }
    public function grid($arguments = null)
    {
        return array(
            array("title"=> "ID",               "name"=>"UsuID",                "editable"=>false,              "width"=>"40",              "hidden"=>true,                 "export"=>false),
            array("title"=>"Nombre/<br>Clone",  "name"=>"UsuNombre",            "editable"=>true,               "width"=>"100",             "align"=>"center",
                "link"=>$arguments["link"],     "linkoptions"=>"class='box'"),
            array("title"=>"Rol",               "name"=>"UsuRol",               "editable"=>true,               "width"=>"40",              "align"=>"center",
                "editoptions"=>array("value"=>$arguments["rol"]),               "edittype"=>"select",           "op"=>"eq",                 "formatter"=>"select",
                "searchoptions"=>array("value"=>$arguments["rol"]),             "stype"=> "select" ),
            array("title"=>"Codigo",              "name"=>"UsuCodigo",          "editable"=>false,               "width"=>"100",             "align"=>"left",),
            array("title"=>"Activo",            "name"=>"UsuActivo",            "editable"=>true,               "width"=>"20",              "align"=>"left",
                "formatter"=>"checkbox",        "edittype"=>"checkbox",         "editoptions"=>array("value"=>"1:0")),
            array("title"=>"Permisos",          "name"=>"permisos",             "editable"=>false,              "width"=>"20",              "align"=>"left",                "search"=>false,
                "default"=>$arguments["permiso"])
        );
    }

    public function forma($arguments = null)
    {
       $code = sprintf('%012s', $this->Ultimo("UsuID", $this->Tabla));
       //echo $code;
        return " <div id='usuarioPanel'>
        	<form id='usuarioForm'>
        		<div class='row gx-5'>
        			<div class='col-lg-4 col-md-6'>
        				<input type='hidden' id='UsuID' name='UsuID' /> <input id='CSRF' type='hidden'
        					value='".$_SESSION["CSRF"]."' />
        				<div class='form-floating mb-3'>
        					<input class='form-control' id='UsuCodigo'
        						name='UsuCodigo' type='text' value='$code'  readonly /> <label for='UsuCodigo'>
        						Codigo</label>					
        				</div>
                    </div>
                    <div class='col-lg-4 col-md-6'>
        				<div class='form-floating mb-3'>
        					<input class='form-control border-danger valida' id='UsuNombre'
        						name='UsuNombre' type='text' data-type='nombre' data-unico='true' data-validar='Usuario' /> 
        					<label for='UsuNombre'> Nombre Completo</label>					
        				</div>
                    </div>
                    <div class='col-lg-4 col-md-6'>
                        <div class='form-floating mb-3'>
        					<input class='form-control border-danger valida' id='UsuUsuario'
        						name='UsuUsuario' type='text' data-type='usuario' data-unico='true' data-validar='Usuario' /> 
        					<label for='UsuUsuario'> Usuario</label>					
        				</div>
                    </div>
                    <div class='col-lg-4 col-md-6'>
                        <div class='form-floating mb-3'>
        					<input class='form-control border-danger valida' id='UsuClave'
        						name='UsuClave' type='password'  /> 
        					<label for='UsuClave'> Clave</label>					
        				</div>
                    </div>
                    <div class='col-lg-4 col-md-6'>
                        <div class='form-floating mb-3'>
        					<select class='form-select' id='UsuRol'
        						name='UsuRol'>".
        						$arguments["rol"]
        					."</select>
        					<label for='UsuRol'> Funci&oacute;n</label>					
        				</div>
                                				        				
        			</div>
        
        		
        			 <div class='col-lg-4 col-md-6'>
        				<!-- Submit Button-->
        				<div class='button-group text-center'>
        					<button class='btn btn-primary' id='submitButtonUsuario'
        						type='submit'>Enviar</button>
        					<button class='btn btn-danger' id='resetButtonUsuario'
        						type='reset'>Limpiar</button>
        				</div>
        			</div>
        		</div>
        	</form>
        </div>";
    }

    public function modal($arguments = null)
    {}   
    public function Ultimo(string $id, string $tabla)
    {
        return $this->ultiorecord($tabla, $id)[0]->ultimo+1;
    }



}

