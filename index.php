<!DOCTYPE html>

<html>
<head runat="server">
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
