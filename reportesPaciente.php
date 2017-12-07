<?php
header("Content-type: application/pdf");
header("Content-type: application-download");
header("Content-Disposition: attachment; filename=reporte_terapias.pdf");
header("Content-Transfer-Encoding: binary");

require_once("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/tcpdf/tcpdf.php");
require_once("./aplicacion/model/sucursal/Sucursal.php");
require_once("./aplicacion/model/paciente/Paciente.php");
require_once("./include/dabejas_config.php");

//money_format('%i', $number)
//number_format($number, 2, '.', '');

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Doctoras Abejas');
	$pdf->SetSubject('Reporte diario de pacientes y terapias');
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

	$pdf->SetFont('times', '', 11, '', true);
	$pdf->AddPage();


	//recuperar los parámetros
	$cdSucursal = $_POST["cmbSucursal"];
	$feInicio = $_POST["txtFeInicioTerapia"];
	$feFin = $_POST["txtFeFinTerapia"];
	$tipoReporte = $_POST["reporte"];

	//si no coloco valores en fechas entonces va la fecha actual del día por defecto
	if(!$feInicio) {
		$feInicio = date('Y-m-d');
		$feFin = date('Y-m-d');
	}


	//establcer la conexion con la bdd
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();
	$sql ="sin sentencia";

	//consultar la sucursal
	$nmSucursal = "";
	if($cdSucursal != -1 && $cdSucursal != '') {
		$sucursal = new Sucursal();
		$sucursal->setCdSucursal($cdSucursal);
		$sqlSucursal = $sucursal->consultarSucursal();
		$filaSucursal = $pdo->pdoGetRow($sqlSucursal);
		$sucursal->obtenerSucursal($filaSucursal);
		$nmSucursal = $sucursal->getNmSucursal();
	} else
		$nmSucursal = "Todas";	


	//obtener el inventario activo
	$paciente = new Paciente();
	$paciente->setCdSucursal($cdSucursal);

	$reporte ="";

	switch($tipoReporte) {
		
		case "terapias_diario":
		
			$reporte ="terapias_diario";
			$sql=$paciente->consultaPacientesConTerapiasPorFechas($feInicio, $feFin);
			$result = $pdo->pdoGetAll($sql);	

			//inicio de generación de reporte	
			$tbl = '<table border="1">';
			//$tbl .= '<tr><td colspan="8">'.$sql.'</td></tr>';
			$tbl .= '<tr><td colspan="8"><b>REPORTE DE PACIENTES Y TERAPIAS</b></td></tr>';
			$tbl .= '<tr><td colspan="8"><b>Sucursales: '.$nmSucursal . ' / Fechas desde: ' . $feInicio . ' hasta: ' . $feFin . '</b></td></tr>';
			$tbl .= '<tr><td><b>No.</b></td><td><b>Historia</b></td><td><b>Apellidos</b></td><td><b>Nombres</b></td><td><b>Tratamiento</b></td>';
			$tbl .= '<td><b>Fe.Terapia</b></td><td><b>Notas</b></td>';
			$tbl .= '<td><b>Sucursal</b></td>';
			$tbl .= '</tr>';

			$registros = 0;

			foreach($result as $fila) {
				$registros++;
				$tbl .="<tr>";
				$tbl .= "<td>" . $registros . "</td>";
				$tbl .= "<td>" . $fila["cd_paciente"] . "</td>";
				$tbl .= "<td>" . $fila["apellidos_paciente"] . "</td>";
				$tbl .= "<td>" . $fila["nombres_paciente"] . "</td>";
				$tbl .= "<td>" . $fila["nm_tratamiento"] . "</td>";
				$tbl .= "<td>" . substr($fila["fe_sesion"], 0, 10) . "</td>";
				$tbl .= "<td>" . $fila["notas_sesion"] . "</td>";
				$tbl .= "<td>" . $fila["nm_sucursal"] . "</td>";
				$tbl .="</tr>";	
			}

			$tbl .= "</table>";		
			//fin de generación de reporte

			break;
			
		case "movimientos_fecha":	
			$reporte ="movimientos_fecha";
			break;
		
		case "stock":
			$reporte ="stock";
			break;

	} 

	/*
	--------------------------------------------------
	generar datos para reporte - tabla de movimientos
	--------------------------------------------------
	*/

	// Print text using writeHTMLCell()
	$pdf->writeHTML($tbl, true, false, false, false, '');

	$pdf->Output('name.pdf', 'I');
}
?>