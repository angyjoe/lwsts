<?php
/*
 * Author: Sari Haj Hussein
 */
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] != "yes" || $_SESSION['login'] == "") {
	header("Location: login.php");
	exit();
}

include('classes/user.php');

$user = new user();
$user->db_open();

$uid = $_SESSION['uid'];

if(isset($_POST['name'])) {
	$updateuserdata = $user->update_userdata($uid, $_POST['name'], $_POST['phone'], $_POST['country'], $_POST['state'], $_POST['address'], $_POST['postal'], $_POST['company'], $_POST['website'], $_POST['city']);
	if(!$updateuserdata) {
		$errormsg = htmlspecialchars($user->get_error(), ENT_QUOTES);
	} else {
		$successmsg = htmlspecialchars($updateuserdata, ENT_QUOTES);
	}
}

if(isset($_POST['oldpassword'])) {
	$updateuserdata = $user->change_password($uid, $_POST['oldpassword'], $_POST['newpassword'], $_POST['confirmpassword']);

	if(!$updateuserdata) {
		$errormsg = htmlspecialchars($user->get_error(), ENT_QUOTES);
	} else {
		$successmsg = htmlspecialchars($updateuserdata, ENT_QUOTES);
	}
}

$array = $user->get_userdata($uid);

if($array) {
	$name = htmlspecialchars($array['name'], ENT_QUOTES);
	$phone = htmlspecialchars($array['phone'], ENT_QUOTES);
	$country = htmlspecialchars($array['country'], ENT_QUOTES);
	$state = htmlspecialchars($array['state'], ENT_QUOTES);
	$address = htmlspecialchars($array['address'], ENT_QUOTES);
	$postal = htmlspecialchars($array['postal'], ENT_QUOTES);
	$company = htmlspecialchars($array['company'], ENT_QUOTES);
	$website = htmlspecialchars($array['website'], ENT_QUOTES);
	$city = htmlspecialchars($array['city'], ENT_QUOTES);
} else {
	$name = NULL;
	$phone = NULL;
	$country = NULL;
	$state = NULL;
	$address = NULL;
	$postal = NULL;
	$company = NULL;
	$website = NULL;
	$city = NULL;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Ticket System - Profile</title>
	<link rel="stylesheet" href="styles/stylesheet.css" type="text/css" media="screen">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800|Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
</head>
<body>
<a href="index.php" style="text-decoration: none;" id="banner">Support Ticket System</a>

<nav>
<ul id="navigation">
	<li><a href="index.php">View Tickets</a></li></li>
	<li><a href="submit.php">Create Ticket</a></li>
	<li><a href="profile.php" style="text-decoration: underline;">Profile</a></li>
	<li><a href="logout.php">Log Out</a></li>
	<?php if($_SESSION['userlevel'] == 3) echo '<li><a href="admin.php">Admin</a></li>'; ?>
</ul>
</nav>


<div class="main">
<div id="spacer">
<div class="blacksmall">Your profile</div>
</div>
<div id="optionbar">
	<div class="whitemedium" style="text-align: center; padding-top: 35px">Here you can view and edit your profile information and change your password.</div>
</div>

<?php 
if(isset($successmsg)) {
	echo '<div class="notification success">' . $successmsg . '</div>';
} if(isset($errormsg)) {
	echo '<div class="notification error">' . $errormsg . '</div>';
}
?>

<form id="profileforms" action="profile.php" method="POST">
<div id="profileform">
	<div style="margin-left: 10px; margin-bottom: 30px" class="greytext">Your information.</div>
	<div id="profileone">
		<label>Name<span class="small">Your full name</span></label>
		<input type="text" name="name" id="name" value="<?php echo $name ?>"></input>
		<label>Company<span class="small">Optional</span></label>
		<input type="text" name="company" id="company" value="<?php echo $company ?>"></input>
		<label>Website<span class="small">Optional</span></label>
		<input type="url" name="website" id="website" value="<?php echo $website ?>"></input>
			<label>Phone<span class="small">+Country code</span></label>
		<input type="text" name="phone" id="phone" value="<?php echo $phone ?>"></input>
	</div>
	<div id="profiletwo">
		<label>Address<span class="small">Full address</span></label>
		<input type="text" name="address" id="address" value="<?php echo $address ?>"></input>
		<label>City<span class="small">City name</span></label>
		<input type="text" name="city" id="city" value="<?php echo $city ?>"></input>
		<label>State<span class="small">State name</span></label>
		<input type="text" name="state" id="state" value="<?php echo $state ?>"></input>
		<label>Zip<span class="small">Min. 4 chars</span></label>
		<input type="text" name="postal" id="postal" value="<?php echo $postal ?>"></input>
		<label>Country<span class="small">Country name</span></label>
		<input type="text" name="country" id="country" value="<?php echo $country ?>"></input>
	</div>

	<input type="submit" class="submitbutton" value="Submit">
</div>
</form>
<form id="profileforms" action="profile.php" method="POST">
<div id="passwordform">
	<div style="margin-left: 10px; margin-bottom: 30px" class="greytext">Change password</div>
	<div id="passwordone">
		<label id="pwformlabels">Old password<span class="small">Your old password</span></label>
		<input type="password" name="oldpassword" id="oldpassword" required></input>
		<label id="pwformlabels">New password<span class="small">New password</span></label>
		<input type="password" name="newpassword" id="newpassword" required></input>
		<label id="pwformlabels">Confirm password<span class="small">Repeat password</span></label>
		<input type="password" name="confirmpassword" id="confirmpassword" required></input>
	</div>
	<input type="submit" value="Submit" class="submitbutton"></button>
</div>
</form>
</div>

</body>
</html>