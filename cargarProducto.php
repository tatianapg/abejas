<html>
<head>
</head>
<body>
<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/accionProducto/accionProducto.php");
include("./aplicacion/model/producto/Producto.php");
include("./aplicacion/model/inventario/Inventario.php");
include("./aplicacion/model/tipoAccion/TipoAccion.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

		$pdo = new PdoWrapper();
		$con = $pdo->pdoConnect();
		//identificar si es una compra o venta
		//$codigoCabecera es cero
		//porque no lo hicieron por venta directa al cliente, sino otra baja/descarga
		$etiquetaAccion = "";
		if(isset($_POST["txtTipoAccion"])) {
			if($_POST["txtTipoAccion"] == 1) {
				$etiquetaAccion = "cargadas";
			} else if($_POST["txtTipoAccion"] == 2) 
				$etiquetaAccion = "descargadas";
		} 
			
		if($con && isset($_POST["txtCodigoProducto"]) && isset($_POST["txtCodigoProducto"]) > 0 ) {
			//obtener algunos datos del producto
			$producto = new Producto();
			$producto->setSkuProducto($_POST["txtCodigoProducto"]);
			$sqlCodigo = $producto->consultarProductoDadoSku();
			
			$resultProducto = $pdo->pdoGetRow($sqlCodigo);
			$producto->obtenerProducto($resultProducto);
			/*
			setAccion( $cd_producto, $cd_sucursal, $cd_tipo_accion, $obs_accion, $cantidad_accion, 
			$fe_accion, $precio_accion, $costo_accion, $cd_inventario, $es_carga_inicial, 
			$cd_subtipo_accion, $cd_usuario, $cd_cabecera)
			*/
			
			$cargaInicial = 0;
			if(isset($_POST["cmbInicial"])) {
				$cargaInicial = $_POST["cmbInicial"];
			} 

			//solo si encontró el código de producto carga, caso contrario no guarda
			//if(isset($_POST["txtCodigoProducto"])) {
			if($producto->getCdProducto() > 0) {
				//recuperar el inventario activo para insertar
				$inventario = new Inventario();
				$inventario->setCdSucursal($_SESSION["suc_venta"]);
				$sqlActivo = $inventario->obtenerCdInventarioActivo();
				$filaActivo = $pdo->pdoGetRow($sqlActivo);
				$cdInventarioActivo = $filaActivo["cd_inventario"];	
				//echo "El inventario es:: " . $cdInventarioActivo;
				
				//indagar si la acción tiene precio o no
				$tipoAccion = new TipoAccion();
				$tipoAccion->setCdTipoAccion($_POST["cmbSubtipo"]);
				$sql = $tipoAccion->consultarTipoAccion();
				$filaTipo = $pdo->pdoGetRow($sql);
				$tipoAccion->obtenerTipoAccion($filaTipo);
				$colocarPrecio = $tipoAccion->getIngresarDineroAccion();
				//setear si se coloca precio o no
				$precio = 0;
				if($colocarPrecio)
					$precio = $producto->getPrecioProducto();
								
				//obtener el usuario
				$cdUsuario = $_SESSION["cd_usuario"];			
						
				$accion = new AccionProducto();
				$accion->setAccion($producto->getCdProducto(), $_SESSION["suc_venta"], $_POST["txtTipoAccion"], 
				"", $_POST["txtCantidadAccion"], date("Y-m-d H:i:s"), $precio, $producto->getCostoInternoProducto(), 
				$cdInventarioActivo, $cargaInicial, $_POST["cmbSubtipo"], $cdUsuario, -1);
				$sql = $accion->crearAccion();
				//echo "consulta sql accionproducto:: " . $sql;
				$numInserts = $pdo->pdoInsertar($sql);
				//echo "Fueron insertadas: " . $numInserts;
				$codigoAccion = $pdo->pdoLasInsertId();
				
				echo "Fueron " . $etiquetaAccion . " " . $_POST["txtCantidadAccion"] . " unidades con codigo " .  $_POST["txtCodigoProducto"] . " - " . strtoupper($producto->getNmProducto());
			} else {
				echo "El código " . $_POST["txtCodigoProducto"] . " no existe.  Por favor revisar.";
			}
					
	} 
}	

?>
</body>
</html>