<!doctype html>
<html lang="en">
	<head>
		<?php
			$description = "%DESCRIPTION%";
			require_once($_SERVER['DOCUMENT_ROOT'] . '\common\meta.php');
		?>
		<link rel="stylesheet" href="<?php echo theme_url('/css/style.css'); ?>">
		<?php
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		if(false !== strpos($url,'posts')){
		?>
			<title>%TITLE%</title>
		<?php
		}
		else{
		?>
			<title>DownloadMii - Blog</title>
		<?php
		}
		?>

		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/js/bootstrap-filestyle.min.js"> </script>
		<script type="text/javascript" src="/js/smooth-scroll.js"></script>

		<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo rss_url(); ?>">

		<script>var base = '<?php echo theme_url(); ?>';</script>
		<script src="<?php echo asset_url('/js/zepto.js'); ?>"></script>
		<script src="<?php echo theme_url('/js/main.js'); ?>"></script>

		<?php if(customised()): ?>
		    <!-- Custom CSS -->
    		<style><?php echo article_css(); ?></style>

    		<!--  Custom Javascript -->
    		<script><?php echo article_js(); ?></script>
		<?php endif; ?>
	</head>
	<body class="<?php echo body_class(); ?>">
		<div class="main-wrap">
			<header id="top">
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
						<li><a href="/blog">ALL POSTS</a></li>
						<li class="dropdown">
				        		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> CATEGORIES <span class="caret"></span></a>
				        		<ul class="dropdown-menu" role="menu">
								<?php while(categories()): ?>
									<li><a href="<?php echo category_url(); ?>" title="<?php echo category_description(); ?>"><?php echo strtoupper(category_title()); ?> <span class="badge"><?php echo category_count(); ?></span></a></li>
								<?php endwhile; ?>
							</ul>
						</li>
					  </ul>
					</div>
				  </div>
				</nav>
			</header>
