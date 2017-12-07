<?php
class Inventario {
	
    private $cd_inventario;
    private $cd_estado_sistema;
    private $nm_inventario;
	private $fe_registro;
    private $fe_inicio_inventario;
    private $fe_fin_inventario;
    private $anio_fiscal_inventario;
    private $obs_inventario;
	private $fe_cierre;
	private $cd_sucursal;
	
	//el siguiente no es atributo de la tabla
	private $cd_inventario_anterior;	
	private $fe_cierre_anterior;
	private $fe_reporte_diario_inicio;
	private $fe_reporte_diario_fin;
	private $cd_inventario_activo;
    
    //constructor de la clase
    function __construct() {
        
    }
	
		      
    function setInventario( $cd_inventario, $cd_estado_sistema, $nm_inventario, $fe_registro,
	$fe_inicio_inventario, $fe_fin_inventario, $anio_fiscal_inventario, $obs_inventario, $fe_cierre, $cd_sucursal)
    {
        $this->cd_inventario = $cd_inventario;
		$this->cd_estado_sistema = $cd_estado_sistema;
        $this->nm_inventario = $nm_inventario;
		$this->fe_registro = $fe_registro;
        $this->fe_inicio_inventario = $fe_inicio_inventario;
        $this->fe_fin_inventario = $fe_fin_inventario;		
        $this->anio_fiscal_inventario = $anio_fiscal_inventario;
        $this->obs_inventario = $obs_inventario;
		$this->fe_cierre = $fe_cierre;
		$this->cd_sucursal = $cd_sucursal;
    }
	

	/* por defecto al crear un inventario este tiene estado activo  */
    function crearInventario() {
        $cons = "Insert into Inventarios( cd_estado_sistema,
		nm_inventario, fe_registro, fe_inicio_inventario,
		fe_fin_inventario, anio_fiscal_inventario, obs_inventario, fe_cierre, cd_sucursal) values ( " .
        $this->cd_estado_sistema . ", " .
		"'" . addslashes($this->nm_inventario) . "', " .
		"'" . $this->fe_registro . "', " .
		"'" . $this->fe_inicio_inventario . "', " .
		"'" . $this->fe_fin_inventario . "', " .
        $this->anio_fiscal_inventario .  ", " .
        "'" . addslashes($this->obs_inventario) . "', " .
		" null, " .
		$this->cd_sucursal . ")";
        
        return $cons;        
    }
    
	/* El estado no se modifica ya que solamente cuando se ingresa uno nuevo, se cambia 
		La sucursal tampoco se modifica.
	*/
    function modificarInventario() {
        $cons = " update Inventarios set " .
        //"cd_estado_sistema = " . $this->cd_estado_sistema . ", " .
        "nm_inventario = '" . addslashes($this->nm_inventario) . "', " .
		"fe_registro = '" . $this->fe_registro . "', " .
		"fe_inicio_inventario = '" . $this->fe_inicio_inventario . "', " .
		"fe_fin_inventario = '" . $this->fe_fin_inventario . "', " .
        "anio_fiscal_inventario = " . $this->anio_fiscal_inventario . ", " .
        "obs_inventario = '" . addslashes($this->obs_inventario) . "' " .
        "where cd_inventario = " . $this->cd_inventario;
        
        return $cons;
    }
    
    function consultarInventario() {
        $cons = "select * from Inventarios where cd_inventario = " . $this->cd_inventario;
        //echo $cons;
        return $cons;
    }
	
    
    function obtenerInventario($fila) {
        //var_dump($fila);
        //echo "===========Entrando a get inventario ===============";
        $this->cd_inventario = $fila["CD_INVENTARIO"];
        $this->cd_estado_sistema = $fila["CD_ESTADO_SISTEMA"];
        $this->nm_inventario = $fila["NM_INVENTARIO"];
        $this->fe_registro = $fila["FE_REGISTRO"];
        $this->fe_inicio_inventario = $fila["FE_INICIO_INVENTARIO"];
        $this->fe_fin_inventario = $fila["FE_FIN_INVENTARIO"];
        $this->anio_fiscal_inventario  = $fila["ANIO_FISCAL_INVENTARIO"];
        $this->obs_inventario = $fila["OBS_INVENTARIO"];        
        $this->fe_cierre = $fila["FE_CIERRE"];        
        $this->cd_sucursal = $fila["CD_SUCURSAL"];        
		
    }
    
	function setCdSucursal($cd_sucursal) {
		$this->cd_sucursal = $cd_sucursal;
	}
	
	function getCdSucursal() {
		return $this->cd_sucursal;
	}
	
	//haer metodos seter y geter
    function setCdInventario($cd_inventario) {
        $this->cd_inventario = $cd_inventario;
    }
	
