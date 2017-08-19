<?php
class Medicacion {
	
	private $cd_medicacion;
	private $cd_tratamiento;
	private $fe_medicacion;
	private $notas_medicacion;
	
	function __construct() {
	}
	
	function setCdMedicacion($cd_medicacion) {
		$this->cd_medicacion = $cd_medicacion;
	}
	
	function setMedicacion($cd_medicacion, $cd_tratamiento, $fe_medicacion, $notas_medicacion) {
		$this->cd_medicacion = $cd_medicacion;
		$this->cd_tratamiento = $cd_tratamiento;
		$this->fe_medicacion = $fe_medicacion;
		$this->notas_medicacion = $notas_medicacion;				
	}
	
	function crearMedicacion() {
		$sql = "insert into medicaciones(cd_tratamiento, fe_medicacion, notas_medicacion) " .
		"values( " . $this->cd_tratamiento . ", '" . $this->fe_medicacion . "', '" . addslashes($this->notas_medicacion) . "')";
		return $sql;
	}
	
	function eliminarMedicacion() {
		$sql = "delete from medicaciones where cd_medicacion = " . $this->cd_medicacion;
		return $sql;
	}
}
?>