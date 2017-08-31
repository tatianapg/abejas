<?php
/* Ingresar el inventario y devolver una bandera de resultado
*/
include("./aplicacion/model/usuario/Usuario.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	////////////////////
	$codigoUsuario = isset($_POST["txtCdUsuario"]) ? $_POST["txtCdUsuario"] : 0 ;

	//setear datos de producto; los valores de categoria, estado y unidad son por defecto 1
	/*
	function setUsuario($cd_usuario, $nm_usuario, $login_usuario, $clave_usuario, $email_usuario, 
	$obs_usuario, $es_usuario_admin, $ver_info_sensible ,$esta_activo) {
	*/
	$usuario = new Usuario();
	$usuario->setCdUsuario($_POST["txtCdUsuario"]);
	$usuario->setS
	$inventario->setCdSucursal($_SESSION["suc_venta"]);
	$inventario->setInventario($_POST["txtCdInventario"], 1 , $_POST["txtNmInventario"], date('Y-m-d H:i:s'),
	$_POST["txtFeInicioInventario"] , $_POST["txtFeFinInventario"],
	$_POST["txtAnioFiscalInventario"], $_POST["txtObsInventario"], "", $_SESSION["suc_venta"]);

	//establecer la conexión
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();

	$del=0;
	if($con) {
		//1er caso, borrar producto
		if(isset($_POST["del"]) && $_POST["del"] == 1) {
			//echo "ingreso a eliminar";
			$inventario->setCdInventario($codigoInventario);
			$sqlEliminar = $inventario->eliminarInventario();
			$numEliminados = $pdo->pdoInsertar($sqlEliminar);
			$codigoInventario = 0;
			$del = $_POST["del"];
		} else {		
			//es una tarea de ingresar, es nuevo, se crea un inventario
			if(!$codigoInventario) {
				/* se debe crear una transacción porque se crea el inventario y se desactiva el otro*/
				//$con->beginTransaction();
				$conexion = $pdo->getConection();
				$conexion->beginTransaction();
				try {
					//1. primero desactiva el inventario
					//obtener inventario anterior
					$sqlActivo = $inventario->validarExisteUnInventarioActivoPorSucursal();
					$filaActivo = $pdo->pdoGetRow($sqlActivo);
					$cdInventarioActivo = $filaActivo["cd_inventario"];	
					//echo "El inventario anterior es: " . $cdInventarioActivo;
					//actualizar el inventario anterior, si existe un inventario activo
					/*si no existe el inventario no se hace nada, puede ser la primera vez que ingresa el inventario */
					if($cdInventarioActivo) {
						$inventario->setFeCierreAnterior(date('Y-m-d H:i:s'));
						$inventario->setCdInventarioAnterior($cdInventarioActivo);
						$sqlInactivar = $inventario->desactivarUltimoInventario();
						echo ":::" .$sqlInactivar;
						$numActualizados = $pdo->pdoInsertar($sqlInactivar);				
					}
					
					//2. luego inserta nuevo inventario
					$sql = $inventario->crearInventario();    			
					$numInserts = $pdo->pdoInsertar($sql);
					echo "Fueron insertadas: " . $numInserts;
					$codigoInventario = $pdo->pdoLasInsertId();
					
					$conexion->commit();					
				} catch(Exception $e) {
					echo $e->getMessage();
					$conexion->rollBack();
				}
				
				/* fin de la transacción*/
			
			} else { 
				//2do caso: es una actualizacion de datos con el codigo de inventario
				$inventario->setCdInventario($codigoInventario);
				$sql = $inventario->modificarInventario();
				//echo "La consulta es: " . $sql;
				$numActualizados = $pdo->pdoInsertar($sql);
				//echo "Fueron actualizadas: " . $numActualizados;
			}
		}	
		$autenticacion->RedirectToURL("index.php?cdinv=" . $codigoInventario . "&del=" . $del .  "&nmi=" . $inventario->getNmInventario());

	} //fin es conexion
/////////////////
}

?>