    function getCdInventario() {
        return $this->cd_inventario;
    }
	

    function getCdEstadoSistema() {
        return $this->cd_estado_sistema;
    }    
    

	function getNmInventario() {
		return $this->nm_inventario;
	}
	
	
	function getFeRegistro() {
		return $this->fe_registro;
	}
    
	function getFeInicioInventario() {
		return $this->fe_inicio_inventario;
	}
	
	function getFeFinInventario() {
		return $this->fe_fin_inventario;
	}
	
	function getAnioFiscalInventario()  {
		return $this->anio_fiscal_inventario;
	}
	
	function setAnioFiscalInventario($anio_fiscal_inventario) {
		$this->anio_fiscal_inventario = $anio_fiscal_inventario;
	}
	
	function getObsInventario() {
		return $this->obs_inventario;
	}
	
	function setFeCierreAnterior($fe_cierre_anterior) {
		$this->fe_cierre_anterior = $fe_cierre_anterior;
	}
	
	function setCdInventarioAnterior($cd_inventario_anterior) {
		$this->cd_inventario_anterior = $cd_inventario_anterior;
	}
		
    //buscar inventarios por anio
    function buscarInventariosPorAnio() {
        $sql = "select cd_inventario, nm_inventario, anio_fiscal_inventario, fe_inicio_inventario, " .
		" fe_fin_inventario, cd_estado_sistema, fe_registro, fe_cierre, s.nm_sucursal, i.cd_sucursal " .
		" from inventarios i, sucursales s where anio_fiscal_inventario >= 2000 " . 
		" and i.cd_sucursal = " . $this->cd_sucursal .
		" and s.cd_sucursal = i.cd_sucursal " .
		" order by fe_inicio_inventario desc ";
		// . $this->anio_fiscal_inventario ;
		//echo "Inventarios activo:: " . $sql;
        return $sql;
    }
    	
	
	/* para borrar un inventario se debe verificar que no tenga acciones asociadas ni esté activo.
	Debe estar inactivo.
	*/
	function eliminarInventario() {
		$sql = "delete from inventarios where cd_inventario = " . $this->cd_inventario . 
		" and cd_estado_sistema = -1 and cd_sucursal = " . $this->cd_sucursal;
		return $sql;
	}
	
	/* Se debe validar si existen acciones asociadas al inventario: compras, ventas, carga inicial
	si existen entonces no se debería eliminar el inventario.
	*/
	function validarEliminarInventario() {
		$sql = "select count(1) as conteo from acciones_producto " .
				" where cd_inventario = " . $this->cd_inventario . 
				" and cd_sucursal = " . $this->cd_sucursal;
		return $sql;
	}
	
	/* para efectos de ingresar las acciones del producto, debemos validar que exista un solo inventario activo*/
	/*
	function validarExisteUnInventarioActivo() {
		$sql = "select count(1) as conteo from inventarios where cd_estado_sistema = 1 and fe_cierre is null ";
		return $sql;
	}
	*/

	function validarExisteUnInventarioActivoPorSucursal() {
		$sql = "select count(cd_inventario) as conteo, max(cd_inventario) as cd_inventario, " .
				" max(fe_fin_inventario) as fe_fin_inventario " .
				" from inventarios where cd_estado_sistema = 1 " . 
				" and cd_sucursal = " . $this->cd_sucursal .
				" group by cd_sucursal";
		return $sql;
	}
	
	
	function obtenerCdInventarioActivo() {
		$sql = "select cd_inventario, count(cd_inventario) as conteo from inventarios where cd_estado_sistema = 1 " .
				//" and fe_cierre is null " .
				" and fe_fin_inventario >= curdate() " .
				" and cd_sucursal = " . $this->cd_sucursal;
		return $sql;
	}

	
	/*colocarle como inactivo al último inventario*/
	function desactivarUltimoInventario() {
		$sql = "update inventarios set " .
		" cd_estado_sistema = -1, " .
		" fe_cierre = '" . $this->fe_cierre_anterior . "' " . 
		" where cd_inventario = " . $this->cd_inventario_anterior . 
		" and cd_sucursal = " . $this->cd_sucursal;
		return $sql;
	}
	
	
	function setCdInventarioActivo($cd_inventario_activo) {
		$this->cd_inventario_activo = $cd_inventario_activo;
	}
	
	function setFeReporteDiarioInicio($fe_reporte_diario_inicio) {
		$this->fe_reporte_diario_inicio = $fe_reporte_diario_inicio;
	}

	function setFeReporteDiarioFin($fe_reporte_diario_fin) {
		$this->fe_reporte_diario_fin = $fe_reporte_diario_fin;
	}		
}

?>