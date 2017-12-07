<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmIngPaciente']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
	  cmbNivelAlergia: "required",
	  cmbNivelDolor: "required",
 	  cmbSucursal: "required",
 	  cmbSexo: "required",
	  
	  txtApellidos: {
		required: true
		
	  },
	  
	  txtNombres: {
		required: true		
	  },

	  txtEdad: {
        required: true,
		number:true	,
        minlength: 1,
        maxlength: 2
	  },
	  txtPeso: {
		number:true //,minlength: 2,maxlength: 3
	  },
	  txtTalla: {
		number:true //,minlength: 2,maxlength: 3
	  },
	  txtFrecuenciaCardiaca: {
		number:true //, minlength: 2, maxlength: 3
	  },
	  txtAntecedentesPersonales: {
		maxlength: 1500
	  },
	  txtAntecedentesFamiliares: {
		maxlength: 1500
	  },
	  txtCirugias: {
		maxlength: 700	  
	  },
	  txtAlergias: {
		maxlength: 700	  
	  },
	  txtMedicacionQuimica: {
		maxlength: 700	  
	  },	  
	  txtMotivoConsulta: {
		maxlength: 700	  
	  },
	  txtDiagnostico: {
  	    required: true,	  
		maxlength: 700	  
	  },
	  txtTelefono: {
		maxlength: 45
	  },
	  txtEmbarazos: {
		number:true, 
		maxlength: 2
	  },
	  txtPartos: {
		number:true, 
		maxlength: 2
	  },
	  txtCesareas: {
		number:true, 
		maxlength: 2
	  },
	  txtAbortos: {
		number:true, 
		maxlength: 2
	  },
	  txtCedula: {
	    number: true,
	    maxlength: 10
	  }
	  	  
	},  
    messages: {
      txtNombres: { 
		required: "requerido"
		
	  },
      txtApellidos: "requerido",
 	  cmbNivelAlergia: "requerido",
	  cmbNivelDolor: "requerido",
	  cmbSucursal: "requerido",
	  cmbSexo: "requerido",
      txtEdad: {
        required: "requerido",
		number: "Ingrese un n&#250;mero correcto",
        maxlength: "N&#250;mero 1-99"
      },
	  txtPeso: { number: "Ingrese un n&#250;mero correcto"},
	  txtTalla: { number: "Ingrese un n&#250;mero correcto"},
	  txtFrecuenciaCardiaca: { number: "Ingrese n&#250;mero correcto"},
	  txtAntecedentesPersonales: { maxlength: "Hasta 1500 caracteres"},
	  txtAntecedentesFamiliares: { maxlength: "Hasta 1500 caracteres"},
	  txtCirugias: { maxlength: "Hasta 700 caracteres"},
	  txtAlergias: { maxlength: "Hasta 700 caracteres"},
	  txtMedicacionQuimica: { maxlength: "Hasta 700 caracteres"},
	  txtMotivoConsulta: { maxlength: "Hasta 700 caracteres"},
	  txtDiagnostico: { 
		maxlength: "Hasta 700 caracteres",
	    required: "requerido"
		},
	  txtTelefono: {
	    maxlength: "Hasta 45 caracteres."
	  },
	  txtEmbarazos: {
		number: "Ingrese un n&#250;mero correcto",
		maxlength: "Hasta 2 caracteres"
	  },
	  txtPartos: {
		number: "Ingrese un n&#250;mero correcto",
		maxlength: "Hasta 2 caracteres"
	  },
	  txtCesareas: {
		number: "Ingrese un n&#250;mero correcto",
		maxlength: "Hasta 2 caracteres"
	  },
	  txtAbortos: {
		number: "Ingrese un n&#250;mero correcto",
		maxlength: "Hasta 2 caracteres"
	  },
	  txtCedula: {
		number: "Ingrese solo n&#250;meros",
		maxlength: "Hasta 10 n&#250;meros"	  
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
/*
<?php echo(isset($_POST["cdpac"]) ? $_POST["cdpac"] : 0 ); ?>
*/
//incluir una librería para ingresar
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/paciente/Paciente.php");
include("./aplicacion/model/sucursal/Sucursal.php");
include("./aplicacion/bdd/PdoWrapper.php");
require_once("./include/dabejas_config.php");

if(!$autenticacion->CheckLogin()) {
	$autenticacion->RedirectToURL("login.php");
    exit;
} else {
	$etiquetaBoton = "Ingresar";
	$paciente = new Paciente();
	$pdo = new PdoWrapper();
	$con = $pdo->pdoConnect();

	//es modificacion de paciente
	if(isset($_GET["cdpac"]) && $_GET["cdpac"] > 0) {
		$etiquetaBoton = "Modificar";

		//echo "existe paciente";
		$sql = $paciente->consultarPaciente($_GET["cdpac"]);        

		if($con) {
			$fila = $pdo->pdoGetRow($sql);
			$paciente->obtenerPaciente($fila);
		} else {
			echo "error conexión bdd!!!";
		}    
	}

	//es eliminación de paciente, verificar antes si se puede eliminar
	$habilitarBoton ="";
	if(isset($_GET["del"]) && $_GET["del"] == 1 ) {
		$etiquetaBoton = "Eliminar";	
		//aqui mismo validar si hay chance de eliminar
		//si tiene tratamientos no se elimina
		$paciente->setCdPaciente($_GET["cdpac"]);
		$sqlValidacion = $paciente->validarEliminarPaciente();	
		$resultTratamientos = $pdo->pdoGetRow($sqlValidacion);
		$numTratamientos = $resultTratamientos["conteo"];
		//deshabilitar el botón porque existen datos asociados
		if($numTratamientos > 0) {
			//echo "se deshabilita el boton por " . $numTratamientos;
			$habilitarBoton = "disabled";		
		}	
	}

	//cargar las sucursales
	$sucursal = new Sucursal();
	$sql = $sucursal->getTodasSucursales();
	$combo = "";
	if($con) {
		$result = $pdo->pdoGetAll($sql);
		$combo = construirCombo($result, $paciente->getCdSucursal());
	}
 
}
 
?>
<form method="post" action="ingresarPaciente.php" name="frmIngPaciente" id="frmIngPaciente">
<input type="hidden" name="txtCdPaciente" value="<?php echo($paciente->getCdPaciente()); ?>"></input>
<input type="hidden" name="del" value="<?php echo($_GET["del"]); ?>"></input>
<div>
<fieldset><legend>Datos personales</legend>
<table>
<tr><td colspan="4">&nbsp;</td><td><label><b>No.Historia: </b></label></td><td colspan="5"><label><b><?php echo($paciente->getCdPaciente()); ?></label></b></td></tr>
<tr>
<td class="etiqueta">Sucursal*</td><td>
<select class="combo" name="cmbSucursal" id="cmbSucursal">
<?php 
/*
$sql = $paciente->getTextosSucursales();
$result = $pdo->pdoGetAll($sql);
$combo = construirCombo($result, $paciente->getCdSucursal());
*/
echo $combo;
?>
</select>
</td>
<td class="etiqueta">C&#233;dula</td><td><input class="cajaLarga" name="txtCedula" id="txtCedula" value="<?php echo($paciente->getCedulaPaciente());?>"></input></td>
<td class="etiqueta">Sexo*</td><td><select class="combo" id="cmbSexo" name="cmbSexo"><option value="">Seleccione</option>
<option value="F" <?php if($paciente->getSexoPaciente() == "F") echo "selected"; ?> >Femenino</option>
<option value="M" <?php if($paciente->getSexoPaciente() == "M") echo "selected"; ?>>Masculino</option>
</select></td>
</tr>
<tr>
<td class="etiqueta">Nombres*</td><td><input class="cajaLarga" name="txtNombres" id="txtNombres" value="<?php echo($paciente->getNombresPaciente());?>"></input></td>
<td class="etiqueta">Apellidos*</td><td><input class="cajaLarga" name="txtApellidos" id="txtApellidos" value="<?php echo($paciente->getApellidosPaciente());?>"></td>
<td class="etiqueta">Edad*</td><td><input class="cajaCortaNumeros" name="txtEdad" id="txtEdad" value="<?php echo($paciente->getEdadPaciente());?>"></td>
</tr>
<tr>
<td class="etiqueta">Ocupaci&#243;n</td><td><input class="cajaLarga" name="txtOcupacion" value="<?php echo($paciente->getOcupacionPaciente());?>"></td>
<td class="etiqueta">Direcci&#243;n</td><td><input class="cajaLarga" name="txtDireccion" value="<?php echo($paciente->getDireccionPaciente());?>"></td>
<td class="etiqueta">Tel&#233;fono</td><td><input class="cajaLarga" name="txtTelefono" value="<?php echo($paciente->getTelefPaciente()) ;?>"></td>
</tr>
</table>
</fieldset>
</div>

<div>
<fieldset><legend>Antecedentes</legend>
<table>
<tr>
<td class="etiqueta">Personales</td><td colspan="5"><textarea name="txtAntecedentesPersonales"><?php echo($paciente->getAntecedentesPersonaPaciente());?></textarea></td>
</tr>
<tr><td class="etiqueta">&nbsp;</td><td colspan="5"><table><tr><td class="etiqueta">Embarazos</td><td><input class="cajaCortaNumeros" name="txtEmbarazos" id="txtEmbarazos" value="<?php echo($paciente->getNumEmbarazosPaciente());?>"></td><td class="etiqueta">Partos</td><td><input class="cajaCortaNumeros" name="txtPartos" id="txtPartos" value="<?php echo($paciente->getNumPartosPaciente());?>"></td><td class="etiqueta">Ces&#225;reas</td><td><input class="cajaCortaNumeros" name="txtCesareas" id="txtCesareas" value="<?php echo($paciente->getNumCesareasPaciente());?>"></td><td class="etiqueta">Abortos</td><td><input class="cajaCortaNumeros" name="txtAbortos" id="txtAbortos" value="<?php echo($paciente->getNumAbortosPaciente());?>"></td></tr></table></td></tr>
<tr>
<td class="etiqueta">Familiares</td><td colspan="5"><textarea name="txtAntecedentesFamiliares"><?php echo($paciente->getAntecedentesFamiliaPaciente());?></textarea></td>
</tr>
<tr>
<td class="etiqueta">Cirug&#237;as</td><td colspan="5"><textarea name="txtCirugias"><?php echo($paciente->getCirugiasPaciente());?></textarea></td>
</tr>
<tr>
<td class="etiqueta">Alergias</td><td colspan="5"><textarea name="txtAlergias"><?php echo($paciente->getAlergiasPaciente());?></textarea></td>
</tr>
<tr>
<td class="etiqueta">Medicaci&#243;n qu&#237;mica</td><td colspan="5"><textarea name="txtMedicacionQuimica"><?php echo($paciente->getMedicacionQuimicaPaciente());?></textarea></td>
</tr>
<tr>
<td class="etiqueta">Motivo consulta</td><td colspan="5"><textarea name="txtMotivoConsulta"><?php echo($paciente->getMotivoConsultaPaciente());?></textarea></td>
</table>
</fieldset>
</div>

<div>
<fieldset><legend>Signos vitales</legend>
<table>
<tr>
<td class="etiqueta">Peso(Kg)</td><td><input name="txtPeso" id="txtPeso" class="cajaCortaNumeros" value="<?php echo($paciente->getPeso());?>"></td>
<td class="etiqueta">Talla(m)</td><td><input name="txtTalla" id="txtTalla" class="cajaCortaNumeros" value="<?php echo($paciente->getTalla());?>"></td>
<td class="etiqueta">P.arterial(mmHg)</td><td><input name="txtPresion" id="txtPresion" class="cajaCorta" value="<?php echo($paciente->getPresion());?>"></td>
<td class="etiqueta">F.card&#237;aca(l/m)</td><td><input name="txtFrecuenciaCardiaca" id="txtFrecuenciaCardiaca" class="cajaCortaNumeros" value="<?php echo($paciente->getFrecuenciaCardiaca());?>"></input></td>
</tr>
</table>
</fieldset>
</div>
<div>
<fieldset><legend>Diagn&#243;stico naturop&#225;tico</legend>
<table>
<tr><td class="etiqueta">Detalles*</td><td colspan="7"><textarea name="txtDiagnostico" id="txtDiagnostico"><?php echo($paciente->getDiagnosticoNaturopatico());?></textarea></td></tr>
<tr>
<table>
<td class="etiqueta">Intensidad dolor*</td>
<td> 
<select class="combo" name="cmbNivelDolor" id="cmbNivelDolor">
    <option value="" <?php echo($paciente->getNivelDolor()== '0' ? "selected" : ""); ?>>Seleccione</option>
    <option value="1" <?php echo($paciente->getNivelDolor()== '1' ? "selected" : ""); ?>>1</option>
    <option value="2" <?php echo($paciente->getNivelDolor()== '2' ? "selected" : ""); ?>>2</option>
    <option value="3" <?php echo($paciente->getNivelDolor()== '3' ? "selected" : ""); ?>>3</option>
    <option value="4" <?php echo($paciente->getNivelDolor()== '4' ? "selected" : ""); ?>>4</option>
    <option value="5" <?php echo($paciente->getNivelDolor()== '5' ? "selected" : ""); ?>>5</option>
    <option value="6" <?php echo($paciente->getNivelDolor()== '6' ? "selected" : ""); ?>>6</option>
    <option value="7" <?php echo($paciente->getNivelDolor()== '7' ? "selected" : ""); ?>>7</option>
    <option value="8" <?php echo($paciente->getNivelDolor()== '8' ? "selected" : ""); ?>>8</option>
    <option value="9" <?php echo($paciente->getNivelDolor()== '9' ? "selected" : ""); ?>>9</option>
    <option value="10" <?php echo($paciente->getNivelDolor()== '10' ? "selected" : ""); ?>>10</option>
</select>
</td>
<td class="etiqueta">Prueba alergias*</td>
<td>
<select class="combo" name="cmbNivelAlergia" id="cmbNivelAlergia">
<?php 
$sql = $paciente->getTextosNivelAlergia();
echo "consulta de textos: " . $sql;
$result = $pdo->pdoGetAll($sql);
$combo = construirCombo($result, $paciente->getCdNivelAlergia());
echo $combo;
?>
</select>
</td>
</table>
</tr>
</table>
</fieldset>
</div>
<!--  -->
<?php 
if(isset($_GET["cdpac"])) {
?>
<div>
<fieldset>
<legend>Tratamientos</legend>
<a href="#" onclick="window.open('frmIngTratamiento.php?cdpac=<?php echo($_GET["cdpac"]);?>', 'targetWindow', 'toolbar=no, scrollbars=no, resizable=no, top=50, left=400, width=700, height=600'); return false;">[Ingresar tratamiento]</a>
<a href="#" onclick="window.open('frmListaTraTer.php?cdpac=<?php echo($_GET["cdpac"]);?>', 'targetWindow', 'toolbar=no, scrollbars=no, resizable=no, top=50, left=400, width=700, height=600'); return false;">[Detalle de terapias]</a>
<a href="#" onclick="window.open('frmListaTraMed.php?cdpac=<?php echo($_GET["cdpac"]);?>', 'targetWindow', 'toolbar=no, scrollbars=no, resizable=no, top=50, left=400, width=700, height=600'); return false;">[Detalle de medicaci&#243;n]</a>

</fieldset>
</div>
<?php 
}
?>
<!-- -->
<p><input class="submit" type="submit" <?php echo($habilitarBoton); ?> value="<?php echo($etiquetaBoton); ?>"><p>
</form>
</body>
</html>
