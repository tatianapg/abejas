<?php
/* Ingresar el inventario y devolver una bandera de resultado
*/
include("./aplicacion/model/inventario/Inventario.php");
include("./aplicacion/model/producto/Producto.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");


if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	
	if(!isset($_SESSION["lista_productos"]))
		$i = 0;
	else
		$i = count($_SESSION["lista_productos"]);	
	
	
	//echo("El valor de i es" . $i . "limpiar " . $_POST["limpiar"]);
	//verificar si hay que limpiar la variabled de sesión por cada vez que presione el botón
	if(isset($_POST["limpiar"]) && $_POST["limpiar"] > 0) {
		
		//si limpiar = 1 --> ingresa un producto  nuevo
		if($_POST["limpiar"] == 1) {
			//guardar los valores en una variable
			///$_SESSION["lista_productos"][$i] = $_POST["txtCodigoProducto"];
			$pdo = new PdoWrapper();
			$con = $pdo->pdoConnect();
				
			$producto = new Producto();
			$producto->setSkuProducto($_POST["txtCodigoProducto"]);
				
			$sqlCodigo = $producto->consultarProductoDadoSku();
			$resultProducto = $pdo->pdoGetRow($sqlCodigo);
			//echo $sqlCodigo;
			$nmProducto = "";
			if($resultProducto) {
				$producto->obtenerProducto($resultProducto);
				$nmProducto = $producto->getNmProducto();
				$precio = $producto->getPrecioProducto();

				//cargar el producto con nombre y precio		
				$_SESSION["lista_productos"][$i]["codigo"] = $_POST["txtCodigoProducto"];
				$_SESSION["lista_productos"][$i]["nombre"] = $nmProducto;
				$_SESSION["lista_productos"][$i]["precio"] = $precio;			
			}
		} else if($_POST["limpiar"] == 2){
			//si limipiar es igual a 2 entonces --> ingresa un descuento
			if($i == 0) {
				echo "<b>IMPORTANTE: </b>Ingrese primero los productos, luego el descuento.";			
			} else {
				//echo("ingreso de descuento");
				unset($_SESSION["descuento"]);
				$_SESSION["descuento"][0]["codigo"] = "0000";
				$_SESSION["descuento"][0]["nombre"] = "Descuento";
				$_SESSION["descuento"][0]["precio"] = $_POST["txtDescuento"];
							
				//unir los arrays
				//$arrayFinal = array_merge($_SESSION["lista_productos"], $_SESSION["descuento"]);
				//$_SESSION["lista_productos"] = array_merge($_SESSION["lista_productos"], $_SESSION["descuento"]);
				
			}
		} else if($_POST["limpiar"] == 3) {
			
			$indiceBorrar = $_POST["cdIndice"];
			//funcion array_splice(array_entrada, ofsset, indiceborrar)
			array_splice($_SESSION["lista_productos"], $indiceBorrar-1, 1);
			//si no existen más elementos y borro todos, entonces borrar también el descuento
			if(count($_SESSION["lista_productos"]) == 0)
				unset($_SESSION["descuento"]);
			
		}
		/////////
		/* Imprimir variable de sesion*/
		//if(isset($_SESSION["lista_productos"]) && count($_SESSION["lista_productos"]) > 0) {
		if(isset($_SESSION["lista_productos"]) && count($_SESSION["lista_productos"]) > 0) {
			//iterar
			$precioTotal = 0;
			$j=0;
			$tabla = "<table>";
			$tabla .= "<tr><td><b>No.</b></td><td><b>C&#243;digo</b></td><td><b>Descripci&#243;n</b></td>";
			$tabla .= "<td><b>Precio($)</b></td><td><b>Cantidad(u)</b></td><td><b>Eliminar</b></td></tr>";
			foreach($_SESSION["lista_productos"] as $fila) {
				$j++;
				$tabla .= "<tr>";
				$tabla .= "<td>".$j."</td>";
				$tabla .= "<td>".$fila["codigo"]."</td>";
				$tabla .= "<td>".$fila["nombre"]."</td>";
				$tabla .= "<td align=\"right\">". number_format($fila["precio"], 2, '.', '')."</td>";
				$tabla .= "<td align=\"right\">1</td>";
				$tabla .= "<td align=\"right\"><a href=\"#\" onclick=\"borrarItem('".$j."');\">[X]</a></td>";
				$tabla .= "</tr>";
				$precioTotal += $fila["precio"];
			}
			//aqui colocar los valores finales en unidades y $$
			//number_format($number, 2, '.', '');	
			//en una variable auxiliar colocar el subtotal
			$auxSubtotal = "<input type=\"hidden\" name=\"txtSubtotal\" id=\"txtSubtotal\" value=\"".$precioTotal."\">";
			$auxTotalItems = "<input type=\"hidden\" name=\"txtItems\" id=\"txtItems\" value=\"".$j."\">";
			$tabla .= "<tr><td align=\"right\" colspan=\"3\"><b>SUB-TOTAL($)</b></td><td align=\"right\"><b>".number_format($precioTotal, 2, '.','')."</b></td><td>".$auxSubtotal.$auxTotalItems."</td></tr>";
			
			//si existen descuento añadir el descuento
			$descuento = 0;
			if(isset($_SESSION["descuento"]) && $_SESSION["descuento"][0]["precio"] != 0) {
				$descuento = $_SESSION["descuento"][0]["precio"];
			}			
			//siempre imprimir el descuento, aunque sea cero.
			$tabla .= "<tr><td align=\"right\" colspan=\"3\"><b>DESCUENTO(-)</b></td><td align=\"right\"><b>".number_format($descuento, 2, '.','')."</b></td><td></td></tr>";
			
			$totalFinal = $precioTotal - $descuento;
			$banderaError = "";
			$textoDescuento = "";
			if($totalFinal < 0) {
				$textoDescuento = "Revise descuento";
				$banderaError = "<input type=\"hidden\" name=\"bndErr\" id=\"bndErr\" value=\"1\">";
			}	
			//finalmente obtener el total
			$tabla .= "<tr><td align=\"right\" bgcolor=\"#FF0000\" colspan=\"3\"><h2>TOTAL($)<h2></td><td align=\"right\"><h2>".number_format($totalFinal, 2, '.','')."</h2></td><td colspan=\"2\"><font color=\"#FF0000\"><b>".$textoDescuento.$banderaError."</b></font></td></tr>";			
			echo($tabla);
		}
		
		//////////		
	} else if(isset($_POST["limpiar"]) && $_POST["limpiar"] == -1) {
		//limpiar la variable de sesion
		unset($_SESSION["lista_productos"]);
		unset($_SESSION["descuento"]);
		echo "Ingrese los productos";
		
	}

	//si el usuario pide quitar un item	
	
} //fin si autenticó la sesión

?>