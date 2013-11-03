<?php
/*
 * Author: Sari Haj Hussein
 */
session_start();
if(!isset($_SESSION['login']) || !$_SESSION['login'] == "yes" || $_SESSION['userlevel'] != 3) {
	header("Location: index.php");
	exit();
}

include('classes/ticket.php');
include('classes/staff.php');

$ticket = new ticket();
$ticket->db_open();

if(isset($_POST['deldepartment']) || isset($_POST['adddepartment']) || isset($_POST['delproduct']) || isset($_POST['addproduct'])) {
	$staff = new staff();
	$staff->db_open();

	if(isset($_POST['deldepartment'])) {
		$edit = $staff->edit_departments($_POST['deldepartment'], 'del');
	} elseif(isset($_POST['adddepartment'])) {
		$edit = $staff->edit_departments($_POST['adddepartment'], 'add');
	} elseif(isset($_POST['delproduct'])) {
		$edit = $staff->edit_products($_POST['delproduct'], 'del');
	} elseif(isset($_POST['addproduct'])) {
		$edit = $staff->edit_products($_POST['addproduct'], 'add');
	}

	if($edit) {
		$successmsg = "Operation successfull";
	} else $errormsg = $staff->get_error();
}

if(isset($_POST['name'])) {
	$user = new user();
	$user->db_open();
	$adduser = $user->add_user($_POST['name'], $_POST['regemail'], $_POST['confirmemail'], $_POST['regpassword'], $_POST['confirmpassword'], 2);
	if($adduser) {
		$successmsg = "New staff user crated.";
	} else {
		$errormsg = $user->get_error;
	}
}

$departments = $ticket->get_departments();
$products = $ticket->get_products();
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
	<li><a href="profile.php">Profile</a></li>
	<li><a href="logout.php">Log Out</a></li>
	<?php if($_SESSION['userlevel'] == 3) echo '<li><a style="text-decoration: underline;" href="admin.php">Admin</a></li>'; ?>
</ul>
</nav>


<div class="main">
<div id="spacer">
<div class="blacksmall">Admin Options</div>
</div>
<div id="optionbar">
	<div class="whitemedium" style="text-align: center; padding-top: 35px">Add/delete department, add/delete product and add staff user.</div>
</div>

<?php 
if(isset($successmsg)) {
	echo '<div class="notification success">' . $successmsg . '</div>';
} if(isset($errormsg)) {
	echo '<div class="notification error">' . $errormsg . '</div>';
}
?>

<form id="profileforms" action="admin.php" method="POST">
<div id="profileform" style="height: 440px;">
	<div style="margin-left: 10px; margin-bottom: 30px" class="greytext">Add staff user.</div>
		<label style="width: 150px;">Full Name<span class="small">Full name</span></label>
		<input style="float: left; margin-left: 20px; width: 250px;" type="text" name="name" id="name" required></input><br />
		<label style="width: 150px;">E-Mail<span class="small">E-Mail address</span></label>
		<input style="float: left; margin-left: 20px; width: 250px;" type="email" name="regemail" id="regemail" required></input>
		<label style="width: 150px;">Confirm E-mail<span class="small">E-mail address</span></label>
		<input style="float: left; margin-left: 20px; width: 250px;" type="email" name="confirmemail" id="confirmemail" required></input><br />
		<label style="width: 150px;">Password<span class="small">Password</span></label>
		<input style="float: left; margin-left: 20px; width: 250px;" type="password" name="regpassword" id="regpassword" required></input>
		<label style="width: 150px;">Confirm Password<span class="small">Type again</span></label>
		<input style="float: left; margin-left: 20px; width: 250px;" type="password" name="confirmpassword" id="confirmpassword" required></input>
		<input style="clear: both;" type="submit" value="Submit"></form>
</div>
</form>
<div id="profileforms">
<div id="productform">
	<form action="admin.php" method="post">
	<div style="margin-left: 10px; margin-bottom: 30px" class="greytext">Add/delete department</div>
	<div>
		<span class="smallblack">Add department</span><br />
		<input style="float: left; width: 250px;" type="text" name="adddepartment" id="adddepartment" required></input>
		<input style="float: right;" type="submit" value="Add"></input></form>
		<br />
		</form>
		<form action="admin.php" method="post">
		<div style="clear: both;" class="smallblack">Delete department:</div>
		<select style="width: 250px;" class="submitboxes" name="deldepartment">
		<?php
		foreach ($departments as $department) {
		echo "<option>$department</option>";
		}
		?>
		</select>
		<input style="float: right;" type="submit" value="Delete"></input></form>
	</div>
</form>
</div>

<div id="productform">
	<form action="admin.php" method="post">
	<div style="margin-left: 10px; margin-bottom: 30px" class="greytext">Add/delete product</div>
	<div>
		<span class="smallblack">Add product</span><br />
		<input style="float: left; width: 250px;" type="text" name="addproduct" id="addproduct" required></input>
		<input style="float: right;" type="submit" value="Add"></input></form>
		<br />
		</form>
		<form action="admin.php" method="post">
		<div style="clear: both;" class="smallblack">Delete product:</div>
		<select style="width: 250px;" class="submitboxes" name="delproduct">
		<?php
		foreach ($products as $product) {
		echo "<option>$product</option>";
		}
		?>
		</select>
		<input style="float: right;" type="submit" value="Delete"></input></form>
	</div>
	</form>
</div>
</div>	
</div>

</body>
</html>