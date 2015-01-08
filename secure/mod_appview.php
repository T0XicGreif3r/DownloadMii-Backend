<?php
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	$_SESSION['mod_token'] = uniqid(mt_rand(), true); //Generate token for moderator action
	
	if (isset($_GET['guid'], $_GET['token'], $myappsToken) && md5($myappsToken) === $_GET['token']) {
			$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.*, appver.number AS version FROM apps app
																LEFT JOIN appversions appver ON appver.versionId = app.version
																WHERE app.guid = ? AND app.publisher = ? LIMIT 1', 'ss', [$_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
	
	printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination
	$currentApp = $matchingApps[0];
	
	echo 'ToDo<br />';
?>
