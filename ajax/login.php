<?php
use Clases\Login\Personal;

/**
 * Intenta logear en el sistema y cuenta el numero de intentos fallidos
 */
/**
 *
 * @version v2020_2
 * @author Martin Mata
 */
@session_start();

global $base_datos;
$base_datos = $_POST["base_datos"];
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . "/../../../"));
include_once 'autoload.php';

if (isset($_POST["form_data"]["usuario"]) && isset($_POST["form_data"]["clave"])) {
    /*
     * if(isset($_SESSION["PROGRAMA_MODE"]) && $_SESSION["PROGRAMA_MODE"] == "empresa")
     * $LOGIN = new Empresas();
     * else $LOGIN = new Privada();
     */
    $LOGIN = new Personal();
    $resultado = $LOGIN->ajaxCall($_POST["form_data"]["usuario"], $_POST["form_data"]["clave"]);
    if ($resultado == 0)
        $_SESSION["intentos"] += 1;
    echo $resultado;
}
?>