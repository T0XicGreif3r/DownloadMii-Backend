<?php
	/*
		DownloadMii Signout Handler
	*/
	
	session_start();

	//Destroy session
	session_unset();
	session_destroy();
	
	//Redirect to login page
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/secure/login.php');
?>