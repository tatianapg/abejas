<?php
class TipoAccion {
	private $cd_tipo_accion;
	private $nm_tipo_accion;
	private $obs_tipo_accion;
	private $cd_tipo_accion_padre;
	private $ingresa_dinero_accion;
	
	//para uso interno de la clase
	private $condicionExcluyente;
	
	function __construct() {
	}
	
	
	function setCdTipoAccion($cd_tipo_accion) {
		$this->cd_tipo_accion = $cd_tipo_accion;
	}
	
	function setTipoAccionPadre($cd_tipo_accion_padre) {
		$this->cd_tipo_accion_padre = $cd_tipo_accion_padre;
	}
	
	function getCdTipoAccion() {
		return $this->cd_tipo_accion;
	}
	
	function getIngresarDineroAccion() {
		return $this->ingresa_dinero_accion;
	}
		    
	/* Obtiene todos los tipos de acción hijos dado el codigo padre
	Los dos grandes tipos de acción son 1 = compra y 2  = venta
	*/		
	function getTodosTiposAccionDadoPadre() {
		$sql = "select cd_tipo_accion as codigo, nm_tipo_accion as texto " .
				" from tipos_accion " .
				" where cd_tipo_accion_padre = " . $this->cd_tipo_accion_padre .
				$this->getCondicionExcluyente() .
				" order by nm_tipo_accion";
		return $sql;
	}	
	
    function consultarTipoAccion() {
        $cons = "select * from tipos_accion where cd_tipo_accion = " . $this->cd_tipo_accion;
        return $cons;
    }
    
    function obtenerTipoAccion($fila) {
        //echo "===========Entrando a get categoria ===============";
        $this->cd_tipo_accion = $fila["CD_TIPO_ACCION"];
        $this->nm_tipo_accion = $fila["NM_TIPO_ACCION"];
        $this->obs_tipo_accion = $fila["OBS_TIPO_ACCION"];
		$this->cd_tipo_accion_padre = $fila["CD_TIPO_ACCION_PADRE"];
		$this->ingresa_dinero_accion = $fila["INGRESA_DINERO_ACCION"];
    }
	
	//en casos en los que se necesita excluir un código de accion
	function setCondicionExcluyente($codigoExcluir) {
		$this->condicionExcluyente = " and cd_tipo_accion != " . $codigoExcluir;
	}
	
	function getCondicionExcluyente() {
		return $this->condicionExcluyente;
	}
	
}
?>