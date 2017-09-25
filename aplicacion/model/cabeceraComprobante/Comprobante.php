<?php
class Comprobante {
	private $cd_cabecera;
	private $fe_comprobante;
	private $nm_cliente;
	private $ci_cliente;
	private $total_comprobante;
	private $descuento_comprobante;
	private $cd_sucursal;
	private $num_items_comprobante;
	private $cd_usuario;
	private $a_pagar_comprobante;
	private $codigo_comprobante;
	
	//campos para ayudar al reporte
	private $fe_reporte_inicio;
	private $fe_reporte_fin;
	
	function __construct() {
	}
	
	function setComprobante($cd_cabecera, $fe_comprobante, $nm_cliente, $ci_cliente, $total_comprobante, 
					$descuento_comprobante, $cd_sucursal, $num_items_comprobante, $cd_usuario, 
					$a_pagar_comprobante, $codigo_comprobante) {
		$this->cd_cabecera = $cd_cabecera;
		$this->fe_comprobante = $fe_comprobante;		
		$this->nm_cliente = $nm_cliente;
		$this->ci_cliente = $ci_cliente;
		$this->total_comprobante = $total_comprobante;
		$this->descuento_comprobante = $descuento_comprobante;
		$this->cd_sucursal = $cd_sucursal;
		$this->num_items_comprobante = $num_items_comprobante;
		$this->cd_usuario = $cd_usuario;
		$this->a_pagar_comprobante = $a_pagar_comprobante;
		$this->codigo_comprobante = $codigo_comprobante;
	}
	

	function setDefaultNumeros() {
		if(!$this->total_comprobante) $this->total_comprobante = 0;
		if(!$this->descuento_comprobante) $this->descuento_comprobante = 0;
		if(!$this->num_items_comprobante) $this->num_items_comprobante = 0;		
		if(!$this->a_pagar_comprobante) $this->a_pagar_comprobante = 0;		
	}	
	
	function crearComprobante() {				
		
		$this->setDefaultNumeros();
		
		//$cons = "insert into comprobantes_cabecera(cd_cabecera, fe_comprobante, nm_cliente, ci_cliente, " . 
		$cons = "insert into comprobantes_cabecera(fe_comprobante, nm_cliente, ci_cliente, " . 
		" total_comprobante, descuento_comprobante, cd_sucursal, num_items_comprobante, cd_usuario, " .
		" a_pagar_comprobante, codigo_comprobante) " .
		" values(" . 
		//$this->cd_cabecera . ", " .
		"'" . $this->fe_comprobante . "', " .
		"'" . addslashes($this->nm_cliente) . "', " .
		"'" . addslashes($this->ci_cliente) . "', " .
		"'" . $this->total_comprobante . "', " .
		"" . $this->descuento_comprobante . ", " .
		"" . $this->cd_sucursal . ", " .
		"" . $this->num_items_comprobante . ", " .
		"" . $this->cd_usuario . ", " .
		"" . $this->a_pagar_comprobante . ", " .
		"'" . $this->codigo_comprobante . "')";
		
		//echo " crear sesion::: " .$cons;
		return $cons;	
	}
	
	
    function consultarComprobante() {
        $cons = "select * from comprobantes_cabecera where cd_cabecera = " . $this->cd_cabecera;
        return $cons;
    }
	
    
    function obtenerComprobante($fila) {
        //echo "===========Entrando a get comprobante ===============";
        $this->cd_cabecera = $fila["CD_CABECERA"];
        $this->fe_comprobante = $fila["FE_COMPROBANTE"];
        $this->nm_cliente = $fila["NM_CLIENTE"];
        $this->ci_cliente= $fila["CI_CLIENTE"];
        $this->total_comprobante = $fila["TOTAL_COMPROBANTE"];
        $this->descuento_comprobante = $fila["DESCUENTO_COMPROBANTE"];
        $this->cd_sucursal  = $fila["CD_SUCURSAL"];
        $this->num_items_comprobante = $fila["NUM_ITEMS_COMPROBANTE"];        		
        $this->cd_usuario = $fila["CD_USUARIO"];        		
        $this->a_pagar_comprobante = $fila["A_PAGAR_COMPROBANTE"];        		
        $this->codigo_comprobante = $fila["CODIGO_COMPROBANTE"];        				
    }
    
