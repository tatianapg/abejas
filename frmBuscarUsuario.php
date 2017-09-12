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
  $("form[name='frmBuscarUsuario']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtUsuario: "required"
	},  
    messages: {
      txtUsuario: "requerido",
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
<form id="frmBuscarUsuario" name="frmBuscarUsuario" method="post">
<div name='divBusqueda'>
<fieldset>
<legend>B&#250;squeda de usuario</legend>

<label>Nombre a buscar*:</label>
<input type="textbox" id="txtUsuario" name="txtUsuario"></input>
<input class="submit" type="button" value="Buscar Usuarios" id="enviarConsulta" onclick="cargarResultadosDivUsuarios();">
(Ingrese % para todos)
</fieldset>
</div>
</form>
<div>
<fieldset><legend>Resultados</legend>
<div id="divResultadosBusquedaUsuario"></div>
</fieldset>
</div>
</form>
</body>
</html>
