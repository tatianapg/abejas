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
		$combo = construirCombo($result, $sucursal->getCdSucursal());
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

  $( "#txtFeInicioTerapia" ).datepicker({
	  dateFormat: "yy-mm-dd"	  
  });
  
  $( "#txtFeFinTerapia" ).datepicker({
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
<form id="frmReportesInventario" name="frmReportesInventario" method="post" action="reportesPaciente.php" target="_blank">
<div name='divBusqueda'>
<fieldset>
<legend>Reportes de pacientes</legend>
<table>
<tr><td colspan="6"><b>Parámetros:</b></td></tr>
<tr><td>Sucursal*</td><td><select class="combo" name="cmbSucursal" id="cmbSucursal">
<?php 
$combo .= "<option value='-1'>Todas</option>";
echo $combo;
?>
</select></td>
<!--
<td>&nbsp;</td>
-->
<td>Fecha inicio terapias*</td><td><input type="text" class="cajaCorta" name="txtFeInicioTerapia" id="txtFeInicioTerapia" value=""></td>
<td >Fecha fin terapias*</td><td><input type="text" class="cajaCorta" name="txtFeFinTerapia" id="txtFeFinTerapia" value=""></td>
</tr>
<tr><td colspan="6"><table>
<tr><td colspan="6"><b>Reportes:</b></td></tr>
<tr>
<td><input type="radio" name="reporte" id="reporte" value="terapias_diario">Reporte diario de terapias</input></td>
</tr>
</td></tr></table>
<tr><td colspan="6"><input type="submit" name="btnReporte" id="btnReporte" value="Generar reporte"></td></tr>
</table>
</fieldset>
</div>
</form>
</body>
</html>
