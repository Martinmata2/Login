<?php
namespace clases\Login;

/**
 *
 * @version v2020_2
 * @author Martin Mata
 */
class Acceso extends Usuario
{

    /**
     * Intenta logear en el sistema
     * Inserta en las cookies usuario y contrasena
     *
     * @param string $usuario
     * @param string $clave
     * @return array|int en caso de error regresa 0 de otra manera regresa el resultado de la consulta
     */
    public function log($usuario, $clave)
    {
        $usuario = $this->conn->escape($usuario);
        $clave = $this->conn->escape($clave);
        $login = $this->consulta("*", "usuarios", $this->base_datos, "(UsuUsuario = '" . $usuario . "' AND UsuClave = '" . $clave . "') AND UsuActivo = 1");
        if (count($login) > 0) {
            @setcookie("auth", session_id(), time() + 60 * 60 * 24, '/');
            $this->resetValores($login[0]);
            $this->sessionIniciar($login[0]->UsuID, $this->base_datos);
            return $login[0];
        } else
            return 0;
    }

    /**
     *
     * Asigna la session a UsuToken
     *
     * @param int $id
     * @return int
     */
    private function sessionIniciar($id)
    {
        return ($this->modificar("usuarios", array(
            "UsuToken" => session_id()
        ), $id, "UsuID", $this->base_datos) !== 0);
    }

    /**
     * Destruye la session
     *
     * @param int $id
     *            ID del usuario
     */
    public function sesionDestruir($id)
    {
        @session_destroy();
        $this->modificar("usuarios", array(
            "UsuToken" => 0
        ), $id, "UsuID", $this->base_datos);
        unset($_COOKIE['auth']);
        @setcookie("auth", "", - 1);
        @setcookie("auth", "", - 1, "/");
        @session_destroy();
    }

    /**
     * Activa el recien creado usuario
     *
     * @param int $uid
     * @param string $actcode
     */
    public function activarUsuario($uid, $actcode)
    {
        if ($this->consulta("UsuActivo", "usuarios", $this->base_datos, "(UsuID = '$uid' and UsuCodigo = '$actcode') and UsuActivo = 0") !== 0) {
            return $this->modificar("usuarios", array(
                "UsuActivo" => 1
            ), "UsuID", '$uid', $this->base_datos);
        }
        return 0;
    }

    /**
     * Crea una cadena aleatoria con un numero de caracteres
     *
     * @param int $length
     * @return string clave de $lenth caracteres
     */
    public function crearllave($length = 10)
    {
        if ($length <= 0) {
            return false;
        }
        $code = "";
        $chars = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
        srand((double) microtime() * 1000000);
        for ($i = 0; $i < $length; $i ++) {
            $code = $code . substr($chars, rand() % strlen($chars), 1);
        }
        return $code;
    }

    /**
     * Verifica si El Cliente tiene iniciada Session
     *
     * @return bool
     */
    public function estaLogueado()
    {
        @session_start();
        if (isset($_SESSION["CLI_BD"]))
            return true;
        elseif (isset($_COOKIE['auth']) && $_COOKIE['auth'] !== false) {
            $credenciales = $this->consulta("*", "usuarios", $this->base_datos, "UsuToken = '" . session_id() . "'");
            return @$this->log($credenciales[0]->UsuUsuario, $credenciales[0]->UsuClave) !== 0;
        } else
            return false;
    }

    /**
     * Asigna el valor del campo UsuUsuario al campo UsuClave
     *
     * @param string $usuario
     * @param string $email
     * @return bool
     */
    public function resetearClave($usuario, $email)
    {
        if (! $this->userExists($usuario)) {
            return false;
        } else {
            $datos = $this->consulta("UsuMail", "usuarios", $this->base_datos, "UsuUsuario = '$usuario'");
            if ($datos[0]->UsuMail == $email)
                return $this->modificar("usuarios", array(
                    "UsuClave" => $usuario
                ), $usuario, "UsuUsuario", $this->base_datos) !== 0;
            else
                return false;
        }
    }

    /**
     * Cambia la clave existente con clave nueva
     *
     * @param int $usuario
     * @param string $claveAntigua
     * @param string $claveNueva
     */
    public function cambiarClave($usuario, $claveAntigua, $claveNueva)
    {
        $datos = $this->consulta("*", "usuarios", $this->base_datos, "UsuUsuario = '$usuario' AND UsuClave = '$claveAntigua'");
        if (count($datos) > 0)
            return $this->modificar("usuarios", array(
                "UsuClave" => $claveNueva
            ), $datos->UsuID, "UsuID", $this->base_datos) !== 0;
        else
            return false;
    }

    /**
     *
     * Verifica que el Usuario Exista
     *
     * @param string $username
     * @return bool
     */
    private function userExists($username)
    {
        return ($this->consulta("UsuID", "usuarios", $this->base_datos, "UsuUsuario = '$username'") !== 0);
    }

    /**
     *
     * Verifica que el email tenga el formato correcto
     *
     * @param string $email
     * @return bool
     */
    private function validEmail($email)
    {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (! preg_match('/' . "^[^@]{1,64}@[^@]{1,255}$" . '/', $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i ++) {
            if (! preg_match('/' . "^(([A-Za-z0-9!#$%&#038;'*+=?^_`{|}~-][A-Za-z0-9!#$%&#038;'*+=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$" . '/', $local_array[$i])) {
                return false;
            }
        }
        if (! preg_match('/' . "^\[?[0-9\.]+\]?$" . '/', $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false;
            }
            for ($i = 0; $i < sizeof($domain_array); $i ++) {
                if (! preg_match("/" . "^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$" . "/", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Asigna valores en session
     *
     * @param \stdClass $usr
     * @return boolean
     */
    private function resetValores($usr)
    {
        $_SESSION["USR_ROL"] = $usr->UsuRol;
        $_SESSION["USR_NOMBRE"] = $usr->UsuNombre;
        $_SESSION["USR_ID"] = $usr->UsuID;
        $_SESSION["CLI_BD"] = $usr->UsuBd;
        $_SESSION["CLI_EMPRESA"] = $usr->UsuEmpresa;
        $_SESSION["USUARIOPAC"] = $usr->UsuUsuarioPAC;
        $_SESSION["CLAVEPAC"] = $usr->UsuClavePAC;
        /**
         * *** Aqui se pueden agregar otros valores necesarios en session ******
         */
        return true;
    }
}