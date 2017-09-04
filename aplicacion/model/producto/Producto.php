<?php
class Producto {
    private $cd_producto;
    private $cd_categoria_producto;
    private $sku_producto; //stock unit product
	private $secuencial_producto;
    private $cd_estado_sistema;
    private $cd_unidad_producto;
    private $nm_producto;
    private $fe_ingreso_producto;
    private $desc_producto;
    private $nm_proveedor_producto;
	private $precio_producto;
    private $costo_interno_producto;
    private $foto_producto;
    private $barcode_producto;
    private $stock_minimo_producto;
    private $obs_producto;
	//auxiliar para los reportes
	private $criterio_buscar;
    
    //constructor de la clase
    function __construct() {
        
    }
	
	
	function setCriterioBuscar($criterio_buscar) {
		$this->criterio_buscar = $criterio_buscar;
	}
	
	
	function getCdEstadoSistema() {
		return $this->cd_estado_sistema;
	}
	
	function getSecuencialProducto() {
		return $this->secuencial_producto;
	}
	
	function setSecuencialProducto($secuencial_producto) {
		$this->secuencial_producto = $secuencial_producto;
	}
	
	function consultarSecuencial() {
		$sql = "select (max(secuencial_producto) + 1) as conteo from productos";
		return $sql;
	}
	
	function generarSkuProducto() {
		if($this->secuencial_producto > 0) {
			$this->sku_producto = str_pad($this->secuencial_producto, 4, "0", STR_PAD_LEFT);
			echo "el SKU es: " . $this->sku_producto;
		}	
	}
		      
    function setProducto( $cd_producto, $cd_categoria_producto, $sku_producto, $secuencial_producto,
	$cd_estado_sistema, $cd_unidad_producto, $nm_producto, $fe_ingreso_producto,
	$desc_producto, $nm_proveedor_producto, $precio_producto, $costo_interno_producto, 
	$foto_producto,	$barcode_producto, $stock_minimo_producto, $obs_producto)
    {
        $this->cd_producto = $cd_producto;
		$this->cd_categoria_producto = $cd_categoria_producto;
        $this->sku_producto = $sku_producto;
		$this->secuencial_producto = $secuencial_producto;
        $this->cd_estado_sistema = $cd_estado_sistema;
        $this->cd_unidad_producto = $cd_unidad_producto;		
        $this->nm_producto = $nm_producto;
        $this->fe_ingreso_producto = $fe_ingreso_producto;
        $this->desc_producto = $desc_producto;
        $this->nm_proveedor_producto = $nm_proveedor_producto;
        $this->precio_producto = $precio_producto;		
        $this->costo_interno_producto = $costo_interno_producto;
        $this->foto_producto = $foto_producto;
        $this->desc_producto = $desc_producto;
        $this->nm_proveedor_producto = $nm_proveedor_producto;
        $this->precio_producto = $precio_producto;
        $this->costo_interno_producto = $costo_interno_producto;		
        $this->foto_producto = $foto_producto;
        $this->barcode_producto = $barcode_producto;
        $this->stock_minimo_producto = $stock_minimo_producto;
        $this->obs_producto = $obs_producto;
    }
	
	
	//esta funcion permite setear valores a cero, los números 
	function setDefaultNumeros() {
		if(!$this->costo_interno_producto) $this->costo_interno_producto = 0;
        if(!$this->precio_producto) $this->precio_producto = 0;
        if(!$this->stock_minimo_producto) $this->stock_minimo_producto = 0;		
	}
	
    
    function crearProducto() {
		
		$this->setDefaultNumeros();
		
        $cons = "Insert into Productos(cd_categoria_producto,
		sku_producto, secuencial_producto, cd_estado_sistema,
		cd_unidad_producto, nm_producto, fe_ingreso_producto,
		desc_producto, nm_proveedor_producto, precio_producto,
		costo_interno_producto, foto_producto, barcode_producto,
		stock_minimo_producto, obs_producto ) values ( " .
        $this->cd_categoria_producto . ", " .
		"'" . $this->sku_producto . "', " .
		$this->secuencial_producto . ", " .
		$this->cd_estado_sistema . ", " .
		$this->cd_unidad_producto . ", " .
        "'" . addslashes($this->nm_producto) . "', " .
		"'" . $this->fe_ingreso_producto . "', " .		
        "'" . addslashes($this->desc_producto) . "', " .
        "'" . addslashes($this->nm_proveedor_producto) . "', " .
        $this->precio_producto .  ", " .
        $this->costo_interno_producto .  ", " .		
        "'" . addslashes($this->foto_producto) . "', " .
        "'" . addslashes($this->barcode_producto) . "', " .
        $this->stock_minimo_producto .  ", " .		
        "'" . $this->obs_producto . "')"; 
        
        return $cons;        
    }
    
    function modificarProducto() {
		
		$this->setDefaultNumeros();
		
        $cons = " update Productos set " .
        "cd_categoria_producto = " . $this->cd_categoria_producto . ", " .
		//"sku_producto = '" . $this->sku_producto . "', " .
		//"secuencial_producto =" . $this->secuencial_producto . ", " .
		"cd_estado_sistema =" . $this->cd_estado_sistema . ", " .
		//"cd_unidad_producto = " . $this->cd_unidad_producto . ", " .
        "nm_producto = '" . addslashes($this->nm_producto) . "', " .
		//"fe_ingreso_producto = '" . $this->fe_ingreso_producto . "', " . 
		"desc_producto = '" . addslashes($this->desc_producto) . "', " .
		"nm_proveedor_producto = '" . addslashes($this->nm_proveedor_producto) . "', " .
        "precio_producto = " . $this->precio_producto . ", " .
		"costo_interno_producto = " . $this->costo_interno_producto . ", " .	
        "foto_producto = '" . addslashes($this->foto_producto) . "', " .
        //"barcode_producto = " . "'" .  $this->barcode_producto . "', " .
        "stock_minimo_producto = " . $this->stock_minimo_producto . ", " .
        "obs_producto = '" . addslashes($this->obs_producto) . "' " .
        "where cd_producto = " . $this->cd_producto;
        
        return $cons;
    }
    
