<?php
include("DBConfig.php");

class DBConnect {
	
	private $conn;
	
    // constructor
    function __construct() {
         
    }
 
    // destructor
    function __destruct() {
        // $this->close();
    }
 
    // Connecting to database
    public function connect() {
        // connecting to mysql
        //$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		if ($this->conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
    }
 
	public function getConn() {
		return $this->conn;
	}
    // Closing database connection
    public function close() {
        $this->conn->close();
    }
	
 
}
?>