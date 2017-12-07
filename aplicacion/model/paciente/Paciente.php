<?php
class Paciente {
    private $cd_paciente;
    private $cd_sucursal;
    private $nombres_paciente;
    private $apellidos_paciente;
    private $edad_paciente;
    private $ocupacion_paciente;
    private $telef_paciente;
    private $direccion_paciente;
    private $fe_ingreso_paciente;
    private $antecedentes_persona_paciente;
    private $antecedentes_familia_paciente;
    private $cirugias_paciente;
    private $alergias_paciente;
    private $medicacion_quimica_paciente;
    private $motivo_consulta_paciente;
    private $peso;
    private $talla;
    private $presion;
    private $diagnostico_naturopatico;
    private $cd_nivel_alergia;
    private $nivel_dolor;
    private $frecuencia_cardiaca;
	private $cedula_paciente;
	private $num_embarazos_paciente;
	private $num_partos_paciente;
	private $num_cesareas_paciente;
	private $num_abortos_paciente;
	private $sexo_paciente;
	
	//no es campo de la tabla
	private $campo_buscar;
    
    //constructor de la clase
    function __construct() {
        
    }
    
    
    function setPaciente(
    $cd_paciente, 
    $cd_sucursal, $nombres_paciente, $apellidos_paciente, $edad_paciente,
    $ocupacion_paciente, $telef_paciente, $direccion_paciente, $celular_paciente, $fe_ingreso_paciente, 
    $antecedentes_persona_paciente, $antecedentes_familia_paciente, $cirugias_paciente, $alergias_paciente, $medicacion_quimica_paciente,
    $motivo_consulta_paciente, $peso, $talla, $presion, $diagnostico_naturopatico, $cd_nivel_alergia, $frecuencia_cardiaca, $nivel_dolor, $cedula_paciente, $num_embarazos_paciente, $num_partos_paciente, $num_cesareas_paciente, $num_abortos_paciente, $sexo_paciente)
    {
        $this->cd_paciente = $cd_paciente;
        $this->cd_sucursal = $cd_sucursal;       
        $this->nombres_paciente = $nombres_paciente;
        $this->apellidos_paciente = $apellidos_paciente;
        $this->edad_paciente = $edad_paciente;
        $this->ocupacion_paciente = $ocupacion_paciente;
        $this->telef_paciente = $telef_paciente;
        $this->direccion_paciente = $direccion_paciente;
        $this->celular_paciente = $celular_paciente;
        $this->fe_ingreso_paciente = $fe_ingreso_paciente;
        $this->antecedentes_familia_paciente = $antecedentes_familia_paciente;
        $this->antecedentes_persona_paciente = $antecedentes_persona_paciente;
        $this->cirugias_paciente = $cirugias_paciente;
        $this->alergias_paciente = $alergias_paciente;
        $this->medicacion_quimica_paciente = $medicacion_quimica_paciente;
        $this->motivo_consulta_paciente = $motivo_consulta_paciente;
        $this->peso = $peso;
        $this->talla = $talla;
        $this->presion = $presion;
        $this->diagnostico_naturopatico = $diagnostico_naturopatico;
        $this->cd_nivel_alergia = $cd_nivel_alergia;
        $this->frecuencia_cardiaca = $frecuencia_cardiaca;
        $this->nivel_dolor = $nivel_dolor;		
		$this->cedula_paciente = $cedula_paciente;
		$this->num_embarazos_paciente = $num_embarazos_paciente;
		$this->num_partos_paciente = $num_partos_paciente;
		$this->num_cesareas_paciente = $num_cesareas_paciente;
		$this->num_abortos_paciente = $num_abortos_paciente;
		$this->sexo_paciente = $sexo_paciente;
    }
	
