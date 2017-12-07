<?php
include("PdoWrapper.php");

$pdo = new PdoWrapper();

echo "cree el wrapper";

$conOk=$pdo->pdoConnect();

echo "cree la conexion";

if($conOk) {
	
	echo "pude conectarme";
    //11111111er bloque de prueba de consultas
    //ejecutar una consulta
    $consulta = "select a.cd_producto, sum(ap.cantidad_accion) cantidad_compras from aux_acciones a left outer join
acciones_producto ap on a.cd_producto = ap.cd_producto and ap.cd_inventario = 30 and ap.cd_tipo_accion = 1 and
ap.es_carga_inicial = 0 group by a.cd_producto order by a.cd_producto
";    
    //devuelve el resultado de la consulta: todas las filas
    //OK
    $res = $pdo->pdoGetAll($consulta);
    //OK
    //$res = $pdo->pdoGetRow($consulta);
    
	$values = array_values($res);
	$keys = array_keys($res);
	
    if(!$res) { 
        echo "Se produjo una excepción... salir";
        exit; 
    }
    
    echo("<br>Resultset::: <br>");
    print_r($res);
	
	echo("<br></br>");
	echo("<br></br>");
	echo("<br>Vlues::: <br>");
	print_r($values);
	
	echo("<br></br>");
	echo("<br></br>");
	echo("<br>Keys::: <br>");
	print_r($keys);

	echo("<br>000</br>");
	echo $res[0]["cd_producto"] . " - " . $res[0]["cantidad_compras"];
	echo("<br>222</br>");
	echo $res[2]["cd_producto"] . " - " .  $res[2]["cantidad_compras"];
    //contar el número de filas
    echo "número de filas es:: " . count($res);
    
    
    
    //sirve la iterar por el grupo de filas devueltas
    /*
    echo("<br>Iterando por filas del array::: <br>");
    foreach($res as $fila) {
        $cd_usuario = $fila['cd_usuario'];
        $nm_usuario = $fila['nm_usuario'];
        echo "<br>" . $cd_usuario . "--" . $nm_usuario . "<br>";      
    }
    */
    
    //sirve para obtener una fila, una sola devuelta
    /*
    if($res) {
        $cd_usuario = $res['cd_usuario'];
        $nm_usuario = $res['nm_usuario'];
        echo "<br>" . $cd_usuario . "--" . $nm_usuario . "<br>";              
    }
    */
    
           
    
    //222222222
    //probar inserción de consulta
    /*
    $consulta = "insert into usuarios(nm_usuario, login_usuario, clave_usuario, email_usuarioss) values('Ana Roja', 'aroja', md5('prueba'), 'aroja@mailinator.com')";
    $numInserts = $pdo->pdoInsertar($consulta);
    
    if($numInserts)
        echo "Registros insertados::: " . $numInserts;
    else 
        echo "errrr";
    */

    //3333333
    //bloque 3 probar que funcione el update de los registros
    /*   
    $consulta = "update usuarios set nm_usuario='Ana Roja', email_usuario='aroja@mailinator.com' where cd_usuario = 5";
    $numInserts = $pdo->pdoInsertar($consulta);
    
    if($numInserts)
        echo "<br>Registros actualizados::: " . $numInserts;
    else 
        echo "errrr";
    */
    
    //444444444444
    //bloque 4 bloque borrar
    /*
    $consulta = "delete from usuarios where cd_usuario = 5";
    $numInserts = $pdo->pdoEliminar($consulta);
    if($numInserts)
        echo "<br>Registros eliminados::: " . $numInserts;
    else 
        echo "No fue eliminado el registro";
    */
    
    
    ///55555555555555
    //bloque 5 bloque borrar
    //$tablasHijos = array("usuarios_licencias", "usuarios_permisos");
    //$pdo->pdoEliminarPadreHijo("usuarios", $tablasHijos, "cd_usuario", "2");           
    
} else {
    echo "hay un error en la conexion";
    
}
?>