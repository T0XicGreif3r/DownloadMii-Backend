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
	<link href="css/bootstrap.css" rel="stylesheet"/>
	<link href="css/widgets.css" rel="stylesheet"/>
	<link href="css/blog.css" rel="stylesheet"/>
	<link href="css/social.css" rel="stylesheet"/>
    <link href="css/mainStruct.css" rel="stylesheet"/>
	<link href="css/sidebar.css" rel="stylesheet">

	<!-- IN CASE OF IE 9 -->
	<!--[if lt IE 9]> 
 		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
 	<![endif]-->
	
	<!-- PLATFORM SPECIFIC -->
		<!-- MICROSOFT -->
		<meta name="application-name" content="DownloadMii" />
		<link rel="dns-prefetch" href="http://filfatstudios.com/"/>
		<link rel="prerender" href="http://filfatstudios.com/" />
		
		<!-- IE SITE PIN -->
		<meta name="msapplication-starturl" content="http://www.downloadmii.com" />
		<meta name="msapplication-navbutton-color" content="#1009FFF" />
		<meta name="msapplication-window" content="width=1024;height=768" />
		<meta name="msapplication-tooltip" content="Visit DownloadMii" />
        <meta content="name=Home;action-uri=./index.html;icon-uri=./favicon.ico" name="msapplication-task" />
		<meta content="name=User CP;action-uri=./secure/myapps.php;icon-uri=./favicon.ico" name="msapplication-task" />


		<!-- LIVE TILE -->
		<meta name="msapplication-TileColor" content="#009FFF" />
		<meta name="msapplication-square70x70logo" content="/img/LiveTiles/smalltile.png" />
		<meta name="msapplication-square150x150logo" content="/img/LiveTiles/mediumtile.png" />
		<meta name="msapplication-wide310x150logo" content="/img/LiveTiles/widetile.png" />
		<meta name="msapplication-square310x310logo" content="/img/LiveTiles/largetile.png" />
		
		<!-- APPLE -->
		<link rel="apple-touch-icon" href="/img/Apple/touch-icon-iphone.png" /> 
        <link rel="apple-touch-icon" size="76x76" href="/img/Apple/touch-icon-ipad.png" /> 
        <link rel="apple-touch-icon" size="120x120" href="/img/Apple/touch-icon-iphone-retina.png" />
        <link rel="apple-touch-icon" size="152x152" href="/img/Apple/touch-icon-ipad-retina.png" />
		<link rel="apple-touch-startup-image" href="/img/Apple/startup.png" />
		<meta name="apple-mobile-web-app-status-bar-style" content="white" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-title" content="DownloadMii"/>
		
		<!-- ANDROID -->
		<link rel="icon" size="128x128" href="/img/Apple/touch-icon-iphone-retina.png" />

    <title>DownloadMii</title>
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
			  <a class="navbar-brand" href="#">DownloadMii</a>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse-main">
			  <ul class="nav navbar-nav navbar-right">
				<li><a data-scroll href="#HOMEwp">HOME</a></li>
				<!--li><a data-scroll href="#ABOUTwp">ABOUT</a></li-->
				<li><a data-scroll href="#DOWNLOADwp">DOWNLOAD</a></li>
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
				<img class="Product" src="img/Logo.png" alt="" />
				<p class="lead" style="margin-top:75px;">
					DownloadMii is an Online marketplace for Nintendo 3DS homebrew applications and its 100% free of charge for the end-user. It is currently under development, and the Github project can be found <a href="https://github.com/DownloadMii/DownloadMii">here</a>!<br />
					We expect to deliver the first consumer release of DownloadMii late January or early February 2015.<br />
					We will also in the future add an blog so you can stay up-to-date with the latest store news and such, stay tuned!
				</p>
			  </div>
			</div>
		  </div>
		</div>
		<!-- /HOME -->
<?php
	require_once('/common/ucpfooter.php');
?>
