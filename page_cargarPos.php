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
        $sql = $autenticacion->obtenerPermisosUsuario($cdUsuario);
        //hacer la consulta
        $res = $pdo->pdoGetAll($sql);    
        $cadena = $autenticacion->formatearPermisos($res); 
    }
    else { 
        $cadena = "Error al obtener los permisos";
    }         	 	 	 
}
//en todo caso hacer un unset de las variables de sesion
unset($_SESSION["lista_productos"]);
unset($_SESSION["descuento"]);

if(isset($_GET["suc"]) && $_GET["suc"] != 0) {
	$texto = "Sucursal fijada.";
} else
	$texto = "No existe sucursal para ventas.";
?>
<div id="marco">
  <div id="ladoIzquierdo"><?php echo($cadena) ;?>  
  </div>
  <div id="ladoDerecho"><?php echo($texto);?><a href="" onClick="return loadQueryResults('frmVentaProducto.php?cdsuc=<?php echo($_GET["suc"])?>');">Ingresar ventas para: <?php echo($_GET["sun"]) ?></a></div>
</div>
</body>
</html>