<?php
class AccionProducto {
	
	private $cd_accion;
	private $cd_producto;
	private $cd_sucursal;
	private $cd_tipo_accion;
	private $obs_accion;	
	private $cantidad_accion;
	private $fe_accion;
	private $precio_accion;
	private $costo_accion;
	private $cd_inventario;
	private $es_carga_inicial;
	private $cd_subtipo_accion;
	private $cd_usuario;
	private $cd_cabecera;
	private $dato_sensible;
	
	//campos para reportes
	private $fe_reporte_diario_fin;
	private $fe_reporte_diario_inicio;
	
		
	function __construct() {
	}
	
	function setDatoSensible($dato_sensible) {
		$this->dato_sensible = $dato_sensible;
	}
	
	function setCdUsuario($cd_usuario) {
		$this->cd_usuario = $cd_usuario;
	}
	
	function getCdUsuario() {
		return $this->cd_usuario;
	}
	
	function setCdCabecera($cd_cabecera) {
		$this->cd_cabecera = $cd_cabecera;
	}
	
	function getCdCabecera() {
		return $this->cd_cabecera;
	}

	function setCdInventario($cd_inventario) {
		$this->cd_inventario = $cd_inventario;
	}
	
	function getCdInventario() {
		return $this->getCdInventario;
	}
	
	function setCdSucursal($cd_sucursal) {
		$this->cd_sucursal = $cd_sucursal;
	}
	
	function setCdTipoAccion($cd_tipo_accion) {
		$this->cd_tipo_accion = $cd_tipo_accion;
	}

	function setCdSubtipoAccion($cd_subtipo_accion) {
		$this->cd_subtipo_accion = $cd_subtipo_accion;
	}
	
	function setFeReporteDiarioInicio($fe_reporte_diario_inicio) {
		$this->fe_reporte_diario_inicio = $fe_reporte_diario_inicio;
	}

	function setFeReporteDiarioFin($fe_reporte_diario_fin) {
		$this->fe_reporte_diario_fin = $fe_reporte_diario_fin;
	}

	
	function setAccion($cd_producto, $cd_sucursal, $cd_tipo_accion, $obs_accion, $cantidad_accion, $fe_accion, $precio_accion, $costo_accion, $cd_inventario, $es_carga_inicial, $cd_subtipo_accion, $cd_usuario, $cd_cabecera) {
		//$this->cd_accion = $cd_accion;
		$this->cd_producto = $cd_producto;
		$this->cd_sucursal = $cd_sucursal;
		$this->cd_tipo_accion = $cd_tipo_accion;				
		$this->obs_accion = $obs_accion;				
		$this->cantidad_accion = $cantidad_accion;				
		$this->fe_accion = $fe_accion;				
		$this->precio_accion = $precio_accion;				
		$this->costo_accion = $costo_accion;
		$this->cd_inventario = $cd_inventario;
		$this->es_carga_inicial = $es_carga_inicial;
		$this->cd_subtipo_accion = $cd_subtipo_accion;
		$this->cd_usuario = $cd_usuario;
		$this->cd_cabecera = $cd_cabecera;
	}
	
	
	function setDefaultNumeros() {
		if(!$this->es_carga_inicial) $this->es_carga_inicial = 0;
	}
	
	function crearAccion() {
		$this->setDefaultNumeros();
		
		$sql = "insert into acciones_producto(cd_producto, cd_sucursal, cd_tipo_accion, obs_accion, cantidad_accion, fe_accion, precio_accion, costo_accion, cd_inventario, es_carga_inicial, " .
		" cd_subtipo_accion, cd_usuario, cd_cabecera) values( " . 
		$this->cd_producto . ", " . 
		$this->cd_sucursal . "," . 
		$this->cd_tipo_accion . "," .
		"'". $this->obs_accion . "', " . 
		$this->cantidad_accion . ", " .
		"'" . $this->fe_accion . "', " .
		$this->precio_accion . ", " .
		$this->costo_accion . ", " .
		$this->cd_inventario . ", " . 
		$this->es_carga_inicial . "," .
		$this->cd_subtipo_accion . ", " .
		$this->cd_usuario . ", " .
		$this->cd_cabecera . ")";
		return $sql;
	}

//reportes de acciones_producto

