<?php
	/*
		DownloadMii App Remove Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['remove_token']), 422); //Check if session app remove token is set
	$removeToken = md5($_SESSION['remove_token']);
	unset($_SESSION['remove_token']);
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['pass'], $_POST['removetoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue($removeToken !== $_POST['removetoken'], 422); //Check if POST login token is correct
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['user_app_guid']), 422); //Check if GUID of app to remove is set
	
	printAndExitIfTrue(mb_substr($_POST['pass'], -1) !== '!', 'No exclamation mark entered at the end of the password.'); //Check if question mark was entered
	
	$tryUserPass = mb_substr($_POST['pass'], 0, -1);
	
	$mysqlConn = connectToDatabase();
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT password FROM users WHERE userId = ? LIMIT 1', 's', [$_SESSION['user_id']]);
	
	$user = $matchingUsers[0];
	printAndExitIfTrue(crypt($tryUserPass, $user['password']) != $user['password'], 'Invalid password.'); //Check if password is correct
	
	//Check if app not hidden already
	$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT publishstate FROM apps WHERE guid = ?', 's', [$_SESSION['user_app_guid']]);
	printAndExitIfTrue($matchingApps[0]['publishstate'] === 3, 'This app is already hidden.');
	
	executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET publishstate = 3 WHERE guid = ? LIMIT 1', 's', [$_SESSION['user_app_guid']]); //Update publish state in database
	$mysqlConn->close();
	
	//TODO: Actually remove the apps in the future?
	
	print('App hidden.');
?>