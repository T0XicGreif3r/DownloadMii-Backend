<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\user.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	
	//Redirect to login page
	if (!(isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])) && strcasecmp($_SERVER['REQUEST_URI'], '/secure/login.php') ) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/secure/login.php');
	}
?>
<html>
	<head>
		<title>DownloadMii User CP</title>
		<link href="/css/bootstrap.css" rel="stylesheet"/>
		<link href="/css/mainStruct.css" rel="stylesheet"/>
		<style>
			body {
				margin-top: 24px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					<a class="navbar-brand" href="/">DownloadMii</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li><a href="/secure/myapps.php">My Apps</a></li>
						<li><a href="/secure/publish.php">Publish App</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="/secure/action_signout.php">Log out</a></li>
					</ul>
				</div>
			</nav>
