<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/producto/Producto.php");
include("./aplicacion/model/categoria/Categoria.php");
require_once("./include/dabejas_config.php");

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/style.css"/>
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/jquery_validate.js"></script>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmIngProducto']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtNmProducto: {
		  required: true,
		  maxlength: 50
	  },
	  cmbCategoriaProducto: "required",
	  cmbEstadoSistema: "required",
	  txtPrecioProducto: {
		required: true,
		number:true
	  },
	  txtStockMinimoProducto: {
		number:true 
	  },
	  txtCostoInternoProducto: {
		number:true  
	  }
	  
	},  
    messages: {
      txtNmProducto: { 
		required: "requerido",
		maxlength: "Hasta 50 caracteres."
	  },
	  cmbCategoriaProducto: "requerido", 
	  cmbEstadoSistema: "requerido",
	  txtPrecioProducto: 
	  { 
	    required: "requerido",
	    number: "Ingrese un n&#250;mero correcto"
	  },
	  txtStockMinimoProducto: { 
	    number: "Ingrese un n&#250;mero correcto"
	  },
	  txtCostoInternoProducto: {
		number: "Ingrese un n&#250;mero correcto"
	  }
	  
	  
    },
 
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      form.submit();
    }
  });
});

</script>
</head>
<body>
<?php

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	/////
	//si tiene la bandera sensible entonces puede ver el costo|
	$banderaSensible = $_SESSION['ver_infosen'];
	
	$etiquetaBoton = "Ingresar";
	$producto = new Producto();
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	//es modificacion de paciente
	if(isset($_GET["cdpro"]) && $_GET["cdpro"] > 0) {
		$etiquetaBoton = "Modificar";

		//echo "existe paciente";
		$producto->setCdProducto($_GET["cdpro"]);
		$sql = $producto->consultarProducto();        

		if($con) {
			$fila = $pdo->pdoGetRow($sql);
			$producto->obtenerProducto($fila);
		} else {
			echo "error conexión bdd!!!";
		}    
	}

	//es eliminación de producto, verificar antes si se puede eliminar
	$habilitarBoton ="";
	if(isset($_GET["del"]) && $_GET["del"] == 1 ) {
		$etiquetaBoton = "Eliminar";	
		//aqui mismo validar si hay chance de eliminar
		//si tiene tratamientos no se elimina
		$producto->setCdProducto($_GET["cdpro"]);
		$sqlValidacion = $producto->validarEliminarProducto();
		
		$resultAcciones = $pdo->pdoGetRow($sqlValidacion);
		$numAcciones = $resultAcciones["conteo"];
		//deshabilitar el botón porque existen datos asociados al producto
		if($numAcciones > 0) {
			//echo "se deshabilita el boton por " . $numAcciones;
			$habilitarBoton = "disabled";		
		}	
	}

	//recuperar las categorias
	$categoria = new Categoria();
	$sql = $categoria->getTodasCategorias();

	if($con) {
		$result = $pdo->pdoGetAll($sql);
		$combo = construirCombo($result, $producto->getCdCategoriaProducto());
	}
	////
}
?>
<form method="post" action="ingresarProducto.php" name="frmIngProducto" id="frmIngProducto" enctype="multipart/form-data">
<input type="hidden" name="txtCdProducto" value="<?php echo($producto->getCdProducto()); ?>"></input>
<input type="hidden" name="del" value="<?php echo($_GET["del"]); ?>"></input>
<input type="hidden" name="txtSkuProducto" value="<?php echo($producto->getSkuProducto());?>"></input>
<input type="hidden" name="txtSecuencialProducto" value="<?php echo($producto->getSecuencialProducto());?>"></input>
<input type="hidden" name="txtFotoProducto" value="<?php echo($producto->getFotoProducto());?>"></input>
<div>
<fieldset><legend>Detalle de Producto</legend>
<table>
<tr>
<td class="etiqueta">Nombre*</td><td colspan="3"><input maxlength="50" class="cajaExtraLarga" name="txtNmProducto" id="txtNmProducto" value="<?php echo($producto->getNmProducto());?>"></input></td>
</tr>
<tr>
<td class="etiqueta">C&#243;digo &#250;nico</td><td><input disabled class="cajaCorta" name="skuProducto" id="skuProducto" value="<?php echo($producto->getSkuProducto());?>"></input></td>
<td class="etiqueta">Categor&#237;a*</td><td><select id="cmbCategoriaProducto" name="cmbCategoriaProducto">
<?php 
echo $combo;
?>
</select></td>
</tr>
<tr>
<td class="etiqueta">Proveedor</td><td><input name="txtNmProveedorProducto" id="txtNmProveedorProducto" maxlength="60" value="<?php echo($producto->getNmProveedorProducto());?>"></td>
<td class="etiqueta">Stock m&#237;nimo</td><td><input class="cajaCorta" name="txtStockMinimoProducto" id="txtStockMinimoProducto" value="<?php echo($producto->getStockMinimoProducto());?>"></td>
</tr>
<tr>
<?php 
$costoProducto = "*******";
if($banderaSensible == 1)
	$costoProducto = $producto->getCostoInternoProducto();
