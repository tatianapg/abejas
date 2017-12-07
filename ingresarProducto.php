<?php
/* Ingresar el producto y devolver una bandera de resultado
*/
include("./aplicacion/model/producto/Producto.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	$codigoProducto = isset($_POST["txtCdProducto"]) ? $_POST["txtCdProducto"] : 0 ;
	//----------------------------Validar archivo foto que se carga --------------------
	//subir el archivo y luego insertar
	$dirFotos = "./fotos_productos/";
	$fotoCargar = $dirFotos . basename($_FILES["fileFotoProducto"]["name"]);
	$upload = 1;
	$problemas = "";
	$tipoImagen = pathinfo($fotoCargar, PATHINFO_EXTENSION);

	//verificar si es imagen
	if(isset($_POST["btnProducto"])) {
		$verificar = getimagesize($_FILES["fileFotoProducto"]["tmp_name"]);
		if($verificar !== false) {
			echo "es una imagen";
			$upload = 1;
		} else { 
			$upload = 0;
			$problemas = "I";
		}	
	}
	//verificar el tamaño: el número está en bytes: seteado para 1 KB
	if($_FILES["fileFotoProducto"]["size"] > 1500000) {
		echo "tamaño supera 1500 Kb";
		$upload = 0;
		$problemas .= "T";	
	}
	//validar tipos de imagenes
	if($tipoImagen != "jpg" && $tipoImagen != "png" && $tipoImagen != "jpeg" && $tipoImagen != "gif" 
		&& $tipoImagen != "jpe") {
		echo "Solo se admiten tipos: JPG, JPEG, PNG y GIF";
		$upload = 0;
		$problemas .= "F";		
	}
	//verificar si se puede continuar o no
	$cargaCorrecta = 0;
	$nombreArchivo = "";
	if($upload == 0) {
		echo "El archivo no fue subido.";
	} else {
		if(move_uploaded_file($_FILES["fileFotoProducto"]["tmp_name"], $fotoCargar)) {
			echo "El archivo fue cargado " . basename( $_FILES["fileFotoProducto"]["name"]);
			$nombreArchivo = $_FILES["fileFotoProducto"]["name"];
			$cargaCorrecta = 1;
		} else {
			echo "El archivo no fue subido.";
		}
	}

	/*
	$cd_producto, $cd_categoria_producto, $sku_producto, $secuencial_producto,
		$cd_estado_producto, $cd_unidad_producto, $nm_producto, $fe_ingreso_producto,
		$desc_producto, $nm_proveedor_producto, $precio_producto, $costo_interno_producto, 
		$foto_producto,	$barcode_producto, $stock_minimo_producto, $obs_producto
	*/

	if(!$upload) {
		if(isset($_POST["txtFotoProducto"]) && $_POST["txtFotoProducto"] != "") {
			$nombreArchivo = $_POST["txtFotoProducto"];
		}
	}

	//setear datos de producto; los valores de categoria, estado y unidad son por defecto 1
	$producto = new Producto();
	$producto->setProducto($_POST["txtCdProducto"], $_POST["cmbCategoriaProducto"], $_POST["txtSkuProducto"], $_POST["txtSecuencialProducto"], 
	$_POST["cmbEstadoSistema"], 1, $_POST["txtNmProducto"], date("Y-m-d"), $_POST["txtDescProducto"], 
	$_POST["txtNmProveedorProducto"], $_POST["txtPrecioProducto"], $_POST["txtCostoInternoProducto"], $nombreArchivo , "archivo_barcode.jpg", $_POST["txtStockMinimoProducto"], "");
	//$_POST["fileFotoProducto"] 

	//establecer la conexión
	$pdo = new PdoWrapper(); 
	$con = $pdo->pdoConnect();

	$autenticacion = new Autenticacion();

	$del=0;
	if($con) {
		//1er caso, borrar producto
		if(isset($_POST["del"]) && $_POST["del"] == 1) {
			//echo "ingreso a eliminar";
			$producto->setCdProducto($codigoProducto);
			$sqlEliminar = $producto->eliminarProducto();
			$numEliminados = $pdo->pdoInsertar($sqlEliminar);
			$codigoProducto = 0;
			$del = $_POST["del"];
		} else {		
			//es una tarea de ingresar, es nuevo, se crea la secuencia
			if(!$codigoProducto) {			
				//se debe añadir la secuencia, el barcode
				$sqlSecuencial = $producto->consultarSecuencial();
				$resultSecuencial = $pdo->pdoGetRow($sqlSecuencial);
				$valorSecuencial = $resultSecuencial["conteo"];
				//esto solo debe ocurrir para el primer producto que se ingresa.
				if(!$valorSecuencial)
					$valorSecuencial = 1;
				$producto->setSecuencialProducto($valorSecuencial);
				$producto->generarSkuProducto();
				$producto->setFotoProducto($nombreArchivo);
				$sql = $producto->crearProducto();    			
				echo "Sentencia sql " . $sql;
				$numInserts = $pdo->pdoInsertar($sql);
				echo "Fueron insertadas: " . $numInserts;
				$codigoProducto = $pdo->pdoLasInsertId();
			
			} else { 
				//2do caso: es una actualizacion de datos con el codigo de producto
				$producto->setCdProducto($codigoProducto);
				$sql = $producto->modificarProducto();
				//echo "La consulta es: " . $sql;
				$numActualizados = $pdo->pdoInsertar($sql);
				//echo "Fueron actualizadas: " . $numActualizados;
			}
		}	
		$autenticacion->RedirectToURL("index.php?cdpro=" . $codigoProducto . "&nmp=" . $producto->getNmProducto() . "&del=" . $del);

	} //fin es conexion

}

?>