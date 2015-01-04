<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])) {
		$myappsToken = generateRandomString();
		$_SESSION['myapps_token'] = md5(getConfigValue('salt_token') . $myappsToken);
		
		$mysqlConn = connectToDatabase();
		$userApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, appver.number AS version FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														WHERE app.publisher = ?', 'i', [$_SESSION['user_id']]);
	
		foreach ($userApps as $app) {
?>
		<div class="well clearfix">
			<h4 class="pull-left"><?php echo $app['name'] . ' (' . $app['version'] . ')'; ?></h4>
			<div class="btn-toolbar pull-right">
				<a role="button" class="btn btn-primary" href="publish.php?guid=<?php print($app['guid']); ?>&token=<?php print($myappsToken); ?>">Edit</a>
				<a role="button" class="btn btn-danger" href="remove.php?guid=<?php print($app['guid']); ?>&token=<?php print($myappsToken); ?>">Remove</a> <!-- Take user to confirmation -->
			</div>
		</div>
<?php
		}
	}
	
	require_once('../common/ucpfooter.php');
?>