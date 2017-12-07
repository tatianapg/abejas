<?php
include("./aplicacion/controller/Controller.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/style.css"/>
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/demo.js"></script>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmConsultarVenta']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtCdRecibo: "required"
	},  
    messages: {
      txtCdRecibo: "requerido",
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
<div id="ladoDerecho">
<form id="frmConsultarVenta" name="frmConsultarVenta" method="post">
<div id='divBusqueda'>
<fieldset>
<legend>Consulta de venta</legend>
<label>No. de recibo a buscar*</label>
<input type="textbox" id="txtCdRecibo" name="txtCdRecibo"></input>
</select>
<input class="submit" type="button" value="Buscar Recibo" id="enviarConsulta" onclick="cargarResultadosDivRecibo();">
</fieldset>
</div>
<div id="resultados" name="resultados"> 
<fieldset><legend>Resultados</legend>
<div id="divResultadosRecibo"></div>
</fieldset>
</div>
</form>
</div>
</body>
</html>
