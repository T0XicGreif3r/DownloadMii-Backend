<!DOCTYPE html>

<html>
<head runat="server">
	<!-- SEO -->
	<meta name="description" content="DownloadMii is an online marketplcae for Homebrew applications" />
    <meta name="keywords" content="Nintendo 3DS Homebrew, Homebrew Browser, 3ds, filfat, Wii U Homebrew" />
	<meta name="AUTHOR" CONTENT="DownloadMii Team">
	<meta name="COPYRIGHT" CONTENT="&copy; 2013-2015 Filiph Sandström && DownloadMii Team">
	<meta name="google-site-verification" content="-" />
	<meta name="msvalidate.01" content="-" />
    <meta charset="utf-8" />
	
	<!-- METADATA -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=1,chrome=edge,safari=edge">
	<link rel="shortcut icon" type="image/ico" href="https://www.downloadmii.com/favicon.ico" />
	
	<!-- STYLESHEETS -->
	<link href="/css/bootstrap.css" rel="stylesheet"/>
    	<link href="/css/mainStruct.css" rel="stylesheet"/>
	
	<!-- IN CASE OF IE 9 -->
	<!--[if lt IE 9]> 
 		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
 	<![endif]-->
	
	<!-- PLATFORM SPECIFIC -->
		<!-- MICROSOFT -->
		<meta name="application-name" content="DownloadMii" />
		<link rel="dns-prefetch" href="http://filfatstudios.com/"/>
		<link rel="prerender" href="http://filfatstudios.com" />
		
		<!-- IE SITE PIN -->
		<meta name="msapplication-starturl" content="http://www.downloadmii.com" />
		<meta name="msapplication-navbutton-color" content="#1009FFF" />
		<meta name="msapplication-window" content="width=1024;height=768" />
		<meta name="msapplication-tooltip" content="Visit DownloadMii" />
        <meta content="name=Home;action-uri=./index.html;icon-uri=./favicon.ico" name="msapplication-task" />


		<!-- LIVE TILE -->
		<meta name="msapplication-TileColor" content="#009FFF" />
		<meta name="msapplication-square70x70logo" content="/LiveTiles/smalltile.png" />
		<meta name="msapplication-square150x150logo" content="/LiveTiles/mediumtile.png" />
		<meta name="msapplication-wide310x150logo" content="/LiveTiles/widetile.png" />
		<meta name="msapplication-square310x310logo" content="/LiveTiles/largetile.png" />
		
		<!-- APPLE -->
		<link rel="apple-touch-icon" href="/img/Apple/touch-icon-iphone.png" /> 
        <link rel="apple-touch-icon" sizes="76x76" href="/img/Apple/touch-icon-ipad.png" /> 
        <link rel="apple-touch-icon" sizes="120x120" href="/img/Apple/touch-icon-iphone-retina.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="/img/Apple/touch-icon-ipad-retina.png" />
		<link rel="apple-touch-startup-image" href="/img/Apple/startup.png" />
		<meta name="apple-mobile-web-app-status-bar-style" content="white" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-title" content="Company Name" />
		
		<!-- ANDROID -->
		<link rel="icon" sizes="128x128" href="/img/Apple/touch-icon-iphone-retina.png" />

    <title>DownloadMii - Download</title>
</head>
<body>
	<div class="WayPoint" id="HOMEwp"></div>
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
				<li><a href="/#HOMEwp">HOME</a></li>
				<li><a href="/donate">DONATE</a></li>
				<li><a href="/secure/myapps.php">USER CP</a></li> <!-- ToDo: redirect to user CP instead of "my apps" list -->
			  </ul>
			</div>
		  </div>
		</nav>
	</header>
	<div id="content">
		<!-- HOME -->
		<div id="HOME" class="pad-section">
		  <div class="container">
			<div class="row">
			  <div class="col-sm-12 text-center">
			  	<h1 class="text-center">Download</h1>
				<br />
				<div class="row text-center">
				  <div class="row">
					  <div class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
					  	<h2 class="font-white">Latest Release</h2>
						<h4 class="font-white">Coming Soon!</h4>
						<p><a class="btn btn-lg btn-flat disabled" href="/releases/release.zip" role="button">Download</a></p>
					  </div>
					  <div class="col-sm-2 col-xs-0"></div>
					  <div class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
					  	<h2 class="font-white">Latest Beta</h2>
						<h4 class="font-white">Coming Soon!</h4>
						<p><a class="btn btn-lg btn-flat disabled" href="/download/#beta" role="button">Download</a></p>
					  </div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		<!-- /HOME -->
<?php
	require_once('../common/ucpfooter.php');
?>

