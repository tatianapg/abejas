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
  $("form[name='frmIngUsuario']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtNmUsuario: "required",
	  txtLogin: {
		  required: true,
		  minlength: 8
	  },	  
	  cmbEstado: "required",
	  txtClave: {
		  required: true,
		  minlength: 8
	  }	  
	},  
    messages: {
      txtNmUsuario: "requerido",
	  txtLogin: {
        required: "requerido",
        minlength: "Al menos 8 caracteres."
		  
	  },
	  txtClave: {
        required: "requerido",
        minlength: "Al menos 8 caracteres."		  
	  },
	  cmbEstado: "requerido"
	  
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

}
 
?>
<form method="post" action="ingresarUsuario.php" name="frmIngUsuario" id="frmIngUsuario">
<div>
<fieldset><legend>Datos de Usuario</legend>
<table>
<tr>
<td class="etiqueta">Nombre*</td><td><input name="txtNmUsuario" id="txtNmUsuario" value="<?php echo($usuario->getNmUsuario());?>"></input></td>
<td class="etiqueta">Login*</td><td><input class="cajaCorta" name="txtLogin" id="txtLogin" value="<?php echo($usuario->getLoginUsuario());?>"></input></td>
</tr>
<tr>
<td class="etiqueta">Clave*</td>
<td><input class="cajaCorta" type="password" name="txtClave" id="txtClave" value="<?php echo($usuario->getClaveUsuario());?>"></input></td>
<td class="etiqueta">Estado*</td><td>
<select id="cmbEstado" name="cmbEstado">
<option value="">Seleccione</option>
<option value="1" <?php if($usuario->getEstaActivo() == 1) echo "selected"; ?>>Activo</option>
<option value="-1" <?php if($usuario->getEstaActivo() == -1) echo "selected"; ?>>Inactivo</option>
</select></td>
</tr>
<tr>
	<td><input class="checkbox" type="checkbox" name="ver_sensible" id="ver_sensible" value="sensible" <?php if($usuario->getVerInfoSensible() == 1) echo "checked";?>></td><td class="etiqueta">Ver datos sensibles?</td><td></td><td></td>
</tr>
<tr>
<td><input class="checkbox" type="checkbox" name="es_admin" id="es_admin" value="admin"></td><td class="etiqueta">Es administrador?</td><td></td><td></td>
</tr>
<td colspan="4">
	<table>	
	<tr>
	<td class="etiqueta">Observaciones</td>
	</tr>
	<tr>
	<td colspan="4"><textarea name="txtObsUsuario" id="txtObsUsuario"><?php echo($usuario->getObsUsuario());?></textarea></td>
	</tr>
	</table>
</td>
</tr>
</table>
<p><input class="submit" type="submit" value="<?php echo($etiquetaBoton); ?>" name="btnUsuario" id="btnUsuario"><p>
</fieldset>
</div>
</form>
</body>
</html>