<?php
header('Content-type: application/pdf');
header("Content-type: application-download");
header("Content-Disposition: attachment; filename=recibo_compra.pdf");
header("Content-Transfer-Encoding: binary");

/**
 * @author 
 * @copyright 2017
 */

/*
Generar reporte de productos

ó &#243;
Á &#193;
Ó &#211;
*/
require_once('./include/tcpdf/tcpdf.php');
require_once("./aplicacion/model/sucursal/Sucursal.php");
require_once("./aplicacion/model/usuario/Usuario.php");
require_once("./aplicacion/model/accionProducto/AccionProducto.php");
include("./aplicacion/model/cabeceraComprobante/Comprobante.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf = new TCPDF('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Doctoras Abejas');
	$pdf->SetSubject('Recibo de compra');
	$pdf->SetHeaderData('texto.png', PDF_HEADER_LOGO_WIDTH, "Tratamientos Naturales", "Dir.1 Av. Mariscal Sucre y Pasaje N Local No.34\nDir.2 Centro Comercial de Mayoristas y Negocios Andinos, entrada principal");

	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 9));
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
	// Set font
	$pdf->SetFont('times', '', 9, '', true);
	$pdf->AddPage('P', 'A5');

	/*
	---------------------------------------------------------------------
	generar datos para reporte
	---------------------------------------------------------------------	
	*/
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect("localhost", "tatianag", "Cpsr19770428", "bdd_abejas");
		
	//consultar la cabecera
	$comprobante = new Comprobante();
	$comprobante->setCdCabecera($_GET["rec"]);
	$comprobante->setCdSucursal($_SESSION["suc_venta"]);
	$sql = $comprobante->getComprobante();
	$result = $pdo->pdoGetRow($sql);
	$comprobante->obtenerComprobante($result);
	$descuento = $comprobante->getDescuentoComprobante();	
		
	//consultar usuario y sucursal
	$usuario = new Usuario();
	$usuario->setCdUsuario($comprobante->getCdUsuario());
	$sql = $usuario->consultarUsuario();
	$res = $pdo->pdoGetRow($sql);
	$usuario->obtenerUsuario($res);
			
	$sucursal = new Sucursal();
	$sucursal->setCdSucursal($comprobante->getCdSucursal());
	$sql = $sucursal->consultarSucursal();
	$resSucursal = $pdo->pdoGetRow($sql);
	$sucursal->obtenerSucursal($resSucursal);
	
	//consultar el detalle
	$accion = new AccionProducto();
	$accion->setCdCabecera($_GET["rec"]);
	$accion->setCdSucursal($_SESSION["suc_venta"]);
	$sql = $accion->recuperarAccionesDadaCabecera();
	$resultDetalle = $pdo->pdoGetAll($sql);
	
	
/*	
	---------------------------------------------------------------------
	generar datos para reporte
	---------------------------------------------------------------------
*/	
	$tblEntrega ="";

	$tbl = "";	
	$tbl .= "<table border=\"1\">";	
	$tbl .= '<tr><td colspan="6"><b>RECIBO DE ENTREGA DE PRODUCTOS No. '.$_GET["rec"].'</b></td></tr>';
	
	$tbl .= "<tr>";
	$tbl .= "<td width=\"25\"><b>No.</b></td><td width=\"50\"><b>Código</b></td><td><b>Unidades</b></td>";
	$tbl .= "<td><b>Precio Unidad($)</b></td><td width=\"153\"><b>Descripción</b></td><td width=\"50\"><b>Total($)</b></td>";
	$tbl .= "</tr>";	

	if(isset($_GET["rec"])) {
		$subTotal = 0;
		$i=1;
		foreach($resultDetalle as $fila) {
			$tbl .= "<tr>"; 
			$tbl .= "<td width=\"25\" align=\"right\">".$i."</td>"; 
			$tbl .= "<td width=\"50\" align=\"right\">".$fila["codigo"]."</td>"; 
			$tbl .= "<td align=\"right\">1</td>"; 
			$tbl .= "<td align=\"right\">". number_format($fila["precio"], 2, ".", "") . "</td>"; 
			$tbl .= "<td width=\"153\">". $fila["nombre"] . "</td>";
			$tbl .= "<td align=\"right\" width=\"50\">". number_format($fila["precio"], 2, ".", "") . "</td>";
			$tbl .= "</tr>"; 
			$subTotal += $fila["precio"]; 
			$i++;
		}
		$tbl .= "<tr><td></td><td></td><td></td><td></td><td><b>SUBTOTAL($):</b></td><td align=\"right\"><b>". number_format($subTotal,2, ".", "")."</b></td></tr>";
		$tbl .= "<tr><td></td><td></td><td></td><td></td><td><b>DESCUENTO(-):</b></td><td align=\"right\"><b>". number_format($descuento,2, ".", "")."</b></td></tr>";
		
		$totalFinal = $subTotal - $descuento;
		$tbl .= "<tr><td></td><td></td><td></td><td></td><td><b>TOTAL($):</b></td><td align=\"right\"><b>". number_format($totalFinal,2, ".", "")."</b></td></tr>";
		$tbl .= "</table><p></p>";
		
		$tblEntrega .= "<table>";
		$tblEntrega .= '<tr>';
		//date('Y-m-d H:i:s')
		$tblEntrega .= '<td><b>Sucursal:</b></td><td>'. $sucursal->getNmSucursal() .'</td>';
		$tblEntrega .= '<td><b>Fecha compra:</b></td><td>'. $comprobante->getFeComprobante() .'</td>';
		$tblEntrega .= '</tr>';
		$tblEntrega .= '<tr><td><b>Entregado:</b></td><td>'. $usuario->getLoginUsuario() . '</td>';
		$tblEntrega .= '<td><b>Cliente:</b></td><td>'.$comprobante->getNmCliente() .'</td></tr>';
		$tblEntrega .= "</table>";
	}	

	//unset($_SESSION["lista_productos"]);
	
	//$tbl .= "</table>";	
	// Print text using writeHTMLCell()
	$pdf->writeHTML($tbl, true, false, false, false, '');
	$pdf->SetFont('times', '', 9, '', true);
	$pdf->writeHTML($tblEntrega, true, false, false, false, '');
	 
	$pdf->Output('recibo_compra.pdf', 'I');
}
?>