<html>
	<head>
		<title>DownloadMii User CP</title>
		<link href="/css/bootstrap.css" rel="stylesheet"/>
		<link href="/css/mainStruct.css" rel="stylesheet"/>
		<style>
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
					<li><a data-scroll href="/#DOWNLOADwp">DOWNLOAD</a></li>
					<li><a href="/donate">DONATE</a></li>
					<li><a href="/secure/myapps.php">USER CP</a></li> <!-- ToDo: redirect to user CP instead of "my apps" list -->
				  </ul>
				</div>
			  </div>
			</nav>
		</header>
		<div class="text-center">
			<?php
			$requestUri = strtok(getenv('REQUEST_URI'), '?');
			$param = explode('/', rtrim(substr($requestUri, strlen('/error/')), '/')); //All URL "directories" after /api/ -> array
			$topLevelRequest = $param[0];
			//http_response_code($topLevelRequest); //todo
			switch ($topLevelRequest) {
				case '404':
			?>
				<h1>Error 404</h1>
				<h4>The requested page cannot be found!</h4>
			<?php
				break;
				case '500':
			?>
				<h1>Error 500</h1>
				<h4>Server error!</h4>
			<?php
				break;
				default:
			?>
				<h1>Error</h1>
				<h4>Unknown error!</h4>
			<?php
			}
			echo $topLevelRequest;
			?>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>
