<?php
	/*
		DownloadMii Login Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['login_token']), 422); //Check if session login token is set
	$sessionToken = $_SESSION['login_token'];
	unset($_SESSION['login_token']);
	
	printAndExitIfTrue(isset($_SESSION['user_id']) && $_SESSION['user_id'], 'You are already logged in.'); //Check if already logged in
	sendResponseCodeAndExitIfTrue(!(isset($_POST['user'], $_POST['pass'], $_POST['logintoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue($sessionToken != md5(getConfigValue('salt_token') . $_POST['logintoken']), 422); //Check if POST login token is correct
	
	$tryUserName = htmlspecialchars($_POST['user']);
	$tryUserPass = htmlspecialchars($_POST['pass']);
	$hashedTryUserPass = crypt($tryUserPass, getConfigValue('salt_password'));
	
	$mysqlConn = connectToDatabase();
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT userId, nick FROM users WHERE nick = ? AND password = ? LIMIT 2', 'ss', [$tryUserName, $hashedTryUserPass]);
	
	printAndExitIfTrue(count($matchingUsers) != 1, 'Invalid username and/or password.'); //Check if there is one user matching attempted user/pass combination
	$user = $matchingUsers[0];
	
	executePreparedSQLQuery($mysqlConn, 'UPDATE users SET token = ? WHERE userId = ? LIMIT 1', 'ss', [$sessionToken, $user['userId']]); //Update user token in database
	$mysqlConn->close();
	
	$_SESSION['user_id'] = $user['userId'];
	$_SESSION['user_nick'] = $user['nick'];
	$_SESSION['user_token'] = $sessionToken;
	
	//Redirect somewhere based on the domain root. If no redirect URL set, redirect to "my apps" list
	$redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
	if (isset($_GET['redirect'])) {
		$redirectUrl .= $_GET['redirect'];
	}
	else {
		$redirectUrl .= 'secure/myapps.php';
	}
	
	header('Location: ' . $redirectUrl);
?>