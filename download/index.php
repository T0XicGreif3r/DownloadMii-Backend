<!DOCTYPE html>
<?php
	/*
		DownloadMii Terms of Use page
	*/
	$title = 'Download';
	require_once('../common/uiheader.php');
?>
<div id="content">
<!-- HOME -->
<div id="HOME" class="pad-section">
  <div class="container">
	<div class="row">
	  <div class="col-sm-12 text-center">
	  	<h1 class="animated bounceInDown text-center">Download</h1>
		<br />
		<div class="animated bounceInLeft text-center">
			<p>Hi! We are glad to see that you are interested in DownloadMii.<br />
			Before you download DownloadMii, take a while and figure out which version is right for you.<br />
			We recommend the more stable version (release), however if you want to be bad-ass and try the lates and greatest festured pick the beta release :)</p><br />
		</div>
		<br />
		<div class="animated bounceInRight row text-center">
		  <div class="row">
			  <div id="release" class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
			  	<h2 class="font-white">Latest Release</h2>
				<h4 class="font-white">1.0.5.8</h4>
				<p><a class="btn btn-lg btn-flat" href="https://github.com/DownloadMii/DownloadMii/releases/download/1.0.5.8/1058.zip" role="button">Download</a></p>
			  </div>
			  <div class="col-sm-2 col-xs-0"></div>
			  <div id="beta" class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
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