	function setCodigoComprobante($codigo_comprobante) {
		$this->codigo_comprobante = $codigo_comprobante;
	}
	
	function getCodigoComprobante() {
		return $this->codigo_comprobante;
	}
	
	//haer metodos seter y geter
    function setCdCabecera($cd_cabecera) {
        $this->cd_cabecera = $cd_cabecera;
    }
	
    function getCdCabecera() {
        return $this->cd_cabecera;
    }
	
	function getComprobante() {
		$sql = "select * " .
				/*
				"c.cd_cabecera, c.fe_comprobante, c.nm_cliente, " .
				" c.total_comprobante, c.descuento_comprobante, c.num_items_comprobante, " .
				*/
				//" u.login_usuario, s.nm_sucursal " .
				" from comprobantes_cabecera " .
				" where cd_cabecera = '" . $this->cd_cabecera . "' " .
				" and cd_sucursal = " . $this->cd_sucursal;
		return $sql;
	}	
	
	
	function getAPagarComprobante() {
		return $this->a_pagar_comprobante;
	}

	
	function getDescuentoComprobante() {
		return $this->descuento_comprobante;
	}
	
	function getFeComprobante() {
		return $this->fe_comprobante;
	}

	function getNmCliente() {
		return $this->nm_cliente;
	}

	function setCdSucursal($cd_sucursal) {
		$this->cd_sucursal = $cd_sucursal;
	}
	
	function setCdUsuario($cd_usuario) {
		$this->cd_usuario = $cd_usuario;
	}
	
	function getCdUsuario() {
		return $this->cd_usuario;
	}
	
	function setFeReporteInicio($fe_reporte_inicio) {
		$this->fe_reporte_inicio = $fe_reporte_inicio;
	}

	function setFeReporteFin($fe_reporte_fin) {
		$this->fe_reporte_fin = $fe_reporte_fin;
	}
	
	
	/* Esta función sirve para el reporte de ventas, ya que se deben descontar los descuentos*/
	function obtenerDescuentosPorParametros() {
		
		$condicionSucursal = "";
		if($this->cd_sucursal != -1 && $this->cd_sucursal != '')
			$condicionSucursal = " and cd_sucursal = " . $this->cd_sucursal . " ";

		$condicionUsuario = "";
		if($this->cd_usuario != 0)
			$condicionUsuario = " and cd_usuario = " . $this->cd_usuario;
		
		
		$sql = "select sum(DESCUENTO_COMPROBANTE) as suma_descuentos, " .
				" count(cd_cabecera) as recibos_con_descuento " .
				" from comprobantes_cabecera " .
				" where " .
  			    " fe_comprobante between '" .  $this->fe_reporte_inicio ."' and '" . $this->fe_reporte_fin ."' " .
				$condicionSucursal . 
				$condicionUsuario;

		//echo $sql;		
		return $sql;		
	}
	
	function getTotalComprobante() {
		return $this->total_comprobante;
	}
	
	function getCdSucursal() {
		return $this->cd_sucursal;
	}
	
	function generarCodigoComprobante() {
		$this->codigo_comprobante = str_pad($this->cd_sucursal, 3, "0", STR_PAD_LEFT) . "-" . 
									str_pad($this->cd_cabecera, 7, "0");
		echo $this->codigo_comprobante;
	}
	
	function obtenerSecuencialComprobante($baseApp) {
		$sql = "select auto_increment as conteo from information_schema.TABLES " .
			" where TABLE_SCHEMA='".$baseApp."' and TABLE_NAME ='comprobantes_cabecera'";
		return $sql;		
	}
	
	function crearCabeceraDefectoPorSucursal() {
		$sql = "insert into comprobantes_cabecera(cd_cabecera, fe_comprobante, total_comprobante, ".
			"cd_sucursal, num_items_comprobante, cd_usuario, a_pagar_comprobante, codigo_comprobante) ".
			" values(-1, '1900-01-01', -1, " . $this->cd_sucursal . ", -1, -1, -1, 'SIN COMPROBANTE')";
		
		//echo "CABECERA DEFECTO:: " .$sql;
		return $sql;			
	}			
}
?>