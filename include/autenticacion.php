<?PHP
/*
Registration/Login script from HTML Form Guide
V1.0

This program is free software published under the
terms of the GNU Lesser General Public License.
http://www.gnu.org/copyleft/lesser.html
  
This program is distributed in the hope that it will
be useful - WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.

For updates, please visit:
http://www.html-form-guide.com/php-form/php-registration-form.html
http://www.html-form-guide.com/php-form/php-login-form.html
*/

class Autenticacion
{
    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $dbHost;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    
    var $error_message;
    
    //-----Initialization -------
    function Autenticacion()
    {
        $this->sitename = 'localhost';
        $this->rand_key = '0iQx5oBk66oVZep';
		
    }
    
	function setDatabase($database) {
		$this->database = $database;
	}
	
	function getDatabase() {
		return $this->database;
	}
	
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
        
    function Login($pdo)
    {
        if(empty($_POST['txtUsuario']))
        {
            $this->HandleError("El usuario está vacío.");
            return false;
        }
        
        if(empty($_POST['txtPassword']))
        {
            $this->HandleError("La clave está vacía.");
            return false;
        }
        
        $usuario = trim($_POST['txtUsuario']);
        $password = trim($_POST['txtPassword']);
                       
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($usuario,$password, $pdo))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $usuario;
        
        return true;
    }
    
	
    function CheckLogin()
    {
         if(!isset($_SESSION)) { 
            session_start(); 
         }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
    function getLoginUsuario()
    {
        return isset($_SESSION['login_usuario'])?$_SESSION['login_usuario']:'';
    }
    
    function getCdUsuario()
    {
        return isset($_SESSION['cd_usuario'])?$_SESSION['cd_usuario']:'';
    }
    
    function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;

		//se eliminan todas las variables de sesión	
		session_unset();
    }
       
    
    
    //-------Public Helper functions -------------
        
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function GetSelfScript()
    {
        //echo " $_server:: " . $_SERVER['PHP_SELF'];
        return htmlentities($_SERVER['PHP_SELF']);
    }    
	
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
	
    function SanitizeForSQL($str)
    {
		$ret_str = addslashes( $str );
        return $ret_str;
    }
	
    
    function CheckLoginInDB($username,$password, $pdo)
    {           
        $numFilas = 0;
        $consulta = "";
		
        $username = $this->SanitizeForSQL($username);		
        
        $consulta = "SELECT LOGIN_USUARIO, CD_USUARIO, ES_USUARIO_ADMIN, VER_INFO_SENSIBLE " .
			" FROM USUARIOS WHERE login_usuario = '$username' " .
			" and clave_usuario = md5('$password')" . 
			" and esta_activo = 1 ";
        //echo $consulta;
		
        $nresult = $pdo->pdoGetRow($consulta);
                         
        if($nresult) {
            $_SESSION['login_usuario']  = $nresult['LOGIN_USUARIO'];
            $_SESSION['cd_usuario'] = $nresult['CD_USUARIO'];
            $_SESSION['es_admin'] = $nresult['ES_USUARIO_ADMIN'];
            $_SESSION['ver_infosen'] = $nresult['VER_INFO_SENSIBLE'];
            $numFilas++;            
         } 
         
         return $numFilas;
    }

        
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }    
	
    function obtenerPermisosUsuario($cdUsuario) {
        $sql = "select e.nm_entidad as entidad, a.nm_accion as accion, a.forma_accion as forma " . 
               " from entidades e, usuario_perfiles up, " .
			   " perfil_acciones pa, acciones a  " . 
               " where up.cd_usuario =  " . $cdUsuario .
               " and up.cd_perfil = pa.cd_perfil and " .
               " a.cd_accion = pa.cd_accion and " . 
               " a.cd_entidad = e.cd_entidad " . 
               " order by e.orden_entidad, a.orden_accion";               
        return $sql;       
    }
    
    function formatearPermisos($result) {
        $cadena = "<ul class=\"goo-collapsible goo-coll-stacked\">";
        $cadena .= "<li class=\"header\">Usuario: <b>". strtoupper($_SESSION["login_usuario"]) . "</b></li>";
        $cadena .= "<li class=\"header\">Sucursal: <b>". strtoupper($_SESSION['suc_nombre']) . "</b></li>";
        $cadena .= "<li class=\"header\"><b>Menú Principal</b></li>";
        $cadena .= "<li class=\"dropdown\">";
        $aux = "";
        
        foreach($result as $fila) {
            
            if(!strcmp($aux, $fila['entidad'])) {
                //$cadena .= "<ul>";
                //$cadena .= $entidad;
                
            } else {
                if($aux) {
                    $cadena .= "</ul></li><li class=\"dropdown\">";
                    
                }    
                $cadena .= "<a href=\"#\">" .$fila['entidad'] . "</a>";
                $cadena .= "<ul>";
                //$cadena .= "<ul>";
                //$cadena .= "<ul>";
                //$cadena .= "<li>" . $fila["entidad"];
            }
            $aux = $fila["entidad"];
            
            $cadena .= "<li><a href=\"". "" ."\" onClick=\"return loadQueryResults('". $fila["forma"] . "');\">";
            $cadena .=  $fila["accion"];
            $cadena .= "</a></li>";            
            /*                 
            echo "Entidad " . $fila["entidad"];
            echo " - Accion " . $fila["accion"];
            echo " - Forma " . $fila["forma"];    
            echo "<br>";
            */    
        }
        
        $cadena .= "</ul></li>";
                //al final siempre añadir la opción de salir 
        $cadena .= "<li><a href=\"logout.php\">Salir</a></li>";      

        $cadena .= "</ul>";  //del colapsible      
                
        //echo $cadena;
        return $cadena;
    }
	
}
?>