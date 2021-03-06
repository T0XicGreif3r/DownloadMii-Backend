<?php
	/*
		DownloadMii Page Header
		This file automatically includes user.php, which includes functions.php
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\user.php');
	header('Cache-Control: private');
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			require_once($_SERVER['DOCUMENT_ROOT'] . '\common\meta.php');
		?>
		<title><?php if (isset($title)) echo $title . ' - '; ?>DownloadMii</title>
		<style>
			header {
				margin-bottom: 75px;
			}
			
			#maincontent {
				margin-left: auto;
				margin-right: auto;
				max-width: 1060px;
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
					<li><a href="/about">ABOUT</a></li>
					<li><a data-scroll href="#DOWNLOADwp">DOWNLOAD</a></li>
					<li><a href="/donate">DONATE</a></li>
					  <li class="dropdown">
				        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> 
						<?php
							if (clientLoggedIn()) {
								$displayNotificationInfo = (!isset($printNotificationsInHeader) || $printNotificationsInHeader) && $unreadNotificationCount > 0;
								
								echo strtoupper($_SESSION['user_nick']);
								if ($displayNotificationInfo) {
									echo ' <span class="badge">!</span>';
								}
							}
							else {
								echo 'ACCOUNT';
							}
						?>
						
						<span class="caret"></span></a>
				        <ul class="dropdown-menu" role="menu">
						<?php
							if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])) {
						?>
						<li><a href="/secure/myapps/">MY APPS</a></li>
						<li><a href="/secure/publish/">SUBMIT APP</a></li>
						<li role="presentation" class="divider"></li>
						
						<?php
							// ** START NOTIFICATIONS **
							if ($displayNotificationInfo) {
						?>
						<?php
							foreach ($unreadNotificationSummaries as $notification) {
								echo '<li><a href="/secure/notifications/"><strong>' . $notification . '</strong></a></li>';
							}
						?>
						<li><a href="/secure/notifications/">ALL NOTIFICATIONS <span class="badge"><?php echo $unreadNotificationCount; ?></span></a></li>
						<?php
							}
							else {
						?>
						<li><a href="/secure/notifications/">NOTIFICATIONS</a></li>
						
						<?php
							}
							// ** END NOTIFICATIONS **
							
							if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token']) && clientPartOfGroup('Moderators')) {
						?>
						<li role="presentation" class="divider"></li>
						<li><a href="/secure/mod/">MOD CP</a></li>
						<?php
							}
						?>
						<?php
							if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token']) && clientPartOfGroup('Administrators')) {
						?>
						<li><a href="/secure/admin/">ADMIN CP</a></li>
						<?php
							}
						?>
						
						<li role="presentation" class="divider"></li>
						<li><a href="/secure/signout/">LOGOUT</a></li>
						<?php
							}
							else {
						?>
						<li><a href="/secure/login/">LOGIN</a></li>
						<li><a href="/secure/register/">REGISTER</a></li>
						<?php
							}
						?>
					</ul>
				  </li>
				  </ul>
				</div>
			  </div>
			</nav>
		</header>
		<div id="content">
			<div id="maincontent">
