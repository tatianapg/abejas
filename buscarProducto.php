<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/producto/Producto.php");
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
	$txtPro = "";
	$criterio = "";

	//$criterio = $_POST["cmbCriterio"]
	if(isset($_POST["txtPro"])) {
		$txtPro = reemplazarCaracteresEspeciales($_POST["txtPro"]);		
	} 
	$criterio = $_POST["cmbCriterio"];

	$numResultado = 0;
	//fin para paginacion

	if($con) {
		//if(isset($_POST["txtPro"])) {
		if($txtPro) {
			//$txtPro = $_GET["txtPro"];
			$producto = new Producto();
			//obtener el número total de productos		
			$producto->setNmProducto($txtPro);
			$producto->setCriterioBuscar($criterio);
			$sql = $producto->buscarProductosPorNombre($inicio, $numRegPagina, 1);
			$res = $pdo->pdoGetAll($sql);
			$numResultado = count($res);
			//echo "\nconteo res consulta " .  count($res);
			
			$sql = $producto->buscarProductosPorNombre($inicio, $numRegPagina, 0);
			$res = $pdo->pdoGetAll($sql);
					
			//iterar para obtener los resultados
			//echo("<b>Encontrados " . count($res) . " registros de un total de " . $numTotal . ".</b><hr>");
			echo("<b>Encontrados " . $numResultado . " registros.</b><hr>");
			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\">");
			echo("<tr><td>No.</td><td>Producto</td><td>Código</td><td>Categoría</td><td>Editar</td><td>Eliminar</td></tr>");
			$indice = 0;
			foreach($res as $fila) {
				if($indice%2)
					$color = "#b3ecff";
				else 	
					$color = "#66d9ff";							
				
				$indice++;
				echo("<tr bgcolor=\"". $color ."\">");
				echo "<td>" . ($indice + $inicio) . "</td>";
				echo "<td>" . $fila["nm_producto"] . "</td>";
				echo "<td>" . $fila["sku_producto"] . "</td>";
				echo "<td>" . $fila["nm_categoria_producto"] . "</td>";		
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngProducto.php?cdpro=". $fila["cd_producto"] . "');\"><img src=\"images/updatei.png\" alt=\"Actualizar\"></a></td>";
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngProducto.php?del=1&cdpro=". $fila["cd_producto"] . "');\"><img src=\"images/deletei.png\" alt=\"Borrar\"></a>" ."</td>";									
				
				echo("<tr>");
				
			}
			echo("</table>");
		
		}
		
	}

	$paginasTotales = ceil($numResultado / $numRegPagina);
	//echo ("registros totales son: " . $numTotal);
	//echo ("Las páginas totales son: " . $paginasTotales);

	$txtPaginas = "";
	for($i = 1; $i <= $paginasTotales; $i++) {
		if($i == $pagina) {
			$txtPaginas .= "<b>[".$i."]</b>";
		} else {
		//"cargarResultadosDivProductos();"
		//"buscarProducto.php?txtPro=".$txtPro."&pag=". $i. "
			//$txtPaginas .= "<a href=\"buscarProducto.php?txtPro=".$txtPro."&pag=". $i. "\">[".$i."]</a>";
			$txtPaginas .= "<a href=\"#\" onclick=\"cargarProductosPaginacion('".$txtPro."',".$i. ",'" .$criterio ."');\">[".$i."]</a>";
		}	
	}
	echo("Páginas: " . $txtPaginas);
	
}
?>
</body>
</html>