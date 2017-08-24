<?php
header('Content-type: application/pdf');
header("Content-type: application-download");
header("Content-Disposition: attachment; filename=listado_productos.pdf");
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
require_once("./aplicacion/model/producto/Producto.php");
require_once("./aplicacion/model/tratamiento/Tratamiento.php");
require_once("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Doctoras Abejas');
	$pdf->SetSubject('Listado de Productos');
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Tratamientos Naturales", "Dir.1 Av. Mariscal Sucre y Pasaje N Local No.34\nDir.2 Centro Comercial de Mayoristas y Negocios Andinos, entrada principal");

	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	//set margins
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set default font subsetting mode
	$pdf->setFontSubsetting(true);
	// Set font
	$pdf->SetFont('times', '', 11, '', true);
	$pdf->AddPage();

	/*
	---------------------------------------------------------------------
	generar datos para reporte
	---------------------------------------------------------------------
	*/
	$tbl = "";	
	$producto = new Producto();
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();
	$sql = $producto->obtenerListadoTodosProductos();
	$result = $pdo->pdoGetAll($sql);



	$tbl .= '<table border="1"><tr><td colspan="7"><b>LISTADO DE PRODUCTOS</b></td></tr>';
	$tbl .= '<tr><td><b>No.</b></td><td><b>Código</b></td><td><b>Producto</b></td><td><b>Categoría</b></td>'.
			'<td><b>Estado</b></td><td><b>Fe.Ingreso</b></td><td><b>Precio($) c/u</b></td></tr>';


	$registros = 0;
	foreach($result as $fila) {
		$registros++;
		$tbl .= '<tr>';
		$tbl .= '<td>'.$registros.'</td>';
		$tbl .= '<td>'.$fila["sku_producto"].'</td>';
		$tbl .= "<td>".$fila["nm_producto"]."</td>";
		$tbl .= "<td>".$fila["nm_categoria_producto"]."</td>";
		$tbl .= "<td>".$fila["nm_estado_sistema"]."</td>";	
		$tbl .= "<td>".$fila["fe_ingreso_producto"]."</td>";	
		$tbl .= "<td align=\"right\">". number_format($fila["precio_producto"], 2)."</td>";	
		$tbl .= "</tr>";	
	}
		$tbl .= "</table>";


	/*
	---------------------------------------------------------------------
	fin generar datos para reporte
	---------------------------------------------------------------------
	*/

	// Print text using writeHTMLCell()
	$pdf->writeHTML($tbl, true, false, false, false, '');

	$pdf->Output('listado_productos.pdf', 'I');

}
?>