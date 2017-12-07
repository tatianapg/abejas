<?php
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(isset($_POST['submitted']))
{
	/* Obtener un objeto conexión y pasarlo para que haga login*/
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();
	$sql = $pdo->cambiarBdd();
	$pdo->pdoExecute($sql);

	if($con) {
	   if($autenticacion->Login($pdo))
	   {
			$autenticacion->RedirectToURL("frmSetSucursal.php");
	   }
	} else {
		echo "Error al acceder a la base de datos.";
	}
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>Ingreso al sistema - Dra.Abejas</title>
      <link rel="STYLESHEET" type="text/css" href="css/style.css" />
      <script type='text/javascript' src='js/jquery.js'></script>

<script>
$(function() {
  $("#txtUsuario").focus();
});
</script>	  
</head>

<body>

<!-- Form Code Start -->
<div id='marcoLogin'>

<form id='login' action='<?php echo $autenticacion->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
<fieldset>
<legend>Ingreso al sistema</legend>

<input type='hidden' name='submitted' id='submitted' value='1'/>

<div class='explicacionCorta'>* Campos requeridos</div>

<div><span class='error'><?php echo $autenticacion->GetErrorMessage(); ?></span></div>
<div class='contenedor'>
    <label for='username' >Usuario*:</label><br/>
    <input type='text' name='txtUsuario' id='txtUsuario' maxlength="50" /><br/>
    <span id='login_txtUsuario_errorloc' class='error'></span>
</div>
<div class='contenedor'>
    <label for='password' >Clave*:</label><br/>
    <input type='password' name='txtPassword' id='txtPassword' maxlength="50" /><br/>
    <span id='login_txtPassword_errorloc' class='error'></span>
</div>

<div class='contenedor'>
    <input type='submit' name='Submit' value='Ingresar' />
</div>


</fieldset>
</form>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

</div>
<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>
