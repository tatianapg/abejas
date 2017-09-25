<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/accionProducto/accionProducto.php");
include("./aplicacion/model/producto/Producto.php");
include("./aplicacion/model/inventario/Inventario.php");
include("./aplicacion/model/tipoAccion/TipoAccion.php");
include("./aplicacion/model/cabeceraComprobante/Comprobante.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	//aqui instanciamos un objeto acción
	$accion = new AccionProducto();		
	$comprobante = new Comprobante();	

	if(isset($_POST["grabarVenta"]) && $_POST["grabarVenta"] == 1) {

		$pdo = new PdoWrapper();
		$con = $pdo->pdoConnect();

		//identificar si es una compra o venta
		//tenemos un array que tiene: codigo precio nombre, la cantidad es 1
		/*
		setAccion( $cd_producto, $cd_sucursal, $cd_tipo_accion, $obs_accion, $cantidad_accion, $fe_accion, $precio_accion, $costo_accion, $cd_inventario, $es_carga_inicial, $cd_subtipo_accion, $cd_usuario)
		*/
		$cdSubtipoAccion = 5;

		if($con && isset($_SESSION["suc_venta"]) && isset($_SESSION["cd_usuario"]) 
			&& isset($_SESSION["lista_productos"]) ) {		
			$insertados = 0;
			$secuencialCabecera = 0;		
			//array par los codigos insertados
			$codigosInsertados = array();	
			$banderaImprimir = 1;
			
			/*-------------------------------------------------------
			-- mediante una transacción ingresar cabecera y detalles
			---------------------------------------------------------
			*/
			//primero insertar la cabecera y luego el detalle
			//-------------------------------------------------------
			//inicio: guardó todos los detalles ahora, guardar el COMPROBANTE
			//-------------------------------------------------------
			/*
			($cd_cabecera, $fe_comprobante, $nm_cliente, $ci_cliente, $total_comprobante, 
			$descuento_comprobante, $cd_sucursal, $num_items_comprobante)
			*/			
			$nombreCliente = "Consumidor final";
			if(isset($_POST["txtCliente"]) && $_POST["txtCliente"] != "" ) {
				$nombreCliente = $_POST["txtCliente"];
				/* limpiar el nombre del cliente ya que puede contener mayores y menores y
					esto da problema al imprimir con la librería PDF
				*/
				$nombreCliente = str_replace(array('<', '>'), '', $nombreCliente);
			}
			$subtotal = "0";
			if(isset($_POST["txtSubtotal"]) && $_POST["txtSubtotal"] != 0 ) {
				$subtotal = $_POST["txtSubtotal"];
			}
			$numItems = "0";
			if(isset($_POST["txtItems"]) && $_POST["txtItems"] != 0 ) {
				$numItems = $_POST["txtItems"];
			}			
			$descuento = "0";
			if(isset($_SESSION["descuento"][0]["precio"]) && $_SESSION["descuento"][0]["precio"] != "" )
				$descuento = $_SESSION["descuento"][0]["precio"];
			
			$aPagarComprobante = $subtotal - $descuento;
			
			//obtener el secuencial del comprobante
			$sql = $comprobante->obtenerSecuencialComprobante($pdo->getDbApp());
			$resultCodigo = $pdo->pdoGetRow($sql);
			$ultimoIncrement = $resultCodigo["conteo"];
			
			//con este valor actualizo el código del comprobante
			$codigoComprobante = str_pad($_SESSION["suc_venta"], 3, "0", STR_PAD_LEFT) . "-" . str_pad($ultimoIncrement, 7, "0", STR_PAD_LEFT);

						
			$comprobante->setComprobante(0, date("Y-m-d H:i:s"), $nombreCliente, "", $subtotal,
			$descuento, $_SESSION["suc_venta"], $numItems, $_SESSION["cd_usuario"], $aPagarComprobante,
			$codigoComprobante);						
			$sqlComprobante = $comprobante->crearComprobante();
			
			//////----- INICIAR TRANSACCIÓN PARA GUARDAR COMPROBANTE Y DETALLE AL MISMO TIEMPO -----
			$conexion = $pdo->getConection();
			$conexion->beginTransaction();
						
			try {
				//echo "entro a la transaccion...";
				$pdo->pdoInsertar($sqlComprobante);
				$secuencialCabecera = $pdo->pdoLasInsertId();
				
														
				//Inicio guardar el detalle de la venta
				foreach($_SESSION["lista_productos"] as $fila) {
					$skuProducto = $fila["codigo"];		
					////////////////////////	
					/////----
					//obtener algunos datos del producto
					$producto = new Producto();
					$producto->setSkuProducto($skuProducto);
					$sqlCodigo = $producto->consultarProductoDadoSku();
						
					$resultProducto = $pdo->pdoGetRow($sqlCodigo);
					$producto->obtenerProducto($resultProducto);
						
					//solo si encontró el código de producto carga, caso contrario no guarda
					if($producto->getCdProducto() > 0) {
						//recuperar el inventario activo para insertar
						$inventario = new Inventario();
						$inventario->setCdSucursal($_SESSION["suc_venta"]);
						$sqlActivo = $inventario->obtenerCdInventarioActivo();
						$filaActivo = $pdo->pdoGetRow($sqlActivo);
						$cdInventarioActivo = $filaActivo["cd_inventario"];	
						
						//indagar si la acción tiene precio o no
						$tipoAccion = new TipoAccion();
						$tipoAccion->setCdTipoAccion($cdSubtipoAccion);
						$sql = $tipoAccion->consultarTipoAccion();
						$filaTipo = $pdo->pdoGetRow($sql);
						$tipoAccion->obtenerTipoAccion($filaTipo);
						$colocarPrecio = $tipoAccion->getIngresarDineroAccion();
						//setear si se coloca precio o no
						$precio = 0;
						if($colocarPrecio)
							$precio = $producto->getPrecioProducto();
						// 2 es tipo acción ventas; 5 es subtipoacción venta final						
						//crear la acción, pero se crea arriba con el secuencial que se necesita
						$accion->setAccion($producto->getCdProducto(), $_SESSION["suc_venta"], 2, "",$fila["cantidad"], 
						date("Y-m-d H:i:s"), $precio, $producto->getCostoInternoProducto(), 
						$cdInventarioActivo, 0, 5, $_SESSION["cd_usuario"], $secuencialCabecera);
						$sql = $accion->crearAccion();
						$numInserts = $pdo->pdoInsertar($sql);
						$codigoAccion = $pdo->pdoLasInsertId();
						//insertados
									
						if($codigoAccion) {
							$codigosInsertados[$insertados] = $codigoAccion;
							$insertados++;						
						}
					} else {
						echo "El código " . $_POST["txtCodigoProducto"] . " no existe.  Por favor revisar.";
					}									
											
				}//fin for each	
				
				//si todo fue bien hacer commit
				$conexion->commit();												
				
			} catch(Exception $e) {
				$banderaImprimir = 0;
				echo $e->getMessage();
				$conexion->rollBack();
			}
			//////----- FIN DE LA TRANSACCIÓN PARA GUARDAR COMPROBANTE Y DETALLE -----

			if($banderaImprimir) {
				///-----
				//borrar los datos de la sesión
				unset($_SESSION["lista_productos"]);
				unset($_SESSION["descuento"]);
				
				//-------------------------------------------
				//Impresión en pantalla: esto sale de la base
				//------------------------------------------				
				if(count($codigosInsertados) > 0) {
					//consultar con el secuencial el detalle
					$accion->setCdCabecera($secuencialCabecera);
					$accion->setCdSucursal($_SESSION["suc_venta"]);
					$sql = $accion->recuperarAccionesDadaCabecera();
					$resultDetalle = $pdo->pdoGetAll($sql);
					
					//consultar la cabecera
					$comprobante->setCdCabecera($secuencialCabecera);
					$comprobante->setCdSucursal($_SESSION["suc_venta"]);
					$sql = $comprobante->getComprobante();
					$result = $pdo->pdoGetRow($sql);
					$comprobante->obtenerComprobante($result);
					
					$i=0;
					$precioTotal = 0;

					$tabla = "<table>";
					$tabla .= "<tr><td><b>Cantidad</b></td><td><b>Código</b></td><td><b>Descripción</b></td>";
					$tabla .= "<td><b>P.Unitario($)</b></td><td><b>Valor($)</b></td></tr>";
					foreach($resultDetalle as $fila) {
						$i++;
						$tabla .= "<tr>";
						$tabla .= "<td>".$fila["cantidad"]."</td>";
						$tabla .= "<td>".$fila["codigo"]."</td>";
						$tabla .= "<td>".$fila["nombre"]."</td>";
						$tabla .= "<td align=\"right\">". number_format($fila["precio"], 2, '.', '')."</td>";
						$valorFila = $fila["precio"] * $fila["cantidad"];
						$tabla .= "<td align=\"right\">".number_format($valorFila, 2, '.', '')."</td>";
						$tabla .= "</tr>";
						//$precioTotal += $fila["precio"];
					} 				
				}
				
				//imprimir el subtotal
				$totalSinDescuento = $comprobante->getTotalComprobante();
				$tabla .= "<tr><td align=\"right\" colspan=\"4\"><b>SUB-TOTAL($)</b></td><td align=\"right\"><b>".number_format($totalSinDescuento, 2, '.','')."</b></td><td></td></tr>";
					
				//imprimir el descuento				
				$descuento = $comprobante->getDescuentoComprobante();
				$tabla .= "<tr><td colspan=\"4\" align=\"right\"><b>DESCUENTO(-)</b></td><td align=\"right\"><b>". number_format($descuento, 2, '.', '') ."</b></td></tr>";			
				
				//imprimir el total 
				$totalFinal = $comprobante->getAPagarComprobante(); //$precioTotal - $descuento;
				//echo "El TOTAL FINAL ES::: " . $totalFinal;
														
				if($insertados==$i) {
					//si todo salió bien imprimir el mensaje;	
					$tabla .= "<tr><td align=\"right\" bgcolor=\"#00FF00\" colspan=\"4\"><h2>TOTAL($)</h2></td><td align=\"right\"><h2>".number_format($totalFinal, 2, '.','')."</h2></td><td align=\"right\"><h1></h1></td></tr>";
					//aquí crear un link para generar el reporte				
					//$tabla .= "<tr><td colspan=\"5\" bgcolor=\"#00FF00\"><h3><a href=\"#\" onclick=\"window.open('generarRecibo.php?rec=".$secuencialCabecera."')\">[Imprimir recibo de entrega No.".$secuencialCabecera."]</h3></a></td></tr></table>";				
					$tabla .= "<tr><td colspan=\"5\" bgcolor=\"#00FF00\"><h3><a href=\"#\" onclick=\"window.open('generarRecibo.php?rec=".$secuencialCabecera."')\">[Imprimir recibo de entrega No.".$codigoComprobante."]</h3></a></td></tr></table>";				
					$tabla .= "</table>";
					echo ($tabla);
				}
									
				///-----fin impresion en pantalla dessde base de datos
			}	

		} else {
			$accion->setCdSucursal($_SESSION["suc_venta"]);
			$accion->setCdUsuario($_SESSION["cd_usuario"]);
			$sql = $accion->consultarUltimoReciboGenerado();	
			$fila = $pdo->pdoGetRow($sql);
			$ultimoSeq = $fila["conteo"];
			echo "El recibo ya fue generado, ver el último ingresado por este usuario. <a href=\"#\" onclick=\"window.open('generarRecibo.php?rec=".$ultimoSeq."')\">[No. ".$ultimoSeq."]</a>";
		}
	}	
}
?>