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
				$_SESSION["lista_productos"][$i]["cantidad"] = 1; //cantidad por defecto			
			}
		} else if($_POST["limpiar"] == 2){
			//si limpiar es igual a 2 entonces --> ingresa un descuento
			if($i == 0) {
				echo "<b>IMPORTANTE: </b>Ingrese primero los productos, luego el descuento.";			
			} else {
				//echo("ingreso de descuento");
				unset($_SESSION["descuento"]);
				$_SESSION["descuento"][0]["codigo"] = "0000";
				$_SESSION["descuento"][0]["nombre"] = "Descuento";
				$_SESSION["descuento"][0]["precio"] = $_POST["txtDescuento"];
				
			}
		} else if($_POST["limpiar"] == 3) {
			//si limpiar = 3, entonces se trata de borrar una linea
			$indiceBorrar = $_POST["cdIndice"];
			//funcion array_splice(array_entrada, ofsset, indiceborrar)
			array_splice($_SESSION["lista_productos"], $indiceBorrar-1, 1);
			
			//si no existen más elementos y borro todos, entonces borrar también el descuento
			if(count($_SESSION["lista_productos"]) == 0) {
				unset($_SESSION["descuento"]);
			}
		}  else if($_POST["limpiar"] == 4) {
			$indiceActualizar = $_POST["cdIndice"];
			//echo "la nueva cantidad es: " . $_POST["nuevaCantidad"];
			if(isset($_POST["nuevaCantidad"]) && is_numeric($_POST["nuevaCantidad"]) && $_POST["nuevaCantidad"] > 0) {
				$_SESSION["lista_productos"][$indiceActualizar-1]["cantidad"] = $_POST["nuevaCantidad"];	
			} else {
				$_SESSION["lista_productos"][$indiceActualizar-1]["cantidad"] = 1;	
			}
			//se trata de actualizar una cantidad del array
			
		}
		
		/**************************************/
		/* Inicio Imprimir variable de sesion*/

		if(isset($_SESSION["lista_productos"]) && count($_SESSION["lista_productos"]) > 0) {
			//iterar
			$numItems = count($_SESSION["lista_productos"]);			
			$precioTotal = 0;
			$cantidadTotal = 0;
			$j=0;
			$tabla = "<table>";
			$tabla .= "<tr>";
			$tabla .= "<td><b>Cantidad</b></td>";
			$tabla .= "<td><b>C&#243;digo</b></td><td><b>Descripci&#243;n</b></td>";
			//$tabla .= "<td><b>Cantidad(u)</b></td>";
			$tabla .= "<td><b>P.Unitario($)</b></td>";
			$tabla .= "<td><b>Valor($)</b></td>";
			$tabla .= "<td><b>Eliminar</b></td>";
			$tabla .= "</tr>";
			foreach($_SESSION["lista_productos"] as $fila) {
				$j++;
				$tabla .= "<tr>";
				if($j <> $numItems)
					$tabla .= "<td align=\"right\">". number_format($fila["cantidad"], 0, '.', '')."</td>";
				else {	
					//imprimir una caja de texto solo en el último renglón
					$tabla .= "<td align=\"right\"><a onclick=\"abrirCaja('".$j."');\"  href=\"#\" >[".number_format($fila["cantidad"])."]</a><div id=\"divCantidad\" style=\"display:none\"><input type=\"text\" maxlength=\"2\" id=\"txtCantidad\" class=\"cajaCortaNumeros\"></input><a href=\"#\" onclick=\"cerrarCaja('".$j."');\">[Listo]</a></div></td>";
					//[Listo] <img src=\"images/okcantidad.png\" >
				}
				
				//$tabla .= "<td>".$j."</td>";
				$tabla .= "<td align=\"right\">".$fila["codigo"]."</td>";
				$tabla .= "<td>".$fila["nombre"]."</td>";	
				$tabla .= "<td align=\"right\">". number_format($fila["precio"], 2, '.', '')."</td>";
				$valorFila = $fila["cantidad"] * $fila["precio"];
				$tabla .= "<td align=\"right\">". number_format($valorFila, 2, '.', '')."</td>";
				$tabla .= "<td align=\"right\"><a href=\"#\" onclick=\"borrarItem('".$j."');\">[X]</a></td>";

				$tabla .= "</tr>";
				$precioTotal += $valorFila;				
				$cantidadTotal += $fila["cantidad"];
			}
			//aqui colocar los valores finales en unidades y $$
			//number_format($number, 2, '.', '');	
			//en una variable auxiliar colocar el subtotal
			$auxSubtotal = "<input type=\"hidden\" name=\"txtSubtotal\" id=\"txtSubtotal\" value=\"".$precioTotal."\">";
			$auxTotalItems = "<input type=\"hidden\" name=\"txtItems\" id=\"txtItems\" value=\"".$cantidadTotal."\">";
			$tabla .= "<tr><td align=\"right\" colspan=\"4\"><b>SUB-TOTAL($)</b></td><td align=\"right\"><b>".number_format($precioTotal, 2, '.','')."</b></td><td>".$auxSubtotal.$auxTotalItems."</td></tr>";
			
			//si existen descuento añadir el descuento
			$descuento = 0;
			if(isset($_SESSION["descuento"]) && $_SESSION["descuento"][0]["precio"] != 0) {
				$descuento = $_SESSION["descuento"][0]["precio"];
			}			
			//siempre imprimir el descuento, aunque sea cero.
			$tabla .= "<tr><td align=\"right\" colspan=\"4\"><b>DESCUENTO(-)</b></td><td align=\"right\"><b>".number_format($descuento, 2, '.','')."</b></td><td></td></tr>";
			
			$totalFinal = $precioTotal - $descuento;
			$banderaError = "";
			$textoDescuento = "";
			if($totalFinal < 0) {
				$textoDescuento = "Revise descuento";
				$banderaError = "<input type=\"hidden\" name=\"bndErr\" id=\"bndErr\" value=\"1\">";
			}	
			//finalmente obtener el total
			$tabla .= "<tr><td align=\"right\" bgcolor=\"#FF0000\" colspan=\"4\"><h2>TOTAL($)<h2></td><td align=\"right\"><h2>".number_format($totalFinal, 2, '.','')."</h2></td><td colspan=\"2\"><font color=\"#FF0000\"><b>".$textoDescuento.$banderaError."</b></font></td></tr>";			
			echo($tabla);
		}
		
		//////////	
		/***FIN imprimir variable de sesión con productos *******/		
	} else if(isset($_POST["limpiar"]) && $_POST["limpiar"] == -1) {
		//limpiar la variable de sesion
		unset($_SESSION["lista_productos"]);
		unset($_SESSION["descuento"]);
		echo "Ingrese los productos";
		
	}

	//si el usuario pide quitar un item	
	
} //fin si autenticó la sesión

?>