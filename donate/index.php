<?php
	/*
		DownloadMii Donate page
	*/
	$title = 'Donate';
	require_once('../common/uiheader.php');
?>
<div class="text-center">
	<h1 class="animated bounceInDown">Donate</h1>
	<br />
	<div class="animated bounceInRight row text-center">
	  <div class="row">
		  <div class="col-sm-5 col-xs-12 well clearfix" style="background: #25A4D6;box-shadow: 0 4px 2px -2px rgba(0,0,0,0.4);border:0;">
			<h2 class="font-white">Gratipay</h2>
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
	<p class="animated fadeIn">Keeping the server up costs quite a bit, so does also the time we spend<br />
	   developing this application. If you like DownloadMii consider donating<br />
	   to help keep DownloadMii alive! Thanks :)</p>
</div>
<?php
	require_once('../common/ucpfooter.php');
?>
