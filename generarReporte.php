<?php
header('Content-type: application/pdf');
header("Content-type: application-download");
header("Content-Disposition: attachment; filename=Historia_paciente.pdf");
header("Content-Transfer-Encoding: binary");

/*
Generar reporte de paciente
ó &#243;
Á &#193;
Ó &#211;
*/

require_once('./include/tcpdf/tcpdf.php');
require_once("./aplicacion/model/paciente/Paciente.php");
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
	$pdf->SetSubject('Historia del Paciente');
	//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
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
	// dejavusans is a UTF-8 Unicode font, if you only need to
	// print standard ASCII chars, you can use core fonts like
	// helvetica or times to reduce file size.

	//$pdf->SetFont('helvetica', 'B', 20);dejavusans
	$pdf->SetFont('times', '', 11, '', true);
	$pdf->AddPage();

	/*
	---------------------------------------------------------------------
	generar datos para reporte
	---------------------------------------------------------------------
	*/
	$paciente = new Paciente();
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();
	$sql ="sin sentencia";
	if($con) {
		$sql = $paciente->consultarPaciente($_GET["cdpac"]);
		$fila = $pdo->pdoGetRow($sql);
		$paciente->obtenerPaciente($fila);
		
		$sqlAlergia = $paciente->getNmNivelAlergiaPaciente();
		$filaAlergia = $pdo->pdoGetRow($sqlAlergia);
		$textoNivelAlergia = $filaAlergia["nm_nivel_alergia"];
	} 


	//tabla de datos personales
	$tbl = '<h3><b>FICHA DEL PACIENTE</b></h3>';
	$tbl .= '<table border="1"><tr><td colspan="6"><b>DATOS PERSONALES</b></td></tr>';
	$tbl .= '<tr><td><b>No.Historia:</b></td><td>'.$paciente->getCdPaciente().'</td>';
	$tbl .= '<td><b>Cédula:</b></td><td>'.$paciente->getCedulaPaciente().'</td>';
	$tbl .= '<td><b>Sexo:</b></td><td>'.$paciente->getSexoPaciente().'</td>';
	$tbl .= '</tr>';
	$tbl .= "<tr><td><b>Nombres:</b></td><td>". $paciente->getNombresPaciente() ."</td>";
	$tbl .= "<td><b>Apellidos:</b></td><td>". $paciente->getApellidosPaciente() . "</td>";
	$tbl .= "<td><b>Edad(años):</b></td><td>". $paciente->getEdadPaciente() . "</td>";
	$tbl .= "</tr>";
	$tbl .= "<tr><td><b>Ocupaci&#243;n:</b></td><td>". $paciente->getOcupacionPaciente() ."</td>";
	$tbl .= "<td><b>Direcci&#243;n:</b></td><td>". $paciente->getDireccionPaciente() . "</td>";
	$tbl .= "<td><b>Tel&#233;fono:</b></td><td>". $paciente->getTelefPaciente() . "</td>";
	$tbl .= "</tr>";
	$tbl .= "</table><p></p>";

	//tabla de antecedentes
	$tbl .= '<table border="1"><tr><td><b>ANTECEDENTES</b></td></tr>';
	$tbl .= '<tr><td><b>1. Personales</b></td></tr>';
	$tbl .= '<tr><td>'.  $paciente->getAntecedentesPersonaPaciente() . '</td></tr>';
	$tbl .= '<tr><td><b>Detalle gestas:</b>Embarazos:'.$paciente->getNumEmbarazosPaciente() .' Partos: '.$paciente->getNumPartosPaciente().' Cesáreas: '.$paciente->getNumCesareasPaciente().' Abortos: '.$paciente->getNumAbortosPaciente().'</td></tr>';
	$tbl .= '<tr><td><b>2. Familiares</b></td></tr>';
	$tbl .= '<tr><td>'.  $paciente->getAntecedentesFamiliaPaciente() . '</td></tr>';
	$tbl .= '<tr><td><b>3. Cirug&#237;as</b></td></tr>';
	$tbl .= '<tr><td>'.  $paciente->getCirugiasPaciente() . '</td></tr>';
	$tbl .= '<tr><td><b>4. Alergias</b></td></tr>';
	$tbl .= '<tr><td>'.  $paciente->getAlergiasPaciente() . '</td></tr>';
	$tbl .= '<tr><td><b>5. Medicaci&#243;n qu&#237;mica</b></td></tr>';
	$tbl .= '<tr><td>'.  $paciente->getMedicacionQuimicaPaciente() . '</td></tr>';
	$tbl .= '<tr><td><b>6. Motivo consulta</b></td></tr>';
	$tbl .= '<tr><td>'.  $paciente->getMotivoConsultaPaciente() . '</td></tr>';
	$tbl .= "</table><p></p>";


	//tabla de signos vitales
	$tbl .= '<table border="1"><tr><td colspan="8"><b>SIGNOS VITALES</b></td></tr>';
	$tbl .= "<tr>";
	$tbl .= '<td><b>Peso</b></td><td>'. $paciente->getPeso() .'</td>';
	$tbl .= '<td><b>Talla</b></td><td>'. $paciente->getTalla() .'</td>'; 
	$tbl .= '<td><b>Presi&#243;n arterial</b></td><td>'. $paciente->getPresion() .'</td>';
	$tbl .= '<td><b>Frecuencia card&#237;aca</b></td><td>'. $paciente->getFrecuenciaCardiaca() .'</td>';
	$tbl .= "</tr>";
	$tbl .= "</table><p></p>";

	//tabla de diagnostico neuropatico
	$tbl .= '<table border="1"><tr><td colspan="4"><b>DIAGN&#211;STICO NATUROP&#193;TICO</b></td></tr>';
	//$tbl .= '<tr><td><b>Detalles</b></td></tr>';
	$tbl .= '<tr><td colspan="4">' . $paciente->getDiagnosticoNaturopatico() .'</td></tr>';
	$tbl .= "<tr><td><b>Nivel de dolor</b></td><td>".$paciente->getNivelDolor() ."</td><td><b>Prueba de alergia</b></td><td>". $textoNivelAlergia ."</td></tr>";
	$tbl .= "</table><p></p>";

	/*
	--------------------------------------------------
	generar datos para reporte - tabla de tratamientos
	--------------------------------------------------
	*/
	$tratamiento = new Tratamiento();
	$sql = $tratamiento->consultarDetalleTratamientosPorPaciente($paciente->getCdPaciente());
	$result = $pdo->pdoGetAll($sql);

	$tbl .= '<table border="1"><tr><td><b>TRATAMIENTOS</b></td></tr>';
	//$tbl .= '<tr><td><b>'. $sql .'</b></td><td>lista de tratamientos' . $paciente->getCdPaciente() . '</td></tr>';
	$tbl .= "</table>";

	/* por cada tratamiento además se debería obtener la medicacion
	*/
	$registros = 0;
	foreach($result as $fila) {
		$registros++;
		$tbl .= '<table border="1">';
		$tbl .= '<tr><td><b>'. $registros .'. Descripci&#243;n</b></td><td colspan="3">'. $fila["nm_tratamiento"] . '</td></tr>';
		$tbl .= '<tr>';	
		$fecha = strtotime($fila["fe_tratamiento"]);
		$fechaSalida = date('Y-m-d', $fecha);
		$tbl .= '<td><b>Fecha</b></td><td>'. $fechaSalida .'</td><td><b>No. terapias sugeridas</b></td><td>' . $fila["terapias_tratamiento"] . '</td>';
		$tbl .= '</tr>';
		$tbl .= '<tr><td colspan="2"><b>Medicaci&#243;n</b></td><td colspan="2"><b>Observaciones</b></td></tr>';
		$tbl .= '<tr><td colspan="2">' . $fila["medicacion_tratamiento"] . '</td><td colspan="2">' . $fila["obs_tratamiento"] . '</td></tr>';
		$tbl .= "</table>";
		
	}

	/*
	---------------------------------------------------------------------
	fin generar datos para reporte
	---------------------------------------------------------------------
	*/

	// Print text using writeHTMLCell()
	$pdf->writeHTML($tbl, true, false, false, false, '');

	$pdf->Output('name.pdf', 'I');
}

?>