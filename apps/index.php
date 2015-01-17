<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/uiheader.php');
	
	$mysqlConn = connectToDatabase();
	$allApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.description, app.downloads, app.publishstate, app.failpublishmessage, appver.number AS version, appver.largeIcon AS largeIcon FROM apps app
													LEFT JOIN appversions appver ON appver.versionId = app.version
													WHERE app.publishstate = 1 ORDER BY appver.versionId DESC');
?>

	<h1 class="text-center">All Apps</h1>
	<br />
<?php
	foreach ($allApps as $app) {
?>
	<div class="well clearfix">
			<div class="app-vertical-center-outer pull-left">
			<img class="app-icon" src="<?php if (!empty($app['largeIcon'])) echo $app['largeIcon']; else echo '/img/no_icon.png'; ?>" />
			<div class="pull-right">
				<h4 class="app-vertical-center-inner">
<?php
		echo escapeHTMLChars($app['name'] . ' ' . $app['version']);
?>

				</h4>
			</div>
		</div>
		<div class="app-vertical-center-outer pull-right btn-toolbar">
			<div class="btn-toolbar app-vertical-center-inner">
				<button class="btn btn-default disabled"><span class="glyphicon glyphicon-download"></span> <?php echo $app['downloads']; ?> downloads</button>
			</div>
		</div>
		<div class="clear-float" style="padding-top: 8px">
<?php
		echo escapeHTMLChars($app['description']);
?>
		</div>
	</div>
<?php
	}
		
	require_once('../common/uifooter.php');
?>