	function setDefaultNumeros() {
		
		if(!$this->peso) $this->peso = 0;
		if(!$this->talla) $this->talla = 0;
		if(!$this->cd_nivel_alergia) $this->cd_nivel_alergia = 0;
		if(!$this->frecuencia_cardiaca) $this->frecuencia_cardiaca = 0;			
		if(!$this->nivel_dolor) $this->nivel_dolor = 0;			
		if(!$this->num_embarazos_paciente) $this->num_embarazos_paciente = 0;
		if(!$this->num_partos_paciente) $this->num_partos_paciente = 0;
		if(!$this->num_cesareas_paciente) $this->num_cesareas_paciente = 0;
		if(!$this->num_abortos_paciente) $this->num_abortos_paciente = 0;
	}
	
    
    function crearPaciente() {
		
		$this->setDefaultNumeros();
        
        $cons = "Insert into Pacientes(cd_sucursal, nombres_paciente, apellidos_paciente, edad_paciente,
        ocupacion_paciente, telef_paciente, direccion_paciente, celular_paciente,
        fe_ingreso_paciente, antecedentes_persona_paciente, antecedentes_familia_paciente, cirugias_paciente,
        alergias_paciente, medicacion_quimica_paciente, motivo_consulta_paciente, peso, talla, 
        presion, diagnostico_naturopatico, cd_nivel_alergia, frecuencia_cardiaca, nivel_dolor, cedula_paciente, num_embarazos_paciente, num_partos_paciente, num_cesareas_paciente, num_abortos_paciente, sexo_paciente) values ( " .
        //$this->cd_paciente . ", " .
        $this->cd_sucursal . ", " .
        "'" . addslashes($this->nombres_paciente) . "', " .
        "'" . addslashes($this->apellidos_paciente) . "', " .
        $this->edad_paciente . ", " .
        "'" . addslashes($this->ocupacion_paciente) . "', " .
        "'" . $this->telef_paciente .  "', " .
        "'" . addslashes($this->direccion_paciente) . "', " .
        "'" . $this->celular_paciente . "', " . 
        "'" . $this->fe_ingreso_paciente ."', " .
        "'" . addslashes($this->antecedentes_persona_paciente) . "', " .
        "'" . addslashes($this->antecedentes_familia_paciente) . "', " .
        "'" . addslashes($this->cirugias_paciente) . "', " .
        "'" . addslashes($this->alergias_paciente) . "', " .
        "'" . addslashes($this->medicacion_quimica_paciente) . "', " .
        "'" . addslashes($this->motivo_consulta_paciente) . "', " .
        $this->peso . ", " .
        $this->talla . ", " .
        "'" . addslashes($this->presion) . "', " . 
        "'" . addslashes($this->diagnostico_naturopatico) . "', " .
        $this->cd_nivel_alergia . ", " .
        $this->frecuencia_cardiaca . ", " .
        $this->nivel_dolor . ", " . 
		"'" . $this->cedula_paciente . "', " .
		$this->num_embarazos_paciente . ", " .
		$this->num_partos_paciente . ", " .
		$this->num_cesareas_paciente . ", " .
		$this->num_abortos_paciente . ", " .
        "'" . $this->sexo_paciente . "') ";
		
        return $cons;        
    }
    
    function modificarPaciente() {
		
		$this->setDefaultNumeros();
		
        $cons = " update Pacientes set " .
        "nombres_paciente = '" . addslashes($this->nombres_paciente) . "', " .
        "apellidos_paciente = '" . addslashes($this->apellidos_paciente) . "', " .
        "edad_paciente = " . $this->edad_paciente . ", " .
		"cd_sucursal = " . $this->cd_sucursal . ", " .	
        "ocupacion_paciente = '" . addslashes($this->ocupacion_paciente) . "', " .
        "telef_paciente = " . "'" .  $this->telef_paciente . "', " .
        "direccion_paciente = '" . addslashes($this->direccion_paciente) . "', " .
        "celular_paciente = '" . $this->celular_paciente . "', " .
        //"fe_ingreso_paciente = " . $this->fe_ingreso_paciente ."', " .
        "antecedentes_persona_paciente = '" . addslashes($this->antecedentes_persona_paciente) . "', " .
        "antecedentes_familia_paciente = '" . addslashes($this->antecedentes_familia_paciente) . "', " .
        "cirugias_paciente = '" . addslashes($this->cirugias_paciente) . "', " .
        "alergias_paciente = '" . addslashes($this->alergias_paciente) . "', " .
        "medicacion_quimica_paciente = '" . addslashes($this->medicacion_quimica_paciente) . "', " .
        "motivo_consulta_paciente = '" . addslashes($this->motivo_consulta_paciente) . "', " .
        "peso = " . $this->peso . ", " . 
        "talla = " . $this->talla . ", " .
        "presion = '" . addslashes($this->presion) . "', " . 
        "diagnostico_naturopatico = '" . addslashes($this->diagnostico_naturopatico) . "', " .
        "cd_nivel_alergia = " . $this->cd_nivel_alergia . ", " .
        "frecuencia_cardiaca = " . $this->frecuencia_cardiaca . ", " .
        "nivel_dolor = " . $this->nivel_dolor . ", " .
		"cedula_paciente = '" . $this->cedula_paciente . "', " .
		"num_embarazos_paciente = " . $this->num_embarazos_paciente . ", " .
		"num_partos_paciente = " . $this->num_partos_paciente . ", " .
		"num_cesareas_paciente = " . $this->num_cesareas_paciente . ", " .
		"num_abortos_paciente = " . $this->num_abortos_paciente . ", " .
		"sexo_paciente = '" . $this->sexo_paciente . "' " . 
        "where cd_paciente = " .$this->cd_paciente;
        
        return $cons;
    }
    
