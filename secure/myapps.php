<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	if (clientLoggedIn()) {
		$_SESSION['myapps_token'] = uniqid(mt_rand(), true);
		
		$mysqlConn = connectToDatabase();
		$userApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.publishstate, appver.number AS version FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														WHERE app.publisher = ?', 'i', [$_SESSION['user_id']]);
?>

		<h1 class="text-center">My apps</h1>
		<br />
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
				<a role="button" class="btn btn-primary<?php if ($app['publishstate'] === 0) echo ' disabled'; ?>" href="publish.php?guid=<?php echo $app['guid']; ?>&token=<?php echo md5($_SESSION['myapps_token']); ?>">Update</a>
				<a role="button" class="btn btn-danger" href="remove.php?guid=<?php print($app['guid']); ?>&token=<?php echo md5($_SESSION['myapps_token']); ?>">Remove</a> <!-- Take user to confirmation -->
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