	/* esta consulta permite obtener todos los movimientos (grupos grandes: compras o ventas) realizados
	en una fecha, o entre fechas.
	*/
	function generarDetalleMovimientos($bddSeguridad) {
		//verificar si existe sucursal
		$condicionSucursal = "";
		if($this->cd_sucursal != -1 && $this->cd_sucursal != '')
			$condicionSucursal = " and a.cd_sucursal = " . $this->cd_sucursal . " ";
		
		$condicionAccion = "";
		if($this->cd_tipo_accion != -1 && $this->cd_tipo_accion != '')	   
			$condicionAccion = " and a.cd_tipo_accion = '" . $this->cd_tipo_accion . "' ";

		$condicionUsuario = "";
		if($this->cd_usuario != 0)
			$condicionUsuario = " and u.cd_usuario = " . $this->cd_usuario;
		
		$condicionFechas = "";
		if($this->fe_reporte_diario_inicio && $this->fe_reporte_diario_fin) {
			$condicionFechas = " and fe_accion between '" .  $this->fe_reporte_diario_inicio ."' and '" . $this->fe_reporte_diario_fin ."' ";
		}
		
		$condicionInventario = " a.cd_inventario > 0 ";
		if($this->cd_inventario) {
			" a.cd_inventario = " . $this->cd_inventario;
		}
		
		$sql = "select p.nm_producto, p.sku_producto, a.cantidad_accion cantidad, a.precio_accion as precio, " .
			   " (a.cantidad_accion * a.precio_accion) as ingreso, a.fe_accion as fe_ultima_compra, " . 
			   " s.nm_sucursal, t.nm_tipo_accion as nm_subtipo, u.login_usuario, a.cd_cabecera " .
			   " from acciones_producto a, productos p, sucursales s, tipos_accion t, ". $bddSeguridad.".usuarios u " .
			   " where " .
			   //cambio_inventario
			   $condicionInventario .
			   $condicionFechas .		
			   $condicionSucursal .
			   $condicionAccion . 	
			   $condicionUsuario .
			   " and a.es_carga_inicial = 0 " .
			   " and a.cd_producto = p.cd_producto " .
			   " and a.cd_sucursal = s.cd_sucursal " .
			   " and a.cd_subtipo_accion = t.cd_tipo_accion " .
			   " and a.cd_usuario = u.cd_usuario " . 
			   " order by s.nm_sucursal, a.cd_cabecera, p.nm_producto, u.login_usuario, a.fe_accion ";		
		
		//echo "consulta sql: " . $sql;	
		return $sql;
	}

	/* 0. Reporte INVENTARIO ACTIVO:  0.  encerar  */	
	/* Estas funciones permiten generar el reporte de stock
	primero limpiar la tabla auxiliar.
	*/
	function limpiarCodigosStock() {
		//$sql = "truncate table aux_acciones ";
		$sql = "delete from aux_acciones where " .
				" cd_sucursal = " . $this->cd_sucursal .
				" and cd_inventario = " . $this->cd_inventario;
		return $sql;
		
	}
	
	/*
	Este es el reporte de stock actual
	En esta consulta se obtienen los códigos de inventario inicial, compras/ventas intermedias 
	primero se consulta inventario final, luego compras y luego ventas
	Estamos obteniendo todo el panorama del inventario, por es no se toman en cuenta fechas, sino todos
	los movimientos
	*/
	
	/* Reporte INVENTARIO ACTIVO: 1er grupo de datos: inventario inicial */
	/* Se buscan los códigos de toodas 1)las compras "iniciales", 2)luego las compras y
	de 3)todas las ventas.
	*/
	function insertarCodigosStock() {
		$sql = "insert into aux_acciones(cd_producto, cd_sucursal, cd_inventario) " .
				" select distinct a.cd_producto, a.cd_sucursal, a.cd_inventario " .
				" from acciones_producto a " .
				" where es_carga_inicial = 1 " .
				" and a.cd_tipo_accion = 1 " .
				" and a.cd_inventario = " . $this->cd_inventario .
				" and a.cd_sucursal = " . $this->cd_sucursal .
				" union distinct " .
				" select distinct a.cd_producto, a.cd_sucursal, a.cd_inventario " .
				" from acciones_producto a " .
				" where " .
				" a.cd_inventario = " . $this->cd_inventario .
				" and a.cd_sucursal = " . $this->cd_sucursal .
				" and a.cd_tipo_accion = 1 " .
				" and es_carga_inicial = 0 " .
				" union distinct " .
				" select distinct a.cd_producto, a.cd_sucursal, a.cd_inventario " .
				" from acciones_producto a " .
				" where  " .
				" a.cd_inventario = " . $this->cd_inventario .
				" and a.cd_sucursal = " . $this->cd_sucursal .
				" and a.cd_tipo_accion = 2 ".
				" and es_carga_inicial = 0 " .
				" order by 1 ";
				
		return $sql;
	}
	