    function consultarProducto() {
        $cons = "select * from Productos where cd_producto = " . $this->cd_producto;
        //echo $cons;
        return $cons;
    }
	
	function consultarProductoDadoSku() {
		$cons = "select * from Productos where sku_producto = '" . $this->sku_producto . "'";
        return $cons;
	}
    
    function obtenerProducto($fila) {
        //var_dump($fila);
        //echo "===========Entrando a get producto===============";
        $this->cd_producto = $fila["CD_PRODUCTO"];
        $this->cd_categoria_producto = $fila["CD_CATEGORIA_PRODUCTO"];
        $this->sku_producto = $fila["SKU_PRODUCTO"];
        $this->secuencial_producto = $fila["SECUENCIAL_PRODUCTO"];
        $this->cd_estado_sistema = $fila["CD_ESTADO_SISTEMA"];
        $this->cd_unidad_producto = $fila["CD_UNIDAD_PRODUCTO"];
        $this->nm_producto = $fila["NM_PRODUCTO"];
        $this->fe_ingreso_producto = $fila["FE_INGRESO_PRODUCTO"];
        $this->desc_producto = $fila["DESC_PRODUCTO"]; 
        $this->nm_proveedor_producto = $fila["NM_PROVEEDOR_PRODUCTO"];
        $this->precio_producto = $fila["PRECIO_PRODUCTO"];
        $this->costo_interno_producto = $fila["COSTO_INTERNO_PRODUCTO"];
        $this->foto_producto = $fila["FOTO_PRODUCTO"];
        $this->barcode_producto = $fila["BARCODE_PRODUCTO"];
        $this->stock_minimo_producto = $fila["STOCK_MINIMO_PRODUCTO"];
        $this->obs_producto = $fila["OBS_PRODUCTO"];
        
    }
    
	//haer metodos seter y geter
    function setCdProducto($cdProducto) {
        $this->cd_producto = $cdProducto;
    }
	
    function getCdProducto() {
        return $this->cd_producto;
    }
	

    function getCdCategoriaProducto() {
        return $this->cd_categoria_producto;
    }    
    

	function getSkuProducto() {
		return $this->sku_producto;
	}
	
	function setSkuProducto($sku_producto) {
		$this->sku_producto = $sku_producto;
	}
	
	function getCdEstadoProducto() {
		return $this->cd_estado_sistema;
	}
    
	function getCdUnidadProducto() {
		return $this->cd_unidad_producto;
	}
	
	function getNmProducto() {
		return $this->nm_producto;
	}

	function setNmProducto($nmProducto) {
		$this->nm_producto = $nmProducto;
	}
	
	function getFeIngresoProducto() {
		return $this->fe_ingreso_producto;
	}
	
	function getDescProducto() {
		return $this->desc_producto;
	}
	
	function getNmProveedorProducto() {
		return $this->nm_proveedor_producto;
	}
	
	function getPrecioProducto() {
		return $this->precio_producto;
	}
	
	function getCostoInternoProducto() {
		return $this->costo_interno_producto;
	}
	
	function setFotoProducto($fotoProducto) {
		$this->foto_producto = $fotoProducto;
	}
	
	function getFotoProducto() {
		return $this->foto_producto;
	}
	
	function getBarcodeProducto() {
		return $this->barcode_producto;
	}
	
	function getStockMinimoProducto() {
		return $this->stock_minimo_producto;
	}
	
	function getObsProducto() {
		return $this->obs_producto;
	}
	
	
    //buscar productos por el nombre
    function buscarProductosPorNombre($inicio, $fin, $contarTodos) {
		
        $sql = "select cd_producto, nm_producto, sku_producto, cd_estado_sistema, nm_categoria_producto ".
		" from productos p, categorias_producto c " .
		" where ". $this->criterio_buscar ." like '%" . $this->nm_producto . "%' " .
		" and p.cd_categoria_producto = c.cd_categoria_producto" .
		" order by nm_producto ";
		if(!$contarTodos) {
			$sql .= " limit " . $inicio . ", " . $fin;
		}
				
        return $sql;
    }
    	
	
	function eliminarProducto() {
		$sql = "delete from productos where cd_producto = " . $this->cd_producto;
		return $sql;
	}
	
	/* Se debe validar si existen acciones asociadas al producto: compras o ventas,
	si existen entonces no se debería eliminar el producto.
	*/
	function validarEliminarProducto() {
		$sql = "select count(1) as conteo from acciones_producto where cd_producto =" . $this->cd_producto;
		//echo "validacioneliinacionpaciente::: " . $sql;
		return $sql;
	}
	
	function obtenerNumTotalProductos() {
		$sql = "select count(*) as conteo from productos";
		return $sql;
	}
	
	
	
	function obtenerListadoTodosProductos() {
		$sql = "select sku_producto, nm_producto, c.nm_categoria_producto, e.nm_estado_sistema, " .
				" p.fe_ingreso_producto, p.precio_producto " .
				" from productos p, categorias_producto c, estados_sistema e " .
				" where " .
				" p.cd_categoria_producto = c.cd_categoria_producto " .
				" and p.cd_estado_sistema = e.cd_estado_sistema " .
				" order by p.nm_producto ";

		return $sql;
	}
}

?>