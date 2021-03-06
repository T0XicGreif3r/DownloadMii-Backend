<?php
	if($_SERVER["HTTPS"] != "on")
	{
	    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	    exit();
	}
?>
<!-- SEO -->
<?php if (isset($description)) { ?>
	<meta name="description" content="<?php echo $description ?>" />
<?php } else{ ?>
	<meta name="description" content="DownloadMii is an online marketplace for homebrew applications. Choose from tons of Homebrew applications and download them directly to your Nintendo 3DS" />
<?php }?>
<meta name="keywords" content="DownloadMii, Nintendo 3DS Homebrew, Homebrew Browser, 3ds, filfat, Wii U Homebrew, build, release, latest, homebrew applications, nightly, " />
<link rel="author" href="https://plus.google.com/+Filfatofficial"/>
<link rel="publisher" href="https://plus.google.com/+Filfatofficial/"/>
<meta name="google-site-verification" content="oUzBcLbkKoA1gb5G8ZWgzzLR_LmIMFa0F8gsCH39LRc" />
<meta name="msvalidate.01" content="5F5165B227A54717CAF54D2E5DB6DE79" />
<link rel="alternate" href="downloadmii.com" hreflang="en-us">
<link rel="alternate" href="downloadmii.com" hreflang="en">
<meta charset="utf-8" />
<script type="application/ld+json">
	{
	  "@context": "http://schema.org",
	  "@type": "WebSite",
	  "url": "https://www.downloadmii.com/",
	  "potentialAction": {
		"@type": "SearchAction",
		"target": "https://www.downloadmii.com/apps?find={search_term_string}",
		"query-input": "required name=search_term_string"
	  }
	}
</script>

<!-- METADATA -->
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon" type="image/ico" href="https://www.downloadmii.com/favicon.ico" />

<!-- STYLESHEETS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/mainStruct.min.css"/>
<link rel="stylesheet" href="/css/animate.css">
<?php if(isset($page))
	if($page == 'AppView') echo '<link rel="stylesheet" href="/css/appview.css">';
	else if($page == 'SingleAppViewPage') echo '<link rel="stylesheet" href="/css/singleappviewpage.css">';
	else if($page == 'MyApps') echo '<link rel="stylesheet" href="/css/myapps.css">';
	else if($page == 'Userpage') echo '<link rel="stylesheet" href="/css/userpage.css">';
?>

<!-- PLATFORM SPECIFIC -->
<!-- MICROSOFT -->
<meta name="application-name" content="DownloadMii" />
<link rel="dns-prefetch" href="https://www.downloadmii.com"/>
<link rel="prerender" href="https://www.downloadmii.com/" />

<!-- IE SITE PIN -->
<meta name="msapplication-starturl" content="https://www.downloadmii.com" />
<meta name="msapplication-navbutton-color" content="#25A4D6" />
<meta name="msapplication-tooltip" content="Visit DownloadMii" />
<meta content="name=Home;action-uri=./;icon-uri=./favicon.ico" name="msapplication-task" />
<meta content="name=User CP;action-uri=./secure/myapps/;icon-uri=./favicon.ico" name="msapplication-task" />


<!-- LIVE TILE -->
<meta name="msapplication-TileColor" content="#25A4D6" />
<meta name="msapplication-square70x70logo" content="/img/LiveTiles/smalltile.png" />
<meta name="msapplication-square150x150logo" content="/img/LiveTiles/mediumtile.png" />
<meta name="msapplication-wide310x150logo" content="/img/LiveTiles/widetile.png" />
<meta name="msapplication-square310x310logo" content="/img/LiveTiles/largetile.png" />
<meta name="msapplication-notification" content="frequency=30; polling-uri=/livetiles/1.xml; polling-uri2=/livetiles/2.xml; polling-uri3=/livetiles/3.xml" />

<!-- APPLE -->
<link rel="apple-touch-icon" href="/img/Apple/touch-icon-iphone.png" /> 
<link rel="apple-touch-icon" sizes="76x76" href="/img/Apple/touch-icon-ipad.png" /> 
<link rel="apple-touch-icon" sizes="120x120" href="/img/Apple/touch-icon-iphone-retina.png" />
<link rel="apple-touch-icon" sizes="152x152" href="/img/Apple/touch-icon-ipad-retina.png" />
<link rel="apple-touch-startup-image" href="/img/Apple/startup.png" />
<meta name="apple-mobile-web-app-status-bar-style" content="white" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="DownloadMii"/>
<link rel="apple-touch-startup-image" href="/img/Apple/startup.png">

<!-- GOOGLE -->
<link rel="icon" sizes="128x128" href="/img/Apple/touch-icon-iphone-retina.png" />
<meta name="theme-color" content="#25A4D6">
<meta name="mobile-web-app-capable" content="yes">
<link rel="prerender" href="https://www.downloadmii.com/">

<!-- SOCIAL MEDIA -->
<meta property="og:type" content="website"/>
<meta name="twitter:card" content="summary">
<meta property="og:image" content="https://www.downloadmii.com/img/livetiles/largetile.png"/>
<meta name="twitter:image" content="https://www.downloadmii.com/img/livetiles/largetile.png">
<meta name="twitter:site" content="@filfat">
<meta name="twitter:creator" content="@filfat">
<?php
$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if(false !== strpos($url,'posts')){
?>
	<meta name="twitter:title" content="%SOCIAL_TITLE%">
	<meta property="og:title" content="%SOCIAL_TITLE%"/>
	<meta name="twitter:description" content="%SOCIAL_DESC%">
	<meta name="twitter:url" content="https://www.downloadmii.com%SOCIAL_URL%">
	<meta property="og:url" content="https://www.downloadmii.com%SOCIAL_URL%"/>
<?php
}
else{
?>
	<meta property="og:title" content="DownloadMii - Download homebrew directly to your 3DS"/>
	<meta property="og:url" content="https://www.downloadmii.com"/>
	<meta name="twitter:title" content="DownloadMii">
<?php
}
?>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4db3337339acc1fc" async="async"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-36429627-11', 'auto');
  ga('send', 'pageview');

</script>
