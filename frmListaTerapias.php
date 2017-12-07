<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/tratamiento/Tratamiento.php");
require_once("./include/dabejas_config.php");


if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	$tratamiento = new Tratamiento();
	$tratamiento->setCdTratamiento($_GET["cdtra"]);
	$sqlTratamiento = $tratamiento->consultarTratamientoPorCd($_GET["cdtra"]);
	$fila = $pdo->pdoGetRow($sqlTratamiento);
	$tratamiento->obtenerTratamiento($fila);

	$sql = $tratamiento->getDatosSesionesdeTratamiento();

	if($con) {
		$res = $pdo->pdoGetAll($sql);

				echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" >");
				echo("<tr><td colspan=\"3\"><b>". $tratamiento->getNmTratamiento() ."</b></td></tr>");
				echo("<tr><td>Fecha</td><td>Notas</td><td>Eliminar</td></tr>");
				//<td>No.terapias hechas</td>
				$indice=0;
				$color = "#ccf2ff";
				foreach($res as $fila) {
					if($indice%2)
						$color = "#b3ecff";//"#4dd2ff";//"#66d9ff";							
					else 	
						$color = "#66d9ff";							
				
					echo("<tr bgcolor=\"". $color ."\">");
					//formatear la fecha            
					$fechaSalida = strtotime($fila["fe_sesion"]);
					$fechaFormateada = date("Y/m/d", $fechaSalida);            
					echo "<td>" . $fechaFormateada . "</td>";		
					echo "<td align=\"center\">" . $fila["notas_sesion"] . "</td>";		
					echo "<td align=\"center\"><a href=\"frmListaTraTer.php?cdtra=". $fila["cd_tratamiento"] . "&cdses=" .$fila["cd_sesion"] . "&cdpac=". $tratamiento->getCdPaciente()  ."\"><img src=\"images/deletei.png\"></a></td>";
					echo("</tr>");
					$indice++;
				} //fin foreach
				echo("</table>");
	}
}
?>