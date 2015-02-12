<?php
	/*
		DownloadMii Error page
	*/
	$title = 'Error';
	require_once('../common/uiheader.php');
?>
<div class="text-center">
	<?php
	$requestUri = strtok(getenv('REQUEST_URI'), '?');
	$param = explode('/', rtrim(substr($requestUri, strlen('/error/')), '/')); //All URL "directories" after /api/ -> array
	$topLevelRequest = $param[0];
	http_response_code(404); //todo
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
