<?php
class Usuario {
	private $cd_usuario;
	private $nm_usuario;
	private $login_usuario;
	private $clave_usuario;
	private $email_usuario;
	private $obs_usuario;
	private $es_usuario_admin;
	private $ver_info_sensible;
	private $esta_activo;
	
	function __construct() {
	}
	
	function setUsuario($cd_usuario, $nm_usuario, $login_usuario, $clave_usuario, $email_usuario, 
	$obs_usuario, $es_usuario_admin, $ver_info_sensible ,$esta_activo) {
		$this->cd_usuario = $cd_usuario;
		$this->nm_usuario = $nm_usuario;
		$this->login_usuario = $login_usuario;
		$this->clave_usuario = $clave_usuario;
		$this->email_usuario = $email_usuario;
		$this->obs_usuario = $obs_usuario;
		$this->es_usuario_admin = $es_usuario_admin;
		$this->ver_info_sensible = $ver_info_sensible;
		$this->esta_activo = $esta_activo;
	}
	
	function setNmUsuario($nm_usuario) {
		$this->nm_usuario = $nm_usuario;
	}

	function getClaveUsuario() {
		return $this->clave_usuario;
	}
	
	function getNmUsuario() {
		return $this->nm_usuario;
	}
	
	function getObsUsuario() {
		return $this->obs_usuario;
	}
	
	function getEstaActivo() {
		return $this->esta_activo;	
	}
	
	function setCdUsuario($cd_usuario) {
		$this->cd_usuario = $cd_usuario;
	}
	
	function getCdUsuario() {
		return $this->cd_usuario;
	}
	
	function getLoginUsuario() {
		return $this->login_usuario;
	}
	
	function getEsUsuarioAdmin() {
		return $this->es_usuario_admin;
	}
	
	function getVerInfoSensible() {
		return $this->ver_info_sensible;
	}
	    	
	function crearUsuario() {		
		$sql = "insert into usuarios(cd_usuario, nm_usuario, login_usuario, clave_usuario, email_usuario, obs_usuario, es_usuario_admin, ver_info_sensible, esta_activo) values( " . 
		$this->cd_usuario . ", " .
		"'" . $this->nm_usuario . "', " .
		"'" . $this->login_usuario . "', " . 
		" md5('" . $this->clave_usuario . "'), " . 
		"null" . ", " .
		"'" . $this->obs_usuario . "', " .
		$this->es_usuario_admin . ", " . 
		$this->ver_info_sensible . ", " . 
		$this->esta_activo . ")";
		
		//echo $sql;
		return $sql;
	}
	
	function modificarUsuario() {
		$sql = "update usuarios set " .
			" nm_usuario = '" . $this->nm_usuario . "', " .
			" login_usuario = '" . $this->login_usuario . "', " . 
			" clave_usuario = md5('" . $this->clave_usuario . "'), " .
			" email_usuario = null, " . 
			" obs_usuario = '" . $this->obs_usuario . "', " . 
			" es_usuario_admin = " . $this->es_usuario_admin . ", " .
			" ver_info_sensible = " . $this->ver_info_sensible . ", " . 
			" esta_activo = " . $this->esta_activo . 
			" where cd_usuario = " . $this->cd_usuario;
			
		//echo "modificar " . $sql;	
		return $sql;	
	}
	
    function consultarUsuario() {
        $cons = "select * from usuarios where cd_usuario = " . $this->cd_usuario;
        return $cons;
    }

    function obtenerUsuario($fila) {
        //echo "===========Entrando a get usuario ===============";
        $this->cd_usuario = $fila["CD_USUARIO"];
        $this->nm_usuario = $fila["NM_USUARIO"];
        $this->login_usuario = $fila["LOGIN_USUARIO"];
        $this->clave_usuario = $fila["CLAVE_USUARIO"];
        $this->email_usuario = $fila["EMAIL_USUARIO"];
        $this->obs_usuario = $fila["OBS_USUARIO"];
        $this->es_usuario_admin  = $fila["ES_USUARIO_ADMIN"];
        $this->ver_info_sensible = $fila["VER_INFO_SENSIBLE"];
		$this->esta_activo = $fila["ESTA_ACTIVO"];
		
    }	 

    function buscarUsuariosPorNombre($inicio, $fin, $contarTodos) {
        $sql = "select u.cd_usuario, u.nm_usuario, u.login_usuario, p.nm_perfil, u.esta_activo, u.ver_info_sensible ".
		" from usuarios u, usuario_perfiles up, perfiles p " .
		" where u.nm_usuario like '%" . $this->nm_usuario . "%' " .		
		" and u.cd_usuario = up.cd_usuario " .
		" and p.cd_perfil = up.cd_perfil " .
		" order by u.nm_usuario ";
		if(!$contarTodos) {
			$sql .= " limit " . $inicio . ", " . $fin;
		}
		
		//echo $sql;	
        return $sql;		
	}
	
	function validarNombreRepetido() {
		$sql = "select count(1) as conteo from usuarios where login_usuario = '" . $this->login_usuario . "'";

		return $sql;
	}

}
?>