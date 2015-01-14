<html>
	<head>
		<?php
			require_once('../common/meta.php');
		?>
		<title>DownloadMii - Donate</title>
		<style>
			body{
				overflow-x:hidden;
				-ms-overflow-x: hidden;
				max-width: 100%;
			}
			header{
				margin-bottom: 75px;
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
					<li><a href="/">HOME</a></li>
					<!--li><a data-scroll href="#ABOUTwp">ABOUT</a></li-->
					<li><a data-scroll href="/#DOWNLOADwp">DOWNLOAD</a></li>
					<li><a href="/donate">DONATE</a></li>
					<li><a href="/secure/myapps.php">USER CP</a></li> <!-- ToDo: redirect to user CP instead of "my apps" list -->
				  </ul>
				</div>
			  </div>
			</nav>
		</header>
		<div class="text-center">
			<h1>Donate</h1>
			<br />
			<div class="row text-center">
			  <div class="row">
				  <div class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
					<h2 class="font-white">Girtpay</h2>
					<h4 class="font-white">(recommended)<h4>
					<script data-gratipay-username="filfat" src="//grtp.co/v1.js"></script>
				  </div>
				  <div class="col-sm-2 col-xs-0"></div>
				  <div class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
					<h2 class="font-white">PayPal</h2>
					<br />
					<br />
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_donations">
						<input type="hidden" name="business" value="filfat@hotmail.se">
						<input type="hidden" name="lc" value="US">
						<input type="hidden" name="no_note" value="0">
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				  </div>
			  </div>
			</div>
			<p>Keeping the server up costs quite a bit, so does also the time we spend<br />
			   developing this application. If you like DownloadMii consider donating<br />
			   to help us keep DownloadMii alive! Thanks :)</p>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>