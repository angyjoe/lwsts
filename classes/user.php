<?php
require_once('classes/utility.php');
/*
 * Author: Sari Haj Hussein
 */
 
/**
 * Representation of a user.
 * 
 * <code>
 * require_once('classes/user.php');
 *
 * $user = new user();
 *
 * $adduser = $user->add_user('email', 'email', 'password', 'password', 'userlevel');
 *
 * echo $adduser;
 * </code>
 */

class user extends utility
{
	//Add a new user to the database. 
	//Userlevel is as follows: 1 = Regular user, 2 = Staff user and 3 = Adminstrator
	function add_user($name, $email, $emailconfirm, $password, $passwordconfirm, $userlevel = 1)
	{
		//Checks that the passwords and emails match.
		if($password != $passwordconfirm) {
			$this->error = "Passwords do not match.";
			return false;
		}

		if($email != $emailconfirm) {
			$this->error = "E-mails do not match.";
			return false;
		}

		//Check if email already exists.
		$statement = $this->mysqli->prepare("SELECT * FROM user WHERE email=?;");
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->store_result();
		$numrows = $statement->num_rows();
		$statement->close();

		if($numrows != 0) {
			$this->error = 'E-email already used.';
			return false;
		} else {
			$this->mysqli->autocommit(FALSE); //Turn off autocommit to allow transactions.

			//Password hashing.
			$hashed = hash('sha256', $password); 		//hash the password.
			$randomstring = md5(uniqid(rand(), true)); 	//Get a random unique string for salt.
			$salt = substr($randomstring, 0, 8); 		//Cut the salt from the random string.
			$hashed = hash('sha256', $salt . $hashed); 	//Hash the password with salt.

			//Insert the user into the database.
			$statement = $this->mysqli->prepare("INSERT INTO user (email, password, salt, userlevel) VALUES (?, ?, ?, ?);");
			$statement->bind_param("sssi", $email, $hashed, $salt, $userlevel);
			$statement->execute();
	        $userid = $statement->insert_id;
	        $arows = $statement->affected_rows;
	        $statement->close();

			//Check for errors.
	        if($arows != 1) {
	        	$this->error = "Database error, user not added.";
	        	$this->mysqli->rollback();
	        	return false;
	        } else {
	        	//Insert uid and name in userdata table.
    			$query = "INSERT INTO userdata (uid, name) 
	        			  VALUES (?, ?);";
				$statement = $this->mysqli->prepare($query);
				$statement->bind_param('is', $uid, $name);
				$insertuserdata = $statement->execute();
		        
		        //Check for errors.
		        if(!$insertuserdata) {
		        	$this->error = "Database error: " . $this->mysqli->error;
		        	$this->mysqli->rollback();
		        	return false;
		        } else {
	        		$this->mysqli->commit();
					return "User created.";
		        }
	        }
	        $this->mysqli->autocommit(TRUE);
        }
	}

