<?php
/*
 * Author: Sari Haj Hussein
 */
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] != "yes" || $_SESSION['login'] == "") {
	header("Location: login.php");
	exit();
}

include('classes/ticket.php');

$ticket = new ticket();
$ticket->db_open();

$departments = $ticket->get_departments();
$products = $ticket->get_products();

if(isset($_POST['department'])) {
	$product = $_POST['product'];
	$department = $_POST['department'];
	$subject = $_POST['subject'];
	$message = $_POST['message'];

	$addticket = $ticket->add_ticket($_SESSION['uid'], $message, $subject, $product, $department);

	if(!$addticket) {
		$errormsg = htmlspecialchars($user->get_error(), ENT_QUOTES);
	} else {
		header('Location: http://' . $_SERVER['SERVER_NAME'] . '/newticket/view.php?tid=' . $addticket);
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Ticket System - Create Ticket</title>
	<link rel="stylesheet" href="styles/stylesheet.css" type="text/css" media="screen">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800|Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
</head>
<body>
<a href="index.php" style="text-decoration: none;" id="banner">Support Ticket System</a>

<nav>
<ul id="navigation">
	<li><a href="index.php">View Tickets</a></li></li>
	<li><a href="submit.php" style="text-decoration: underline;">Create Ticket</a></li>
	<li><a href="profile.php">Profile</a></li>
	<li><a href="logout.php">Log Out</a></li>
	<?php if($_SESSION['userlevel'] == 3) echo '<li><a href="admin.php">Admin</a></li>'; ?>
</ul>
</nav>


<div class="main">
<div id="spacer">
<div class="blacksmall">Create Ticket</div>
</div>
<div id="optionbar">
	<div id="submitselect">
		<form action="submit.php" method="POST">
			<span class="whitemedium">Department:</span>
			<select class="submitboxes" name="department">
				<?php
				foreach ($departments as $department) {
				echo "<option>$department</option>";
				}
				?>
			</select>

			<span class="whitemedium">Product:</span>
			<select class="submitboxes" name="product">
				<?php
					foreach ($products as $product) {
					echo "<option>$product</option>";
					}
				?>
			</select>
	</div>
</div>

<?php 

if(isset($errormsg)) {
	echo '<div class="notification error">' . htmlspecialchars($errormsg, ENT_QUOTES) . '</div>';
}
?>

<table id="submittable">
	<tr><td class="submitlabel"><span class="blackmedium">Subject:</span></td>
    <td class="submitfield"><input type="text" name="subject" class="submitinput" style="width: 50%;" required></input></td></tr>

	<tr><td class="submitlabel"><span class="blackmedium">Text:</span></td>
	<td class="submitfield"><textarea name="message" class="submitinput" required></textarea></td>
	</tr>
	<tr>
		<td></td><td><input style="float: left;" class="submitbutton" type="reset" value="Clear all"/>
		<input style="float: right;" class="submitbutton" type="submit" value="Submit"/></td>
	</tr>
</table>
</form>
</div>

</body>
</html>