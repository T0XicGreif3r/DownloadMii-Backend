<?php
	/*
		DownloadMii App Information Page
	*/
	
	//TODO: Add more information (screenshots, reviews, etc.)
	
	require_once('../common/functions.php');
	
	$requestUri = strtok(getenv('REQUEST_URI'), '?');
	$appGuid = rtrim(substr($requestUri, strlen('/apps/')), '/');
	
	$mysqlConn = connectToDatabase();
	$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.name, app.description, app.downloads, app.publishstate, app.failpublishmessage,
														user.nick AS publisher, appver.number AS version, appver.largeIcon, maincat.name AS category, subcat.name AS subcategory, group_concat(scr.url) AS screenshots FROM apps app
														LEFT JOIN users user ON user.userId = app.publisher
														LEFT JOIN appversions appver ON appver.versionId = app.version
														LEFT JOIN categories maincat ON maincat.categoryId = app.category
														LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
														LEFT JOIN screenshots scr ON scr.appGuid = app.guid
														WHERE app.publishstate = 1 AND app.guid = ?
														GROUP BY app.guid', 's', [$appGuid]);
	
	printAndExitIfTrue(count($matchingApps) !== 1, 'Invalid app GUID.');
	$app = $matchingApps[0];
	
	$title = $app['name'];
	require_once('../common/uiheader.php');
?>

	<h1 class="text-center"><?php echo $app['name']; ?></h1>
	<h3 class="text-center">
		<span id="maincat" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"<?php if ($app['subcategory'] !== null) echo ' itemref="subcat"'; ?>>
			<a itemprop="url" href="/apps/<?php echo $app['category']; ?>" style="color: black;">
				<span itemprop="title"><?php echo $app['category']; ?></span>
			</a>
		</span>
<?php
	if ($app['subcategory'] !== null) {
		echo
		'<span class="glyphicon glyphicon-arrow-right" style="font-size: 20px;"></span>
		<span id="subcat" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemprop="child">
			<a itemprop="url" href="/apps/' . $app['category'] . '/' . $app['subcategory'] . '" style="color: black;">
				<span itemprop="title">' . $app['subcategory'] . '</span>
			</a>
		</span>';
	}
?>

	</h3>
	<br />
	<div id="appcontainer">
		<div class="well clearfix">
			<div class="app-vertical-center-outer pull-left">
				<img class="app-icon" src="<?php if (!empty($app['largeIcon'])) echo $app['largeIcon']; else echo '/img/no_icon.png'; ?>" />
				<div class="pull-right">
					<h4 class="app-vertical-center-inner">
<?php
	echo '<span itemprop="name">' . escapeHTMLChars($app['name']) . '</span> <span itemprop="softwareVersion">' . escapeHTMLChars($app['version']) . '</span> by <span itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="font-style: italic;">' . $app['publisher'] . '</span>';
?>

					</h4>
				</div>
			</div>
			<div class="app-vertical-center-outer pull-right btn-toolbar">
				<div class="btn-toolbar app-vertical-center-inner">
					<button class="btn btn-default disabled"><span class="glyphicon glyphicon-download"></span> <?php echo $app['downloads']; ?> unique downloads</button>
				</div>
			</div>
			<div class="clear-float" style="padding-top: 8px">
<?php
	echo escapeHTMLChars($app['description']);
?>

			</div>
			
<?php
	if ($app['screenshots'] !== null) {
		$screenshots = explode(',', $app['screenshots']);
		$screenshotRows = array_chunk($screenshots, 2);
		
		foreach ($screenshotRows as $screenshotRow) {
			echo '<div class="row">';
			foreach ($screenshotRow as $screenshot) {
				echo
				'<div class="col-md-' . (12 / count($screenshotRow)) . '" style="text-align: center;">
					<img class="app-screenshot" src="' . $screenshot . '" />
				</div>';
			}
			echo '</div>';
		}
	}
?>

		</div>
	</div>
<?php		
	require_once('../common/uifooter.php');
?>