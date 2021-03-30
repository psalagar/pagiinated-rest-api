<?php
class Database{
	// specify your own database credentials
	private $host = "localhost";
	private $db = "test";
	private $username = "postgres";
	private $password = "root";
	// private $dsn = "pgsql:host=$host;port=5432;dbname=$db";
	public $conn;
	// get the database connection
	public function getConnection(){
		$this->conn = null;
		try{
			$this->conn = new PDO("pgsql:host=" . $this->host . "; port=5432; dbname=" . $this->db, $this->username, $this->password);
			$this->conn->exec("set names utf8");
		}
		catch(PDOException $exception){
			echo "Connection error: " . $exception->getMessage();
		}
		return $this->conn;
 }
}
?>