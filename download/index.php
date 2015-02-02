<!DOCTYPE html>

<html>
<head runat="server">
	<?php
		require_once('../common/meta.php');
	?>
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
				<li><a href="/apps">BROWSE APPS</a></li>
				<li><a href="/blog">BLOG</a></li>
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
						<h4 class="font-white">1.0.5.4</h4>
						<p><a class="btn btn-lg btn-flat" href="https://github.com/DownloadMii/DownloadMii/releases/download/1.0.5.4/1054.zip" role="button">Download</a></p>
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

