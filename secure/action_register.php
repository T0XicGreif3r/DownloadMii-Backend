<?php
	/*
		DownloadMii Register Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	require_once('../common/recaptchalib.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['register_token']), 422); //Check if session register token is set
	$registerToken = $_SESSION['register_token'];
	unset($_SESSION['register_token']);
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['user'], $_POST['pass'], $_POST['pass2'], $_POST['email'], $_POST["g-recaptcha-response"], $_POST['registertoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue($registerToken !== md5(getConfigValue('salt_token') . $_POST['registertoken']), 422); //Check if POST register token is correct
	
	printAndExitIfTrue($_POST['pass'] !== $_POST['pass2'], 'Passwords don\'t match.'); //Check if passwords match
	printAndExitIfTrue(strlen($_POST['pass']) < 8, 'Password is too short.'); //Check password length
	printAndExitIfTrue(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL), 'Invalid email address.');
	
	//Check captcha
	$reCaptcha = new ReCaptcha(getConfigValue('apikey_recaptcha_secret'));
	$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
	printAndExitIfTrue($resp == null || !$resp->success, 'Invalid or no captcha response.');
	
	$tryRegisterName = htmlspecialchars($_POST['user']);
	$tryRegisterPass = $_POST['pass'];
	$tryRegisterEmail = htmlspecialchars($_POST['email']);
	$hashedTryRegisterPass = crypt($tryRegisterPass, getConfigValue('salt_password'));
	
	$mysqlConn = connectToDatabase();
	
	//Check if there are any users with the same nick or email
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT userId FROM users WHERE LOWER(nick) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1', 'ss', [$tryRegisterName, $tryRegisterEmail]);
	printAndExitIfTrue(count($matchingUsers) != 0, 'User with this name and/or email already exists.');
	
	//Insert user into database
	executePreparedSQLQuery($mysqlConn, 'INSERT INTO users (nick, password, email)
											VALUES (?, ?, ?)', 'sss', [$tryRegisterName, $hashedTryRegisterPass, $tryRegisterEmail]);
	
	$mysqlConn->close();
	
	print('Register complete.');
?>