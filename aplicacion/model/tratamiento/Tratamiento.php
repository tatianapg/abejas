<?php
class Tratamiento {
    private $cd_tratamiento;
    private $cd_paciente;
    private $nm_tratamiento;
    private $fe_tratamiento;
    private $medicacion_tratamiento;
    private $obs_tratamiento;
    private $terapias_tratamiento;
    
    //constructor de la clase
    function __construct() {
        
    }    
    
    function setTratamiento($cd_tratamiento, $cd_paciente, $nm_tratamiento, $fe_tratamiento,
        $medicacion_tratamiento, $obs_tratamiento, $terapias_tratamiento)
    {
        $this->cd_tratamiento = $cd_tratamiento;
        $this->cd_paciente = $cd_paciente;       
        $this->nm_tratamiento = $nm_tratamiento;
        $this->fe_tratamiento = $fe_tratamiento;        
        $this->medicacion_tratamiento = $medicacion_tratamiento;
        $this->obs_tratamiento = $obs_tratamiento;
        $this->terapias_tratamiento = $terapias_tratamiento;
    }
	
	function setDefaultNumeros() {
		
		if(!$this->terapias_tratamiento) $this->terapias_tratamiento = 0;
	}	
    
    function crearTratamiento() {
        
		$this->setDefaultNumeros();
		
        $cons = "Insert into Tratamientos(cd_paciente, nm_tratamiento, fe_tratamiento,
        medicacion_tratamiento, obs_tratamiento, terapias_tratamiento) values ( " .
        $this->cd_paciente . ", " .
        "'" . addslashes($this->nm_tratamiento) . "', " .
        "'" . addslashes($this->fe_tratamiento) ."', " .
        "'" . addslashes($this->medicacion_tratamiento) . "', " .
        "'" . addslashes($this->obs_tratamiento) . "', " .
        $this->terapias_tratamiento . ")"; 
        
        return $cons;        
    }
    
    function modificarTratamiento() {
		$this->setDefaultNumeros();
		
        $cons = " update Tratamientos set " .
        "cd_paciente = '" . $this->cd_paciente . "', " .
        "nm_tratamiento = '" . addslashes($this->nm_tratamiento) . "', " .
        "fe_tratamiento = '" . $this->fe_tratamiento . "', " .
        "medicacion_tratamiento = '" . addslashes($this->medicacion_tratamiento) . "', " .
        "obs_tratamiento = " . "'" .  addslashes($this->obs_tratamiento) . "', " .
        "terapias_tratamiento = '" . addslashes($this->terapias_tratamiento) . "' " .
        "where cd_tratamiento = " .$this->cd_tratamiento;
        
        return $cons;
    }
	
	/*esta función permite validar si se puede eliminar el tratamiento
	es necesario verificar que no existan medicaciones y sesiones asociadas
	*/
	function validarEliminarTratamiento() {
		
	}
	
	function getNumSesionesPorTratamiento() {
		$sql = "select count(1) as conteo from sesiones s where s.cd_tratamiento = " . $this->cd_tratamiento;
		return $sql;
	}

	function getNumMedicacionesPorTratamiento() {
		$sql = "select count(1) as conteo from medicaciones where cd_tratamiento = " . $this->cd_tratamiento;
		return $sql;
	}

	
	function eliminarTratamiento() {
		$cons = "delete from tratamientos where cd_tratamiento = " . $this->cd_tratamiento;
		return $cons;
	}
    
    function consultarTratamientoPorCd($cdTratamiento) {
        $cons = "select * from Tratamientos where cd_tratamiento = " . $cdTratamiento;
        return $cons;
    }
    

    function consultarTratamientosPorPaciente($cdPaciente) {
        $cons = "select cd_tratamiento, nm_tratamiento, fe_tratamiento, cd_paciente, terapias_tratamiento, " .
				"( select count(1) from sesiones s where s.cd_tratamiento = t.cd_tratamiento ) num_sesiones " .
				" from Tratamientos t where cd_paciente = " . $cdPaciente . " order by fe_tratamiento desc ";
		//echo $cons;
        return $cons;
    }
	
	
    function consultarDetalleTratamientosPorPaciente($cdPaciente) {
        $cons = "select cd_tratamiento, nm_tratamiento, fe_tratamiento, medicacion_tratamiento, obs_tratamiento, terapias_tratamiento from Tratamientos t where cd_paciente = " . $cdPaciente . " order by fe_tratamiento desc ";
		
        return $cons;
    }
	
    
    
    function obtenerTratamiento($fila) {
        //var_dump($fila);
        //echo "===========Entrando a get tratamiento===============";
        $this->cd_tratamiento = $fila["CD_TRATAMIENTO"];
        $this->cd_paciente = $fila["CD_PACIENTE"];
        $this->nm_tratamiento = $fila["NM_TRATAMIENTO"];
        $this->fe_tratamiento = $fila["FE_TRATAMIENTO"];
        $this->medicacion_tratamiento = $fila["MEDICACION_TRATAMIENTO"];
        $this->obs_tratamiento = $fila["OBS_TRATAMIENTO"];
        $this->terapias_tratamiento = $fila["TERAPIAS_TRATAMIENTO"];        
    }
    
    function setCdTratamiento($cdTratamiento) {
        $this->cd_tratamiento = $cdTratamiento;
    }

    function getCdTratamiento() {
        return $this->cd_tratamiento;
    }    
    
    
    function getCdPaciente() {
        return $this->cd_paciente;
    }
    
    function getNmTratamiento() {
        return $this->nm_tratamiento;
    }
        
    function getFeTratamiento() {
        return $this->fe_tratamiento;    
    }
           
    function getMedicacionTratamiento() {
        return $this->medicacion_tratamiento;    
    }
    
    function getObsTratamiento() {
        return $this->obs_tratamiento;    
    }
    
    function getTerapiasTratamiento() {
        return $this->terapias_tratamiento;    
    }
	
	/* obtener laa sesiones que tiene el tratamiento
	   coloco estas consultas aquí, porque primero existe el 
	   tratamiento y luego la sesion
	*/
	
	function getNumSesionesDeTratamiento() {
		$cons = "select count(1) from sesiones where cd_tratamiento = " . $this->cd_tratamiento;
		return $cons;
	}
	
	
	
	function getDatosSesionesdeTratamiento() {
		$cons = "select cd_sesion, cd_tratamiento, fe_sesion, notas_sesion from sesiones where cd_tratamiento = " . $this->cd_tratamiento . " order by fe_sesion desc";
		
		return $cons;
	}


	/*
    function consultarDetalleMedicacionesPorPaciente($cdPaciente) {
        $cons = "select cd_tratamiento, nm_tratamiento, fe_tratamiento, medicacion_tratamiento, obs_tratamiento, terapias_tratamiento from Tratamientos t where cd_paciente = " . $cdPaciente . " order by fe_tratamiento desc ";
		
        return $cons;
    }
	
	*/
	
	function getDatosMedicacionesdeTratamiento() {
		$cons= "select cd_medicacion, cd_tratamiento, fe_medicacion, notas_medicacion from medicaciones where cd_tratamiento=" .
		$this->cd_tratamiento . " order by fe_medicacion";
		return $cons;
	}
    
}
?>