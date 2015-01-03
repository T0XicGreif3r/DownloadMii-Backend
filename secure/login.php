<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	$token = generateRandomString();
	$_SESSION['login_token'] = md5(getConfigValue('salt_token') . $token);
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" manifest="/Manifest.ashx">
<head runat="server">
	<!-- SEO -->
	<meta name="description" content="DownloadMii is a online marketplcae for Homebrew applications" />
    <meta name="keywords" content="Nintendo 3DS Homebrew, Homebrew Browser, 3ds, filfat, Wii U Homebrew" />
	<meta name="AUTHOR" CONTENT="DownloadMii Team">
	<meta name="COPYRIGHT" CONTENT="&copy; 2013-2015 Filiph Sandström && DownloadMii Team">
	<meta name="google-site-verification" content="-" />
	<meta name="msvalidate.01" content="-" />
    <meta charset="utf-8" />
	
	<!-- METADATA -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=1,chrome=edge,safari=edge">
	<link rel="shortcut icon" type="image/ico" href="favicon.ico" />
	
	<!-- STYLESHEETS -->
	<link href="css/bootstrap.css" rel="stylesheet"/>
	<link href="css/widgets.css" rel="stylesheet"/>
	<link href="css/blog.css" rel="stylesheet"/>
	<link href="css/social.css" rel="stylesheet"/>
    <link href="css/mainStruct.css" rel="stylesheet"/>
	<link href="css/sidebar.css" rel="stylesheet">
	
	<!-- SCRIPTS  -->
	<script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
	<script src="js/jquery.scrollUp.js"></script>
	
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
		<meta name="msapplication-starturl" content="http://downloadmii.filfatstudios.com" />
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

    <title>DownloadMii - Login</title>
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
				<li><a href="/">HOME</a></li>
				<li><a href="/donate">DONATE</a></li>
				<li><a href="/secure/login.php">User CP</a></li> <!-- ToDo: redirect to user CP instead of login-->
			  </ul>
			</div>
		  </div>
		</nav>
	</header>
	<div id="content">
		<!-- LOGIN -->
		<div id="FULLSCREEN" class="pad-section">
		  <div class="container">
			<div class="row">
				<form action="action_login.php" method="post" accept-charset="utf-8">
				<input type="text" name="user" size="40" required>
				<input type="password" name="pass" size="40" required>
				<input type="hidden" name="logintoken" value="<?php echo $token; ?>">
				<input type="submit" name="submit" value="Login">
				</form>
			</div>
		  </div>
		  <div class="WayPoint" id="DOWNLOADwp"></div>
		</div>
		<!-- /LOGIN -->

	</div>
	<footer>
		<div class="content">
			&copy;filfat Studio's 2014-2015; Website designed by <a href="http://www.filfatstudios.com">filfat Studio's</a>
		</div>
	</footer>
	
	<!-- SCRIPTS  -->
	<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
	<script src="js/smooth-scroll.js"></script>
	<script>
    	smoothScroll.init({
	    speed: 500, // Integer. How fast to complete the scroll in milliseconds
	    easing: 'easeInOutQuint', // Easing pattern to use
	    updateURL: false
	});
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip();
	})
	$("[rel=tooltip]").tooltip({html:true});
	</script>
	
</body>
</html>
