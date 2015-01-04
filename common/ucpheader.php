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
		<header>
			<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			  <div class="container-fluid">
				<div class="navbar-header">
				  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-main">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </button>
				  <a class="navbar-brand" href="#">DownloadMii</a>
				</div>
				<div class="collapse navbar-collapse" id="navbar-collapse-main">
				  <ul class="nav navbar-nav navbar-right">
					<li><a href="/secure/myapps.php">My Apps</a></li>
					<li><a href="/secure/publish.php">Publish App</a></li>
					<li><a href="/secure/action_signout.php">Log out</a></li>
				  </ul>
				</div>
			  </div>
			</nav>
		</header>
