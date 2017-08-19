<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/sucursal/Sucursal.php");
include("./include/autenticacion.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect("localhost", "tatianag", "Cpsr19770428", "bdd_abejas");

	$codigo = $_POST['cmbSucursal'];

	$cdUsuario = (isset($_SESSION['cd_usuario']) ? $_SESSION['cd_usuario'] : 0);
	echo "el codigo del usuario es: " . $cdUsuario;

/*
	if(!isset($_SESSION)){ 
		echo "No hay SESSION";
		session_start(); 
	}	
		//poner la sesion
	echo "poner session";
*/	

	$sucursal = new Sucursal();
	$sucursal->setCdSucursal($codigo);
	$sql = $sucursal->consultarSucursal();
	$filaSucursal = $pdo->pdoGetRow($sql);
	$sucursal->obtenerSucursal($filaSucursal);
	$nmSucursal = $sucursal->getNmSucursal();

	$_SESSION['suc_venta'] = $codigo;
	$_SESSION['suc_nombre'] = $nmSucursal;
	unset($_SESSION["lista_productos"]);
	unset($_SESSION["descuento"]);
	
	$autenticacion->RedirectToURL("index.php?cdven=1&suc=" . $codigo . "&sun=" . $nmSucursal);
} 
// fin de la sesion	
?>