<?php
include("./aplicacion/controller/Controller.php");
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

	//luego hacer la consulta
	$sql = $tratamiento->getDatosMedicacionesdeTratamiento();

}
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
<?php
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
				$fechaSalida = strtotime($fila["fe_medicacion"]);
				$fechaFormateada = date("Y/m/d", $fechaSalida);            
				echo "<td>" . $fechaFormateada . "</td>";		
				echo "<td>" . $fila["notas_medicacion"] . "</td>";		
				echo "<td><a href=\"frmListaTraMed.php?cdtra=". $fila["cd_tratamiento"] . "&cdmed=" .$fila["cd_medicacion"] . "&cdpac=". $tratamiento->getCdPaciente()  ."\"><img src=\"images/deletei.png\"></a></td>";
				$indice++;
			} //fin foreach
			echo("</table>");
}
?>
</body>
</html>