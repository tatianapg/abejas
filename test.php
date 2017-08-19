<?php

require_once("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/tcpdf/tcpdf.php");
require_once("./aplicacion/model/inventario/Inventario.php");
require_once("./aplicacion/model/accionProducto/AccionProducto.php");
require_once("./aplicacion/model/cabeceraComprobante/Comprobante.php");
require_once("./aplicacion/model/sucursal/Sucursal.php");
require_once("./include/dabejas_config.php");



	//recuperar los parámetros
	$cdSucursal = 1;
	$feInicio = "2017-08-16";
	$feFin = "2017-08-16";
	$tipoReporte = "resumen_ventas";
	$tipoMovimiento = 2;
	//un usuario o todos
	$cdUsuario = 1;


	//establcer la conexion con la bdd
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect("localhost", "tatianag", "Cpsr19770428", "bdd_abejas");
	$sql ="sin sentencia";

	//consultar la sucursal
	$nmSucursal = "";
	if($cdSucursal != -1 && $cdSucursal != '') {
		$sucursal = new Sucursal();
		$sucursal->setCdSucursal($cdSucursal);
		$sqlSucursal = $sucursal->consultarSucursal();
		$filaSucursal = $pdo->pdoGetRow($sqlSucursal);
		$sucursal->obtenerSucursal($filaSucursal);
		$nmSucursal = $sucursal->getNmSucursal();
	} else
		$nmSucursal = "Todas";	


	//obtener el inventario activo
	$inventario = new Inventario();
	$inventario->setCdSucursal($cdSucursal);
	$sqlActivo = $inventario->obtenerCdInventarioActivo();		
	$filaActivo = $pdo->pdoGetRow($sqlActivo);
	$cdInventarioActivo = $filaActivo["cd_inventario"];	
	
	if(!$cdInventarioActivo) {
		$tbl = "<table><tr><td>No existe inventario activo para la sucursal seleccionada.</td></tr></table>";
			
	} else {

	/////////////////////////////////
		//setear los parámetros
		$reporteAccion = new AccionProducto();
		$reporteAccion->setCdInventario($cdInventarioActivo);
		$reporteAccion->setFeReporteDiarioInicio($feInicio . " 00:00:00");
		$reporteAccion->setFeReporteDiarioFin($feFin . " 23:59:59"); //date('Y-m-d')
		$reporteAccion->setCdSucursal($cdSucursal);
		$reporteAccion->setCdTipoAccion($tipoMovimiento);

		$reporte = "";

		switch($tipoReporte) {
			
			case "resumen_ventas":
			
				$reporte ="resumen_ventas";
				//configurar para ventas: porque es reporte de ventas diario
				//hacer la consulta para el reporte
				//en primera instancia son ventas cobras (accion, subtipo/ 2, 5 / ventas, venta cliente final)
				$reporteAccion->setCdTipoAccion(2);
				$reporteAccion->setCdSubtipoAccion(5);
				$sql = $reporteAccion->generarResumenDiarioVentas();
				$result = $pdo->pdoGetAll($sql);	

				/*
				********************************************
				 ************ Primera parte: ventas cobradas
				 ********************************************
				*/	
				//inicio de generación de reporte: primera parte VENTAS COBRADAS	
				$tbl = '<table border="1">';
				$tbl .= '<tr><td colspan="9">'.$sql.'</td></tr>';
				$tbl .= '<tr><td colspan="9"><b>RESUMEN DE VENTAS - COBROS A CLIENTES</b></td></tr>';
				$tbl .= '<tr><td colspan="9"><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . ' - SOLO VENTAS COBRADAS A CLIENTES</b></td></tr>';
				$tbl .= '<tr><td width=\"25\"><b>No.</b></td><td width=\"144\"><b>Producto</b></td><td><b>Código</b></td><td><b>Unidades</b></td>';
				$tbl .= '<td><b>Precio($)</b></td><td><b>Ingreso($)</b></td><td><b>Costo($)</b></td>';
				$tbl .= '<td><b>Sucursal</b></td><td><b>Usuario</b></td>';
				$tbl .= '</tr>';

				$registros = 0;
				$sumaUnidades = 0;
				$sumaIngreso= 0;
				$sumaCosto = 0;
				
				foreach($result as $fila) {
					$registros++;
					$tbl .="<tr>";
					$tbl .= "<td>" . $registros . "</td>";
					$tbl .= "<td>" . $fila["nm_producto"] . "</td>";
					$tbl .= "<td>" . $fila["sku_producto"] . "</td>";
					//$tbl .= "<td>" . $fila["nm_subtipo"] . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["cantidad"], 0) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["precio"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["ingreso"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["costo"], 2) . "</td>";
					//$tbl .= "<td>" . $fila["fe_ultima_compra"] . "</td>";
					$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
					$tbl .= "<td>" . $fila["login_usuario"] . "</td>";
					$tbl .="</tr>";	
					//sumas de las unidades vendidas y precios
					$sumaUnidades += $fila["cantidad"];
					$sumaIngreso += $fila["ingreso"];
					$sumaCosto += $fila["costo"];
				}
				
				$tbl .= "<tr><td></td><td></td><td><b>Totales:</b></td><td align=\"right\"><b>". $sumaUnidades . "</b></td>";
				$tbl .= "<td></td><td align=\"right\"><b>". number_format($sumaIngreso, 2)."</b></td><td align=\"right\"><b>".number_format($sumaCosto, 2)."</b></td><td></td></tr>";			
				$tbl .= "</table><p></p>";		
				
				/*
				********************************************
				 ************ Segunda parte: devoluciones efectuadas
				 ********************************************
				*/
				$reporteAccion->setCdTipoAccion(1);
				$reporteAccion->setCdSubtipoAccion(4);
				$sql = $reporteAccion->generarResumenDiarioVentas();
				$result = $pdo->pdoGetAll($sql);	
				$tbl .= '<table border="1">';
				$tbl .= '<tr><td colspan="9">'.$sql.'</td></tr>';
				$tbl .= '<tr><td colspan="9"><b>RESUMEN DE DEVOLUCIONES A CLIENTES</b></td></tr>';
				$tbl .= '<tr><td colspan="9"><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
				$tbl .= '<tr><td width=\"25\"><b>No.</b></td><td width=\"144\"><b>Producto</b></td><td><b>Código</b></td><td><b>Unidades</b></td>';
				$tbl .= '<td><b>Precio($)</b></td><td><b>Egreso($)</b></td><td><b>Costo($)</b></td>';
				$tbl .= '<td><b>Sucursal</b></td><td><b>Usuario</b></td>';
				$tbl .= '</tr>';

				$registros = 0;
				$sumaUnidades = 0;
				$sumaEgreso= 0;
				
				foreach($result as $fila) {
					$registros++;
					$tbl .="<tr>";
					$tbl .= "<td>" . $registros . "</td>";
					$tbl .= "<td>" . $fila["nm_producto"] . "</td>";
					$tbl .= "<td>" . $fila["sku_producto"] . "</td>";
					//$tbl .= "<td>" . $fila["nm_subtipo"] . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["cantidad"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["precio"],2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["ingreso"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["costo"], 2) . "</td>";
					//$tbl .= "<td>" . $fila["fe_ultima_compra"] . "</td>";
					$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
					$tbl .= "<td>" . $fila["login_usuario"] . "</td>";
					$tbl .= "</tr>";	
					//sumas de las unidades vendidas y precios
					$sumaUnidades += $fila["cantidad"];
					$sumaEgreso += $fila["ingreso"];
				}
				
				
				$tbl .= "<tr><td></td><td></td><td><b>Totales:</b></td><td align=\"right\"><b>". $sumaUnidades . "</b></td>";
				$tbl .= "<td></td><td align=\"right\"><b>". number_format($sumaEgreso, 2, '.', '')."</b></td><td></td><td></td></tr>";			
				$tbl .= "</table><p></p>";	
				
				
				/*
				********************************************
				 ************ Tercera parte: descuentos efectuadas
				 ********************************************
				*/
				$comprobante = new Comprobante();
				$comprobante->setCdUsuario($cdUsuario);
				$comprobante->setCdSucursal($cdSucursal);
				$comprobante->setFeReporteInicio($feInicio . " 00:00:00");
				$comprobante->setFeReporteFin($feFin . " 23:59:59");			
				$sql = $comprobante->obtenerDescuentosPorParametros();
				$result = $pdo->pdoGetRow($sql);	
				$totalDescuentos = $result["suma_descuentos"];
				
				$tbl .= '<table border="1">';
				$tbl .= '<tr><td>'.$sql.'</td></tr>';
				$tbl .= '<tr><td><b>RESUMEN DE DESCUENTOS EN VENTAS</b></td></tr>';
				$tbl .= '<tr><td><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
				$tbl .= '<tr><td>Descuentos efectuados($): <b>'.$totalDescuentos.'</b></td></tr>';
				$tbl .= "</table><p></p>";
				
				$totalCaja = $sumaIngreso - $sumaEgreso - $totalDescuentos;
				$tbl .= "<table><tr><td><h3>Total caja(Ingresos - Egresos - Descuentos): $</h3></td><td><h3>".number_format($totalCaja, 2, '.', '')."</h3></td></tr>";
				$tbl .= "<tr><td>Fecha/hora de generación del reporte: </td><td>".date('Y-m-d H:i:s')."</td></tr></table>";
				
				

				//fin de generación de reporte
			
				break;
			
			case "movimientos_diario":
			
				$reporte ="movimientos_diario";
				//si quiere obtener todos los movimientos o solo de un tipo: compras(1) o ventas(2)
				//hacer la consulta para el reporte
				$sql = $reporteAccion->generarDetalleMovimientos();
				$result = $pdo->pdoGetAll($sql);	

				//inicio de generación de reporte	
				$tbl = '<table border="1">';
				//$tbl .= '<tr><td colspan="10">'.$sql.'</td></tr>';
				$tbl .= '<tr><td colspan="10"><b>REPORTE DE MOVIMIENTOS</b></td></tr>';
				$tbl .= '<tr><td colspan="10"><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
				$tbl .= '<tr><td><b>No. venta</b></td><td><b>Producto</b></td><td><b>Código</b></td><td><b>Tipo</b></td>';
				$tbl .= '<td><b>Unidades</b></td>';
				$tbl .= '<td><b>Precio($)</b></td><td><b>Ingreso($)</b></td><td><b>Fe.venta</b></td>';
				$tbl .= '<td><b>Sucursal</b></td>';
				$tbl .= '<td><b>Usuario</b></td>';
				$tbl .= '</tr>';

				$registros = 0;
				$sumaUnidades = 0;
				$sumaIngreso= 0;
				
				foreach($result as $fila) {
					$registros++;
					$tbl .="<tr>";
					//$tbl .= "<td>" . $registros . "</td>";
					$tbl .= "<td>" . $fila["cd_cabecera"] . "</td>";
					$tbl .= "<td>" . $fila["nm_producto"] . "</td>";
					$tbl .= "<td>" . $fila["sku_producto"] . "</td>";
					$tbl .= "<td>" . $fila["nm_subtipo"] . "</td>";
					$tbl .= "<td align=\"right\">" . $fila["cantidad"] . "</td>";
					$tbl .= "<td align=\"right\">" . $fila["precio"] . "</td>";
					$tbl .= "<td align=\"right\">" . $fila["ingreso"] . "</td>";
					$tbl .= "<td>" . $fila["fe_ultima_compra"] . "</td>";
					$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
					$tbl .= "<td>" . $fila["login_usuario"] . "</td>";
					$tbl .="</tr>";	
					//sumas de las unidades vendidas y precios
					$sumaUnidades += $fila["cantidad"];
					$sumaIngreso += $fila["ingreso"];
				}
				
				$tbl .= "<tr><td><b>Totales</b></td><td></td><td></td><td></td><td align=\"right\"><b>". $sumaUnidades."</b></td>";
				$tbl .= "<td></td><td align=\"right\"><b>". number_format($sumaIngreso, 2, '.', '')."</b></td><td></td><td></td>";
				$tbl .= "<td></td></tr>";
				
				$tbl .= "</table>";		
				//fin de generación de reporte

				break;
				
			case "movimientos_fecha":	
				$reporte ="movimientos_fecha";
				break;
			
			case "stock":
				$reporte ="stock";
				//1. limpiar codigos
				$sql = $reporteAccion->limpiarCodigosStock();
				$numEliminados = $pdo->pdoInsertar($sql);					
				//2. insertar codigos del inventario actual
				$sql = $reporteAccion->insertarCodigosStock();
				$numInsertados = $pdo->pdoInsertar($sql);	
				
				
				//3. obtener codigos y nombres de productos	
				$sql = $reporteAccion->obtenerNombresStock();
				$resultProductos = $pdo->pdoGetAll($sql);
				$tbl = "";
				///$tbl .= "<table><tr><td>".$sql."</td></tr></table>";	
				
				//4. obtener el inventario inicial
				$sql = $reporteAccion->obtenerIInicialStock();
				$resultIInicial = $pdo->pdoGetAll($sql);	
				//$tbl .= "<table><tr><td colspan=\"7\">".$sql."</td></tr></table>";		

				//5. obtener las compras
				$sql = $reporteAccion->obtenerComprasStock();
				$resultCompras = $pdo->pdoGetAll($sql);	
				///$tbl .= "<table><tr><td>".$sql."</td></tr></table>";	
				
				//6. obtener las ventas
				$sql = $reporteAccion->obtenerVentasStock();
				$resultVentas = $pdo->pdoGetAll($sql);	
				///$tbl .= "<table><tr><td>".$sql."</td></tr></table>";		
				//$tbl = "<table><tr><td>del ".$sql."</td></tr></table>";
				
				/* considerar el número de productos que se recuperaron
				
				*/
				$filas = count($resultProductos);
				$tbl .= "<table border=\"1\">";
				//$tbl .= "<tr><td>".$sql."</td></tr>";
				$tbl .= '<tr><td colspan="7"><b>REPORTE DE INVENTARIO ACTIVO ACTUAL</b></td></tr>';
				$tbl .= '<tr><td colspan="7"><b>Sucursales: Todas / Movimientos: Todos los del inventario activo / Fecha de generación: '.date('Y-m-d').'<br></b></td></tr>';
				
				$tbl .= "<tr><td><b>No.</b></td><td><b>Código</b></td><td><b>Producto</b></td><td align=\"right\"><b>I.Inicial</b></td>";
				$tbl .= "<td align=\"right\"><b>(+)Compras</b></td><td align=\"right\"><b>(-)Ventas</b></td><td align=\"right\"><b>(=)I.Final</b></td></tr>";
				$i=0;
				
				$sumaIInicial=0;
				$sumaCompras = 0;
				$sumaVentas = 0;
				$sumaIFinal = 0;

				for($i=0; $i < $filas; $i++) {
					$iinicial = 0;
					$compras = 0;
					$ventas = 0;
					$ifinal = 0;
					
					$tbl .= "<tr>";
					$tbl .= "<td>".($i+1)."</td>";
					$tbl .= "<td>" . $resultProductos[$i]["sku_producto"] . "</td>";
					$tbl .= "<td>" . $resultProductos[$i]["nm_producto"] . "</td>";
					if($resultProductos[$i]["cd_producto"] == $resultIInicial[$i]["cd_producto"]) {				
						if(!$resultIInicial[$i]["cantidad_inicial"])
							$iinicial = 0;
						else
							$iinicial = $resultIInicial[$i]["cantidad_inicial"];				
						//$tbl .= "<td align=\"right\">" . $resultIInicial[$i]["cantidad_inicial"] . "</td>";
						$tbl .= "<td align=\"right\">" . $iinicial . "</td>";
						$sumaIInicial += $iinicial;
					}	
					if($resultProductos[$i]["cd_producto"] == $resultCompras[$i]["cd_producto"]) {
						
						if (!$resultCompras[$i]["cantidad_compras"]) {
							$compras = 0;
						} else
							$compras = $resultCompras[$i]["cantidad_compras"];
						$tbl .= "<td align=\"right\">" . $compras . "</td>";
						$sumaCompras += $compras;
					}	
					if($resultProductos[$i]["cd_producto"] == $resultVentas[$i]["cd_producto"]) {
						if(!$resultVentas[$i]["cantidad_ventas"])
							$ventas = 0;
						else
							$ventas = $resultVentas[$i]["cantidad_ventas"];
						
						$tbl .= "<td align=\"right\">" . $ventas . "</td>";
						$sumaVentas += $ventas;
					}	
					//calcular el inventario final
					$ifinal = $iinicial + $compras - $ventas;
					$tbl .= "<td align=\"right\">" . $ifinal . "</td>";
					$tbl .= "</tr>";
					
					$sumaIFinal += $ifinal;
				}
				//$tbl .= "</table>";
				
				//colocar una fila con los totales al final
				$tbl .= "<tr><td></td><td></td><td></td><td align=\"right\">".$sumaIInicial."</td><td align=\"right\">".$sumaCompras."</td><td align=\"right\">".$sumaVentas."</td><td align=\"right\">".$sumaIFinal."</td></tr>";
				$tbl .= "</table>";
				
				
				break;

			case "mas_vendidos":
				$registros = 0;
				$reporte ="mas_vendidos";
				$sql = $reporteAccion->obtenerProductosMasVendidos();
				$result = $pdo->pdoGetAll($sql);
				
				$tbl = '<table border="1">';
				$tbl .= '<tr><td colspan="6"><b>REPORTE DE PRODUCTOS MÁS VENDIDOS</b></td></tr>';
				$tbl .= '<tr><td colspan="6"><b>Sucursales:'.$nmSucursal. ' Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b><br></td></tr>';
				$tbl .= '<tr><td><b>No.</b></td><td><b>Producto</b></td><td><b>Código</b></td><td><b>Cantidad vendida(u)</b></td>';
				$tbl .= '<td><b>Fe.mínima compra</b></td><td><b>Fe.máxima compra</b></td></tr>';

			
				foreach($result as $fila) {
							$registros++;
							$tbl .="<tr>";
							$tbl .= "<td>" . $registros . "</td>";
							$tbl .= "<td>" . $fila["nm_producto"] . "</td>";
							$tbl .= "<td>" . $fila["sku_producto"] . "</td>";
							$tbl .= "<td align=\"right\">" . $fila["cantidad_ventas"] . "</td>";
							$tbl .= "<td align=\"right\">" . $fila["fe_venta_min"] . "</td>";
							$tbl .= "<td align=\"right\">" . $fila["fe_venta_max"] . "</td>";
							$tbl .="</tr>";	
				}
				
				$tbl .= "</table>";
				
				break;
		} 
			
	/////////////////////////////////
	
	} //fin si existe inventario activo
	/*
	--------------------------------------------------
	generar reporte PDF - tabla de movimientos
	--------------------------------------------------
	*/
	echo ($tbl);

?>