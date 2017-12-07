<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/sucursal/Sucursal.php");
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
	//$numTotal = 0;
	$txtSucursal = "";

	if(isset($_POST["txtSucursal"])) {
		$txtSucursal = $_POST["txtSucursal"];
	} 

	$numResultado = 0;
	//fin para paginacion

	if($con) {
		if($txtSucursal) {
			$sucursal = new Sucursal();
			//obtener el número total de sucursales
			//$txtSucursal = str_replace(array('<', '>', '{', '}', '[', ']', '\'', '"'), '', $_POST["txtSucursal"]);
			$txtSucursal = reemplazarCaracteresEspeciales($_POST["txtSucursal"]);
			$sucursal->setNmSucursal($txtSucursal);

			$sql = $sucursal->buscarSucursalesPorNombre($inicio, $numRegPagina, 1);
			$res = $pdo->pdoGetAll($sql);
			$numResultado = count($res);
			
			$sql = $sucursal->buscarSucursalesPorNombre($inicio, $numRegPagina, 0);
			$res = $pdo->pdoGetAll($sql);
					
			//iterar para obtener los resultados
			echo("<b>Encontrados " . $numResultado . " registros.</b><hr>");
			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\">");
			echo("<tr><td>No.</td><td>Nombre</td><td>Editar</td></tr>");
			$indice = 0;
			foreach($res as $fila) {
				if($indice%2)
					$color = "#b3ecff";
				else 	
					$color = "#66d9ff";							
				
				$indice++;
				echo("<tr bgcolor=\"". $color ."\">");
				//echo "<td>" . ($indice + $inicio) . "</td>";
				echo "<td>" . str_pad($fila["cd_sucursal"], '3', '0', STR_PAD_LEFT) . "</td>";
				echo "<td>" . $fila["nm_sucursal"] . "</td>";
				
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngSucursal.php?cdsuc=". $fila["cd_sucursal"] . "');\"><img src=\"images/updatei.png\" alt=\"Actualizar\"></a></td>";				
				echo("<tr>");				
			}
			echo("</table>");		
		}
		
	}

	$paginasTotales = ceil($numResultado / $numRegPagina);

	$txtPaginas = "";
	for($i = 1; $i <= $paginasTotales; $i++) {
		if($i == $pagina) {
			$txtPaginas .= "<b>[".$i."]</b>";
		} else {
			$txtPaginas .= "<a href=\"#\" onclick=\"cargarSucursalesPaginacion('".$txtSucursal."','".$i."');\">[".$i."]</a>";
		}	
	}
	echo("Páginas: " . $txtPaginas);
	
}
?>
</body>
</html>