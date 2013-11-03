<?php
require_once('classes/user.php');
/*
 * Author: Sari Haj Hussein
 */
 
/**
 * Representation of a staff user, with methods for admin specific operations.
 * 
 * <code>
 * require_once('classes/staff.php');
 *
 * $staff = new staff();
 *
 * $delete = $staff->delete_user(10);
 *
 * echo $delete;
 * </code>
 */

class staff extends user {

	//Get a list of all users.
	function get_all_users($page = 0, $limit = 10) 
	{
		//Calculate the offset.
		if($page != 0) {
			$page = $page * $limit;
		}

		//Get all the users.
		$result = $this->mysqli->query("SELECT user.uid, user.email, 
										user.userlevel, userdata.name, 
										userdata.phone, 
										userdata.country, userdata.state, 
										userdata.address, userdata.postal, 
										userdata.company, userdata.website 
										FROM user
										LEFT JOIN userdata
										ON user.uid = userdata.uid
										LIMIT $page, $limit;");

		//Add to array.
		while($row = $result->fetch_assoc()) {
			$userarray[] = $row;
		}

		//Check for errors.
		if($this->mysqli->error) {
			$this->error = "Database error: " . $this->mysqli->error;
			return false;
		} else {
			return $userarray;
		}
	}

	//Delete a user from the database.
	function delete_user($uid)
	{
			//Check if uid is numeric.
			if(!is_numeric($uid)) {
				$this->error = "Userid must be a number.";
				return false;
			}

			//Delete the user.
			$query = "DELETE FROM user WHERE uid=?;";
			$statement = $this->mysqli->prepare($query);
			$statement->bind_param('i', $uid);
			$deleteuser = $statement->execute();

			//Check for errors.
			if(!$deleteuser) {
				$this->error = "Database error: " . $this->mysqli->error;
				return false;
			} else {
				return "User deleted.";
			}
	}

	//Delete a ticket.
	function delete_ticket($tid)
	{
		if(!is_numeric($tid)) {
			$this->error = "Ticket id must be a number.";
			return false;
		}
		
		//Delete the ticket.
		$query = "DELETE FROM ticket WHERE tid=?;";
		$statement = $this->mysqli->prepare($query);
		$statement->bind_param('i', $tid);
		$deleteticket = $statement->execute();

		//Check for errors.
		if(!$deleteticket) {
        	$this->error = "Database error, user not added.";
        	return false;
		} else {
			return "Ticket deleted.";
		}
	}

	//Delete or add a new product.
	function edit_products($name, $cmd)
	{
		//Define queries depending on delete or add command.
		if($cmd == "del") {
			$query = "DELETE FROM product WHERE product=?;";
			$msg = "Product deleted.";
		} elseif ($cmd == "add") {
			$query = "INSERT INTO product VALUES (?);";
			$msg = "Product added.";
		}

		//Execute the query.
		$statement = $this->mysqli->prepare($query);
		$statement->bind_param('s', $name);
		$execute = $statement->execute();

		//Check for errors.
		if($execute) {
			return $msg;
		} else {
			$this->error = "Datebase error: " . $this->mysqli->error;
			return false;
		}
		
	}

	//Delete or add a new department.
	function edit_departments($name, $cmd)
	{
		//Define queries depending on delete or add command.
		if($cmd == "del") {
			$query = "DELETE FROM department WHERE department=?;";
			$msg = "Department deleted.";
		} elseif ($cmd == "add") {
			$query = "INSERT INTO department VALUES (?);";
			$msg = "Department added.";
		}

		//Execute the query.
		$statement = $this->mysqli->prepare($query);
		$statement->bind_param('s', $name);
		$execute = $statement->execute();

		//Check for errors.
		if($execute) {
			return $msg;
		} else {
			$this->error = "Datebase error: " . $this->mysqli->error;
			return false;
		}
		
	}
}
?>