?>
<td class="etiqueta">Costo/unidad</td><td><input class="cajaCorta" name="txtCostoInternoProducto" id="txtCostoInternoProducto" value="<?php echo ($costoProducto);?>"></td>
<td class="etiqueta">Precio/unidad*</td><td><input class="cajaCorta" name="txtPrecioProducto" id="txtPrecioProducto" value="<?php echo($producto->getPrecioProducto());?>"></td>
</tr>
<tr>
<td class="etiqueta">Descripci&#243;n</td><td colspan="3"><textarea name="txtDescProducto" id="txtDescProducto"><?php echo($producto->getDescProducto());?></textarea></td>
<tr>
<td class="etiqueta">Estado*</td><td><select id="cmbEstadoSistema" name="cmbEstadoSistema"><option value="">Seleccione</option>
<option value="1" <?php if($producto->getCdEstadoSistema() == 1) echo "selected";  ?>>Activo</option>
<option value="-1" <?php if($producto->getCdEstadoSistema() == -1) echo "selected";  ?>>Inactivo</option>
</select></td>
<td class="etiqueta">Imagen del producto</td><td>
<input type="file" name="fileFotoProducto" id="fileFotoProducto"><div class="etiqueta">Tipos: JPG, JPE, JPEG, PNG, GIF.  Max: 1500 Kb</div>
</td></tr>
</tr>
</table>
<?php 
if(isset($_GET["cdpro"]) && $_GET["cdpro"] != 0 ) {
	$archivoFoto = $producto->getFotoProducto();
	$rutaFoto = "./fotos_productos/" . $archivoFoto;
	//<?php echo($producto->getSkuProducto()
?>
<?php 
$skuLink = $producto->getSkuProducto();
?>
<!--

-->
<div id="divImagenes">
<table cellpadding="1">
<tr>
<td class="etiqueta" >Foto del producto</td>
<td><img src="<?php echo($rutaFoto);?>" border="1" height="200" width="200"></td>
<td class="etiqueta" >C&#243;digo de barras 
<!--
<a href="#" onclick="window.open('imprimirBarcode.php?sku=<?php echo($skuLink);?>');">[Imprimir]</a>
-->
</td>
<td ><img src="verImagenBarras.php?sku=<?php echo($producto->getSkuProducto());?>" border="1" height="200" width="200"></td>
</tr>
</table>
</div>

<!--
</div>
-->
<?php 
}
?>
<div>
</div>
</fieldset>
</div>
<?php if ($banderaSensible == 1) { ?>
<p><input class="submit" type="submit" name="btnProducto" id="btnProducto" value="<?php echo($etiquetaBoton); ?>" <?php echo($habilitarBoton); ?>><p>
<?php } ?>
</form>
</body>
</html>
