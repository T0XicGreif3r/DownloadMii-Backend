<?php
	/*
		DownloadMii Page Header
		This file automatically includes user.php, which includes functions.php
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\user.php');
	header('Cache-Control: private');
?><!DOCTYPE html>
<html>
	<head>
		<?php
			require_once($_SERVER['DOCUMENT_ROOT'] . '\common\meta.php');
		?>
		<title><?php if (isset($title)) echo $title . ' - '; ?> DownloadMii</title>
		<style>
			header {
				margin-bottom: 75px;
			}
			
			#maincontent {
				margin-left: auto;
				margin-right: auto;
				max-width: 1060px;
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
			
			.small-text {
				font-size: 10pt;
			}
			
			.app-icon {
				width: 48px;
				height: 48px;
				margin-right: 12px;
			}
			
			.app-screenshot {
				width: 400px;
				height: auto;
				margin-top: 32px;
			}
			
			.app-vertical-center-outer {
				height: auto;
				display: table;
			}
			
			.app-vertical-center-inner {
				height: 48px;
				display: table-cell;
				vertical-align: middle;
			}
			
			.app-screenshot-vertical-center {
				float: none;
				display: inline-block;
				vertical-align: middle;
			}
			
			.app-vertical-center-inner {
				height: 48px;
			}
			
			.clear-float {
				clear: both;
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
				  	<li><a href="/apps">BROWSE APPS</a></li>
					<li><a href="/blog">BLOG</a></li>
					<?php
						if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])) {
					?>
					<li><a href="/secure/myapps.php">MY APPS</a></li>
					<li><a href="/secure/publish.php">SUBMIT APP</a></li>
					<?php
						if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token']) && $_SESSION['user_role'] >= 3) {
					?>
					<li><a href="/secure/mod_apps.php">MOD CP</a></li>
					<?php
						}
					?>
					<?php
						if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token']) && $_SESSION['user_role'] >= 4) {
					?>
					<li><a href="/secure/admin.php">ADMIN CP</a></li>
					<?php
						}
					?>
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
