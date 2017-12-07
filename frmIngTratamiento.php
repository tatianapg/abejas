<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/tratamiento/Tratamiento.php");
require_once("./include/dabejas_config.php");

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo getBaseUrl(); ?>css/style.css"/>
<script src="<?php echo getBaseUrl(); ?>js/jquery.js"></script>
<script src="<?php echo getBaseUrl(); ?>js/jquery_validate.js"></script>


<script type="text/javascript">
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmIngTratamiento']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtNmTratamiento: {
		required: true,
		maxlength: 150	  		
		},
      txtMedicacionTratamiento: {
		required: true,
		maxlength: 700	  		
	  },
	  txtTerapiasTratamiento: {
		number: true,
        required: true,
        minlength: 1,
        maxlength: 2	  		
	  },
	  txtObsTratamiento: {
		maxlength: 700	  		
	  }
	},  
    messages: {
		txtNmTratamiento: { 
			required: "requerido",
			maxlength: "Solo ingrese 150 caracteres"		
		},
		txtMedicacionTratamiento: {
			required: "requerido",
			maxlength: "Solo ingrese 700 caracteres"		
		},
		txtTerapiasTratamiento: {
			number: "Ingrese un n&#250;mero correcto",
			required: "requerido",
			maxlength: "Número 1-99"
		},
		txtObsTratamiento: {
			maxlength: "Solo ingrese 700 caracteres"		
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

	//incluir una librería para ingresar
	//set configuración local
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	$tratamiento = new Tratamiento();
	$etiquetaBoton = "Ingresar";

	if(isset($_GET["cdtra"])) {
		$etiquetaBoton = "Modificar";
		//echo "existe tratamiento";
		$sql = $tratamiento->consultarTratamientoPorCd($_GET["cdtra"]);        
		
		if($con) {
			$fila = $pdo->pdoGetRow($sql);
			$tratamiento->obtenerTratamiento($fila);
		} else {
			echo "error conexión bdd!!!";
		}           
	}
	$habilitarBoton = "";
	if(isset($_GET["del"])) {
		//habilitar o deshabilitar el boton
		$etiquetaBoton = "Eliminar";
		$habilitarBoton = "";
		//también obtener los tratamiento que tiene el paciente        
		//verificar si se puede eliminar o no 
		/////////////
		$sqlNumSesiones = $tratamiento->getNumSesionesPorTratamiento();
		$resultSesiones = $pdo->pdoGetRow($sqlNumSesiones);
		$numSesiones = $resultSesiones["conteo"];				
		$sqlNumMedicaciones = $tratamiento->getNumMedicacionesPorTratamiento();
		$resultMedicaciones = $pdo->pdoGetRow($sqlNumMedicaciones);
		$numMedicaciones = $resultMedicaciones["conteo"];
				
		if(($numSesiones + $numMedicaciones) > 0) {
			$habilitarBoton="disabled";
		} 		
	}

	$cdPaciente = ( isset($_GET["cdpac"]) ? $_GET["cdpac"] : $tratamiento->getCdPaciente() );
	 
	/*------------------------------
	-- para botones cuando hay una sesion
	-------------------------------*/
	$ocultarElementoS = "hidden";
	$ocultarEspacioS = "none";
	//botones del tratamiento
	$botonOcultarElemento = "visible";
	$botonOcultarEspacio = "block";

	// zona de botones y div de medicacion
	$ocultarElementoM = "hidden";
	$ocultarEspacioM = "none";
	//$botonOcultarElementoM = "visible";
	//$botonOcultarEspacioM = "block";

	if(isset($_GET["ses"])) {
		$ocultarElementoS = "visible";
		$ocultarEspacioS = "block";	
		$botonOcultarElemento = "hidden";
		$botonOcultarEspacio = "none";	
	}

	if(isset($_GET["med"])) {
		$ocultarElementoM = "visible";
		$ocultarEspacioM = "block";	
		$botonOcultarElemento = "hidden";
		$botonOcultarEspacio = "none";	
	}
		
}
?>
<div id="ladoDerecho">
<form method="post" action="ingresarTratamiento.php" name="frmIngTratamiento" id="frmIngTratamiento">
<div>
<fieldset><legend>Detalles tratamiento</legend>
<table>
<input type="hidden" name="txtCdTratamiento" value="<?php echo($tratamiento->getCdTratamiento());?>"></input>
<input type="hidden" name="txtCdPaciente" value="<?php echo($cdPaciente);?>"></input>
<input type="hidden" name="del" value="<?php echo($_GET["del"]);?>"></input>
<input type="hidden" name="ses" value="<?php echo($_GET["ses"]);?>"></input>
<input type="hidden" name="med" value="<?php echo($_GET["med"]);?>"></input>
<tr> <!-- Diagn&#243;stico naturop&#225;tico -->
<td class="etiqueta">Descripci&#243;n*</td><td colspan="5"><input name="txtNmTratamiento" id="txtNmTratamiento" value="<?php echo($tratamiento->getNmTratamiento());?>"></input></td>
</tr>
<tr>
<td class="etiqueta">Medicaci&#243;n*</td><td colspan="5"><textarea name="txtMedicacionTratamiento" id="txtMedicacionTratamiento"><?php echo($tratamiento->getMedicacionTratamiento());?></textarea></td>
</tr>
<tr>
<td class="etiqueta">Observaciones</td><td colspan="5"><textarea name="txtObsTratamiento" id="txtObsTratamiento"><?php echo($tratamiento->getObsTratamiento());?></textarea></td>
</tr>
<tr>
<td class="etiqueta">No. Terapias*</td><td><input name="txtTerapiasTratamiento" id="txtTerapiasTratamiento" class="cajaCortaNumeros" value="<?php echo($tratamiento->getTerapiasTratamiento());?>"></td>
</tr>
</table>
<!-- botones del tratamiento
-->
<!-- inicio del div -->
<div id="divBotonesTratamiento" style="visibility: <?php echo($botonOcultarElemento);?>; display: <?php echo($botonOcultarEspacio);?>;">
<p>
<input class="submit" type="submit" <?php echo($habilitarBoton);?> value="<?php echo($etiquetaBoton); ?>"></input>
<input type="button" class="submit" value="Cancelar" onclick="window.close();"></input>
</p>
</div>
</fieldset>
</div>
<!-- display block; visibility: visible -->
<!-- inicio del div sesion -->
<div id="divSesion" style="visibility: <?php echo($ocultarElementoS);?>; display: <?php echo($ocultarEspacioS);?>;">
<fieldset><legend>Detalles de terapia</legend>
<table>
<!-- 
Presi&#243;n 
-->
<tr><td class="etiqueta">Notas*</td>
<td colspan="9"><textarea name="txtNotasSesion" id="txtNotasSesion"></textarea></td></tr>
<tr>
<td class="etiqueta">Intensidad dolor</td><td>
<select class="combo" name="cmbNivelDolor" id="cmbNivelDolor">
    <option value="0">Seleccione</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
</select>
</td>
<td class="etiqueta">Peso(kg)</td><td><input name="txtPeso" id="txtPeso" class="cajaCortaNumeros" value="0"></td>
<td class="etiqueta">Talla(m)</td><td><input name="txtTalla" id="txtTalla" class="cajaCortaNumeros" value="0"></td>
<td class="etiqueta">Presion</td><td><input name="txtPresion" id="txtPresion" class="cajaCorta" value="0"></td>
<td class="etiqueta">F.card&#237;aca(l/m)</td><td><input name="txtFrecuenciaCardiaca" id="txtFrecuenciaCardiaca" class="cajaCortaNumeros" value="0"></input></td>
</tr>
</table>
<div id="divBotonesSesion">
<p>
<!-- onclick="validarTerapia();" botones de la terapia -->
<input class="submit" type="submit" value="Ingresar" onClick="return validarTerapia();"></input>
<input type="button" class="submit" value="Cancelar" onclick="window.close();"></input>
</p>
</div>
</fieldset>
</div>
<!-- div para mostrar la medicacion
-->
<div id="divMedicacion" name="divMedicacion" style="visibility: <?php echo($ocultarElementoM);?>; display: <?php echo($ocultarEspacioM);?>;">
<fieldset>
<legend>Medicaci&#243;n</legend>
<table>
<tr><td class="etiqueta">Notas*</td><td><textarea name="txtNotasMedicacion" id="txtNotasMedicacion"></textarea></td>
</tr>
</table>
<div id="divBotonesMedicacion">
<p>
<!-- onclick="validarTerapia();" botones de la terapia -->
<input class="submit" type="submit" value="Ingresar" onClick="return validarMedicacion();"></input>
<input type="button" class="submit" value="Cancelar" onclick="window.close();"></input>
</p>
</div>
</fieldset>
</div>
<!-- fin div para mostrar la medicacion
-->

<div id="listaTratamientos">
<fieldset>
<legend>Lista de tratamientos</legend>
<?php
        $sqlTratamientos = $tratamiento->consultarTratamientosPorPaciente($_GET["cdpac"]);
        //echo $sqlTratamientos;
        $res = $pdo->pdoGetAll($sqlTratamientos);

		echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" >");
		echo("<tr><td>Descripci&#243;n</td><td>Fecha</td><td>No.Terapias</td><td>Editar</td><td>Eliminar</td><td>A&#241;adir terapia</td><td>A&#241;adir medicaci&#243;n</td><td>No.Terapias hechas</td></tr>");
		//<td>No.terapias hechas</td>
		$indice=0;
		$color = "#ccf2ff";
		foreach($res as $fila) {
			if($indice%2)
				$color = "#b3ecff";//"#4dd2ff";//"#66d9ff";							
			else 	
				$color = "#66d9ff";							
		
			echo("<tr bgcolor=\"". $color ."\">");
			echo "<td>" . $fila["nm_tratamiento"] . "</td>";
            //formatear la fecha            
            $fechaSalida = strtotime($fila["fe_tratamiento"]);
            $fechaFormateada = date("Y/m/d", $fechaSalida);            
			echo "<td>" . $fechaFormateada . "</td>";		
			echo "<td align=\"center\">" . $fila["terapias_tratamiento"] . "</td>";		
			echo "<td align=\"center\"><a href=\"frmIngTratamiento.php?cdtra=". $fila["cd_tratamiento"] . "&cdpac=" . $fila["cd_paciente"] . "\"><img src=\"images/updatei.png\"></a></td>";			
			echo "<td align=\"center\"><a href=\"frmIngTratamiento.php?del=1&cdpac=". $fila["cd_paciente"] . "&cdtra=". $fila["cd_tratamiento"] . "\"><img src=\"images/deletei.png\" alt=\"Borrar\"></a>" ."</td>";			
			echo "<td align=\"center\"><a href=\"frmIngTratamiento.php?ses=1&cdtra=". $fila["cd_tratamiento"] . "&cdpac=" . $fila["cd_paciente"] . "\"><img src=\"images/terapia.png\"></a></td>";				
//
			echo "<td align=\"center\"><a href=\"frmIngTratamiento.php?med=1&cdtra=". $fila["cd_tratamiento"] . "&cdpac=" . $fila["cd_paciente"] . "\"><img src=\"images/medicacion.png\"></a></td>";
			echo "<td align=\"center\">". $fila["num_sesiones"] ."</td>";						
			echo("</tr>");
			$indice++;
		} //fin foreach
		echo("</table>");

?>
</fieldset>
</div>
<!-- -->
</form>
</div>
</body>
</html>