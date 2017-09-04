<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/paciente/Paciente.php");
include("./aplicacion/controller/Controller.php");
require_once("./include/dabejas_config.php");

?>
<html>
<head>
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/jquery_validate.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/demo.js"></script>

</head>
<body>
<?php

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	//para paginacion
	$numRegPagina = 10;
	$pagina = 0;
	if(isset($_POST["pag"])) {
		$pagina = $_POST["pag"];
	} else {
		$pagina = 1;
	}
	$inicio = ($pagina-1) * $numRegPagina;
	$numTotal = 0;
	$txtApe = "";
	$cmbCam = "";

	if(isset($_POST["txtApe"]) && isset($_POST["cmbCam"])) {
		
		$txtApe = reemplazarCaracteresEspeciales($_POST["txtApe"]);
		$cmbCam = $_POST["cmbCam"];
	} 

	$numResultado = 0;
	//fin para paginacion

	if($con) {
		if(isset($_POST["txtApe"])) {
			$paciente = new Paciente();
			//obtener el número total de pacientes
			/*
			$sqlNumPacientes = $paciente->obtenerNumTotalPacientes();
			$resTotal = $pdo->pdoGetRow($sqlNumPacientes);
			$numPacientes = 0;
			if($resTotal)
				$numPacientes = $resTotal["conteo"];
			*/
			
			//setear campos para buscar pacientes
			$paciente->setCampoBuscar($_POST["cmbCam"]);
			$paciente->setApellidosPaciente($txtApe);					
			$sql = $paciente->buscarPacientesPorApellidos($inicio, $numRegPagina, 1);
			//contar los registros totales de la consulta
			$res = $pdo->pdoGetAll($sql);
			$numResultado = count($res);	
			//solo traer los registros con el límite por página
			$sql = $paciente->buscarPacientesPorApellidos($inicio, $numRegPagina, 0);
			$res = $pdo->pdoGetAll($sql);		
			
			//iterar para obtener los resultados
			echo("<b>Encontrados " . $numResultado . " registros.</b><hr>");
			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" >");
			echo("<tr><td>No.</td><td>Historia</td><td>Apellidos</td><td>Nombres</td><td>Cédula</td><td>Sexo</td><td>Editar</td><td>Eliminar</td><td>Reporte</td></tr>");
			$indice = 0;
			foreach($res as $fila) {
				if($indice%2)
					$color = "#b3ecff";
				else 	
					$color = "#66d9ff";							

				$indice++;	
				echo("<tr bgcolor=\"". $color ."\">");
				echo "<td>" . ($indice + $inicio) . "</td>";
				echo "<td align=\"right\">" . $fila["cd_paciente"] . "</td>";
				echo "<td>" . $fila["apellidos_paciente"] . "</td>";
				echo "<td>" . $fila["nombres_paciente"] . "</td>";		
				echo "<td>" . $fila["cedula_paciente"] . "</td>";		
				echo "<td>" . $fila["sexo_paciente"] . "</td>";		
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngPaciente.php?cdpac=". $fila["cd_paciente"] . "');\"><img src=\"images/updatei.png\" alt=\"Actualizar\"></a></td>";
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngPaciente.php?del=1&cdpac=". $fila["cd_paciente"] . "');\"><img src=\"images/deletei.png\" alt=\"Borrar\"></a>" ."</td>";			
				echo "<td align=\"center\"><a href=\"#\" onclick=\"window.open('generarReporte.php?cdpac=". $fila["cd_paciente"] ."')\"><img src=\"images/historia.png\" alt=\"Generar reporte\"></a>" ."</td>";	
				echo("<tr>");
				
			}
			echo("</table>");	
		}	
	} // fin si existe conexion

	$paginasTotales = ceil($numResultado / $numRegPagina);
	//echo ("registros totales son: " . $numTotal);
	//echo ("Las páginas totales son: " . $paginasTotales);

	$txtPaginas = "";
	for($i = 1; $i <= $paginasTotales; $i++) {
		if($i == $pagina) {
			$txtPaginas .= "<b>[".$i."]</b>";
		} else {
			$txtPaginas .= "<a href=\"#\" onclick=\"cargarPacientesPaginacion('".$txtApe."','". $cmbCam ."',".$i.");\">[".$i."]</a>";
		}	
	}
	echo("Páginas: " . $txtPaginas);
}
?>
</body>
</html>