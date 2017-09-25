<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/usuario/Usuario.php");
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
	$sql = $pdo->cambiarBdd();
	$pdo->pdoExecute($sql);	

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
	$txtUsuario = "";

	if(isset($_POST["txtUsuario"])) {
		$txtUsuario = $_POST["txtUsuario"];
	} 

	$numResultado = 0;
	//fin para paginacion

	if($con) {
		if($txtUsuario) {
			$usuario = new Usuario();
			//obtener el número total de usuarios
			$txtUsuario = reemplazarCaracteresEspeciales($_POST["txtUsuario"]);
			$usuario->setNmUsuario($txtUsuario);			
			$sql = $usuario->buscarUsuariosPorNombre($inicio, $numRegPagina, 1);
			$res = $pdo->pdoGetAll($sql);
			$numResultado = count($res);
			
			$sql = $usuario->buscarUsuariosPorNombre($inicio, $numRegPagina, 0);
			$res = $pdo->pdoGetAll($sql);
					
			//iterar para obtener los resultados
			echo("<b>Encontrados " . $numResultado . " registros.</b><hr>");
			echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\">");
			echo("<tr><td>No.</td><td>Nombre</td><td>Usuario</td><td>Perfil</td><td>Estado</td><td>Ver info.sensible</td><td>Editar</td></tr>");
			$indice = 0;
			foreach($res as $fila) {
				if($indice%2)
					$color = "#b3ecff";
				else 	
					$color = "#66d9ff";							
				
				$indice++;
				echo("<tr bgcolor=\"". $color ."\">");
				echo "<td>" . ($indice + $inicio) . "</td>";
				echo "<td>" . $fila["nm_usuario"] . "</td>";
				echo "<td>" . $fila["login_usuario"] . "</td>";
				echo "<td>" . $fila["nm_perfil"] . "</td>";
				$activo = $fila["esta_activo"];
				$etiquetaActivo = "Inactivo";
				if($activo == 1)
					$etiquetaActivo = "Activo";
				echo "<td>" . $etiquetaActivo . "</td>";		
				
				$sensible = $fila["ver_info_sensible"];
				$etiquetaSensible = "No";
				if($sensible == 1)
					$etiquetaSensible = "Si";				
				echo "<td>" . $etiquetaSensible . "</td>";		
				echo "<td align=\"center\"><a href=\"#\" onclick=\"return loadQueryResults('frmIngUsuario.php?cdusu=". $fila["cd_usuario"] . "');\"><img src=\"images/updatei.png\" alt=\"Actualizar\"></a></td>";				
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
			$txtPaginas .= "<a href=\"#\" onclick=\"cargarUsuariosPaginacion('".$txtUsuario."','".$i."');\">[".$i."]</a>";
		}	
	}
	echo("Páginas: " . $txtPaginas);
	
}
?>
</body>
</html>