<?php 
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmBuscarSucursal']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtSucursal: "required"
	},  
    messages: {
      txtSucursal: "requerido",
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
<form id="frmBuscarSucursal" name="frmBuscarSucursal" method="post">
<div name='divBusqueda'>
<fieldset>
<legend>B&#250;squeda de sucursal</legend>

<label>Sucursal a buscar*:</label>
<input type="textbox" id="txtSucursal" name="txtSucursal"></input>
<input class="submit" type="button" value="Buscar Sucursal" id="enviarConsulta" onclick="cargarResultadosDivSucursales();">
(Ingrese % para buscar todas)
</fieldset>
</div>
</form>
<div>
<fieldset><legend>Resultados</legend>
<div id="divResultadosBusquedaSucursal"></div>
</fieldset>
</div>
</form>
</body>
</html>
