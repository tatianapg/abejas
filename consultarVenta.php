<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/model/cabeceraComprobante/Comprobante.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/accionProducto/accionProducto.php");
include("./aplicacion/model/sucursal/Sucursal.php");
include("./aplicacion/model/usuario/Usuario.php");
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
////////////////////////////
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	$txtCdRecibo = "";

	if(isset($_POST["txtCdRecibo"])) {
		$txtCdRecibo = $_POST["txtCdRecibo"];
	} 

	if($con) {
		if($txtCdRecibo) {
			$comprobante = new Comprobante();
			//obtener el número total de productos		
			$comprobante->setCdCabecera($txtCdRecibo);
			$comprobante->setCdSucursal($_SESSION["suc_venta"]);
			$sql = $comprobante->getComprobante();		
			$res = $pdo->pdoGetRow($sql);
			$comprobante->obtenerComprobante($res);
			
			if($res) {
				//solo si existe el comprobante se hacen consultas de usuario y sucursal
				$usuario = new Usuario();
				$usuario->setCdUsuario($comprobante->getCdUsuario());
				$sql = $usuario->consultarUsuario();
				$res = $pdo->pdoGetRow($sql);
				$usuario->obtenerUsuario($res);
				
				$sucursal = new Sucursal();
				$sucursal->setCdSucursal($comprobante->getCdSucursal());
				$sql = $sucursal->consultarSucursal();
				$resSucursal = $pdo->pdoGetRow($sql);
				$sucursal->obtenerSucursal($resSucursal);
									
		///////////////////	
				$accion = new AccionProducto();
				$accion->setCdCabecera($txtCdRecibo);
				$accion->setCdSucursal($_SESSION["suc_venta"]);
				$sql = $accion->recuperarAccionesDadaCabecera();
				//echo "Consulta de cabecera: " . $sql;
				$resultDetalle = $pdo->pdoGetAll($sql);
						
				//consultar la cabecera
				$comprobante->setCdCabecera($txtCdRecibo);
				$sql = $comprobante->getComprobante();
				$result = $pdo->pdoGetRow($sql);
				//echo "\nConsulta de detalle: " . $sql;
				$comprobante->obtenerComprobante($result);
					
				$i=0;
				$subtotal = 0;

				$tabla = "<table cellpadding=\"1\">";
				$tabla .= "<tr><td align=\"center\" colspan=\"3\"><h3>Recibo No. ".$txtCdRecibo."</h3></td></tr>";
				$tabla .= "<tr>";
				$tabla .= "<td><b>Cliente:</b> ". $comprobante->getNmCliente() ."</td><td></td>";
				$tabla .= "<td><b>Fecha compra:</b> ".$comprobante->getFeComprobante() ."</td>";
				$tabla .= "</tr>";
				$tabla .= "<tr>";
				$tabla .= "<td><b>Sucursal:</b> ".$sucursal->getNmSucursal() ."</td><td></td>";
				$tabla .= "<td><b>Vendedor:</b> ".$usuario->getLoginUsuario() ."</td>";
				$tabla .= "</tr>";
				$tabla .= "<tr><td colspan=\"2\"></td></tr>";
				
				$tabla .= "<table>";
				$tabla .= "<tr><td><b>No.</b></td><td><b>Código</b></td><td><b>Descripción</b></td>";
				$tabla .= "<td><b>Precio($)</b></td><td><b>Cantidad(u)</b></td></tr>";
				foreach($resultDetalle as $fila) {
					$i++;
					$tabla .= "<tr>";
					$tabla .= "<td>".$i."</td>";
					$tabla .= "<td>".$fila["codigo"]."</td>";
					$tabla .= "<td>".$fila["nombre"]."</td>";
					$tabla .= "<td align=\"right\">". number_format($fila["precio"], 2, '.', '')."</td>";
					$tabla .= "<td align=\"right\">1</td>";
					$tabla .= "</tr>";
					$subtotal += $fila["precio"];
				} //fin del foreach 				

				//imprimir el subtotal
				$tabla .= "<tr><td align=\"right\" colspan=\"3\"><b>SUB-TOTAL($)</b></td><td align=\"right\"><b>".number_format($subtotal, 2, '.','')."</b></td><td></td></tr>";
						
				//imprimir el descuento				
				$descuento = $comprobante->getDescuentoComprobante();
				$tabla .= "<tr><td></td><td></td><td align=\"right\"><b>DESCUENTO(-)</b></td><td align=\"right\"><b>". number_format($descuento, 2, '.', '') ."</b></td></tr>";			
					
				//imprimir el total 
				$totalFinal = $subtotal - $descuento;
				$tabla .= "<tr><td align=\"right\" colspan=\"3\"><h2>TOTAL($)</h2></td><td align=\"right\"><h2>".number_format($totalFinal, 2, '.','')."</h2></td><td align=\"right\"><h1></h1></td></tr>";
			
				echo($tabla);							
			} // fin res
			else {
				echo "No existen datos para el recibo No. " . $txtCdRecibo;
			}
		
		}
	}
//////////////
}

?>
</body>
</html>