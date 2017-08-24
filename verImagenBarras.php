<?php
require_once(dirname(__FILE__).'/include/tcpdf/tcpdf_barcodes_1d_include.php');
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	// set the barcode content and type
	//$barcodeobj = new TCPDFBarcode('http://www.tcpdf.org', 'C128');
	$sku = $_GET["sku"];
	$barcodeobj = new TCPDFBarcode($sku, 'C128');

	// output the barcode as PNG image
	//TG: original 2:30
	$barcodeobj->getBarcodePNG(2, 30, array(0,0,0));
}
?>