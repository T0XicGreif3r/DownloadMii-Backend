<?php
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	$_SESSION['mod_token'] = uniqid(mt_rand(), true); //Generate token for moderator action
	
	$mysqlConn = connectToDatabase();
	$pendingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, appver.number AS version, user.nick AS publisher FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														LEFT JOIN users user ON user.userId = app.publisher
														WHERE app.publishstate = 0 LIMIT 50');
														
	$mysqlConn->close();
	
	echo 'Pending apps (showing only oldest 50):<br />';
	
	foreach ($pendingApps as $app) {
		echo '<br />' . '<a href="mod_appview.php?guid=' . $app['guid'] . '&token=' . md5($_SESSION['mod_token']) . '">' . $app['guid'] . '</a> (name: ' . $app['name'] . ', version: ' . $app['version'] . ', publisher: ' . $app['publisher'] . ')';
	}
?>
