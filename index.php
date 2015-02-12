<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		require_once('/common/meta.php');
	?>
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
			  <a class="navbar-brand" href="/">DownloadMii</a>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse-main">
			  <ul class="nav navbar-nav navbar-right">
				<li><a href="/apps">BROWSE APPS</a></li>
				<li><a href="/blog">BLOG</a></li>
				<li><a href="/about">ABOUT</a></li>
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
				<div class="animated bounceInDown"><img class="Product" src="img/Logo.png" alt="DownloadMii logo" /></div>
				<p class="lead">
					DownloadMii is an online marketplace for Nintendo 3DS homebrew applications and its 100% free of charge for the end-user. It is currently under development, and the GitHub project can be found <a href="https://github.com/DownloadMii/DownloadMii">here</a>!<br />
					You can read about the latest news, 3DS news and app releases/showcases <a href="https://www.downloadmii.com/blog/">here</a>.<br />
					To use DownloadMii, you will need a way to run .3dsx files on your Nintendo 3DS. The .cia format for the main application in the future,<br />
					However DownloadMii will still only support downloading .3dsx applications.<br />
					<br />
					Are you a Developer? Remember:<br />
					We <span class="glyphicon glyphicon-heart" aria-hidden="true" style="color: red;"></span> Developers!
				</p>
				<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/DownloadMii" data-widget-id="565828916062212097">Tweets av @DownloadMii</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				<h1 class="SEO">DownloadMii</h1>
				<h2 class="SEO">Download free 3DS homebrew applications</h2>
			  </div>
			</div>
		  </div>
		</div>
		<!-- /HOME -->
		
		<!-- SEO -->
		
		<!-- /SEO -->
		<div>
<?php
	require_once('/common/uifooter.php');
?>
