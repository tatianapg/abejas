<?php
include("./aplicacion/bdd/PdoWrapper.php");
include("./aplicacion/controller/Controller.php");
include("./aplicacion/model/inventario/Inventario.php");
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

<script>
$(function() {

  $( "#txtFeInicioInventario" ).datepicker({
	  dateFormat: "yy-mm-dd",
	  minDate: 0,
	  maxDate: "+30D",
	  numberOfMonths: 1,
	  onSelect: function(selected) {
		  $("#txtFeInicioInventario").datepicker("option", "minDate", selected)
	  }
  });
  
  $( "#txtFeFinInventario" ).datepicker({
	    dateFormat: "yy-mm-dd",
		minDate: 30,
		maxDate: "+180D",
		numberOfMonths: 1,
		onSelect: function(selected) {
			$("#txtFeFinInventario").datepicker("option", "maxDate", selected)
		}
  });		
  
  // Initialize form validation on the registration form.
  $("form[name='frmIngInventario']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtNmInventario: "required",
	  txtAnioFiscalInventario: {
        required: true,
		number:true
	  },
	  txtFeInicioInventario: {
		required: true
	  },
	  txtFeFinInventario: {
		required:true 
	  }
	  
	},  
    messages: {
      txtNmInventario: "requerido",
      txtAnioFiscalInventario: {
        required: "requerido",
		number: "Ingrese un n&#250;mero correcto"
      },
	  txtFeInicioInventario: 
	  { 
	    required: "requerido"
	  },
	  txtFeFinInventario: { 
	    required: "requerido"
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
	$etiquetaBoton = "Ingresar";
	//es eliminación de inventario, verificar antes si se puede eliminar
	$habilitarBoton ="";
		
	/* Validar si existe un inventario activo POR SUCURSAL para no dejar ingresar otro*/
	$numInventariosActivos = 0;
	$cdInventarioActivo = 0;
	$banderaErrorInventarios = 0;
	$mensajeActivos = "";
	$mensajeCaduco = "";
	
	$inventario = new Inventario();
	$inventario->setCdSucursal($_SESSION["suc_venta"]);
	$sqlActivoSucursal = $inventario->validarExisteUnInventarioActivoPorSucursal();
	//echo $sqlActivoSucursal;
	$filaActivo = $pdo->pdoGetRow($sqlActivoSucursal);
	if($filaActivo) {
		$numInventariosActivos = $filaActivo["conteo"];
		$cdInventarioActivo = $filaActivo["cd_inventario"];
		//si  hay más de dos inventarios activos hay UN ERROR!!!!		
		if($numInventariosActivos > 1) {
			$banderaErrorInventarios = 1;
			$habilitarBoton = "disabled";
			$mensajeActivos .= "Existen dos inventarios activos, existe un error.<br>";
		} else if($numInventariosActivos == 1){
			//ver la fecha de fin del inventario			
			$fechaActual = time();
			$fechaFinInventarioActivo = strtotime($filaActivo["fe_fin_inventario"]);
			//echo "<br>fechas comparar: " . $fechaActual . " y " . $fechaFinInventarioActivo;
			if($fechaFinInventarioActivo < $fechaActual)
				$mensajeCaduco = "<div style=\"color:#FF0000\">El inventario activo tiene fecha de fin menor que la fecha actual.</div>";
			
		}
	}
	//echo "existen " . $numInventariosActivos . " inventarios activos";
	$mensajeActivos .= "IMPORTANTE: Existe " . $numInventariosActivos . " inventario activo. " . $mensajeCaduco;
		$mensajeActivos .= "<br>Al ingresar uno nuevo inventario, el anterior queda inactivo y todos los movimientos se asocian al nuevo inventario.";

	$referencia="";	
	//es modificacion de inventario
	if(isset($_GET["cdinv"]) && $_GET["cdinv"] > 0) {
		$etiquetaBoton = "Modificar";

		//echo "existe inventario";
		$inventario->setCdInventario($_GET["cdinv"]);
		$sql = $inventario->consultarInventario();        
		if($con) {
			$fila = $pdo->pdoGetRow($sql);
			$inventario->obtenerInventario($fila);
			$referencia = "INV-" . str_pad($inventario->getCdSucursal(), '3', '0', STR_PAD_LEFT) . "-" . str_pad($inventario->getCdInventario(), '3', '0', STR_PAD_LEFT);
		} else {
			echo "error conexión bdd!!!";
		}    
	}


	if(isset($_GET["del"]) && $_GET["del"] == 1 ) {
		$etiquetaBoton = "Eliminar";	
		//aqui mismo validar si hay chance de eliminar
		//si tiene acciones_producto no se elimina
		$inventario->setCdInventario($_GET["cdinv"]);
		$sqlValidacion = $inventario->validarEliminarInventario();	
		$resultAcciones = $pdo->pdoGetRow($sqlValidacion);
		$numAcciones = $resultAcciones["conteo"];
		//deshabilitar el botón porque existen acciones asociadas al inventario
		if($numAcciones > 0) {
			//echo "se deshabilita el boton por " . $numAcciones;
			$habilitarBoton = "disabled";		
			$mensajeActivos .= "El inventario tiene movimientos asociados, no se puede eliminar.";
		} else  {
			//se puede eliminar sin problema el inventario		
			$mensajeActivos .= "";
		}
	}
 /////
}
 
?>
<form method="post" action="ingresarInventario.php" name="frmIngInventario" id="frmIngInventario">
<input type="hidden" name="txtCdInventario" id="txtCdInventario"  value="<?php echo($inventario->getCdInventario()); ?>"></input>
<input type="hidden" name="del" id="del" value="<?php echo($_GET["del"]); ?>"></input>

<div>
<fieldset><legend>Datos de Inventario</legend>
<table>
<tr>
<td class="etiqueta" colspan="1">Referencia: </td><td><b><?php echo($referencia);?></b></td>
<td class="etiqueta">Sucursal</td><td><b><?php echo($_SESSION["suc_nombre"]);?></b></td>
</tr>
<td class="etiqueta">Nombre*</td><td><input name="txtNmInventario" id="txtNmInventario" value="<?php echo($inventario->getNmInventario());?>"></input></td>
<td class="etiqueta">A&#241;o Fiscal*</td><td><input class="cajaCorta" name="txtAnioFiscalInventario" id="txtAnioFiscalInventario" value="<?php echo($inventario->getAnioFiscalInventario());?>"></td><td></td></input>
</tr>
<tr><td colspan="4">Siempre revise la <b>vigencia</b> del inventario activo (Fecha de inicio menor que fin*).</td></tr>
<tr>
<td class="etiqueta">Fecha inicio*</td><td><input type="text" class="cajaCorta" name="txtFeInicioInventario" id="txtFeInicioInventario" value="<?php echo($inventario->getFeInicioInventario());?>"></td>
<td class="etiqueta">Fecha fin*</td><td><input type="text"	 class="cajaCorta" name="txtFeFinInventario" id="txtFeFinInventario" value="<?php echo($inventario->getFeFinInventario());?>"></td><td><div><?php echo($mensajeCaduco);?></td></div>
</tr>
<?php
$etiquetaEstadoInventario = "";
if($inventario->getCdEstadoSistema() == -1) 
	$etiquetaEstadoInventario = "Inactivo";
else if ($inventario->getCdEstadoSistema() == 1)
	$etiquetaEstadoInventario = "Activo";
?>
<tr>
<td class="etiqueta">Estado</td><td colspan="4"><label><?php echo ($etiquetaEstadoInventario);?></label></td>
</tr>
<tr>
<td colspan="5">
<table>
<tr>
<td class="etiqueta">Observaciones</td>
</tr>
<tr>
<td colspan="4"><textarea name="txtObsInventario" id="txtObsInventario"><?php echo($inventario->getObsInventario());?></textarea></td>
</tr>
</table>
</td>
</tr>
</table>
<?php 
//si ya existe un inventario activo, no se permite el ingreso de otro hasta inactivar el anterior.
?>
<div id="mensajeActivos" id="mensajeActivos"><b><?php echo($mensajeActivos);?></b></div>
<p><input class="submit" type="submit" <?php echo($habilitarBoton); ?> value="<?php echo($etiquetaBoton); ?>" name="btnInventario" id="btnInventario"><p>
</fieldset>
</div>
</form>
</body>
</html>