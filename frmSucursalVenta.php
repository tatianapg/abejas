<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/sucursal/Sucursal.php");

//abrir una conexion con la bdd
$pdo = new PdoWrapper();
$con = $pdo->pdoConnect("localhost", "tatianag", "Cpsr19770428", "bdd_abejas");

$sucursal = new Sucursal();
$sql = $sucursal->getTodasSucursales();
if($con) {
	$result = $pdo->pdoGetAll($sql);
	$combo = construirCombo($result, $sucursal->getCdSucursal());
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmSucursalVenta']").validate({
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
<form id="frmSucursalVenta" name="frmSucursalVenta" method="post" action="sucursalVenta.php">
<div name='divBusqueda'>
<fieldset>
<legend>Sucursal para registro de ventas</legend>
Seleccione la sucursal <select id="cmbSucursal" name="cmbSucursal">
<?php echo($combo);?>
</select>
<input class="submit" type="submit" value="Fijar sucursal" id="fijarSucursal" ></input>
</fieldset>
</div>
</form>
</body>
</html>
