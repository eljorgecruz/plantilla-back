<?php

class DatabaseConnection {
    private $serverName;
    private $connectionOptions;
    private $conn;
    
    public function __construct() {
        $this->serverName = $_ENV['BD_SERVER'];
        $this->connectionOptions = array(
            "Database" => $_ENV['BD_NAME'],
            "Uid" => $_ENV['BD_USER'],
            "PWD" => $_ENV['BD_PASSWORD']
        );
    }

    public function connect() {
        $this->conn = sqlsrv_connect($this->serverName, $this->connectionOptions);
        if ($this->conn === false) {
            echo "{
                \"status\": \"error\",
                \"message\": \"Error al conectar a la base de datos.\" 
            }";
            die();
        }
        return $this->conn;
    }
}
?>