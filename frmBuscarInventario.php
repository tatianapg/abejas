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
  $("form[name='frmBuscarInventario']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtAnioBuscar: "required"
	},  
    messages: {
      txtAnioBuscar: "requerido",
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
<form id="frmBuscarInventario" name="frmBuscarInventario" method="post">
<div name='divBusqueda'>
<fieldset>
<legend>B&#250;squeda de inventario</legend>
<label>A&#241;o del inventario*</label><input name="txtAnioBuscar" id="txtAnioBuscar">
<input class="submit" type="button" value="Buscar Inventarios" id="enviarConsulta" onclick="cargarResultadosDivInventarios();">
<label>(Ingrese 1 para buscar todos)</label>
</fieldset>
</div>
</form>
<div>
<fieldset><legend>Resultados</legend>
<div id="divResultadosBusquedaInventario"></div>
</fieldset>
</div>
</form>
</body>
</html>
