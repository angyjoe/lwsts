<?php
/*
 * Author: Sari Haj Hussein
 */
require_once('classes/user.php');
if(isset($_POST['email'])) {
	$user = new user();
	$user->db_open();

	$generatetoken = $user->password_reset($_POST['email']);

	if($generatetoken) {
		$successmsg = "A link to reset the password has been sent to your email.";
	} else {
		$errormsg = $user->get_error();
	}
}

if(isset($_GET['utoken']) && isset($_GET['uemail'])) {
	$user = new user();
	$user->db_open();

	$takepasswordreset = $user->take_password_reset($_GET['utoken'], $_GET['uemail']);

	if($takepasswordreset) {
		header('Location: login.php?reset=1');
	} else {
		$errormsg = $user->get_error();
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Ticket System - Reset password</title>
	<link rel="stylesheet" href="styles/stylesheet.css" type="text/css" media="screen">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800|Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
</head>
<body>
<a href="index.php" style="text-decoration: none;" id="banner">Support Ticket System</a>

<nav>
<ul id="navigation">
	<li><a href="index.php">View Tickets</a></li></li>
	<li><a href="submit.php">Create Ticket</a></li>
	<li><a href="profile.php">Profile</a></li>
	<li><a href="logout.php">Log Out</a></li>
</ul>
</nav>


<div class="main mainlogin">
<div id="loginbar">
<div class="loginbuttons resetbutton">Reset Password</div>
</div>

<?php 
	if(isset($errormsg)) {
		echo '<div class="notification error">' . htmlspecialchars($errormsg, ENT_QUOTES) . '</div>';
	} else if(isset($successmsg)) {
		echo '<div class="notification success">' . htmlspecialchars($successmsg, ENT_QUOTES) . '</div>';
	}
?>

<div class="loginforms loginform">
	<br />
<div class="greymedium">Here you can request a new password</div><br />
<div class="greysmall">Please enter the email used to register the user below.</div>
<div class="blueseperator"></div>
<form id="profileforms" action="reset.php" method="POST">
	<div id="loginformspec"><br />
		<label>E-mail<span class="small">E-mail address</span></label>
		<input type="email" name="email" id="email" required></input><br />
	</div>
<input style="clear: both;" type="submit" value="Submit"></input></form>
</form>
</div>
</div>
</body>
</html>