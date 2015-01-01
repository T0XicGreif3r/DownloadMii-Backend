<?php
	/*
		DownloadMii Login Handler
	*/
	
	include('user.php');
	include('../functions.php');
	
	printAndExitIfTrue(!isset($_SESSION['login_token']), 422); //Check if session login token is set
	$sessionToken = $_SESSION['login_token'];
	unset($_SESSION['login_token']);
	
	printAndExitIfTrue(isset($_SESSION['user_id']) && $_SESSION['user_id'], 'You are already logged in.'); //Check if already logged in
	sendResponseCodeAndExitIfTrue(!(isset($_POST['user'], $_POST['pass'], $_POST['logintoken'], $_POST['submit'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue($sessionToken != md5(getenv('TOKEN_salt') . $_POST['logintoken']), 422); //Check if POST login token is correct
	
	$tryUserName = htmlspecialchars($_POST['user']);
	$tryUserPass = htmlspecialchars($_POST['pass']);
	$hashedTryUserPass = crypt($tryUserPass, getenv('USERLOGIN_salt'));
	
	$mysqlConn = connectToDatabase();
	$queryResponseArr = getArrayFromSQLQuery($mysqlConn, 'SELECT userId, nick, token FROM users WHERE nick = ? AND password = ?', 'ss', [$tryUserName, $hashedTryUserPass]);
	$mysqlConn->close();
	
	printAndExitIfTrue(count($queryResponseArr) != 1, 'Wrong username or password.'); //Check if there is one user matching attempted user/pass combination
	
	$_SESSION['user_id'] = $queryResponseArr[0]->userId;
	$_SESSION['user_nick'] = $queryResponseArr[0]->nick;
	$_SESSION['user_token'] = $queryResponseArr[0]->token;
	
	//Redirect somewhere. If no redirect URL set, redirect to domain root
	$redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
	if (isset($_GET['redirect'])) {
		$redirectUrl .= $_GET['redirect'];
	}
	
	#header('Location: ' . $redirectUrl);
?>