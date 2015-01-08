<?php
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	$_SESSION['mod_token'] = uniqid(mt_rand(), true); //Generate token for moderator action
	
	echo 'ToDo<br />';
?>
