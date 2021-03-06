<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./include/autenticacion.php");
require_once("./include/dabejas_config.php");

//antes validar que la sesi�n es v�lida, es decir que s� se logue�.
if(!$autenticacion->CheckLogin())
{
    $autenticacion->RedirectToURL("login.php");
    exit;
} else {
    //con el c�digo del usuario se pasa a obtener los men�es
    //echo "el login en sesion es: " . $_SESSION["login_usuario"];
    //echo "el usuario en sesion es: " . $_SESSION["cd_usuario"];
    $cdUsuario = (isset($_SESSION['cd_usuario']) ? $_SESSION['cd_usuario'] : 0);
    
    //hace consulta de los elementos a los que tiene acceso:
    $pdo = new PdoWrapper();
    $con = $pdo->pdoConnect("localhost", "tatianag", "Cpsr19770428", "bdd_seguridades");
                         
    if($con) {
        //obtener la consulta de los permisos
        $autenticacion = new Autenticacion();
        $sql = $autenticacion->obtenerPermisosUsuario($cdUsuario);
        //hacer la consulta
        $res = $pdo->pdoGetAll($sql);    
        $cadena = $autenticacion->formatearPermisos($res); 
    }
    else { 
        $cadena = "Error al obtener los permisos";
    }         	 	 	 
}

if(isset($_GET["del"]) && $_GET["del"] == 1) {
	$texto = "Paciente eliminado: ";
} else
	$texto = "Paciente ingresado: ";

?>
<div id="marco">
  <div id="ladoIzquierdo"><?php echo($cadena) ;?>  
  </div>
  <div id="ladoDerecho"><?php echo($texto);?><a href="" onClick="return loadQueryResults('frmIngPaciente.php?cdpac=<?php echo($_GET["cdpac"])?>');"><?php echo($_GET["ape"] . " - " . $_GET["cdpac"])?></a></div>
</div>
</body>
</html>