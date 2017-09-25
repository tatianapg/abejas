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
    $cdUsuario = (isset($_SESSION['cd_usuario']) ? $_SESSION['cd_usuario'] : 0);
    
    //hace consulta de los elementos a los que tiene acceso:
    $pdo = new PdoWrapper();
    $con = $pdo->pdoConnect();
	$sql = $pdo->cambiarBdd();
	$pdo->pdoExecute($sql);
	
    if($con) {
        //obtener la consulta de los permisos
        //$autenticacion = new Autenticacion();
        $sql = $autenticacion->obtenerPermisosUsuario($cdUsuario, $pdo->getDbSeguridad());
        //hacer la consulta
        $res = $pdo->pdoGetAll($sql);    
        $cadena = $autenticacion->formatearPermisos($res); 
    }
    else { 
        $cadena = "Error al obtener los permisos";
    }    
     
}

?>
<div id="marco">
  <div id="ladoIzquierdo"><?php echo($cadena) ;?>  
  </div>
  <div id="ladoDerecho"></div>
</div>
</body>
</html>

