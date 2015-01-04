<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	sendResponseCodeAndExitIfTrue(!(isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])), 403); //Check if logged in
	
	$mysqlConn = connectToDatabase();
	$userApps = getArrayFromSQLQuery($mysqlConn, 'SELECT guid, name, version FROM apps WHERE publisher = ?', 'i', [$_SESSION['user_id']]);
	foreach ($userApps as $app) {
?>
		<div class="well clearfix">
			<h4 class="pull-left"><?php print($app['name'] . ' (' . $app['version'] . ')'); ?></h4>
			<div class="btn-toolbar pull-right">
				<a role="button" class="btn btn-primary" href="publish.php?guid=<?php print($app['guid']); ?>">Edit</a>
				<a role="button" class="btn btn-danger" href="remove.php?guid=<?php print($app['guid']); ?>">Remove</a> <!-- Take user to confirmation -->
			</div>
		</div>
<?php
	}
	
	require_once('../common/ucpfooter.php');
?>