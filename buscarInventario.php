<html>
<head>
</head>
<body>
<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/inventario/Inventario.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

/////////////////
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	if($con) {
		if(isset($_POST["txtAnioBuscar"])) {
			$inventario = new Inventario();
			$inventario->setAnioFiscalInventario($_POST["txtAnioBuscar"]);
			$inventario->setCdSucursal($_SESSION["suc_venta"]);
			$sql = $inventario->buscarInventariosPorAnio();
			//echo "consulta sql " . $sql;
			$res = $pdo->pdoGetAll($sql);
			
			//iterar para obtener los resultados
			//<td>Editar</td><td>Eliminar</td>
			//<td>Fe.registro</td><td>Fe.cierre</td>
			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" >");
			echo("<tr><td>Referencia</td><td>Sucursal</td><td>Año</td><td>Fe.inicio</td><td>Fe.fin</td><td>Estado</td>
			<td>Nombre</td><td>Resumen</td><td>Editar</td><td>Eliminar</td></tr>");
			$indice = 0;
			foreach($res as $fila) {
				if($indice%2)
					$color = "#b3ecff";
				else 	
					$color = "#66d9ff";							

				echo("<tr bgcolor=\"". $color ."\">");
				//armar la referencia aqui
				$referencia = "INV-" . str_pad($fila["cd_sucursal"], '3', '0', STR_PAD_LEFT) . "-" . str_pad($fila["cd_inventario"], '3', '0', STR_PAD_LEFT);
				echo "<td>" . $referencia . "</td>";
				echo "<td>" . $fila["nm_sucursal"] . "</td>";
				echo "<td>" . $fila["anio_fiscal_inventario"] . "</td>";
				echo "<td>" . $fila["fe_inicio_inventario"] . "</td>";
				echo "<td>" . $fila["fe_fin_inventario"] . "</td>";		
				$estadoMostrar = $fila["cd_estado_sistema"];
				$estadoEtiqueta = ($estadoMostrar == 1 ? "<b><--Activo</b>" : "Inactivo");
				
				echo "<td>" . $estadoEtiqueta . "</td>";		
				echo "<td>" . substr($fila["nm_inventario"], 0, 25) . "</td>";		
				//echo "<td>" . $fila["fe_registro"] . "</td>";		
				//echo "<td>" . $fila["fe_cierre"] . "</td>";		
				echo "<td align=\"center\"><a href=\"#\" onclick=\"window.open('reportesInventario.php?invver=".$fila["cd_inventario"]."')\"><img src=\"images/historia.png\" alt=\"Generar reporte\"></a></td>";
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngInventario.php?cdinv=". $fila["cd_inventario"] . "');\"><img src=\"images/updatei.png\" alt=\"Actualizar\"></a></td>";
					
				if($estadoMostrar == -1) {	 
					echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngInventario.php?del=1&cdinv=". $fila["cd_inventario"] . "');\"><img src=\"images/deletei.png\" alt=\"Borrar\"></a>" ."</td>";									
				} else {
					echo "<td align=\"center\">&nbsp;</td>";
				}
				echo("<tr>");
				$indice++;
			}
			echo("</table>");
		
		}
		
	}
////////
}

?>
</body>
</html>