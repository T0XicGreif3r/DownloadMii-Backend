<?php
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	$adminToken = generateRandomString(); //Generate token for admin action
	$_SESSION['admin_token'] = md5(getConfigValue('salt_token') . $adminToken);
	
	$mysqlConn = connectToDatabase();
	$pendingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, appver.number AS version, user.nick AS publisher FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														LEFT JOIN users user ON user.userId = app.publisher
														WHERE app.publishstate = 0 LIMIT 50');
														
	$mysqlConn->close();
	
	echo 'Pending apps (showing only oldest 50):<br />';
	
	foreach ($pendingApps as $app) {
		echo '<br />' . '<a href="admin_appview.php?guid=' . $app['guid'] . '&token=' . $adminToken . '">' . $app['guid'] . '</a> (n: ' . $app['name'] . ', v: ' . $app['version'] . ', p: ' . $app['publisher'] . ')';
	}
?>