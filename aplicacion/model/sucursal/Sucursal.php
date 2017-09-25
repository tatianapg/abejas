<?php
class Sucursal {
	private $cd_sucursal;
	private $cd_empresa;
	private $nm_sucursal;
	private $direccion_sucursal;
	
	function __construct() {
	}
	
	function setSucursal($cd_sucursal, $cd_empresa, $nm_sucursal, $direccion_sucursal) {
		$this->cd_sucursal = $cd_sucursal;
		$this->cd_empresa = $cd_empresa;
		$this->nm_sucursal = addslashes($nm_sucursal);
		$this->direccion_sucursal = $direccion_sucursal;
	}
	
	function validarNombreRepetido() {
		$sql = "select count(1) as conteo from sucursales where nm_sucursal = '" . $this->nm_sucursal . "'";
		return $sql;
	}
	
	function getCdSucursal() {
		return $this->cd_sucursal;
	}
	
	function setCdSucursal($cd_sucursal) {
		$this->cd_sucursal = $cd_sucursal;
	}
	
	function getNmSucursal() {
		return $this->nm_sucursal;
	}
	
	function setNmSucursal($nm_sucursal) {
		$this->nm_sucursal = $nm_sucursal;
		
	}
	
	function crearSucursal() {
		$sql = "insert into sucursales(cd_sucursal, cd_empresa, nm_sucursal, direccion_sucursal) values(".
			$this->cd_sucursal . ", " .
			$this->cd_empresa . ", " .
			"'" . $this->nm_sucursal . "', " .
			"'" . $this->direccion_sucursal . "')";
			
		return $sql;	
	}
	
	function modificarSucursal() {
        $cons = " update sucursales set " .
        "nm_sucursal = '" . addslashes($this->nm_sucursal) . "', " .
		"direccion_sucursal = '" . addslashes($this->direccion_sucursal) . "' " .
        "where cd_sucursal = " . $this->cd_sucursal;
        
        return $cons;
    }
    
    function consultarSucursal() {
        $cons = "select * from sucursales where cd_sucursal = " . $this->cd_sucursal;
        return $cons;
    }
    
    function obtenerSucursal($fila) {
        //echo "===========Entrando a get sucursal ===============";
        $this->cd_sucursal = $fila["CD_SUCURSAL"];
        $this->nm_sucursal = $fila["NM_SUCURSAL"];
        $this->cd_empresa = $fila["CD_EMPRESA"];
        $this->direccion_sucursal = $fila["DIRECCION_SUCURSAL"];
    }
    
	function getTodasSucursales() {
		$sql = "select cd_sucursal as codigo, nm_sucursal as texto from sucursales order by nm_sucursal";
		return $sql;
	}	
	
	function eliminarSucursal() {
		$sql = "delete from sucursales where cd_sucursal = " . $this->cd_sucursal;
		return $sql;
	}
	
	function buscarSucursalesPorNombre($inicio, $fin, $contarTodos) {
		$sql = "select cd_sucursal, nm_sucursal from sucursales " . 
			" where nm_sucursal like '%" . $this->nm_sucursal . "%'" . 
			" order by nm_sucursal";
			
		if(!$contarTodos) {
			$sql .= " limit " . $inicio . ", " . $fin;
		}
		//echo $sql;	
		return $sql;	
	}
	
}
?>