<?php
/* Ingresar el paciente y devolver una bandera de resultado
*/
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/paciente/Paciente.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	//establecer la conexión
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();	

	$codigoPaciente = isset($_POST["txtCdPaciente"]) ? $_POST["txtCdPaciente"] : 0 ;	
	$ocupacion = reemplazarCaracteresEspeciales($_POST["txtOcupacion"]);
	$direccion = reemplazarCaracteresEspeciales($_POST["txtDireccion"]);
	$telefono = reemplazarCaracteresEspeciales($_POST["txtTelefono"]);

	//setear datos de paciente
	$paciente = new Paciente();
	$paciente->setPaciente(
	$_POST["txtCdPaciente"], 
	$_POST["cmbSucursal"], $_POST["txtNombres"], $_POST["txtApellidos"], $_POST["txtEdad"], 
	$ocupacion, $telefono, $direccion, "", date("Y-m-d"),
	$_POST["txtAntecedentesPersonales"], $_POST["txtAntecedentesFamiliares"], $_POST["txtCirugias"], $_POST["txtAlergias"], 
	$_POST["txtMedicacionQuimica"], $_POST["txtMotivoConsulta"], $_POST["txtPeso"],  $_POST["txtTalla"], 
	$_POST["txtPresion"], $_POST["txtDiagnostico"], $_POST["cmbNivelAlergia"], $_POST["txtFrecuenciaCardiaca"], 
	$_POST["cmbNivelDolor"], $_POST["txtCedula"], $_POST["txtEmbarazos"], $_POST["txtPartos"], $_POST["txtCesareas"], $_POST["txtAbortos"], $_POST["cmbSexo"] );

		
	$del=0;
	if($con) {
		//1er caso, es una eliminación de paciente	
		if(isset($_POST["del"]) && $_POST["del"] == 1) {
			//echo "ingreso a elominar";
			$paciente->setCdPaciente($codigoPaciente);
			$sqlEliminar = $paciente->eliminarPaciente();
			$numEliminados = $pdo->pdoInsertar($sqlEliminar);
			$codigoPaciente = 0;
			$del = $_POST["del"];
		} else {		
			//es una tarea de modificar o ingresar	
			if(!$codigoPaciente) {
				$sql = $paciente->crearPaciente();    
				//echo "Sentencia sql " . $sql;
				$numInserts = $pdo->pdoInsertar($sql);
				//echo "Fueron insertadas: " . $numInserts;
				$codigoPaciente = $pdo->pdoLasInsertId();
			
			} else { 
				//2do caso: es una actualizacion de datos con el codigo de paciente
				$paciente->setCdPaciente($codigoPaciente);//($_POST["txtCdPaciente"]); //cdPaciente($_GET["cdpac"]); 
				$sql = $paciente->modificarPaciente();
				//echo "La consulta es: " . $sql;
				$numActualizados = $pdo->pdoInsertar($sql);
				//echo "Fueron actualizadas: " . $numActualizados;
			}
		}	
		$autenticacion->RedirectToURL("index.php?cdpac=" . $codigoPaciente . "&ape=" . $paciente->getApellidosPaciente() . "&del=" . $del);

	} //fin es conexion

}
?>