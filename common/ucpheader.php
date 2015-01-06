<?php
	/*
		TEST/TEMPORARY
		This file automatically includes user.php and functions.php
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\user.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	
	if (!(isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token']))) {
		if (strcasecmp($_SERVER['REQUEST_URI'], '/secure/login.php') && strcasecmp($_SERVER['REQUEST_URI'], '/secure/register.php')) {
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/secure/login.php'); //Redirect to login page if logged out and not there already
		}
	}
	else if (!strcasecmp($_SERVER['REQUEST_URI'], '/secure/login.php') || !strcasecmp($_SERVER['REQUEST_URI'], '/secure/register.php')) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/secure/myapps.php'); //Redirect to "my apps" page if trying to access login page while logged in
	}
?>
<html>
	<head>
		<title>DownloadMii - User CP</title>
		<link href="/css/bootstrap.css" rel="stylesheet"/>
		<link href="/css/mainStruct.css" rel="stylesheet"/>
		<style>
			header {
				margin-bottom: 75px;
			}
			
			#maincontent {
				margin-left: auto;
				margin-right: auto;
				max-width: 1200px;
			}
			
			.downloadmii {
				width: 100%;
				height: 300px;
			}
			
			.small-width {
				max-width: 400px;
				margin-left: auto;
				margin-right: auto;
			}
			
			.no-border-radius {
				border-radius: 0;
			}
			
			.no-bottom-border-radius {
				border-bottom-left-radius: 0; border-bottom-right-radius: 0;
			}
			
			.no-top-border-radius {
				border-top-left-radius: 0; border-top-right-radius: 0;
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
				  <a class="navbar-brand" href="/">DownloadMii</a>
				</div>
				<div class="collapse navbar-collapse" id="navbar-collapse-main">
				  <ul class="nav navbar-nav navbar-right">
					<li><a data-scroll href="/#HOMEwp">HOME</a></li>
					<li><a href="/donate">DONATE</a></li>
					<?php
						if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])) {
					?>
					<li><a href="/secure/myapps.php">MY APPS</a></li>
					<li><a href="/secure/publish.php">SUBMIT APP</a></li>
					<li><a href="/secure/action_signout.php">LOGOUT</a></li>
					<?php
						}
						else {
					?>
					<li><a href="/secure/login.php">LOGIN</a></li>
					<li><a href="/secure/register.php">REGISTER</a></li>
					<?php
						}
					?>
				  </ul>
				</div>
			  </div>
			</nav>
		</header>
		<div id="content">
			<div id="maincontent">
