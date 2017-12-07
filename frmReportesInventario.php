<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/sucursal/Sucursal.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	//abrir una conexion con la bdd
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	$sucursal = new Sucursal();
	$sql = $sucursal->getTodasSucursales();
	//echo "consulta de textos: " . $sql;
	if($con) {
		$result = $pdo->pdoGetAll($sql);
		//$combo = construirCombo($result, $sucursal->getCdSucursal());
		$combo = construirComboSoloDatos($result, $sucursal->getCdSucursal());
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/style.css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/jquery-ui.css"/>
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/jquery_validate.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/jquery-ui.min.js"></script>

<script>
$(function() {

  $( "#txtFeInicioInventario" ).datepicker({
	  dateFormat: "yy-mm-dd"	  
  });
  
  $( "#txtFeFinInventario" ).datepicker({
	  dateFormat: "yy-mm-dd"
  });		

  // Initialize form validation on the registration form.
  $("form[name='frmReportesInventario']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      cmbSucursal: "required",
	  txtFeInicioInventario: "required",
	  txtFeFinInventario: "required",
	  reporte: "required"
	  
	},  
    messages: {
      cmbSucursal: "requerido",
	  txtFeInicioInventario: "requerido",
	  txtFeFinInventario: "requerido",
	  reporte: "requerido"
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
<form id="frmReportesInventario" name="frmReportesInventario" method="post" action="reportesInventario.php" target="_blank">
<input type="hidden" name="origen_reportes" id="origen_reportes" value="origen_reportes"></input>
<div name='divBusqueda'>
<fieldset>
<legend>Reportes de productos</legend>
<table>
<?php
$currentLocal = setlocale(LC_ALL, 0);
?>
<tr><td colspan="6"><b>Parámetros:</b></td></tr>
<tr><td>Sucursal*</td><td><select class="combo" name="cmbSucursal" id="cmbSucursal">
<?php 
//$combo .= "<option value='-1'>Todas</option>";
echo $combo;
?>
</select></td>
<td>Tipo movimiento</td><td><select class="combo" name="cmbAccion" id="cmbAccion">
<option value='-1'>Todos</option>
<option value='1'>Compras</option>
<option value='2'>Ventas</option>
</select></td>
<td>Usuarios:<select class="combo" id="cmbUsuario" name="cmbUsuario">
<option value="">Todos los usuarios</option>
<option value="1">Usuario actual</option>
</select></td>
<!--
<td>&nbsp;</td>
-->
</tr>
<tr>
<td>Fecha inicio*</td><td><input type="text" class="cajaCorta" name="txtFeInicioInventario" id="txtFeInicioInventario" value=""></td>
<td >Fecha fin*</td><td><input type="text" class="cajaCorta" name="txtFeFinInventario" id="txtFeFinInventario" value=""></td>
<td></td>
</tr>
<tr><td colspan="6"><table>
<tr><td colspan="6"><b>Reportes:</b></td></tr>
<tr>
<tr>
<td><input type="radio" name="reporte" id="reporte" value="resumen_ventas">Resumen de ventas</input></td>
</tr>
<td><input type="radio" name="reporte" id="reporte" value="movimientos_diario">Reporte de movimientos</input></td>
</tr>

<!--
<tr>
<td><input type="radio" name="reporte" id="reporte" value="movimientos_fecha">Reporte diario de movimientos con fechas</input></td>
</tr>
-->
<tr><td><input type="radio" name="reporte" id="reporte" value="stock">Reporte de inventario activo</input></td></tr>
<tr><td><input type="radio" name="reporte" id="reporte" value="mas_vendidos">Reporte de productos más vendidos</input></td></tr>
</td></tr></table>
<tr><td colspan="6"><input type="submit" name="btnReporte" id="btnReporte" value="Generar reporte"></td></tr>
</table>
</fieldset>
</div>
</form>
</body>
</html>
