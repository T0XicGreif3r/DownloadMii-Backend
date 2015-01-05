<?php
	/*
		DownloadMii Publishing Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	require_once('../common/recaptchalib.php');
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['publish_token']), 422); //Check if session publishing token is set
	$publishToken = $_SESSION['publish_token'];
	unset($_SESSION['publish_token']);
	
	sendResponseCodeAndExitIfTrue(!(isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])), 403); //Check if logged in
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['name'], $_POST['version'], $_POST['category'], $_POST['description'], $_POST['3dsx'], $_POST['smdh'], $_POST["g-recaptcha-response"], $_POST['publishtoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue($publishToken !== md5(getConfigValue('salt_token') . $_POST['publishtoken']), 422); //Check if POST publishing token is correct
	sendResponseCodeAndExitIfTrue(!is_numeric($_POST['category']), 422); //Check if category selected
	
	//Check captcha
	$reCaptcha = new ReCaptcha(getConfigValue('apikey_recaptcha_secret'));
	$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
	printAndExitIfTrue($resp == null || !$resp->success, 'Invalid or no captcha response.');
	
	$appName = htmlspecialchars($_POST['name']);
	$appVersion = htmlspecialchars($_POST['version']);
	$appCategory = htmlspecialchars($_POST['category']);
	$appDescription = htmlspecialchars($_POST['description']);
	
	if (isset($_SESSION['user_app_guid'])) {
		//TODO: Check that if one of 3dsx, smdh, version is changed, the others also are.
		//TODO: Do everything else necessary to update app
	}
	
	//TODO: Handle file uploads, database INSERT/UPDATE
	
	print('Your application has been submitted and is now waiting approval from our staff.');
?>