<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/sucursal/Sucursal.php");
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
<script src="<?php echo getBaseUrl(); ?>js/additional-methods.min.js"></script>


<script>
$(function() {
  
  // Initialize form validation on the registration form.
  $("form[name='frmIngSucursal']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtNmSucursal: {
		  required: true,
		  maxlength: 20,
		  minlength: 5
	  }
	},  
    messages: {
      txtNmSucursal: {
		  required: "requerido",
		  maxlength: "Hasta 20 caracteres.",
		  minlength: "Al menos 6 caracteres."
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
	if($con) {
		$etiquetaBoton = "Ingresar";
		$sucursal = new Sucursal();
		
		//es eliminación de sucursal, verificar antes si se puede eliminar
		$habilitarBoton ="";		
		$codigoSucursal = 0;
		
		if(isset($_GET["cdsuc"]) && $_GET["cdsuc"] > 0) {
			$codigoSucursal = $_GET["cdsuc"];
			$etiquetaBoton = "Modificar";
			
			$sucursal->setCdSucursal($codigoSucursal);
			$sql = $sucursal->consultarSucursal();
			$fila = $pdo->pdoGetRow($sql);
			$sucursal->obtenerSucursal($fila);			
		} 	
	} else {
		echo "error conexión bdd!!!";
	}
}
 
?>
<form method="post" action="ingresarSucursal.php" name="frmIngSucursal" id="frmIngSucursal">
<input type="hidden" name="txtCdSucursal" id="txtCdSucursal" value="<?php echo($codigoSucursal);?>"></input>
<div>
<fieldset><legend>Datos de Sucursal</legend>
<table>
<tr>
<td class="etiqueta">Nombre*</td><td><input maxlength="20" name="txtNmSucursal" id="txtNmSucursal" value="<?php echo($sucursal->getNmSucursal());?>"></input></td>
</tr>
</table>
<p><input class="submit" type="submit" value="<?php echo($etiquetaBoton); ?>" name="btnSucursal" id="btnSucursal"><p>
</fieldset>
</div>
</form>
</body>
</html>