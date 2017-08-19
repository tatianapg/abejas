<?php
class Usuario {
	private $cd_usuario;
	private $nm_usuario;
	private $login_usuario;
	private $clave_usuario;
	private $email_usuario;
	private $obs_usuario;
	
	function __construct() {
	}
	
	function setUsuario($cd_usuario, $nm_usuario, $login_usuario, $clave_usuario, $email_usuario, $obs_usuario) {
		$this->cd_usuario = $cd_usuario;
		$this->nm_usuario = $nm_usuario;
		$this->login_usuario = $login_usuario;
		$this->clave_usuario = $clave_usuario;
		$this->email_usuario = $email_usuario;
		$this->obs_usuario = $obs_usuario;
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
	
	
	    
    function consultarUsuario() {
        $cons = "select * from bdd_seguridades.usuarios where cd_usuario = " . $this->cd_usuario;
        return $cons;
    }
    
    function obtenerUsuario($fila) {
        //echo "===========Entrando a get sucursal ===============";
        $this->cd_usuario = $fila["CD_USUARIO"];
        $this->nm_usuario = $fila["NM_USUARIO"];
        $this->login_usuario = $fila["LOGIN_USUARIO"];
        $this->clave_usuario = $fila["CLAVE_USUARIO"];
        $this->email_usuario = $fila["EMAIL_USUARIO"];
        $this->obs_usuario = $fila["OBS_USUARIO"];
    }
    	
}
?>