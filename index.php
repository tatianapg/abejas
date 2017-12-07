<?php 
include("./aplicacion/controller/Controller.php");
require("page_header.php");

//1. colocar aqui la variable para permitir que ingrese en el else abajo
//2. colocar abajo el else
if( !isset($_GET["cdpro"]) && !isset($_GET["cdpac"]) && !isset($_GET["cdinv"]) && !isset($_GET["cdven"]) 
	&& !isset($_GET["cdusu"]) && !isset($_GET["cdsuc"])) {
	require("page_menu.php");
} else if(isset($_GET["cdpac"])) { 
	require("page_cargarPaciente.php");
} else if(isset($_GET["cdpro"]) )	{ 
	require("page_cargarProducto.php");
} else if(isset($_GET["cdinv"])) {
	require("page_cargarInventario.php");	
} else if(isset($_GET["cdven"])) {
	require("page_cargarPos.php");
} else if(isset($_GET["cdusu"])) {
	require("page_cargarUsuario.php");
} else if(isset($_GET["cdsuc"])) {
	require("page_cargarSucursal.php");
}
?>