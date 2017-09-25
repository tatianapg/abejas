<?php
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

//antes validar que la sesión es válida, es decir que sí se logueó.
if(!$autenticacion->CheckLogin())
{
    $autenticacion->RedirectToURL("login.php");
    exit;
} else {
    //con el código del usuario se pasa a obtener los menúes
    //echo "el login en sesion es: " . $_SESSION["login_usuario"];
    //echo "el usuario en sesion es: " . $_SESSION["cd_usuario"];
    $cdUsuario = (isset($_SESSION['cd_usuario']) ? $_SESSION['cd_usuario'] : 0);
    
    //hace consulta de los elementos a los que tiene acceso:
    $pdo = new PdoWrapper();
    $con = $pdo->pdoConnect();
	$sql = $pdo->cambiarBdd();
	$pdo->pdoExecute($sql);
	
    if($con) {
        //obtener la consulta de los permisos
        //$autenticacion = new Autenticacion();
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
	$texto = "Producto eliminado: ";
} else
	$texto = "Producto ingresado: ";

?>
<div id="marco">
  <div id="ladoIzquierdo"><?php echo($cadena) ;?>  
  </div>
  <div id="ladoDerecho"><?php echo($texto);?><a href="" onClick="return loadQueryResults('frmIngProducto.php?cdpro=<?php echo($_GET["cdpro"])?>');"><?php echo($_GET["nmp"] . " - " . $_GET["cdpro"])?></a></div>
</div>
</body>
</html>