	/* Reporte INVENTARIO ACTIVO: 2do grupo de datos: nombres de productos */	
	function obtenerNombresStock() {
		$sql = "select p.cd_producto, p.sku_producto, p.nm_producto " .
			   //" from aux_acciones a left outer join productos p on a.cd_producto = p.cd_producto " .
			   " from aux_acciones a, productos p " .
			   " where a.cd_producto = p.cd_producto " .
			   " and a.cd_inventario = " . $this->cd_inventario .
			   " and a.cd_sucursal = " . $this->cd_sucursal .
			   " order by a.cd_producto ";
		return $sql;	   
	}

	/* Reporte INVENTARIO ACTIVO: 3er grupo de datos: productos inventario inicial 
	cantidad_inicial es el número de unidades
	valor_inicial es el valor en dólares
	*/		
	function obtenerIInicialStock() {
		$sql = "select a.cd_producto, sum(ap.cantidad_accion) cantidad_inicial, sum(ap.cantidad_accion * ap.precio_accion) valor_inicial " .
			   " from aux_acciones a left outer join acciones_producto ap on a.cd_producto = ap.cd_producto " .
			   " and a.cd_inventario = ap.cd_inventario " . 
			   " and a.cd_sucursal = ap.cd_sucursal " .
			   " and ap.cd_inventario = " . $this->cd_inventario .
			   " and ap.cd_sucursal = " . $this->cd_sucursal .
			   " and ap.cd_tipo_accion = 1 " .
			   " and ap.es_carga_inicial = 1 " .
  			   " where " .
			   " a.cd_inventario = " . $this->cd_inventario . 
			   " and a.cd_sucursal = " . $this->cd_sucursal .
			   " group by a.cd_producto " .
			   " order by a.cd_producto";
		return $sql;
	}
	
	/* Reporte INVENTARIO ACTIVO: 4to grupo de datos: 
	obtener las compras efectuadas entre el inventario inicial y final, 
	estas compras no son carga inicial
	*/		
	function obtenerComprasStock() {
		$sql = "select a.cd_producto, sum(ap.cantidad_accion) cantidad_compras, " .
			   " sum(ap.cantidad_accion * ap.precio_accion) as valor_compras " .
			   " from aux_acciones a left outer join acciones_producto ap on a.cd_producto = ap.cd_producto " .
			   " and a.cd_inventario = ap.cd_inventario " . 
			   " and a.cd_sucursal = ap.cd_sucursal " .			   
			   " and ap.cd_inventario = " . $this->cd_inventario .
			   " and ap.cd_sucursal = " . $this->cd_sucursal .
			   " and ap.cd_tipo_accion = 1 " .
			   " and ap.es_carga_inicial = 0 " .
   			   " where " . 
			   " a.cd_inventario = " . $this->cd_inventario . 
			   " and a.cd_sucursal = " . $this->cd_sucursal .
			   " group by a.cd_producto " .
			   " order by a.cd_producto";
		return $sql;
	}
	
	/* Reporte INVENTARIO ACTIVO: 5to grupo de datos: 
	obtener las ventas efectuadas
	*/		
	function obtenerVentasStock() {
		$sql = "select a.cd_producto, sum(ap.cantidad_accion) cantidad_ventas, sum(ap.cantidad_accion * ap.precio_accion) as valor_ventas " .
			   " from aux_acciones a left outer join acciones_producto ap on a.cd_producto = ap.cd_producto " .
			   " and a.cd_inventario = ap.cd_inventario " . 
			   " and a.cd_sucursal = ap.cd_sucursal " .		
			   " and ap.cd_inventario = " . $this->cd_inventario .
			   " and ap.cd_sucursal = " . $this->cd_sucursal .
			   " and ap.cd_tipo_accion = 2 " .
			   " and ap.es_carga_inicial = 0 " .
			   " where ".
			   " a.cd_inventario = " . $this->cd_inventario . 
			   " and a.cd_sucursal = " . $this->cd_sucursal .
			   " group by a.cd_producto " .
			   " order by a.cd_producto";
		return $sql;
	}

	//es necesario consultar las ventas
	/* Reporte de PRODUCTOOS MAS VENDIDOS:
	Se extraen los datos de los productos que se vendieron al cliente final
	tipo 2 y subtipo 5, porque son ventas y ventas al cliente final
	*/
	function obtenerProductosMasVendidos() {
		$condicionSucursal = "";
		if($this->cd_sucursal != -1 && $this->cd_sucursal != '')
			$condicionSucursal = " and ap.cd_sucursal = " . $this->cd_sucursal . " ";
		
		$condicionInventario = " and ap.cd_inventario > 0 ";
		if($this->cd_inventario) 
			$condicionInventario = " and ap.cd_inventario = " . $this->cd_inventario;
		
		$condicionFechas = "";
		if($this->fe_reporte_diario_inicio && $this->fe_reporte_diario_fin) {
			$condicionFechas = " and fe_accion between '" .  $this->fe_reporte_diario_inicio ."' and '" . $this->fe_reporte_diario_fin ."' ";
		}
		
		$sql = "select p.cd_producto, p.sku_producto, p.nm_producto, " .
			" sum(ap.cantidad_accion) as cantidad_ventas, min(fe_accion) fe_venta_min, " .
			" max(fe_accion) fe_venta_max " .
			" from acciones_producto ap, productos p " .
			" where ap.cd_producto = p.cd_producto " .
			$condicionSucursal .
			///cambio_inventario
			$condicionInventario .
			$condicionFechas .
			" and ap.cd_tipo_accion = 2 " .
			" and ap.cd_subtipo_accion = 5 " .
			" and ap.es_carga_inicial = 0 " .
			" group by p.cd_producto, p.sku_producto, p.nm_producto " .
			" order by 4 desc";
		
		return $sql;
	}	
	
