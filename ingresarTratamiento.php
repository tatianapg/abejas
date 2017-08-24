<?php
/* Ingresar el tratamiento y devolver una bandera de resultado
*/
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");
include("./aplicacion/model/tratamiento/Tratamiento.php");
include("./aplicacion/model/sesion/Sesion.php");
include("./aplicacion/model/medicacion/Medicacion.php");
/*
   function setTratamiento($cd_tratamiento, $cd_paciente, $nm_tratamiento, $fe_tratamiento,
        $medicacion_tratamiento, $obs_tratamiento, $terapias_tratamiento)
*/        

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	//setear datos de tratamiento
	$cdPac = 0;
	$cdTra = 0;
	$ses = 0;
	$tratamiento = new Tratamiento();
	$tratamiento->setTratamiento(
	$_POST["txtCdTratamiento"], 
	$_POST["txtCdPaciente"], $_POST["txtNmTratamiento"], date("Y-m-d"), 
	$_POST["txtMedicacionTratamiento"], $_POST["txtObsTratamiento"], $_POST["txtTerapiasTratamiento"]);

	//establecer la conexión
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();

	if($con) {
		//ingresando una terapia o sesion	
		if(isset($_POST["ses"]) && $_POST["ses"] == 1) {
				//ingresar una sesion
				$sesion = new Sesion();
				$sesion->setSesion(0, $_POST["txtCdTratamiento"], date("Y-m-d"), $_POST["txtNotasSesion"], "",
					$_POST["txtPresion"], 
					$_POST["txtFrecuenciaCardiaca"] , 
					$_POST["txtPeso"], 
					$_POST["txtTalla"],
					$_POST["cmbNivelDolor"]);
				$sql = $sesion->crearSesion();
				$numInserts = $pdo->pdoInsertar($sql);									
		} if(isset($_POST["med"]) && $_POST["med"] == 1) { 	//ingresando una medicacion
				$medicacion = new Medicacion();
				$medicacion->setMedicacion(0, $_POST["txtCdTratamiento"], date("Y-m-d"),$_POST["txtNotasMedicacion"]  );
				$sql = $medicacion->crearMedicacion();
				$numInserts = $pdo->pdoInsertar($sql);					
		} else {	
			//si se trata de ingresar/modificar un tratamiento
			if($_POST["txtCdTratamiento"] == 0) {		
				//crear nuevo tratamiento
				$sql = $tratamiento->crearTratamiento();			
				$numInserts = $pdo->pdoInsertar($sql);
				$cdTratamiento = $pdo->pdoLasInsertId();    
			} else {
				//borrar el tratamiento
				if(isset($_POST["del"]) && $_POST["del"] == 1) {
					$tratamiento->setCdTratamiento($_POST["txtCdTratamiento"]);			
					//si no hay sesiones ni medicaciones
					$sql = $tratamiento->eliminarTratamiento();
					$numEliminados = $pdo->pdoInsertar($sql);										
					
				} else {
					//actualizar tratamiento: actualizacion de datos con el codigo de paciente
					$tratamiento->setCdTratamiento($_POST["txtCdTratamiento"]);
					$sql = $tratamiento->modificarTratamiento();
					$numActualizados = $pdo->pdoInsertar($sql);
				}
			}

		} // fin if-else no está seteado el ingreso de datos de sesión 
		
		$cdTra = (isset($_POST["txtCdTratamiento"]) ? $_POST["txtCdTratamiento"] : $cdTratamiento);
		$cdPac = $_POST["txtCdPaciente"];//(isset($_GET["cdpac"]) ? $_GET["cdpac"] : $cdTratamiento);	
		$ses = (isset($_POST["ses"]) ? $_POST["ses"] : 0);
			
		$autenticacion->RedirectToURL("frmIngTratamiento.php?cdpac=". $cdPac);
	} //fin es conexion
}
?>