	//Check login data.
	function take_login($email, $password)
	{
		//Get userinfo belonging to the email from the database.
		$statement = $this->mysqli->prepare("SELECT uid, email, password, salt, userlevel
											 FROM user
											 WHERE email=?;");
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->store_result();
		$numrows = $statement->num_rows;
		$statement->bind_result($uid, $femail, $fpassword, $fsalt, $fuserlevel);
		$statement->fetch();
		$statement->close();

		//Check if email exists.
		if($numrows < 1) {
			$this->error = "No user with that email.";
			return false;
		} else {
			//Hash the supplied password.
			$hashed = hash('sha256', $fsalt . hash('sha256', $password));

			//Compare to the stored hashed password.
			if($hashed != $fpassword) {
				$this->error = "Wrong password.";
				return false;
			} elseif ($hashed == $fpassword) {
				//Set session parameters.
				session_start();
				$_SESSION['login'] = "yes";
				$_SESSION['uid'] = $uid;
				$_SESSION['userlevel'] = $fuserlevel;
				return true;
			}
		}
	}

	//Update the user profile.
	function update_userdata($uid, $name, $phone = NULL, 
							 $country = NULL, $state = NULL, $address = NULL, 
							 $postal = NULL, $company = NULL, $website = NULL, $city = NULL)
	{
		//Update the userdata table.
		$statement = $this->mysqli->prepare("UPDATE userdata SET name = ?, phone = ?, country = ?, state = ?, address = ?, postal = ?, company = ?, website = ?, city = ? WHERE uid = ?") or die($this->mysqli->error);
		$statement->bind_param("sssssisssi", $name, $phone, $country, $state, $address, $postal, $company, $website, $city, $uid);
		$statement->execute();
		$arows = $statement->affected_rows;
		$statement->close();

		//Check for errors.
		if($arows < 1 && $this->mysqli->error) {
			$this->error = "Database error, could not update profile: " . $this->mysqli->error;
		} else {
			return "Profile has been updated.";
		}
	}

	//Fetch the userdata from the database.
	function get_userdata($uid) 
	{
		$statement = $this->mysqli->prepare("SELECT * FROM userdata WHERE uid=?;");
		$statement->bind_param("i", $uid);
		$statement->execute();
		$statement->store_result();
		$numrows = $statement->num_rows();

		//check for errors.
		if($numrows != 1) {
			$error = 'No user with that ID.';
		return false;
		} else {
			//Bind the returned data and store in an array.
			$statement->bind_result($uid, $name, $phone, $country, $state, $address, $postal, $company, $website, $city);
			$statement->fetch();
			$row = array('uid' => $uid, 'name' => $name, 'phone' => $phone, 'country' => $country, 'state' => $state, 'address' => $address, 'postal' => $postal, 'company' => $company, 'website' => $website, 'city' => $city);
			$statement->close();
			return $row;
		}
	}

	//Change a user or admins password.
	function change_password($uid, $oldpassword, $newpassword, $confirm)
	{
		//Check if new passwords match.
		if($newpassword != $confirm) {
			$this->error = "Passwords do not match.";
			return false;
		}

		//Get the old password and salt from the database.
		$statement = $this->mysqli->prepare("SELECT password, salt
											 FROM user
											 WHERE uid=?;");
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->store_result();
		$numrows = $statement->num_rows;
		$statement->bind_result($password, $salt);
		$statement->fetch();
		$statement->close();

		//Hash the supplied old password.
		$oldpassword = hash('sha256', $salt . hash('sha256', $oldpassword));

		//Compared to the stored old password.
		if($password != $oldpassword) {
			$this->error = "Old password is wrong.";
			return false;
		} elseif ($password == $oldpassword) {
			//Hash the new password.
			$hashed = hash('sha256', $newpassword);
			$randomstring = md5(uniqid(rand(), true));
			$newsalt = substr($randomstring, 0, 8);
			$hashed = hash('sha256', $newsalt . $hashed);

			//Store the new password and salt in the database.
			$statement = $this->mysqli->prepare("UPDATE user SET password=?, salt=? WHERE uid=?");
			$statement->bind_param("sss", $hashed, $newsalt, $uid);
			$statement->execute();
	        $arows = $statement->affected_rows;
	        $statement->close();

	        //Check for errors.
	        if($arows != 1 || $this->mysqli->error) {
	        	$this->error = "Database Error.";
	        	return false;
	        } else {
	        	return "Password changed.";
	        }
		}
	}

	//Initiate password reset. This function generates a unique token and stores it in the passwordreset table.
	function password_reset($email)
	{
		$this->mysqli->autocommit(FALSE); //Turn off autocommit to allow transactions.

		//Create a random string and store it in the database.
		$utoken = md5(uniqid(rand(), true));
		$addutoken = $this->mysqli->query("INSERT INTO passwordreset (email, utoken)
										   VALUES ('$email', '$utoken')
										   ON DUPLICATE KEY UPDATE utoken = '$utoken';");

		//Check for errors.
		if(!$addutoken) {
			$this->error = "No user with that E-mail.";
			$this->mysqli->rollback();
			return false;
		} else {
			//Mail a password reset link to the user.	
			$mailsubject = 'Ticketsystem: Password reset request.';
			$messege = 'Open this link to reset your password. A new password will be mailed to you. http://www.boolean.in/newticket/reset.php?uemail=' . $email . '&utoken=' . $utoken;
			$messege = wordwrap($messege, 70);
			$header = 'From: admin@boolean.in';

			$sendmail = mail($email, $mailsubject, $messege, $header);

			//Check for errors.
			if(!$sendmail) {
				$this->error = "Password reset mail could not be sent, try again.";
				$this->mysqli->rollback();
				return false;
			} else {
				$this->mysqli->commit();
				$this->mysqli->autocommit(TRUE); 
				return "Password reset URL has been mailed to: $email";
			}
		}
	}

	//This function confirms the email and unique token and creates a new random password for the user.
	//The new password is then emailed to the user.
	function take_password_reset($utoken, $email) 
	{
		$this->mysqli->autocommit(FALSE);

		//Get the unique token form the database.
		$result = $this->mysqli->query("SELECT * FROM passwordreset WHERE email='$email' AND utoken='$utoken';");
		$row = $result->fetch_assoc();

		//Check errors.
		if(!is_array($row)) {
			$this->error = 'Wrong user id and/or token.';
			return false;
		} else {
			//Generate a new password (8 chars).
			$randomstring = md5(uniqid(rand(), true));
			$randompassword = substr($randomstring, 0, 8);

			//Hash the password.
			$hashed = hash('sha256', $randompassword); 	//hash the password.
			$randomstring = md5(uniqid(rand(), true)); 	//Get a random string for salt.
			$salt = substr($randomstring, 0, 8); 		//Cut the salt from the random string.
			$hashed = hash('sha256', $salt . $hashed); 	//Hash the password with salt.

			//Update the user table with the new password.
			$updatepassword = $this->mysqli->query("UPDATE user SET
													password = '$hashed',
													salt = '$salt'
													WHERE email='$email';");

			//Check for errors.
			if(!$updatepassword) {
				$this->error = "Database error: " . $this->mysqli->error;
				$this->mysqli->rollback();
				return false;
			} else {
				//Mail the new password to the user.
				$mailsubject = 'Ticketsystem: Password has been reset.';
				$messege = 'You password has been reset to: ' . $randompassword;
				$messege = wordwrap($messege, 70);
				$header = 'From: admin@boolean.in';

				$sendmail = mail($email, $mailsubject, $messege, $header);

				//Check for errors.
				if(!$sendmail) {
					$this->error = "New password mail could not be sent, try again.";
					$this->mysqli->rollback();
					return false;
				} else {
					//Delete the old unique token from the passwordreset database.
					$deleteutoken = $this->mysqli->query("DELETE FROM passwordreset WHERE email='$email'");
					
					//Check for errors.
					if(!$deleteutoken) {
						$this->error = "Database error: " . $this->mysqli->error;
						$this->mysqli->rollback();
						return false;
					} else {
						$this->mysqli->commit();
						$this->mysqli->autocommit(TRUE);
						return "New password sent to $email";
					}
				}
			}
		}
	}
}
?>