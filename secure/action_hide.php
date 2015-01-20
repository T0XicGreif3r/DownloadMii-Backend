<?php
	/*
		DownloadMii App Remove Handler
	*/
	
	require_once('../common/user.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_POST['guidid']), 400);
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['hide_app_guid' . $_POST['guidid']]), 422); //Check if GUID of app to remove is set
	$guid = $_SESSION['hide_app_guid' . $_POST['guidid']]; //Get GUID
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['remove_token' . $guid]), 422); //Check if session app remove token is set
	$removeToken = $_SESSION['remove_token' . $guid];
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['pass'], $_POST['removetoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue(md5($removeToken) !== $_POST['removetoken'], 422); //Check if POST login token is correct
	
	printAndExitIfTrue(mb_substr($_POST['pass'], -1) !== '!', 'No exclamation mark entered at the end of the password.'); //Check if question mark was entered
	
	$tryUserPass = mb_substr($_POST['pass'], 0, -1);
	
	$mysqlConn = connectToDatabase();
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT password FROM users WHERE userId = ? LIMIT 1', 's', [$_SESSION['user_id']]);
	
	$user = $matchingUsers[0];
	printAndExitIfTrue(crypt($tryUserPass, $user['password']) !== $user['password'], 'Invalid password.'); //Check if password is correct
	
	//Check if app not hidden already
	$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT publishstate FROM apps WHERE guid = ?', 's', [$guid]);
	
	printAndExitIfTrue($matchingApps[0]['publishstate'] === 2 || $matchingApps[0]['publishstate'] === 3, 'This app is rejected or already hidden.');
	
	executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET publishstate = 3 WHERE guid = ? LIMIT 1', 's', [$guid]); //Update publish state in database
	$mysqlConn->close();
	
	unset($_SESSION['myapps_token' . $guid]);
	unset($_SESSION['remove_token' . $guid]);
	unset($_SESSION['hide_app_guid' . $_POST['guidid']]);
	
	//TODO: Actually remove the apps in the future?
	
	print('App hidden.');
?>