    function consultarPaciente($cdPaciente) {
        $cons = "select * from Pacientes where cd_paciente = " . $cdPaciente;
        //echo $cons;
        return $cons;
    }
    
    function obtenerPaciente($fila) {
        //var_dump($fila);
        //echo "===========Entrando a get paciente===============";
        $this->cd_paciente = $fila["CD_PACIENTE"];
        $this->cd_sucursal = $fila["CD_SUCURSAL"];
        $this->nombres_paciente = $fila["NOMBRES_PACIENTE"];
        $this->apellidos_paciente = $fila["APELLIDOS_PACIENTE"];
        $this->edad_paciente = $fila["EDAD_PACIENTE"];
        $this->ocupacion_paciente = $fila["OCUPACION_PACIENTE"];
        $this->telef_paciente = $fila["TELEF_PACIENTE"];
        $this->direccion_paciente = $fila["DIRECCION_PACIENTE"];
        $this->celular_paciente = $fila["CELULAR_PACIENTE"]; 
        $this->fe_ingreso_paciente = $fila["FE_INGRESO_PACIENTE"];
        $this->antecedentes_persona_paciente = $fila["ANTECEDENTES_PERSONA_PACIENTE"];
        $this->antecedentes_familia_paciente = $fila["ANTECEDENTES_FAMILIA_PACIENTE"];
        $this->cirugias_paciente = $fila["CIRUGIAS_PACIENTE"];
        $this->alergias_paciente = $fila["ALERGIAS_PACIENTE"];
        $this->medicacion_quimica_paciente = $fila["MEDICACION_QUIMICA_PACIENTE"];
        $this->motivo_consulta_paciente = $fila["MOTIVO_CONSULTA_PACIENTE"];
        $this->peso = $fila["PESO"];
        $this->talla = $fila["TALLA"];
        $this->presion = $fila["PRESION"];
        $this->diagnostico_naturopatico = $fila["DIAGNOSTICO_NATUROPATICO"];
        $this->cd_nivel_alergia = $fila["CD_NIVEL_ALERGIA"];
        $this->frecuencia_cardiaca = $fila["FRECUENCIA_CARDIACA"];
        $this->nivel_dolor = $fila["NIVEL_DOLOR"];
		$this->cedula_paciente = $fila["CEDULA_PACIENTE"];
		$this->num_embarazos_paciente = $fila["NUM_EMBARAZOS_PACIENTE"];
		$this->num_partos_paciente = $fila["NUM_PARTOS_PACIENTE"];
		$this->num_cesareas_paciente = $fila["NUM_CESAREAS_PACIENTE"];
		$this->num_abortos_paciente = $fila["NUM_ABORTOS_PACIENTE"];
		$this->sexo_paciente = $fila["SEXO_PACIENTE"];
        $this->cd_paciente = $fila["CD_PACIENTE"];		        
    }
    
    function setCdPaciente($cdPaciente) {
        $this->cd_paciente = $cdPaciente;
    }

    function getCdPaciente() {
        return $this->cd_paciente;
    }    
    
    
    function getCdSucursal() {
        return $this->cd_sucursal;
    }
	
	function setCdSucursal($cdSucursal) {
		$this->cd_sucursal = $cdSucursal;
	}
    
    function getNombresPaciente() {
        return $this->nombres_paciente;
    }
        
    function getApellidosPaciente() {
        return $this->apellidos_paciente;    
    }
    
    function setApellidosPaciente($apellidosPaciente) {
        $this->apellidos_paciente = $apellidosPaciente;    
    }    
    
    function getEdadPaciente() {
        return $this->edad_paciente;    
    }
    
    function getOcupacionPaciente() {
        return $this->ocupacion_paciente;    
    }
    
    function getTelefPaciente() {
        return $this->telef_paciente;    
    }
    
    function getDireccionPaciente() {
        return $this->direccion_paciente;    
    }
    
    function getFeIngresoPaciente() {
        return $this->fe_ingreso_paciente;    
    } 
    
    function getAntecedentesPersonaPaciente() {
        return $this->antecedentes_persona_paciente;    
    }
    
