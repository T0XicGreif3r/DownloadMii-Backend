<?php
	/*
		DownloadMii Page Footer
	*/
?>
			</div>
			<br />
			<!-- ADS -->
			<div id="FULLSCREEN" class="pad-section" style="height:350px;">
			  <div class="container">
				<div class="row">
						<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
						<!-- DownloadMii -->
						<ins class="adsbygoogle downloadmii"
							 style="display:inline-block"
							 data-ad-client="ca-pub-1408003448017335"
							 data-ad-slot="2971968439"></ins>
						<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
						</script>
				</div>
			  </div>
			</div>
			<!-- /ADS -->
			<!-- DOWNLOAD-->
			<div id="SHOWCASE" class="pad-section">
			  <div class="container">
				<h1 class="animated bounceInLeft text-center" style="color: white !important;" >Download</h1> <hr />
				<div class="row text-center">
				  <div class="row">
					  <div class="col-sm-4 col-xs-12">
						<p><a class="btn btn-lg btn-flat" href="/download/#release" role="button">Latest Release</a></p>
					  </div>
					  <div class="col-sm-4 col-xs-12">
						<p><a class="btn btn-lg btn-flat" href="/download/#beta" role="button">Beta Release</a></p>
					  </div>
					  <div class="col-sm-4 col-xs-12">
						<p><a class="btn btn-lg btn-flat" href="http://build.filfatstudios.com:8080/job/DownloadMii%20(3DS)/" role="button">Nightly Builds</a></p>
					  </div>
				  </div>
				  <div class="row">
					<div class="col-sm-4 col-xs-12">
						Release builds are the main builds. These builds are for the average user as its the most stable version of DownloadMii.
					  </div>
					  <div class="col-sm-4 col-xs-12">
						Beta builds are somewhere in between stable and nightly providing you the benifit of deeply tested software but also the latest features faster, use these builds if you are ready to lose some stability for new features!
					  </div>
					  <div class="col-sm-4 col-xs-12">
						Nightly builds are mainly used for and by developers to quickly test new and awesome features, hotfixes and such. Do not expect stability if you use this version!
					  </div>
				   </div>
				</div>
			  </div>
			  <div class="WayPoint" id="DOWNLOADwp"></div>
			</div>
			<!-- /DOWNLOAD -->
		</div>
		<?php
			require_once($_SERVER['DOCUMENT_ROOT'] . '\common\copyrightbar.php');
		?>
		<!-- SCRIPTS  -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/js/bootstrap-filestyle.min.js"> </script>
		<script type="text/javascript" src="/js/smooth-scroll.js"></script>
		<script type="text/javascript" src="/js/common.min.js"></script>
		<script>
			smoothScroll.init({
				speed: 500, // Integer. How fast to complete the scroll in milliseconds
				easing: 'easeInOutQuint', // Easing pattern to use
				updateURL: false
			});
			$(":file").filestyle({buttonName: "btn-primary"});
			
			$(function () {
			  $('[data-toggle="tooltip"]').tooltip();
			})
			$("[rel=tooltip]").tooltip({html:true});
		</script>
	</body>
</html>
