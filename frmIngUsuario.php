<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/usuario/Usuario.php");
require_once("./include/dabejas_config.php");

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
  
  // Initialize form validation on the registration form.
  $("form[name='frmIngInventario']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtNmInventario: "required",
	  txtAnioFiscalInventario: {
        required: true,
		number:true
	  },
	  txtFeInicioInventario: {
		required: true
	  },
	  txtFeFinInventario: {
		required:true 
	  }
	  
	},  
    messages: {
      txtNmInventario: "requerido",
      txtAnioFiscalInventario: {
        required: "requerido",
		number: "Ingrese un n&#250;mero correcto"
      },
	  txtFeInicioInventario: 
	  { 
	    required: "requerido"
	  },
	  txtFeFinInventario: { 
	    required: "requerido"
	  }
	  
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
<?php

if(!$autenticacion->CheckLogin()) {	
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
				
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();
/////
	$etiquetaBoton = "Ingresar";
	//es eliminación de inventario, verificar antes si se puede eliminar
	$habilitarBoton ="";
	
	
	$usuario = new Usuario();

	//es modificacion de inventario


 /////
}
 
?>
<form method="post" action="ingUsuario.php" name="frmIngUsuario" id="frmIngUsuario">
<div>
<fieldset><legend>Datos de Usuario</legend>
<table>
<tr>
<td class="etiqueta">Nombre*</td><td><input name="txtNmInventario" id="txtNmInventario" value="<?php echo($usuario->getNmUsuario());?>"></input></td>
<td class="etiqueta">Login*</td><td><input class="cajaCorta" name="txtAnioFiscalInventario" id="txtAnioFiscalInventario" value="<?php echo($inventario->getLoginUsuario());?>"></td><td></td></input>
</tr>
<tr>
<td class="etiqueta">Clave*</td><td><input class="cajaCorta" name="txtAnioFiscalInventario" id="txtAnioFiscalInventario" value="<?php echo($inventario->getAnioFiscalInventario());?>"></td><td></td></input>
<td class="etiqueta">Estado*</td><td><input class="cajaCorta" name="txtAnioFiscalInventario" id="txtAnioFiscalInventario" value="<?php echo($inventario->getAnioFiscalInventario());?>"></td><td></td></input>
</tr>
</tr>
<tr>
<td class="etiqueta">Estado</td><td colspan="4"><select>
<option value="">Seleccione</option>
<option value="1">Activo</option>
<option value="-1">Inactivo</option>
</select></td>
</tr>
<tr>
<td colspan="5">
<table>
<tr>
<td class="etiqueta">Observaciones</td>
</tr>
<tr>
<td colspan="4"><textarea name="txtObsInventario" id="txtObsInventario"><?php echo($inventario->getObsInventario());?></textarea></td>
</tr>
</table>
</td>
</tr>
</table>
<?php 
//si ya existe un inventario activo, no se permite el ingreso de otro hasta inactivar el anterior.
?>
<div id="mensajeActivos" id="mensajeActivos"><b><?php echo($mensajeActivos);?></b></div>
<p><input class="submit" type="submit" <?php echo($habilitarBoton); ?> value="<?php echo($etiquetaBoton); ?>" name="btnInventario" id="btnInventario"><p>
</fieldset>
</div>
</form>
</body>
</html>