    function getAntecedentesFamiliaPaciente() {
        return $this->antecedentes_familia_paciente;    
    }
    
    function getCirugiasPaciente() {
        return $this->cirugias_paciente;
    }
    
    function getAlergiasPaciente() {
        return $this->alergias_paciente;    
    }
    
    function getMedicacionQuimicaPaciente() {
        return $this->medicacion_quimica_paciente;    
    } 
    
    function getMotivoConsultaPaciente() {
        return $this->motivo_consulta_paciente;
    }
    
    function getPeso() {
        return $this->peso;
    }
    
    function getTalla() {
        return $this->talla;
    }
    
    function getPresion() {
        return $this->presion;
    }
    
    function getDiagnosticoNaturopatico() {
        return $this->diagnostico_naturopatico;    
    } 
     
    function getCdNivelAlergia() {
        return $this->cd_nivel_alergia;
    }
	
   
    function getFrecuenciaCardiaca() {
        return $this->frecuencia_cardiaca;
    }
    
    function getNivelDolor() {
        return $this->nivel_dolor;
    }
	
	function getCedulaPaciente() {
		return $this->cedula_paciente;
	}
	
	function getNumEmbarazosPaciente() {
		return $this->num_embarazos_paciente;		
	}
	
	function getNumPartosPaciente() {
		return $this->num_partos_paciente;
	}
	
	function getNumAbortosPaciente() {
		return $this->num_abortos_paciente;
	}
	
	function getNumCesareasPaciente() {
		return $this->num_cesareas_paciente;
	}
	
	function getSexoPaciente() {
		return $this->sexo_paciente;
	}
	
    
    //buscar pacientes por el nombre
    function buscarPacientesPorApellidos($inicio, $fin, $contarTodos) {
        $sql = "select cd_paciente, apellidos_paciente, nombres_paciente, cedula_paciente, sexo_paciente " .
				" from pacientes where " . $this->getCampoBuscar() ." like '%" . $this->apellidos_paciente . "%' order by apellidos_paciente, nombres_paciente " ;
		if(!$contarTodos) {
			$sql .= " limit " . $inicio . ", " . $fin;
		}
        return $sql;
    }
    

	//Niveles de alergia
    //esta funcion se añade aquí porque no se tiene una clase para los niveles de alergia
	//estas funciones deben tener dos campos: codigo y texto, y los alias de las columnas
	//deben llamarse de la misma manera.
    function getTextosNivelAlergia() {
        $sql = "select cd_nivel_alergia as codigo, nm_nivel_alergia as texto from niveles_alergia order by cd_nivel_alergia";
        return $sql;
        
    }
	
	function getNmNivelAlergiaPaciente() {
		$sql = "select nm_nivel_alergia from niveles_alergia where cd_nivel_alergia = " . $this->cd_nivel_alergia;
		//$this->getCdNivelAlergia();		
		return $sql;
	}
	
	function setCampoBuscar($campo_buscar) {
		$this->campo_buscar = $campo_buscar;
	}
	
	function getCampoBuscar() {
		return $this->campo_buscar;
	}
	
	function eliminarPaciente() {
		$sql = "delete from pacientes where cd_paciente = " . $this->cd_paciente;
		return $sql;
	}
	
	function validarEliminarPaciente() {
		$sql = "select count(1) as conteo from tratamientos where cd_paciente =" . $this->cd_paciente;
		//echo "validacioneliinacionpaciente::: " . $sql;
		return $sql;
	}
	
	function obtenerNumTotalPacientes() {
		$sql = "select count(cd_paciente) as conteo from pacientes";
		return $sql;		
	}
	
	function consultaPacientesConTerapiasPorFechas($feInicio, $feFin) {
		$sql = "select p.cd_paciente, p.nombres_paciente, p.apellidos_paciente, t.nm_tratamiento, " .
				" s.fe_sesion, s.notas_sesion, su.nm_sucursal " .
				" from pacientes p, tratamientos t, sesiones s, sucursales su " .
				" where ".
				" p.cd_paciente = t.cd_paciente " .
				" and p.cd_sucursal = su.cd_sucursal " .
				" and t.cd_tratamiento = s.cd_tratamiento " .
				" and s.fe_sesion between '". $feInicio."' and '".$feFin."' ";
				if($this->cd_sucursal != -1)
					$sql .= " and p.cd_sucursal = " . $this->cd_sucursal;
		$sql .= " order by p.apellidos_paciente, s.fe_sesion ";
		
		return $sql;
	}
}

?>