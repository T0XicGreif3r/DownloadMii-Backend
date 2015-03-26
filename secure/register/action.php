<?php
	/*
		DownloadMii Register Handler
	*/
	
	require_once('../../common/user.php');
	require_once('../../common/recaptchalib.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['register_token']), 422); //Check if session register token is set
	$registerToken = $_SESSION['register_token'];
	unset($_SESSION['register_token']);
	
	printAndExitIfTrue(clientLoggedIn(), 'You can\'t register while logged in.'); //Check if already logged in
	sendResponseCodeAndExitIfTrue(!(isset($_POST['user'], $_POST['pass'], $_POST['pass2'], $_POST['email'], $_POST["g-recaptcha-response"], $_POST['registertoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue(md5($registerToken) !== $_POST['registertoken'], 422); //Check if POST register token is correct
	
	//Check username
	printAndExitIfTrue(!preg_match('`^[a-zA-Z0-9_]{1,}$`', $_POST['user']), 'Invalid username.');
	printAndExitIfTrue(mb_strlen($_POST['user']) < 3, 'Username is too short.');
	printAndExitIfTrue(mb_strlen($_POST['user']) > 24, 'Username is too long.'); 
	
	//Check passwords
	printAndExitIfTrue($_POST['pass'] !== $_POST['pass2'], 'Passwords don\'t match.');
	printAndExitIfTrue(mb_strlen($_POST['pass']) < 8, 'Password is too short.');
	
	//Check e-mail
	printAndExitIfTrue(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !checkdnsrr(substr($_POST['email'], strpos($_POST['email'], '@') + 1), 'MX'), 'Invalid email address.');
	printAndExitIfTrue(mb_strlen($_POST['email']) > 255, 'E-mail is too long.');
	
	//Check captcha
	$reCaptcha = new ReCaptcha(getConfigValue('apikey_recaptcha_secret'));
	$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
	printAndExitIfTrue($resp == null || !$resp->success, 'Invalid or no captcha response.');
	
	$tryRegisterName = escapeHTMLChars($_POST['user']);
	$tryRegisterPass = $_POST['pass'];
	$tryRegisterEmail = escapeHTMLChars($_POST['email']);
	$hashedTryRegisterPass = crypt($tryRegisterPass, '$2y$07$' . uniqid(mt_rand(), true));
	
	$mysqlConn = connectToDatabase();
	
	//Check if there are any users with the same nick or email
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT userId FROM users WHERE LOWER(nick) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1', 'ss', [$tryRegisterName, $tryRegisterEmail]);
	printAndExitIfTrue(count($matchingUsers) != 0, 'User with this name and/or email already exists.');
	
	//Insert user into database
	$stmt = executePreparedSQLQuery($mysqlConn, 'INSERT INTO users (nick, password, email, token)
											VALUES (?, ?, ?, ?)', 'ssss', [$tryRegisterName, $hashedTryRegisterPass, $tryRegisterEmail, sha1($registerToken)], true);
	$userId = $stmt->insert_id;
	$stmt->close();
	
	//Insert user group connection
	executePreparedSQLQuery($mysqlConn, 'INSERT INTO groupconnections (userId, groupId)
											VALUES (?, 1)', 'i', [$userId]);
	
	$mysqlConn->close();
	
	print('Register complete.');
?>
