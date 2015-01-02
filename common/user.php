<?php
	/*
		DownloadMii User Handler
	*/
	
	session_start();
	
	$now = time();
	
	if (isset($_SESSION['last_active']) && ($now > $_SESSION['last_active'] + 60 * 60)) { //If the user has been inactive for 1 hour...
		//...their session expires
		session_unset();
		session_destroy();
	}
	
	$_SESSION['last_active'] = $now; //Set last activity time to now
?>