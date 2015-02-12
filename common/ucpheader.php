<?php
	/*
		DownloadMii UCP Header
		This file automatically includes uiheader.php, which includes user.php and functions.php
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\user.php');
	
	if (!(isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token']))) {
		if (strcasecmp($_SERVER['REQUEST_URI'], '/secure/login/') && strcasecmp($_SERVER['REQUEST_URI'], '/secure/register/')) {
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/secure/login/'); //Redirect to login page if logged out and not there already
		}
	}
	else if (!strcasecmp($_SERVER['REQUEST_URI'], '/secure/login/') || !strcasecmp($_SERVER['REQUEST_URI'], '/secure/register/')) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/secure/myapps/'); //Redirect to "my apps" page if trying to access login page while logged in
	}
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\uiheader.php');
?>