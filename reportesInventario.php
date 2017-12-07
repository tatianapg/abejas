<?php
header("Content-type: application/pdf");
header("Content-type: application-download");
header("Content-Disposition: attachment; filename=rinventarios.pdf");
header("Content-Transfer-Encoding: binary");

require_once("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/tcpdf/tcpdf.php");
require_once("./aplicacion/model/inventario/Inventario.php");
require_once("./aplicacion/model/accionProducto/AccionProducto.php");
require_once("./aplicacion/model/cabeceraComprobante/Comprobante.php");
require_once("./aplicacion/model/sucursal/Sucursal.php");
require_once("./include/dabejas_config.php");

//money_format('%i', $number)
//number_format($number, 2, '.', '');

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {	
	//si valido el usuario, entonces generar el reporte
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Doctoras Abejas');
	$pdf->SetSubject('Reporte para Doctoras Abejas');
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Tratamientos Naturales", "Dir.1 Av. Mariscal Sucre y Pasaje N Local No.34\nDir.2 Centro Comercial de Mayoristas y Negocios Andinos, entrada principal");
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	// set default font subsetting mode
	$pdf->setFontSubsetting(true);
	$pdf->SetFont('times', '', 11, '', true);
	$pdf->AddPage();

	$cdSucursal = 0;
	if(isset($_POST["cmbSucursal"]))
		$cdSucursal = $_POST["cmbSucursal"];
	
	$feInicio = "";
	if(isset($_POST["txtFeInicioInventario"]))
		$feInicio = $_POST["txtFeInicioInventario"];
	
	$feFin = "";
	if(isset($_POST["txtFeFinInventario"]))
		$feFin = $_POST["txtFeFinInventario"];
	
	$tipoReporte = "";
	if(isset($_POST["reporte"]))
		$tipoReporte = $_POST["reporte"];
	
	$tipoMovimiento = "";
	if(isset($_POST["cmbAccion"]))
		$tipoMovimiento = $_POST["cmbAccion"];
	
	//un usuario o todos
	$cdUsuario = "";
	if(isset($_POST["cmbUsuario"]))
		$cdUsuario = $_POST["cmbUsuario"];

	//si no coloco valores en fechas entonces va la fecha actual del día por defecto
	if(!$feInicio) {
		$feInicio = date('Y-m-d');
		$feFin = date('Y-m-d');
	}
	
	//recuperar los parámetros
	$origen = "";
	if(isset($_POST["origen_reportes"]))
		$origen = $_POST["origen_reportes"];
		
	if(isset($_GET["invver"])) {
		$tipoReporte = "stock";
	}

	//establcer la conexion con la bdd
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();
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

	//analizar que inventario utilizar
	
	//obtener el inventario activo
	$inventario = new Inventario();	

	/* Si el origen es los reportes, entonces tomar como parámetros las fechas.  
	Si  el origen es "edición", tomar como parametro el codigo del reporte
	pero se asigna en el reporte de estado de inventario
	*/	
	$cdInventarioActivo = 0;
	$banderaSensible = 0;
	if(isset($_SESSION['ver_infosen'])  && $_SESSION['ver_infosen'] == 1)
		$banderaSensible = 1;
 
	/////////////////////////////////
		//setear los parámetros para todos los reportes
		$reporteAccion = new AccionProducto();
		$reporteAccion->setDatoSensible($banderaSensible);
		$reporteAccion->setCdInventario($cdInventarioActivo);
		$reporteAccion->setFeReporteDiarioInicio($feInicio . " 00:00:00");
		$reporteAccion->setFeReporteDiarioFin($feFin . " 23:59:59"); //date('Y-m-d')
		$reporteAccion->setCdSucursal($cdSucursal);
		$reporteAccion->setCdTipoAccion($tipoMovimiento);
		if($cdUsuario)
			$reporteAccion->setCdUsuario($_SESSION["cd_usuario"]);

		$reporte = "";

		switch($tipoReporte) {
			
			case "resumen_ventas":
			
				$reporte ="resumen_ventas";
				//configurar para ventas: porque es reporte de ventas diario
				//hacer la consulta para el reporte
				//en primera instancia son ventas cobras (accion, subtipo/ 2, 5 / ventas, venta cliente final)
				$reporteAccion->setCdTipoAccion(2);
				$reporteAccion->setCdSubtipoAccion(5);
				$sql = $reporteAccion->generarResumenDiarioVentas($pdo->getDbSeguridad());
				$result = $pdo->pdoGetAll($sql);	

				/*
				********************************************
				 ************ Primera parte: ventas cobradas
				 ********************************************
				*/	
				//inicio de generación de reporte: primera parte VENTAS COBRADAS	
				$tbl = '<table border="1">';
				//$tbl .= '<tr><td colspan="9">'.$sql.'</td></tr>';
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
					$tbl .= "<td align=\"right\">" . number_format($fila["cantidad"], 0) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["precio"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["ingreso"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["costo"], 2) . "</td>";
					$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
					$tbl .= "<td>" . $fila["login_usuario"] . "</td>";
					$tbl .="</tr>";	
					//sumas de las unidades vendidas y precios
					$sumaUnidades += $fila["cantidad"];
					$sumaIngreso += $fila["ingreso"];
					$sumaCosto += $fila["costo"];
				}
				
				$tbl .= "<tr><td></td><td></td><td><b>Totales:</b></td><td align=\"right\"><b>". number_format($sumaUnidades, 0) . "</b></td>";
				$tbl .= "<td></td><td align=\"right\"><b>". number_format($sumaIngreso, 2)."</b></td><td align=\"right\"><b>".number_format($sumaCosto, 2)."</b></td><td></td></tr>";			
				$tbl .= "</table><p></p>";		
				
				/*
				********************************************
				 ************ Segunda parte: devoluciones efectuadas
				 ********************************************
				*/
				$reporteAccion->setCdTipoAccion(1);
				$reporteAccion->setCdSubtipoAccion(4);
				$sql = $reporteAccion->generarResumenDiarioVentas($pdo->getDbSeguridad());
				$result = $pdo->pdoGetAll($sql);	
				$tbl .= '<table border="1">';
				//$tbl .= '<tr><td colspan="9">'.$sql.'</td></tr>';
				$tbl .= '<tr><td colspan="9"><b>RESUMEN DE DEVOLUCIONES A CLIENTES</b></td></tr>';
				$tbl .= '<tr><td colspan="9"><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
				$tbl .= '<tr><td width=\"25\"><b>No.</b></td><td width=\"144\"><b>Producto</b></td><td><b>Código</b></td><td><b>Unidades</b></td>';
				$tbl .= '<td><b>Precio($)</b></td><td><b>Egreso($)</b></td><td><b>Costo($)</b></td>';
				$tbl .= '<td><b>Sucursal</b></td><td><b>Usuario</b></td>';
				$tbl .= '</tr>';

				$registros = 0;
				$sumaUnidades = 0;
				$sumaDevoluciones= 0;
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
					$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
					$tbl .= "<td>" . $fila["login_usuario"] . "</td>";
					$tbl .= "</tr>";	
					//sumas de las unidades vendidas y precios
					$sumaUnidades += $fila["cantidad"];
					$sumaDevoluciones += $fila["ingreso"];
					$sumaCosto += $fila["costo"];
				}
				
				
				$tbl .= "<tr><td></td><td></td><td><b>Totales:</b></td><td align=\"right\"><b>". number_format($sumaUnidades, 0) . "</b></td>";
				$tbl .= "<td></td><td align=\"right\"><b>". number_format($sumaDevoluciones, 2)."</b></td><td align=\"right\"><b>".number_format($sumaCosto, 2)."</b></td><td></td></tr>";			
				$tbl .= "</table><p></p>";	
				
				
				/*
				********************************************
				 ************ Tercera parte: descuentos efectuadas
				 ********************************************
				*/
				$comprobante = new Comprobante();				
				if($cdUsuario)
					$comprobante->setCdUsuario($_SESSION["cd_usuario"]);

				$comprobante->setCdSucursal($cdSucursal);
				$comprobante->setFeReporteInicio($feInicio . " 00:00:00");
				$comprobante->setFeReporteFin($feFin . " 23:59:59");			
				$sql = $comprobante->obtenerDescuentosPorParametros();
				$result = $pdo->pdoGetRow($sql);	
				$totalDescuentos = $result["suma_descuentos"];
				
				$tbl .= '<table border="1">';
				//$tbl .= '<tr><td>'.$sql.'</td></tr>';
				$tbl .= '<tr><td><b>RESUMEN DE DESCUENTOS EN VENTAS</b></td></tr>';
				$tbl .= '<tr><td><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
				$tbl .= '<tr><td>Descuentos efectuados($): <b>'. number_format($totalDescuentos, 2).'</b></td></tr>';
				$tbl .= "</table><p></p>";
				
				$totalCaja = $sumaIngreso - $sumaDevoluciones - $totalDescuentos;
				$tbl .= "<table><tr><td><h3>Total caja(Ventas - Devoluciones - Descuentos): $</h3></td><td><h3>".number_format($totalCaja, 2)."</h3></td></tr>";
				$tbl .= "<tr><td>Fecha/hora de generación del reporte: </td><td>".date('Y-m-d H:i:s')."</td></tr></table>";
			
				break;
			
			
			case "movimientos_diario":
			
				$reporte ="movimientos_diario";
				
				//si quiere obtener todos los movimientos o solo de un tipo: compras(1) o ventas(2)
				//hacer la consulta para el reporte
				$sql = $reporteAccion->generarDetalleMovimientos($pdo->getDbSeguridad());
				$result = $pdo->pdoGetAll($sql);	

				//inicio de generación de reporte	
				$tbl = '<table border="1">';
				//$tbl .= '<tr><td colspan="10">'.$sql.'</td></tr>';
				$tbl .= '<tr><td colspan="10"><b>REPORTE DE MOVIMIENTOS</b></td></tr>';
				$tbl .= '<tr><td colspan="10"><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
				$tbl .= '<tr><td><b>No.</b></td><td><b>No.Recibo</b></td><td><b>Producto</b></td><td><b>Código</b></td><td><b>Tipo</b></td>';
				$tbl .= '<td><b>Unidades</b></td>';
				$tbl .= '<td><b>Precio($)</b></td><td><b>Total($)</b></td><td><b>Fe.venta</b></td>';
				//$tbl .= '<td><b>Sucursal</b></td>';
				$tbl .= '<td><b>Usuario</b></td>';
				$tbl .= '</tr>';

				$registros = 0;
				$sumaUnidades = 0;
				$sumaIngreso= 0;
				
				foreach($result as $fila) {
					$registros++;
					$tbl .="<tr>";
					$tbl .= "<td>" . $registros . "</td>";
					$tbl .= "<td>" . $fila["cd_cabecera"] . "</td>";
					$tbl .= "<td>" . $fila["nm_producto"] . "</td>";
					$tbl .= "<td>" . $fila["sku_producto"] . "</td>";
					$tbl .= "<td>" . $fila["nm_subtipo"] . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["cantidad"], 0) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["precio"], 2) . "</td>";
					$tbl .= "<td align=\"right\">" . number_format($fila["ingreso"], 2) . "</td>";
					$tbl .= "<td>" . $fila["fe_ultima_compra"] . "</td>";
					//$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
					$tbl .= "<td>" . $fila["login_usuario"] . "</td>";
					$tbl .="</tr>";	
					//sumas de las unidades vendidas y precios
					$sumaUnidades += $fila["cantidad"];
					$sumaIngreso += $fila["ingreso"];
				}
				
				/*
				$tbl .= "<tr><td><b>Totales</b></td><td></td><td></td><td></td><td align=\"right\"><b>". number_format($sumaUnidades, 2)."</b></td>";
				$tbl .= "<td></td><td align=\"right\"><b>". number_format($sumaIngreso, 2)."</b></td><td></td><td></td>";
				$tbl .= "<td></td></tr>";
				*/
				$tbl .= "</table>";		
				//fin de generación de reporte

				break;
				
			
			case "stock":
			
				//verificar antes el origen
				if($origen == "origen_reportes") {
					/* Obtener el inventario activo al momento para obtener el reporte */
					$inventario->setCdSucursal($cdSucursal);
					$sqlActivo = $inventario->obtenerCdInventarioActivo();
					$filaActivo = $pdo->pdoGetRow($sqlActivo);
					$cdInventarioActivo = $filaActivo["cd_inventario"];	
				} else {
					/* si la peticion viene de la pantalla de edición de inventarios
					entonces tomar el parámetro del id del inventario					
					 y poner la sucursal que seteó al inicio del login
					*/
					$reporteAccion->setCdSucursal($_SESSION["suc_venta"]);			
					$cdInventarioActivo = $_GET["invver"];
				}
							
				if(!$cdInventarioActivo) {
					$tbl = "<table><tr><td>No existe inventario activo, o ya caducó.  Por favor revise los inventarios</td></tr></table>";					
				} else {			
					//////////////////----
					$reporteAccion->setCdInventario($cdInventarioActivo);
					$reporte ="stock";
					
					//1. limpiar codigos
					$sql = $reporteAccion->limpiarCodigosStock();
					$numEliminados = $pdo->pdoInsertar($sql);					
					//2. insertar codigos del inventario actual
					$sql = $reporteAccion->insertarCodigosStock();
					$numInsertados = $pdo->pdoInsertar($sql);	
					//$tbl .= "<table><tr><td colspan=\"7\">1 LIMPIAR CODIGOS: ".$sql."</td></tr></table>";	
					
					
					//3. obtener codigos y nombres de productos	
					$sql = $reporteAccion->obtenerNombresStock();
					$resultProductos = $pdo->pdoGetAll($sql);
					$tbl = "";
					//$tbl .= "<table><tr><td colspan=\"7\">OBTENER PRODUCTOS: ".$sql."</td></tr></table>";	
					//echo "Los productos: " . $sql;
					
					//4. obtener el inventario inicial
					$sql = $reporteAccion->obtenerIInicialStock();
					$resultIInicial = $pdo->pdoGetAll($sql);	
					//$tbl .= "<table><tr><td colspan=\"7\">INV.INICIAL: ".$sql."</td></tr></table>";		

					//5. obtener las compras
					$sql = $reporteAccion->obtenerComprasStock();
					$resultCompras = $pdo->pdoGetAll($sql);	
					//$tbl .= "<table><tr><td colspan=\"7\">COMPRAS: ".$sql."</td></tr></table>";	
					
					//6. obtener las ventas
					$sql = $reporteAccion->obtenerVentasStock();
					$resultVentas = $pdo->pdoGetAll($sql);	
					//echo "LAS VENTAS:: " . $sql;
					//$tbl = "<table><tr><td colspan=\"7\">VENTAS: ".$sql."</td></tr></table>";
					
					/* considerar el número de productos que se recuperaron
					
					*/
					$filas = count($resultProductos);
					$tbl .= '<table border="1">';
					//$tbl .= '<tr><td colspan="7">'.$sql . 'el inventario:::' .$cdInventarioActivo.'</td></tr>';
					$tbl .= '<tr><td colspan="7"><b>REPORTE DE INVENTARIO ACTIVO ACTUAL</b></td></tr>';
					$tbl .= '<tr><td colspan="7"><b>Sucursales: '.$nmSucursal.' / Movimientos: Todos los del inventario activo / Fecha de generación: '.date('Y-m-d').'<br></b></td></tr>';
					
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
						
						$iinicial = 0;
						if($resultIInicial && $resultIInicial[$i] && ($resultProductos[$i]["cd_producto"] == $resultIInicial[$i]["cd_producto"])) {				
							if($resultIInicial[$i]["cantidad_inicial"])
								$iinicial = $resultIInicial[$i]["cantidad_inicial"];
						}		
						$tbl .= "<td align=\"right\">" . $iinicial . "</td>";
						$sumaIInicial += $iinicial;
							
						
						$compras = 0;
						if($resultCompras && $resultCompras[$i] && ($resultProductos[$i]["cd_producto"] == $resultCompras[$i]["cd_producto"])) {							
							if ($resultCompras[$i]["cantidad_compras"]) 
								$compras = $resultCompras[$i]["cantidad_compras"];														
						}
						$tbl .= "<td align=\"right\">" . $compras . "</td>";
						$sumaCompras += $compras;

						
						$ventas = 0;
						if($resultVentas && $resultVentas[$i] && ($resultProductos[$i]["cd_producto"] == $resultVentas[$i]["cd_producto"])) {
							if($resultVentas[$i]["cantidad_ventas"])
								$ventas = $resultVentas[$i]["cantidad_ventas"];
						}									
						$tbl .= "<td align=\"right\">" . $ventas . "</td>";
						$sumaVentas += $ventas;
							
						//calcular el inventario final
						$ifinal = $iinicial + $compras - $ventas;
						$tbl .= "<td align=\"right\">" . $ifinal . "</td>";
						$tbl .= "</tr>";
						
						$sumaIFinal += $ifinal;
					} //fin for de productos
					//$tbl .= "</table>";
					
					//colocar una fila con los totales al final
					$tbl .= "<tr><td></td><td></td><td><b>Total unidades</b></td><td align=\"right\">".$sumaIInicial."</td><td align=\"right\">".$sumaCompras."</td><td align=\"right\">".$sumaVentas."</td><td align=\"right\">".$sumaIFinal."</td></tr>";
					$tbl .= "</table>";
					//////////////////----
				}
				
				break;

				
			case "mas_vendidos":
				$registros = 0;
				$reporte ="mas_vendidos";
				$sql = $reporteAccion->obtenerProductosMasVendidos();
				$result = $pdo->pdoGetAll($sql);
				
				$tbl = '<table border="1">';
				$tbl .= '<tr><td colspan="6"><b>REPORTE DE PRODUCTOS MÁS VENDIDOS - Ventas finales a clientes</b></td></tr>';
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

	/*
	--------------------------------------------------
	generar reporte PDF - tabla de movimientos
	--------------------------------------------------
	*/
	// Print text using writeHTMLCell()
	$pdf->writeHTML($tbl, true, false, false, false, '');
	$pdf->Output('rinventarios.pdf', 'I');
}
?>