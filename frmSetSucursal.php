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
	if($con) {
		$result = $pdo->pdoGetAll($sql);
		$combo = construirCombo($result, $sucursal->getCdSucursal());
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/style.css" />
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/jquery_validate.js"></script>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmSetSucursal']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      cmbSucursal: "required"
	},  
    messages: {
      cmbSucursal: "requerido",
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
<!-- colocar el logo -->
	<div id="header">
		<div class="logo">
		<img src="<?php echo getBaseUrl(); ?>images/logo.png" alt="Doctoras Abejas">
		</div>
	</div>
<!-- fin colocar el logo -->
<form id="frmSetSucursal" name="frmSetSucursal" method="post" action="setSucursal.php">
<div align="center" id='ladoDerecho'>
<fieldset>
<legend></legend>
Seleccione la sucursal para trabajar*<select id="cmbSucursal" name="cmbSucursal">
<?php echo($combo);?>
</select>
<input class="button" type="submit" value="Fijar sucursal" id="fijarSucursal" ></input>
<a href="logout.php">[Salir del sistema]</a>
</fieldset>
</div>
</form>
</body>
</html>
