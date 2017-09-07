<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/sucursal/Sucursal.php");
include("./aplicacion/model/inventario/Inventario.php");
include("./aplicacion/model/tipoAccion/TipoAccion.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {	

	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

	////////////////////////////////////
	//abrir una conexion con la bdd
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	//ver si existe un inventario activo
	$inventario = new Inventario();
	$inventario->setCdSucursal($_SESSION["suc_venta"]);
	$sql = $inventario->obtenerCdInventarioActivo();
	$fila = $pdo->pdoGetRow($sql);
	$cdInvActivo = $fila["cd_inventario"];
	//recuperar detalles del inventario activo
	$nombreInventario = "No existe";
	if($cdInvActivo) {
		$inventario->setCdInventario($cdInvActivo);
		$sql = $inventario->consultarInventario();
		$fila = $pdo->pdoGetRow($sql);
		$inventario->obtenerInventario($fila);
		$nombreInventario = $inventario->getNmInventario();
	}

	/*
	$sucursal = new Sucursal();
	$sql = $sucursal->getTodasSucursales();
	//echo "consulta de textos: " . $sql;
	if($con) {
		$result = $pdo->pdoGetAll($sql);
		$combo = construirCombo($result, $sucursal->getCdSucursal());
	}
	*/

	//obtener el tipo de acción: compra o venta
	$tipoAccion = "0";
	$etiqueta = "";
	$accion = "";
	$accionSubtipo = "";
	if(isset($_GET["c"]) && $_GET["c"] == 1) {
		$tipoAccion = "1";
		$etiqueta = "Carga de producto (Nuevas compras / devoluciones de cliente)";	
		$accion = "Cargar producto?";
		$accionSubtipo = "Carga";
	}
	if(isset($_GET["v"]) && $_GET["v"] == 1) {
		$tipoAccion = "2";
		$etiqueta = "Descarga de producto(caducidad / fallas empaque / gratuitades / retorno al proveedor)";	
		$accion = "Descargar producto?";
		$accionSubtipo = "Descarga";
	}

	//obtener también los subtipos de compras o ventas
	$tipo = new TipoAccion();
	$tipo->setTipoAccionPadre($tipoAccion);
	$tipo->setCondicionExcluyente(5);
	$sqlSubtipos = $tipo->getTodosTiposAccionDadoPadre();

	$result = $pdo->pdoGetAll($sqlSubtipos);
	$comboSubtipos = construirCombo($result, $tipo->getCdTipoAccion());
	////////////////////////////////////
}	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script>
$(function() {
  $("#txtCodigoProducto").focus();
  
  // Initialize form validation on the registration form.
  $("form[name='frmCargarProducto']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtCodigoProducto: "required",
	  txtCantidadAccion: "required",
	  cmbSubtipo: "required"	  
	},  
    messages: {
      txtCodigoProducto: "requerido",
	  txtCantidadAccion: "requerido",
	  cmbSubtipo: "requerido"
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
<form id="frmCargarProducto" name="frmCargarProducto" method="post">
<div name='divBusqueda'>
<fieldset>
<legend><?php echo($etiqueta);?></legend>
<table>
<input type="hidden" id="txtTipoAccion" name="txtTipoAccion" value="<?php echo($tipoAccion);?>"></input>
<tr><td>Inventario activo</td><td><b><?php echo(strtoupper($nombreInventario));?></b></td></tr>
<tr><td>C&#243;digo de producto*</td><td><input name="txtCodigoProducto" id="txtCodigoProducto" onblur="cargarDivNombreProducto();"></td><td><b><div id="divNombreProducto" name="divNombreProducto"></div></b></td></tr>

<!--
<tr><td>Sucursal*</td><td>
<select class="combo" name="cmbSucursal" id="cmbSucursal">
<?php //echo $combo;?>
</select><td>&nbsp;</td>
</tr>
-->
<tr><td>Tipo de <?php echo($accionSubtipo);?>*</td><td>
<select class="combo" name="cmbSubtipo" id="cmbSubtipo">
<?php 
echo $comboSubtipos;
?>
</select><td>&nbsp;</td>
</tr>

<tr><td>Cantidad*</td><td><input class="cajaCorta" name="txtCantidadAccion" id="txtCantidadAccion"></input>unidades</td><td>&nbsp;</td></tr>
<?php
if(isset($_GET["c"]) && $_GET["c"] == 1) {
?>
<tr><td>Es carga inicial</td><td>
<select class="combo" id="cmbInicial" name="cmbInicial">
<option value="">Seleccione</option>
<option value="1">Si</option>
<option value="0">No</option>
</select>
</td></tr>
<?php
}
?>

<?php if($cdInvActivo) {
?>
<tr><td><?php echo($accion);?></td><td><input class="button" type="button" value="Proceder" onclick="cargarResultadosDivCargarProducto();"></td><td>&nbsp;</td></tr>
<?php } else {
?>
<tr><td><?php echo($accion);?></td><td><b>No existe un inventario activo, no puede proceder.</b></td><td>&nbsp;</td></tr>
<?php } ?>
</table>
</div>

</form>
<!-- acá abajo se puede mostrar el resultado -->
<div>
<fieldset><legend>Resultados</legend>
<b><div id="divResultadosCargaProducto"></div></b>
</fieldset>
</div>
</form>
</body>
</html>
