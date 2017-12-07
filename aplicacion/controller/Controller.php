<?php
function getBaseUrl() 
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF']; 
    
    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
    $pathInfo = pathinfo($currentPath); 
    
    // output: localhost
    $hostName = $_SERVER['HTTP_HOST']; 
    
    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
    
    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname']."/";
}

//esta función asume que siempre traerá los dats en este orden: 1er campo id; 2do campo texto
function construirCombo($result, $cdSeleccionado) {
	
	$cadena = "<option value=\"\">Seleccione</option>";
	$seleccionado = "";
	
	foreach($result as $opcion) {
		
		$seleccionado = "";
		if($cdSeleccionado == $opcion["codigo"]) {
			$seleccionado = "selected";
		}
		$cadena .= "<option value=\"". $opcion["codigo"] ."\" $seleccionado >" . $opcion["texto"] . "</option>";
	}
	
	return $cadena;	
}

//este combo se construye para listar todos los datos que vienen de la tabla, sin poner otras opciones 
function construirComboSoloDatos($result) {
	
	$cadena = "";
	foreach($result as $opcion) {		
		$cadena .= "<option value=\"". $opcion["codigo"] ."\">" . $opcion["texto"] . "</option>";
	}
	
	return $cadena;	
}

function reemplazarCaracteresEspeciales($cadena) {
	$cadenaLimpia = str_replace(array('<', '>', '{', '}', '[', ']', '\'', '"', '´')	, '', $cadena);
	return $cadenaLimpia;
}
?>