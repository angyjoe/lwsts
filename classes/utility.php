<?php 
/*
 * Author: Sari Haj Hussein
 */
 
/**
 * A utility class, is never called directly.
 */

class utility 
{
	//initialize the connection variables.
	private $username = "ticket";
	private $password = "xZVwRDqbpfR7HHRj";
	private $host = "localhost";
	private $database = "sticketsystem";

	public $mysqli = "";
	public $error;

	//Opens the database connection.
	function db_open() 
	{
		$this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->database);

		if($this->mysqli->connect_errno != 0) {
			$this->error = "Could not connect: " . $this->mysqli->connect_error;
			return false;
		} else {
			return $this->mysqli;
		}
	}

	//Kills the active database connection.
	function db_close() 
	{
		$thread = $this->mysqli->thread_id; //Get active thread.
		$this->mysqli->kill($thread); 		//Kill the thread.
		$this->mysqli->close();				//Close the link

		return "Connection closed";
	}

	//Returns the last error.
	function get_error() 
	{
		$error = $this->error;
		unset($this->error);
		return $error;
	}
}
?>