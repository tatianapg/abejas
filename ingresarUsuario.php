<?php
/* Ingresar el usuario y devolver una bandera de resultado
*/
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/usuario/Usuario.php");
include("./aplicacion/model/usuarioPerfil/usuarioPerfil.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	////////////////////
	$codigoUsuario = isset($_POST["txtCdUsuario"]) ? $_POST["txtCdUsuario"] : 0 ;
	$verSensible = isset($_POST["ver_sensible"]) ? $_POST["ver_sensible"] : -1 ;
	$esAdmin = isset($_POST["es_admin"]) ? $_POST["es_admin"] : -1 ;
	$estado = $_POST["cmbEstado"];

	//setear datos de producto; los valores de categoria, estado y unidad son por defecto 1
	/*
	function setUsuario($cd_usuario, $nm_usuario, $login_usuario, $clave_usuario, $email_usuario, 
	$obs_usuario, $es_usuario_admin, $ver_info_sensible ,$esta_activo) {
	*/
	//establecer la conexión
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();
	$sql = $pdo->cambiarBdd();
	$pdo->pdoExecute($sql);
	
	$nombreUsuario = reemplazarCaracteresEspeciales($_POST["txtNmUsuario"]);
	
	$usuario = new Usuario();
	$usuario->setUsuario($codigoUsuario, $nombreUsuario, $_POST["txtLogin"], $_POST["txtClave"],
	"null", "null", $esAdmin, $verSensible, $estado);
	
	//validar que el login del usuario sea unico
	$sqlValidar = $usuario->validarNombreRepetido();
	$fila = $pdo->pdoGetRow($sqlValidar);
	$numUsu = $fila["conteo"];
	$es_correcto = 1;
	if($numUsu) {
		echo "El nombre del usuario es repetido.";
		$es_correcto = 0;
		$codigoUsuario = -1;
	}
	
	
	$usuPerfil =new UsuarioPerfil();

	$del=0;
	if($con && $es_correcto) {
		//1er caso, borrar producto
		if(isset($_POST["del"]) && $_POST["del"] == 1) {
			//echo "ingreso a eliminar";
			$usuario->setCdUsuario($codigoUsuario);
			$sqlEliminar = $usuario->eliminarUsuario();
			//$numEliminados = $pdo->pdoInsertar($sqlEliminar);
			//$codigoUsuario = 0;
			$del = $_POST["del"];
		} else {		
			//es una tarea de ingresar, es nuevo, se crea un usuario
			if(!$codigoUsuario) {
				
				$conexion = $pdo->getConection();
				$conexion->beginTransaction();
				try {
					$sql = $usuario->crearUsuario();
					$numInsertados = $pdo->pdoInsertar($sql);
					$codigoUsuario = $pdo->pdoLasInsertId();
					/* ingresar el perfil que tiene: como es ingreso nuevo va el código 0
					luego internamente asigna el valor del auto_increment
					*/
					$usuPerfil->setUsuarioPerfil(0, $_POST["cmbPerfil"], $codigoUsuario);
					$sql = $usuPerfil->crearUsuarioPerfil();
					$numInsertados = $pdo->pdoInsertar($sql);				
					$conexion->commit();
				} catch(Exception $e) {
					echo $e->getMessage();
					$conexion->rollBack();
				}	
				
			} else { 
				//2do caso: es una actualizacion de datos con el codigo de usuario
			
				$conexion = $pdo->getConection();
				$conexion->beginTransaction();
				try {	
					$usuario->setCdUsuario($codigoUsuario);
					$sql = $usuario->modificarUsuario();
					$numActualizados = $pdo->pdoInsertar($sql);
					//modificar el perfil, si es que cambia
					$usuPerfil->setCdUsuario($codigoUsuario);
					$usuPerfil->setCdPerfil($_POST["cmbPerfil"]);
					$sql = $usuPerfil->modificarUsuarioPerfil();
					$numActualizados = $pdo->pdoInsertar($sql);
					$conexion->commit();
				} catch(Exception $e) {
					echo $e->getMessage();
					$conexion->rollBack();
				}
			}
		}	
		

	} //fin es conexion
	
	$autenticacion->RedirectToURL("index.php?cdusu=" . $codigoUsuario . "&del=" . $del .  "&nmu=" . $usuario->getLoginUsuario());
/////////////////
}

?>