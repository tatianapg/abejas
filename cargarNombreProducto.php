<html>
<head>
</head>
<body>
<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/producto/Producto.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	if($con) {
		if(isset($_POST["txtCodigoProducto"])) {
			$producto = new Producto();
			$producto->setSkuProducto($_POST["txtCodigoProducto"]);
			$sqlCodigo = $producto->consultarProductoDadoSku();
			//echo "consulta nombr prod " .$sqlCodigo;
			$resultProducto = $pdo->pdoGetRow($sqlCodigo);
			if($resultProducto) {
				$producto->obtenerProducto($resultProducto);
				echo(strtoupper($producto->getNmProducto()));
			} else {
				echo(strtoupper("El código de producto no existe."));
			}
			
		}
	}	
}
?>
</body>
</html>