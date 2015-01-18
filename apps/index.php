<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/uiheader.php');
	
	$mysqlConn = connectToDatabase();
	$allApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.publisher, app.description, app.downloads, app.publishstate, app.failpublishmessage, user.nick AS publisher, appver.number AS version, appver.largeIcon AS largeIcon FROM apps app
													LEFT JOIN users user ON user.userId = app.publisher
													LEFT JOIN appversions appver ON appver.versionId = app.version
													WHERE app.publishstate = 1 ORDER BY appver.versionId DESC');
?>

	<h1 class="text-center">All Apps</h1>
	<br />
		<div class="input-group">
		  <input type="text" class="form-control" placeholder="App name...">
		  <span class="input-group-btn">
			<button class="btn btn-default" type="button">Search</button>
		  </span>
		</div>
	<br />
	
<?php
	foreach ($allApps as $app) { //ToDo: update based on search via Ajax
?>
	<div class="well clearfix">
		<div class="app-vertical-center-outer pull-left">
			<img class="app-icon" src="<?php if (!empty($app['largeIcon'])) echo $app['largeIcon']; else echo '/img/no_icon.png'; ?>" />
			<div class="pull-right">
				<h4 class="app-vertical-center-inner">
<?php
		echo escapeHTMLChars($app['name'] . ' ' . $app['version']) . ' by <span style="font-style: italic;">' . $app['publisher'] . '</span>';
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
