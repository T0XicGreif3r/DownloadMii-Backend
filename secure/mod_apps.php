<?php
	require_once('../common/user.php');
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	$_SESSION['mod_apps_token'] = uniqid(mt_rand(), true); //Generate token for moderator action
	
	$mysqlConn = connectToDatabase();
	$pendingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, appver.number AS version, user.nick AS publisher FROM apps app
														LEFT JOIN appversions appver ON appver.versionId = app.version
														LEFT JOIN users user ON user.userId = app.publisher
														WHERE app.publishstate = 0 ORDER BY version ASC LIMIT 50');
														
	$mysqlConn->close();
	
	echo 'Pending apps (showing only oldest 50):<br />';
	
	$md5Token = md5($_SESSION['mod_apps_token']);
	foreach ($pendingApps as $app) {
		echo '<br />' . '<a href="mod_appview.php?guid=' . $app['guid'] . '&token=' . $md5Token . '">' . $app['guid'] . '</a> (name: ' . $app['name'] . ', version: ' . $app['version'] . ', publisher: ' . $app['publisher'] . ')';
	}
?>
<br />
<br />
<br />
<form action="mod_appview.php" method="get">
Query app by GUID:
<br />
<input type="text" name="guid" size="50">
<input type="hidden" name="token" value="<?php echo $md5Token; ?>">
<input type="submit" value="Query">
</form>