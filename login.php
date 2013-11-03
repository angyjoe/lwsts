<?php
/*
 * Author: Sari Haj Hussein
 */
	//Check for POST data from login form.
	if(isset($_POST['email']) && isset($_POST['password'])) {
		include('classes/user.php');

		$user = new user();
		$user->db_open();

		if($user->take_login($_POST['email'], $_POST['password'])) {
			header('Location: index.php');
			exit();
		} else {
			$errormsg = $user->get_error();
		}
	}

	//Check for POST data from register form.
	if(isset($_POST['name']) && isset($_POST['regemail']) && isset($_POST['confirmemail']) && isset($_POST['regpassword']) && isset($_POST['confirmpassword']))
	{
		include('classes/user.php');

		$user = new user();
		$user->db_open();

		if($user->add_user($_POST['name'], $_POST['regemail'], $_POST['confirmemail'], $_POST['regpassword'], $_POST['confirmpassword'])) {
			$successmsg = "User created, please log in.";
		} else {
			$errormsg = $user->get_error();
		}
	}

	if(isset($_GET['reset'])) {
		$successmsg = "A new password hasb been generated and sent to your E-mail.";
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Ticket System - Login</title>
	<link rel="stylesheet" href="styles/stylesheet.css" type="text/css" media="screen">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800|Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
	<script src="http://code.jquery.com/jquery-latest.js"></script>

<script>
$(document).ready(function() {
	$(".loginbuttons.loginbutton").click( function() {
	$('.loginforms.registerform').fadeOut('slow', function() {
		$('.loginbuttons.loginbutton').css("color", "white");
		$('.loginbuttons.loginbutton').css("background-color", "#49729b");
		$('.loginbuttons.registerbutton').css("color", "black");
		$('.loginbuttons.registerbutton').css("background-color", "transparent");
	    $('.loginforms.loginform').fadeIn('slow', function() {
	  	});
	});
	});

	$(".loginbuttons.registerbutton").click( function() {
	$('.loginforms.loginform').fadeOut('slow', function() {
		$('.loginbuttons.loginbutton').css("color", "black");
		$('.loginbuttons.registerbutton').css("background-color", "#49729b");
		$('.loginbuttons.registerbutton').css("color", "white");
		$('.loginbuttons.loginbutton').css("background-color", "transparent");
		$('.loginforms.registerform').fadeIn('slow', function() {
	  	});
  	});
	});
});
</script>
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
<div class="loginbuttons loginbutton">Login</div>
<div class="loginbuttons registerbutton">Register</div>
</div>

<?php 
	if(isset($errormsg)) {
		echo '<div class="notification error">' . htmlspecialchars($errormsg, ENT_QUOTES) . '</div>';
	} else if($successmsg) {
		echo '<div class="notification success">' . htmlspecialchars($successmsg, ENT_QUOTES) . '</div>';
	}
?>

<div class="loginforms loginform">
	<br />
<div class="greymedium">Welcome to the Support Ticket System</div><br />
<div class="greysmall">Please login below or created a user</div>
<div class="blueseperator"></div>
<form id="profileforms" action="login.php" method="POST">
	<div id="loginformspec"><br />
		<label>E-mail<span class="small">E-mail address</span></label>
		<input type="email" name="email" id="email" required></input><br />
		<label>Password<span class="small">Your password</span></label>
		<input type="password" name="password" id="password" required></input>
	</div>
<input style="clear: both;" type="submit" value="Submit"></input></form>
</form>
<div class="blueseperator"></div><br />
<div class="blacklink" style="text-align: center;"><a class="blacklink" href="reset.php">Forgot password?</a></div>
</div>

<br />

<div class="loginforms registerform">
<div class="greymedium">Register a new user</div><br />
<div class="greysmall">Please fill in the required information to create a new user</div>
<div class="blueseperator"></div>
<form id="profileforms" action="login.php" method="POST">
	<div id="loginformspec"><br />
		<label>Full Name<span class="small">Your full name</span></label>
		<input type="text" name="name" id="name" required></input><br />
		<label>E-Mail<span class="small">Your E-Mail</span></label>
		<input type="email" name="regemail" id="regemail" required></input>
		<label>Confirm E-mail<span class="small">E-mail address</span></label>
		<input type="email" name="confirmemail" id="confirmemail" required></input><br />
		<label>Password<span class="small">Your password</span></label>
		<input type="password" name="regpassword" id="regpassword" required></input>
		<label>Confirm Password<span class="small">Type again</span></label>
		<input type="password" name="confirmpassword" id="confirmpassword" required></input>
	</div>
<input style="clear: both;" type="submit" value="Submit"></input></form>
</form>
</div>
</div>
</body>
</html>