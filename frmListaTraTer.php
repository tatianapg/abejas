<?php
include("./aplicacion/controller/Controller.php");
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/tratamiento/Tratamiento.php");
include("./aplicacion/model/sesion/Sesion.php");
require_once("./include/dabejas_config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/style.css"/>
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/demo.js"></script>
<script>
</script>
</head>
<body>
<form id="frmListaTraTer" name="frmListaTraTer" method="post">
<div id="marco">
	<div class="div_izquierdo"  id="divTratamientos">
		<fieldset>
			<legend>Lista de tratamientos</legend>
<?php

	if(!$autenticacion->CheckLogin()) {
		$autenticacion->RedirectToURL("login.php");
		exit;
	} else {

	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	//antes verificar si debe eliminar la sesión
	if(isset($_GET["cdses"]) && $_GET["cdses"] != 0) {
		$sesion = new Sesion();
		$sesion->setCdSesion($_GET["cdses"]);
		$sql = $sesion->eliminarSesion();
		$numBorrados = $pdo->pdoInsertar($sql);						
	}

	//ahora consultar los tratamientos
	$tratamiento = new Tratamiento();
			$sqlTratamientos = $tratamiento->consultarDetalleTratamientosPorPaciente($_GET["cdpac"]);
			//echo $sqlTratamientos;
			$res = $pdo->pdoGetAll($sqlTratamientos);

			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" >");
			echo("<tr><td>Descripci&#243;n</td><td>Fecha</td><td>No.terapias</td><td>Medicación</td></tr>");
			//<td>No.terapias hechas</td>
			$indice=0;
			$color = "#ccf2ff";
			foreach($res as $fila) {
				if($indice%2)
					$color = "#b3ecff";
				else 	
					$color = "#66d9ff";							
			
				echo("<tr bgcolor=\"". $color ."\">");
				echo "<td>" . $fila["nm_tratamiento"] . "</td>";
				//formatear la fecha            
				$fechaSalida = strtotime($fila["fe_tratamiento"]);
				$fechaFormateada = date("Y/m/d", $fechaSalida);            
				echo "<td>" . $fechaFormateada . "</td>";		
				echo "<td align=\"center\">" . $fila["terapias_tratamiento"] . "</td>";		
				echo "<td align=\"center\"><a href=\"#\" onclick=\"cargarResultadosDivTerapias('".$fila["cd_tratamiento"]."');\"><img src=\"images/terapia.png\">Ver</a></td>";
				/*
				//loadQueryResultsDiv('frmListaTerapias.php?cdtra=". $fila["cd_tratamiento"]."', //'divResultadosTerapias')\" >
				"<img src=\"images/medicacion.png\">Ver</a></td>";
				*/
				echo("</tr>");
				$indice++;
			} //fin foreach
			echo("</table>");


?>
		
		</fieldset>
	</div>	
	<div class="div_derecho" id="divTerapias"> 
		<fieldset>
			<legend>Lista de terapias realizadas</legend>
			<div id="divResultadosTerapias" name="divResultadosTerapias">
<?php
	if(isset($_GET["cdses"]) && $_GET["cdses"] != 0) {
		$sqlTratamiento = $tratamiento->consultarTratamientoPorCd($_GET["cdtra"]);
		$fila = $pdo->pdoGetRow($sqlTratamiento);
		$tratamiento->obtenerTratamiento($fila);

		$sqlSesiones = $tratamiento->getDatosSesionesdeTratamiento();
		if($con) {
			$res = $pdo->pdoGetAll($sqlSesiones);

			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" >");
			echo("<tr><td colspan=\"3\"><b>". $tratamiento->getNmTratamiento() ."</b></td></tr>");
			echo("<tr><td>Fecha</td><td>Notas</td><td>Eliminar</td></tr>");
				$indice=0;
				$color = "#ccf2ff";
				foreach($res as $fila) {
					if($indice%2)
						$color = "#b3ecff";
					else 	
						$color = "#66d9ff";							
				
					echo("<tr bgcolor=\"". $color ."\">");
					$fechaSalida = strtotime($fila["fe_sesion"]);
					$fechaFormateada = date("Y/m/d", $fechaSalida);            
					echo "<td>" . $fechaFormateada . "</td>";		
					echo "<td>" . $fila["notas_sesion"] . "</td>";		
					echo "<td><a href=\"frmListaTraTer.php?cdtra=". $fila["cd_tratamiento"] . "&cdses=" .$fila["cd_sesion"] . "&cdpac=". $tratamiento->getCdPaciente()  ."\"><img src=\"images/deletei.png\"></a></td>";
					$indice++;
				} //fin foreach
				echo("</table>");
		}
	}
}

?>			 				
	</div>
		</fieldset>	
	</div>
</div>

</form>

</body>
</html>
