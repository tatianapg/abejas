<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script>
$(function() {
  // Initialize form validation on the registration form.
  $("form[name='frmBuscarPaciente']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      txtApe: "required"
	},  
    messages: {
      txtApe: "requerido",
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
<form id="frmBuscarPaciente" name="frmBuscarPaciente" method="post">
<div name='divBusqueda'>
<fieldset>
<legend>B&#250;squeda de paciente</legend>
<!--
<label>Apellidos*</label>
-->
Buscar por campo: <select id="cmbCam" name="cmbCam"><option value="apellidos_paciente">Apellido</option><option value="cedula_paciente">C&#233;dula</option></select>
<input name="txtApe" id="txtApe">
<input class="submit" type="button" value="Buscar Pacientes" id="enviarConsulta" onclick="cargarResultadosDivPacientes();"></input>
</fieldset>
</div>
</form>
<div>
<fieldset><legend>Resultados</legend>
<div id="divResultadosBusquedaPaciente"></div>
</fieldset>
</div>
</body>
</html>
