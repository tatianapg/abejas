<?php

class Sesion {
	private $cd_sesion;
	private $cd_tratamiento;
	private $fe_sesion;
	private $notas_sesion;
	private $encargado_sesion;
	private $presion_sesion;
	private $frecuencia_cardiaca_sesion;
	private $peso_sesion;
	private $talla_sesion;	
	private $nivel_dolor_sesion;		
	
	function __construct() {
	}
	
	function setSesion($cd_sesion, $cd_tratamiento, $fe_sesion, $notas_sesion, $encargado_sesion, $presion_sesion, $frecuencia_cardiaca_sesion,
	$peso_sesion, $talla_sesion, $nivel_dolor_sesion) {
		$this->cd_sesion = $cd_sesion;
		$this->cd_tratamiento = $cd_tratamiento;		
		$this->fe_sesion = $fe_sesion;
		$this->notas_sesion = $notas_sesion;
		$this->encargado_sesion = $encargado_sesion;
		$this->presion_sesion = $presion_sesion;
		$this->frecuencia_cardiaca_sesion = $frecuencia_cardiaca_sesion;
		$this->peso_sesion = $peso_sesion;
		$this->talla_sesion = $talla_sesion;
		$this->nivel_dolor_sesion = $nivel_dolor_sesion;
	}
	
	function setCdSesion($cd_sesion) {
		$this->cd_sesion = $cd_sesion;
	}
	
	function setDefaultNumeros() {
		if(!$this->frecuencia_cardiaca_sesion) $this->frecuencia_cardiaca_sesion = 0;
		if(!$this->peso_sesion) $this->peso_sesion = 0;
		if(!$this->talla_sesion) $this->talla_sesion = 0;		
		if(!$this->nivel_dolor_sesion) $this->nivel_dolor_sesion = 0;		
	}	
	
	function crearSesion() {
		$this->setDefaultNumeros();
		
		$cons = "insert into sesiones(cd_tratamiento, fe_sesion, notas_sesion, encargado_sesion, " . 
		" presion_sesion, frecuencia_cardiaca_sesion, peso_sesion, talla_sesion, nivel_dolor_sesion) " .
		" values(" . 
		$this->cd_tratamiento . ", " .
		"'" . $this->fe_sesion . "', " .
		"'" . addslashes($this->notas_sesion) . "', " .
		"'" . $this->encargado_sesion . "', " .
		"'" . addslashes($this->presion_sesion) . "', " .
		"" . $this->frecuencia_cardiaca_sesion . ", " .
		"" . $this->peso_sesion . ", " .
		"" . $this->talla_sesion . ", " .
		"" . $this->nivel_dolor_sesion . ")";
		
		//echo " crear sesion::: " .$cons;
		return $cons;	
	}
	
	
	function eliminarSesion() {
		$cons = "delete from sesiones where cd_sesion = " . $this->cd_sesion;	
		return $cons;
	}
	
}
?>