<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/sucursal/Sucursal.php");
include("./aplicacion/model/inventario/Inventario.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {

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
			
	if(!isset($_SESSION["lista_productos"]))
		$i = 0;
	else
		$i = count($_SESSION["lista_productos"]);
	
	//guardar los valores en una variable
	if(isset($_POST["txtCodigoProducto"]))
		$_SESSION["lista_productos"][$i] = $_POST["txtCodigoProducto"];
	
}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script>
$(function() {
  $("#txtCodigoProducto").focus();
});
</script>
</head>
<body>
<form id="frmVentaProducto" name="frmVentaProducto" method="post">

<div name='divBusqueda'>
<fieldset>
<legend>Venta de productos - Sucursal <?php echo($_SESSION["suc_nombre"]);?></legend>
<table>
<input type="hidden" id="txtTipoAccion" name="txtTipoAccion" value="<?php echo($tipoAccion);?>"></input>
<input type="hidden" id="cmbSubtipo" name="cmbSubtipo" value="<?php echo($subTipo);?>"></input>
<input type="hidden" id="txtCantidadAccion" name="txtCantidadAccion" value="1"></input>
<tr><td>Inventario activo</td><td><b><?php echo(strtoupper($nombreInventario));?></b></td><td></td></tr>
<tr>
<td>C&#243;digo de producto*</td><td><input name="txtCodigoProducto" id="txtCodigoProducto" onblur="cargarResultadosDivVenta(1);"></td>
<td>
</td>
</tr>
</table>
</div>
</form>
<!-- acá abajo se puede mostrar el resultado -->
<div id="marco">
<div>
<fieldset><legend>Productos</legend>
<div id="divVenta">
</div>
</fieldset>
</div>
<div>
<fieldset><legend>Otros datos:</legend>
<table>
<tr>
<td>Descuento</td><td align="left"><input class="cajaCortaNumeros" id="txtDescuento" value="0" name="txtDescuento"></input>
<a href="#" onclick="ingresarDescuento();">[Aplicar]</a>
</td>

</tr>
<tr><td>Cliente</td><td><input id="txtCliente" name="txtCliente" value="Consumidor final"></input></td></tr>
</table>
</fieldset>
</div>
<div>
<fieldset>
<legend>Consultar venta</legend>
<a href="#" onclick="window.open('frmConsultarVenta.php', 'targetWindow', 'toolbar=no, scrollbars=no, resizable=no, top=50, left=400, width=700, height=600')">[Consultar recibo emitido]</a>
</fieldset>
</div>

</div>
<?php if($cdInvActivo) {
?>
<table><tr><td>
<!--
<input class="button" type="submit" value="Ingresar" onclick="cargarResultadosDivVenta();"></td>
-->
<input class="button" type="button" value="Generar comprobante" onclick="grabarVenta();"></td>
<td><input class="button" type="button" id="btnCancelar" value="Limpiar compra" onclick="cargarResultadosDivVenta(-1);"></td>
</tr>
<?php } else {
?>
<td><b>No existe un inventario activo, no puede proceder.</b></td><td>&nbsp;</td></tr>
<?php } ?>
</table>
</form>
</body>
</html>
