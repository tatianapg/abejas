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
  $("form[name='frmBuscarProducto']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtPro: "required"
	},  
    messages: {
      txtPro: "requerido",
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
<form id="frmBuscarProducto" name="frmBuscarProducto" method="post">
<div name='divBusqueda'>
<fieldset>
<legend>B&#250;squeda de producto</legend>

<!--
<label>Texto a buscar*</label>
-->
<label>Producto a buscar por: </label>

<select name="cmbCriterio" id="cmbCriterio">
<option value="nm_producto">Nombre</option>
<option value="sku_producto">Código</option>
</select>
<input type="textbox" id="txtPro" name="txtPro"></input>

<!--
<input name="txtPro" id="txtPro">
-->
<!--
action="buscarProducto.php"
-->
<input class="submit" type="button" value="Buscar Productos" id="enviarConsulta" onclick="cargarResultadosDivProductos();"><br><br><a href="#" onclick="window.open('generarListadoProductos.php')">[Listado de todos los productos]</a>

<!--
<input class="submit" type="submit" value="Buscar Productos" id="enviarConsulta"><br><br><a href="#" onclick="window.open('generarListadoProductos.php')">[Listado de todos los productos]</a>
-->
</fieldset>
</div>
</form>
<div>
<fieldset><legend>Resultados</legend>
<div id="divResultadosBusquedaProducto"></div>
</fieldset>
</div>
</form>
</body>
</html>
