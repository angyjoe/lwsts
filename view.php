<?php
/*
 * Author: Sari Haj Hussein
 */
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] != "yes" || $_SESSION['login'] == "") {
	header("Location: login.php");
	exit();
}

require_once('classes/ticket.php');
require_once('classes/user.php');
require_once('classes/reply.php');
require_once('classes/staff.php');
require_once('functions.php');

$ticket = new ticket();
$ticket->db_open();

$user = new user();
$user->db_open();

if(isset($_GET['tid']) && filter_var($_GET['tid'], FILTER_VALIDATE_INT)) {
	$tid = $_GET['tid'];
} else $tid = '0';

if($_SESSION['userlevel'] != 1 && $_SESSION['userlevel'] != "") {
	$admin = true;
} else $admin = false;

if(isset($_POST['action']) && $_POST['action'] == 'Delete' && $admin) {
	$staff = new staff();
	$staff->db_open();

	$delete = $staff->delete_ticket($_GET['tid']);

	if($delete) {
		header('Location: index.php');
	} else $error = htmlspecialchars($staff->get_error(), ENT_QUOTES);
}

if(isset($_POST['action']) && $_POST['action'] == 'Close' && $admin) {
	$close = $ticket->close_ticket($_GET['tid']);

	if(!$close) {
		$error = htmlspecialchars($staff->get_error(), ENT_QUOTES);
	}
}

if(isset($_POST['action']) && $_POST['action'] == 'Open' && $admin) {
	$open = $ticket->close_ticket($_GET['tid'], TRUE);

	if(!$open) {
		$error = htmlspecialchars($staff->get_error(), ENT_QUOTES);
	}
}

if(isset($_POST['message'])) {
	$reply = new reply();
	$reply->db_open();

	if(!$addreply = $reply->add_reply($tid, $_SESSION['uid'], $_POST['message'], $admin)) {
		$newreplyerror = htmlspecialchars($reply->get_error(), ENT_QUOTES);
	}
}

$ticketdetails = $ticket->get_ticket($tid);

if($ticketdetails) {
	if($ticketdetails['uid'] == $_SESSION['uid'] || $admin) {
		if($userdetails = $user->get_userdata($ticketdetails['uid'])) {
			if($ticketdetails) {
				$reply = new reply();
				$reply->db_open();

				if(!$getreplies = $reply->get_ticket_replies($tid)) {
					$replyerror = htmlspecialchars($reply->get_error(), ENT_QUOTES);
				}
			} else {
				$error = $user->get_error();
			}
		} else {
			$error = $user->get_error;
		}
	} else {
		header('Location: http://' . $_SERVER['SERVER_NAME'] . '/newticket/index.php?error=1');
	}
} else {
	$error = htmlspecialchars($ticket->get_error(), ENT_QUOTES);
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Ticket System</title>
	<link rel="stylesheet" href="styles/stylesheet.css" type="text/css" media="screen">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800|Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
</head>
<body>
<a href="index.php" style="text-decoration: none;" id="banner">Support Ticket System</a>

<nav>
<ul id="navigation">
	<li><a style="text-decoration: underline;" href="index.php">View Tickets</a></li></li>
	<li><a href="submit.php">Create Ticket</a></li>
	<li><a href="profile.php">Profile</a></li>
	<li><a href="logout.php">Log Out</a></li>
	<?php if($_SESSION['userlevel'] == 3) echo '<li><a href="admin.php">Admin</a></li>'; ?>
</ul>
</nav>


<div class="main">
<div id="spacer">
<div class="blacksmall">View Ticket</div>
</div>
<div id="optionbar">
	<table id="ticketinfo">
		<tr><td class="infolabel">Ticket ID:</td><td class="infordata"><?php echo $tid; ?></td><td class="infolabel">Department:</td><td class="infordata"><?php if(isset($ticketdetails['department'])) echo $ticketdetails['department']; ?></td><td class="infolabel">Created:</td><td class="infordata"><?php if(isset($ticketdetails['tstamp'])) echo time_ago($ticketdetails['tstamp']); ?></td></tr>
		<tr><td class="infolabel">Status:</td><td class="infordata <?php if(isset($ticketdetails['status'])) echo $ticketdetails['status']; ?>"><?php if(isset($ticketdetails['status'])) echo $ticketdetails['status']; ?></td><td class="infolabel">Product:</td><td class="infordata"><?php if(isset($ticketdetails['product'])) echo $ticketdetails['product']; ?></td><td class="infolabel">Last reply:</td><td class="infordata"><?php if(isset($ticketdetails['edittstamp'])) echo time_ago($ticketdetails['edittstamp']) ?></td></tr>
	</table>
</div>

<?php 
	if(isset($error)) {
		echo '<div class="notification error">' . $error . '</div>';
	} else if(isset($newreplyerror)) {
		echo '<div class="notification error">' . $newreplyerror . '</div>';
	} else {
		echo '<div id="tickettext">
			  	<div id="subjectline">Subject: ' . htmlspecialchars($ticketdetails['subject'], ENT_QUOTES) . '</div>
			  	<span class="nametext">' . htmlspecialchars($userdetails['name'], ENT_QUOTES) . '</span>
			  	<span class="text">' . nl2br(htmlspecialchars($ticketdetails['message'], ENT_QUOTES)) . '</span>
			  </div>';

		if(!isset($replyerror)) {
			foreach($getreplies as $rows) {
				if($rows['userlevel'] == 1) {
					$userlevel = "userreply";
				} else $userlevel = "staffreply";

				echo '<div class="' . $userlevel . '">
				<span class="nametext">' . htmlspecialchars($rows['name'], ENT_QUOTES) . '</span>
				<span class="datetext">' . time_ago(htmlspecialchars($rows['tstamp'], ENT_QUOTES)) . '</span>
				<span class="text">' . nl2br(htmlspecialchars($rows['message'], ENT_QUOTES)) . '</span>
				</div>';
			}		
		}

		if($_SESSION['userlevel'] == 1 && $ticketdetails['status'] == 'Closed') {
			echo '<div style="clear: both;" class="notification success">The ticket has been closed.</div>';
		} else {
			echo '<div class="replybox">
			<form action="view.php?tid=' . $tid . '" method="post">
			<div id="replytext">Reply:</div>
				<textarea name="message" class="replyinput" required></textarea><br />
				<input style="margin-left: 30px;" class="submitbutton" type="submit" value="Submit"/>
			</form>
			</div>';
		}

		if($admin) {
			echo '<br /><div class="admintools"><form action="view.php?tid=' . $tid . '" method="post">
			<span style="display: block; float: left;" class="blacksmallbold">Admin tools: </span>
			<input style="float: right; margin-left: 10px;" class="submitbutton" name="action" type="submit" value="Delete"/>';
			
				if($ticketdetails['status'] == 'Open' || $ticketdetails['status'] == 'Pending') {
				echo '<input style="float: right; margin-left: 10px;" class="submitbutton" name="action" type="submit" value="Close"/>';
				} else {
					echo '<input style="float: right; margin-left: 10px;" class="submitbutton" name="action" type="submit" value="Open"/>';
				}
			echo '</form></div>';
		}
}
?>
</div>

</body>
</html>