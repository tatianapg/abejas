<?php
class UsuarioPerfil {
	private $cd_usuario_perfil;
	private $cd_perfil;	
	private $cd_usuario;
	
	function __construct() {
	}
	
	function setUsuarioPerfil($cd_usuario_perfil, $cd_perfil, $cd_usuario) {
		$this->cd_usuario_perfil = $cd_usuario_perfil;
		$this->cd_perfil = $cd_perfil;
		$this->cd_usuario = $cd_usuario;
	}
	
	function setCdUsuario($cd_usuario) {
		$this->cd_usuario = $cd_usuario;
	}
	
	function setCdPerfil($cd_perfil) {
		$this->cd_perfil = $cd_perfil; 
	}

	function getCdUsuarioPerfil() {
		return $this->cd_usuario_perfil;
	}
	
	function getCdPerfil() {
		return $this->cd_perfil;
	}
	
	function getCdUsuario() {
		return $this->cd_usuario;
	}
		    	
	function crearUsuarioPerfil() {
		$sql = "insert into usuario_perfiles(cd_usuario_perfil, cd_perfil, cd_usuario) values( " . 
		$this->cd_usuario_perfil . ", " .
		$this->cd_perfil . ", " .
		$this->cd_usuario . ")";		
		return $sql;
	}
	
	function modificarUsuarioPerfil() {
		$sql = "update usuario_perfiles set " .
			" cd_perfil = '" . $this->cd_perfil . "' " .
			" where cd_usuario = " . $this->cd_usuario;			
		//echo "modificar " . $sql;	
		return $sql;	
	}
	
	//devuelve el perfil de usuario
    function consultarPerfilDadoUsuario() {
        $cons = "select cd_perfil from usuario_perfiles where cd_usuario = " . $this->cd_usuario;
        return $cons;
    }

}
?>