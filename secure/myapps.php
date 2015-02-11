<?php
	/*
		DownloadMii App List Page (by current user)
	*/
	
	$title = 'My Apps';
	require_once('../common/ucpheader.php');
	
	if (clientLoggedIn()) {
		$mysqlConn = connectToDatabase();
		$userApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.description, app.downloads, app.publishstate, app.failpublishmessage, appver.number AS version, appver.largeIcon FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														WHERE app.publisher = ? ORDER BY appver.versionId DESC', 'i', [$_SESSION['user_id']]);
?>

		<h1 class="animated bounceInDown text-center">My Apps</h1>
		<br />
<?php
		foreach ($userApps as $app) {
			if (!isset($_SESSION['myapps_token' . $app['guid']])) {
				$_SESSION['myapps_token' . $app['guid']] = uniqid(mt_rand(), true);
			}
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
				<div class="app-vertical-center-inner">
					<a role="button" class="btn btn-primary" href="publish.php?guid=<?php echo $app['guid']; ?>&token=<?php echo md5($_SESSION['myapps_token' . $app['guid']]); ?>">Update</a>
					<div class="pull-right" style="margin-left: 5px;"> <!-- this shouldn't be like this -->
						<div class="btn-group">
<?php
			if ($app['publishstate'] !== 2 && $app['publishstate'] !== 3) {
				echo '<a role="button" class="btn btn-danger" href="hide.php?guid=' . $app['guid'] . '&token=' . md5($_SESSION['myapps_token' . $app['guid']]) . '">Hide</a>';
			}
			
			switch ($app['publishstate']) {
				case 0:
					echo '<button class="btn btn-info disabled"><span class="glyphicon glyphicon-time"></span> Pending approval</button>';
					break;
					
				case 1:
					echo '<button class="btn btn-success disabled"><span class="glyphicon glyphicon-ok"></span> Published</button>';
					break;
					
				case 2:
					if (!empty($app['failpublishmessage'])) {
						echo '<button class="btn btn-danger disabled"><span class="glyphicon glyphicon-ban-circle"></span> ' . escapeHTMLChars($app['failpublishmessage']) . '</button>';
					}
					else {
						echo '<button class="btn btn-danger disabled"><span class="glyphicon glyphicon-ban-circle"></span> Rejected</button>';
					}
					break;
					
				case 3:
					echo '<button class="btn btn-danger disabled"><span class="glyphicon glyphicon-eye-close"></span> Hidden</button>';
					break;
			}
?>

						</div>
						<div class="pull-right" style="margin-left: 5px;">
							<button class="btn btn-default disabled"><span class="glyphicon glyphicon-download"></span> <?php echo $app['downloads']; ?> downloads</button>
						</div>
					</div>
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
	}
	if($userApps == null){
?>
	<br />
	<h4 class="text-center">You have not yet published an app :(</h4>
	<br />
<?php
	}
	require_once('../common/ucpfooter.php');
?>
