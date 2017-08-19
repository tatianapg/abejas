<?php
class Categoria {
	private $cd_categoria_producto;
	private $nm_categoria_producto;
	private $desc_categoria_producto;
	
	function __construct() {
	}
	
	function setCategoria($cd_categoria_producto, $nm_categoria_producto, $desc_categoria_producto) {
		$this->cd_categoria_producto = $cd_categoria_producto;
		$this->nm_categoria_producto = $nm_categoria_producto;
		$this->desc_categoria_producto = $desc_categoria_producto;
	}
	
	function getCdCategoria() {
		return $this->cd_categoria_producto;
	}
	
	function setCdCategoria($cd_categoria_producto) {
		$this->cd_categoria_producto = $cd_categoria_producto;
	}
	
	function getNmCategoria() {
		return $this->nm_categoria_producto;
	}
	
	function crearCategoria() {
		$sql = "insert into categorias_producto(cd_categoria_producto, nm_categoria_producto, desc_categoria_producto) values(".
			$this->cd_categoria_producto . ", " .
			"'" . $this->nm_categoria_producto . "', " .
			"'" . $this->desc_categoria_producto . "')";
			
		return $sql;	
	}
	
	function modificarCategoria() {
        $cons = " update categorias_producto set " .
        "nm_categoria_producto = '" . addslashes($this->nm_categoria_producto) . "', " .
		"desc_categoria_producto = '" . addslashes($this->desc_categoria_producto) . "' " .
        "where cd_categoria_producto = " . $this->cd_categoria_producto;
        
        return $cons;
    }
    
    function consultarCategoria() {
        $cons = "select * from categorias_producto where cd_categoria_producto = " . $this->cd_categoria_producto;
        return $cons;
    }
    
    function obtenerCategoria($fila) {
        //echo "===========Entrando a get categoria ===============";
        $this->cd_categoria_producto = $fila["CD_CATEGORIA_PRODUCTO"];
        $this->nm_categoria_producto = $fila["NM_CATEGORIA_PRODUCTO"];
        $this->desc_categoria_producto = $fila["DESC_CATEGORIA_PRODUCTO"];
    }
    
	function getTodasCategorias() {
		$sql = "select cd_categoria_producto as codigo, nm_categoria_producto as texto from categorias_producto order by nm_categoria_producto";
		return $sql;
	}	
	
	function eliminarCategoria() {
		$sql = "delete from categorias_producto where cd_categoria_producto = " . $this->cd_categoria_producto;
		return $sql;
	}
	
}
?>