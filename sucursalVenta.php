<?php
include("./include/autenticacion.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	
	$codigo = $_SESSION['suc_venta'];
	$nmSucursal = $_SESSION['suc_nombre'];

	unset($_SESSION["lista_productos"]);
	unset($_SESSION["descuento"]);	
	echo("Ingresar ventas para " . "<a href=\"#\" onClick=\"return loadQueryResults('frmVentaProducto.php');\"</b>[Sucursal: ".$nmSucursal."]</a>");	
} 
// fin de la sesion	
?>