<?php
class PdoWrapper {
    
    private $con;
    private $sql;
    private $pdoError;
    private $dbSeguridad;
	private $dbApp;
	
    /* cuando es igual a 1 se debe incluir el archivo con los nombres de las columnas*/
    public $useTableCols = 0;
    /* usar formatero para el datadumper */
    public $useDataDumper = 1;
    
    function __construct() {
    }
    
    
	//funcion para conectar a la base de datos
    function pdoConnect() {
    //function pdoConnect($server, $username, $password, $database) {
        
        try {
			$config = parse_ini_file('config_abe.ini'); 
			$this->dbApp = $config['dbapp'];
			$this->dbSeguridad = $config['dbseguridad'];
            //$this->con = new PDO("mysql:dbname=$database; host=$server", $username, $password);			
            $this->con = new PDO("mysql:dbname=".$config['dbapp']."; host=" . $config['server'], $config['username'], $config['password']);
			
        } catch(PDOException $e) {            
            $this->pdoException($e->getMessage());
            exit;
        }
        $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    }
	
	
	function getConection() {
		return $this->con;
	}
	
	function getDbSeguridad() {
		return $this->dbSeguridad;
	}
	
	function getDbApp() {
		return $this->dbApp;
	}
    
    
    /*
    Obtiene todas las filas y todos los campos de la consulta solicitada
    Es apropiada para un select
    */
    function pdoExecute($query) {
        $this->sql = $query;        
        
        try {
            $stmt = $this->con->prepare($query);
            $stmt->execute();            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);                     
            //return $stmt->fetchAll();
            return $stmt; 
        } catch(PDOException $e) {
            $this->pdoException($e->getMessage());
            exit;
        }
    }
    
            
    function pdoGetAll($query) {
        $stmt = $this->pdoExecute($query);
        $rows = $stmt->fetchAll();
        return $rows;
    }
    
    
    function pdoGetRow($query) {
        $stmt = $this->pdoExecute($query);
        $row = $stmt->fetch();
        return $row;
    }
    
    
    /*
    Esta función sirve para insertar, actualizar y eliminar los registros de una misma tabla
    */
    function pdoInsertar($query) {
        //hacer un script para insertar
        $this->sql = $query;
        try {
            //echo "::: la consulta a ejecutar en PDOINSERT: " . $query;
            $numInserts = $this->con->exec($query);            
            return $numInserts;
        } catch(PDOException $e) { 
              $this->pdoException($e->getMessage());
              exit;          
        }
        
    }
    
    
    /*
    Esta sentencia elimina un registro específico
    */
    function pdoEliminar($query) {
        $this->sql = $query;
        try {
            echo "::: la consulta a ejecutar en PDOBORRAR: " . $query;
            $numInserts = $this->con->exec($query);            
            return $numBorrados;
        } catch(PDOException $e) { 
              $this->pdoException($e->getMessage());
              exit;          
        }
        
    }
    
    
    /*
    Esta funcion elimina los registros de las tablas hijos, y luego de las tablas padre, es decir, elimina todo
    Primero se debe eliminar los registros de las tablas hijos 
    Luego el registro de la tabla padre
    Se asume que el mismo código identificador, viene del padre hacia el hijo, por ello con la misma clave se elimina  
    */
    function pdoEliminarPadreHijo($tablaPadre, $tablasHijo, $nmColumnaPadre, $valorPadre) {
              
        foreach($tablasHijo as $tablaDetalle) {
            //eliminar con 
            $this->sql = "delete from " . $tablaDetalle  . " where " . $nmColumnaPadre . " = '" .  $valorPadre . "'";
            echo "<br>La consulta para eliminar hijos es " . $this->sql;
            $this->pdoEliminar($this->sql);
            
        }
        
        //al final hacer el siguiente delete
        $this->sql = "delete from " . $tablaPadre . " where " . $nmColumnaPadre . " = '" . $valorPadre . "'";
        echo "<br>La consulta para eliminar el padre es: " . $this->sql;
        $this->pdoEliminar($this->sql);
                
    }
    
    
    function pdoLasInsertId() {
        return $this->con->lastInsertId();

    }
    
    
    /*   
    ----------------------------------------------------------------------
    Utilitarios para generar mensajes de la base de datos
    ----------------------------------------------------------------------    
    */
    
    
    /*
    Funcion que formatea la excepción para publicarla de forma entendible
    */
    function pdoException($message) {
        $this->pdoError["Error"] = 'PDO-SQL-ERROR';
        $this->pdoError["PDO_error"] = $message;
        $this->pdoError["Sql"] = $this->sql;
        $this->pdoError["Debug_backtrace"] = debug_backtrace();
        
        if($this->useDataDumper) {
            $this->imprimirError($this->pdoError);
        } else {
            var_dump($pdo_error);
        }
    }
    
    
    /*
    Función que publica el error, en un formato legible
    */
    function imprimirError($arrayError) {
        
        echo "<br>========== ERROR EN ACCESO A BASE DE DATOS ==========<br>";
        foreach($arrayError as $llave => $valor) {
            echo $llave . ": " . $valor . "<br>" ;
        }        
        echo "<br>========== ERROR EN ACCESO A BASE DE DATOS ==========<br>";
        echo "<a href=\"login.php\">Ir al inicio</a>";        
    } 
	
	
	/* La única base por la que podemos cambiar es la base de seguridades */
	function cambiarBdd() {
		$sql = "use " . $this->dbSeguridad;
		return $sql;			
	}
	
	function cambiarBddApp() {
		$sql = "use " . $this->dbApp;
		return $sql;
	}
    
}

?>