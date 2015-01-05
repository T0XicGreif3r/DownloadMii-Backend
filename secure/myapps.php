<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	if (clientLoggedIn()) {
		$myappsToken = generateRandomString();
		$_SESSION['myapps_token'] = md5(getConfigValue('salt_token') . $myappsToken);
		
		$mysqlConn = connectToDatabase();
		$userApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.publishstate, appver.number AS version FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														WHERE app.publisher = ?', 'i', [$_SESSION['user_id']]);
?>

		<h1 class="text-center">My apps</h1>
<?php
		foreach ($userApps as $app) {
?>
		<div class="well clearfix">
			<h4 class="pull-left">
<?php
			echo $app['name'] . ' ' . $app['version'];
			switch ($app['publishstate']) {
				case 0:
					echo ' (pending approval)';
					break;
					
				case 1:
					echo ' (published)';
					break;
					
				case 2:
					echo ' (rejected)';
					break;
			}
?>

			</h4>
			<div class="btn-toolbar pull-right">
				<a role="button" class="btn btn-primary<?php if ($app['publishstate'] === 0) echo ' disabled'; ?>" href="publish.php?guid=<?php print($app['guid']); ?>&token=<?php print($myappsToken); ?>">Update</a>
				<a role="button" class="btn btn-danger" href="remove.php?guid=<?php print($app['guid']); ?>&token=<?php print($myappsToken); ?>">Remove</a> <!-- Take user to confirmation -->
			</div>
		</div>
<?php
		}
	}
	
	require_once('../common/ucpfooter.php');
?>