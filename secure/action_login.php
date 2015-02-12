<?php
	/*
		DownloadMii Login Handler
	*/
	
	require_once('../common/user.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['login_token']), 422); //Check if session login token is set
	$userToken = $_SESSION['login_token'];
	unset($_SESSION['login_token']);
	
	printAndExitIfTrue(clientLoggedIn(), 'You are already logged in.'); //Check if already logged in
	sendResponseCodeAndExitIfTrue(!(isset($_POST['user'], $_POST['pass'], $_POST['logintoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue(md5($userToken) !== $_POST['logintoken'], 422); //Check if POST login token is correct
	
	$tryUserName = $_POST['user'];
	$tryUserPass = $_POST['pass'];
	
	$mysqlConn = connectToDatabase();
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT userId, password, nick, role FROM users WHERE LOWER(nick) = LOWER(?) LIMIT 1', 's', [$tryUserName]);
	
	printAndExitIfTrue(count($matchingUsers) != 1, 'Invalid username and/or password.'); //Check if there is one user matching attempted username
	
	$user = $matchingUsers[0];
	printAndExitIfTrue(crypt($tryUserPass, $user['password']) !== $user['password'], 'Invalid username and/or password.'); //Check if password is correct
	
	$tokenSha1 = sha1($userToken);
	executePreparedSQLQuery($mysqlConn, 'UPDATE users SET token = ? WHERE userId = ? LIMIT 1', 'ss', [$tokenSha1, $user['userId']]); //Update user token in database
	$mysqlConn->close();
	
	$_SESSION['user_id'] = $user['userId'];
	$_SESSION['user_nick'] = $user['nick'];
	$_SESSION['user_role'] = $user['role'];
	$_SESSION['user_token'] = $tokenSha1;
	
	//Redirect to "my apps" list
	$redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/secure/myapps/';
	header('Location: ' . $redirectUrl);
?>