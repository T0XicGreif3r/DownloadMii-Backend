<?php
	/*
		DownloadMii User Handler
		This file automatically includes functions.php
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	
	session_start();
	
	$now = time();
	
	if (isset($_SESSION['last_active']) && ($now > $_SESSION['last_active'] + 60 * 60)) { //If the user has been inactive for 1 hour...
		//...their session expires
		session_unset();
		session_destroy();
	}
	
	if (isset($_SESSION['user_id'], $_SESSION['user_token'])) {
		$mysqlConn = connectToDatabase();
		$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT nick, role FROM users WHERE userId = ? AND token = ? LIMIT 1', 'ss', [$_SESSION['user_id'], $_SESSION['user_token']]);
		
		if (count($matchingUsers) === 1) {
			$_SESSION['user_nick'] = $matchingUsers[0]['nick'];
			$_SESSION['user_role'] = $matchingUsers[0]['role'];
		}
		else {
			session_unset();
			session_destroy();
		}
		
		$mysqlConn->close();
	}
	
	
	$_SESSION['last_active'] = $now; //Set last activity time to now
?>