<?php

class Database {
    private $bbdd;
    
    function Database($server, $user, $password, $bbdd = '') {
        $this->bbdd = new mysqli($server, $user, $password, $bbdd);
    }
    
    function isConnected() {
        return ($this->bbdd->connect_errno == 0);
    }
    
    function strError() {
        return $this->bbdd->connect_error;
    }
    
    function Disconnect() {
        return $this->bbdd->close();
    }
    
    function execSQL($sql, $params, $tipos) {
        $sentencia = $this->bbdd->stmt_init();
        if ($sentencia->prepare($sql)) {
            $aux = array_merge(array($tipos), $params);
            call_user_func_array(array ($sentencia, "bind_param"), $aux);

            $sentencia->execute();
            if ($sentencia->errno) {
                echo $sentencia->error;
                echo "<br>";
                //die ($sentencia->errno);
		return FALSE;
            } else {
		return $sentencia->num_rows;
	    }
            $sentencia->close();
        }
    }
    
    function query($sql, $params = Array(), $tipos = "") {
        $sentencia = $this->bbdd->stmt_init();
        if ($sentencia->prepare($sql)) {
            $aux = array_merge(array($tipos), $params);
            
            @call_user_func_array(array($sentencia, "bind_param"), $aux);
            $sentencia->execute();
            if ($sentencia->errno) {
                echo $sentencia->error;
                echo "<br>";
                die ($sentencia->errno);
            }

            $sentencia->store_result();
            $meta = $sentencia->result_metadata();

	    if ($meta == FALSE)
		return FALSE;

            while ($column = $meta->fetch_field()) {
               $bindVarsArray[] = &$results[$column->name];
            }
            call_user_func_array(array($sentencia, 'bind_result'), $bindVarsArray);
	    $filas = Array();
            while ($sentencia->fetch()) {
                unset ($aux);
                foreach ($results as $clave => $valor) {
                    $aux[$clave] = $valor;
                }
                $filas[] = $aux;
            }
            
            $sentencia->close();
            return $filas;
        }
    }
}

?>