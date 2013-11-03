<?php
/*
 * Author: Sari Haj Hussein
 */
session_start();
$_SESSION = array();
session_destroy();

header('Location: index.php');
?>