	function recuperarAccionesDadaCabecera() {
		
		$sql = "select p.nm_producto as nombre, p.sku_producto as codigo, a.precio_accion as precio, " .
				" a.cantidad_accion as cantidad, a.cd_cabecera " .
				" from acciones_producto a, productos p " .
				" where a.cd_cabecera = '" . $this->cd_cabecera . "' " .
				" and a.cd_sucursal = " . $this->cd_sucursal .
				" and a.cd_producto = p.cd_producto ";	
		//echo "consultar última venta: " . $sql;
		return $sql;	
	}
	
	function consultarSecuencial() {
		$sql = "select (max(cd_cabecera) + 1) as conteo from acciones_producto";
		return $sql;
	}

	/* vamos a consultar el último recibo generado por el usuario actual*/
	function consultarUltimoReciboGenerado() {
		$sql = "select max(cd_cabecera) conteo, max(fe_accion) feultima_venta " .
				" from acciones_producto " .
				" where cd_usuario = " . $this->cd_usuario .
				" and cd_sucursal = " . $this->cd_sucursal;
				
		return $sql;		
	}

	/*
	generar una consulta con las devoluciones	
	*/
	
	function generarResumenDiarioVentas($bddSeguridad) {
		//verificar si existe sucursal
		$condicionSucursal = "";
		if($this->cd_sucursal != -1 && $this->cd_sucursal != '')
			$condicionSucursal = " and a.cd_sucursal = " . $this->cd_sucursal . " ";
		
		$condicionAccion = "";
		if($this->cd_tipo_accion != -1 && $this->cd_tipo_accion != '')	   
			$condicionAccion = " and a.cd_tipo_accion = '" . $this->cd_tipo_accion . "' ";

		$condicionSubtipoAccion = "";
		if($this->cd_subtipo_accion != -1 && $this->cd_subtipo_accion != '')	   
			$condicionSubtipoAccion = " and a.cd_subtipo_accion = '" . $this->cd_subtipo_accion . "' ";
		
		$condicionUsuario = "";
		if($this->cd_usuario != 0)
			$condicionUsuario = " and u.cd_usuario = " . $this->cd_usuario;

		$condicionInventario = " a.cd_inventario > 0 ";
		if($this->cd_inventario) 
			$condicionInventario = " and a.cd_inventario = " . $this->cd_inventario;
		
		$condicionFechas = "";
		if($this->fe_reporte_diario_inicio && $this->fe_reporte_diario_fin) {
			$condicionFechas = " and fe_accion between '" .  $this->fe_reporte_diario_inicio ."' and '" . $this->fe_reporte_diario_fin ."' ";
		}
						
		
		$sql = "select p.nm_producto, p.sku_producto, sum(a.cantidad_accion) as cantidad, " .
			   " a.precio_accion as precio, " .
			   " (sum(a.cantidad_accion) * a.precio_accion) as ingreso, " .
			   " ((sum(a.cantidad_accion) * a.costo_accion)* ".$this->dato_sensible.") as costo, " .
			   " s.nm_sucursal, u.login_usuario " .
			   " from acciones_producto a, productos p, sucursales s, ".$bddSeguridad.".usuarios u " .
			   " where " .
			   ///cambio_inventario
			   $condicionInventario .
			   $condicionFechas .
			   $condicionSucursal .
			   $condicionUsuario .
			   $condicionAccion . 
			   $condicionSubtipoAccion .
			   " and a.es_carga_inicial = 0 " .
			   " and a.cd_producto = p.cd_producto " .
			   " and a.cd_sucursal = s.cd_sucursal " .
			   " and a.cd_usuario = u.cd_usuario " .
			   " group by p.nm_producto, s.nm_sucursal, u.login_usuario " .
			   " order by p.nm_producto, s.nm_sucursal, u.login_usuario ";		
		
		//echo "consulta sql: " . $sql;	
		return $sql;
	}

}
?>
