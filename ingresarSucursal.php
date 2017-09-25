<?php
/* Ingresar el usuario y devolver una bandera de resultado
*/
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/sucursal/Sucursal.php");
include("./aplicacion/model/cabeceraComprobante/Comprobante.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	////////////////////
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	$codigoSucursal = isset($_POST["txtCdSucursal"]) ? $_POST["txtCdSucursal"] : 0 ;
	$nmSucursal = isset($_POST["txtNmSucursal"]) ? $_POST["txtNmSucursal"] : "" ;

	//setear datos de producto; los valores de categoria, estado y unidad son por defecto 1
	/*
	function setSucursal($cd_sucursal, $cd_empresa, $nm_sucursal, $direccion_sucursal) */
	
	$sucursal = new Sucursal();
	//quitar los caracteres especiales
	$nombreSucursal = reemplazarCaracteresEspeciales($_POST["txtNmSucursal"]);
	$nombreSucursal = trim($nombreSucursal);
	$sucursal->setSucursal($codigoSucursal, 1, $nombreSucursal, "null");
	//validar si el nombre ya existe
	$sqlValidar = $sucursal->validarNombreRepetido();
	$fila = $pdo->pdoGetRow($sqlValidar);
	$numSuc = $fila["conteo"];
	$es_correcto = 1;
	if($numSuc) {
		echo "El nombre de sucursal es repetido.";
		$es_correcto = 0;
		$codigoSucursal = -1;
	}
	
	$del=0;
	if($con && $es_correcto) {
		//1er caso, borrar producto
		if(isset($_POST["del"]) && $_POST["del"] == 1) {
			//echo "ingreso a eliminar";
			$sucursal->setCdSucursal($codigoSucursal);
			$sqlEliminar = $sucursal->eliminarSucursal();
			//se debe tener mucho cuidado porque el codigo de sucursal se usa en varios sitios
			//$numEliminados = $pdo->pdoInsertar($sqlEliminar);
			//$codigoSucursal = 0;
			$del = $_POST["del"];
		} else {		
			//es una tarea de ingresar, es nuevo, se crea un usuario
			if(!$codigoSucursal) {
				//validar nombre repetido	
				//hacer una transaccion para que se cree la sucursal y se inserte la cabecera por defecto
				$conexion = $pdo->getConection();
				$conexion->beginTransaction();
				try {
				///
					$sql = $sucursal->crearSucursal();
					$numInsertados = $pdo->pdoInsertar($sql);
					$codigoSucursal = $pdo->pdoLasInsertId();				
					//cuando inserta la sucursal debe insertarse una cabecera en los comprobantes
					$comprobante = new Comprobante();
					$comprobante->setCdSucursal($codigoSucursal);
					$sql = $comprobante->crearCabeceraDefectoPorSucursal();
					$numCabecera = $pdo->pdoInsertar($sql);
					$codigoCabecera = $pdo->pdoLasInsertId();
					$conexion->commit();				
				///
				} catch(Exception $e) {
					echo $e->getMessage();
					$conexion->rollBack();
				}
			} else { 
				//2do caso: es una actualizacion de datos con el codigo de sucursal			
				$sucursal->setCdSucursal($codigoSucursal);
				$sql = $sucursal->modificarSucursal();
				$numActualizados = $pdo->pdoInsertar($sql);
			}
		}	
		//$autenticacion->RedirectToURL("index.php?cdsuc=" . $codigoSucursal . "&del=" . $del .  "&nms=" . $sucursal->getNmSucursal());

	} //fin es conexion
	$autenticacion->RedirectToURL("index.php?cdsuc=" . $codigoSucursal . "&del=" . $del .  "&nms=" . $sucursal->getNmSucursal());
/////////////////
}

?>