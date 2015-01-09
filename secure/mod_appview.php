<?php
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	if (isset($_SESSION['appview_token'])) {
		$appviewToken = $_SESSION['appview_token'];
		unset($_SESSION['appview_token']);
	}
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	$_SESSION['appset_token'] = uniqid(mt_rand(), true); //Generate token for moderator action
	
	sendResponseCodeAndExitIfTrue(!(isset($_GET['guid'], $_GET['token'])), 400);
	sendResponseCodeAndExitIfTrue(!isset($appviewToken) || md5($appviewToken) !== $_GET['token'], 422);
	
	$mysqlConn = connectToDatabase();
	
	$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.*, user.nick AS publisher, appver.number AS version, maincat.name AS category, subcat.name AS subcategory, appver.3dsx_md5 AS 3dsx_md5, appver.smdh_md5 AS smdh_md5 FROM apps app
														LEFT JOIN users user ON user.userId = app.publisher
														LEFT JOIN appversions appver ON appver.versionId = app.version
														LEFT JOIN categories maincat ON maincat.categoryId = app.category
														LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
														WHERE app.guid = ? LIMIT 1', 's', [$_GET['guid']]); //Get app with requested GUID
	
	printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID
	$currentApp = $matchingApps[0];
	
	//Print all app attributes
	foreach ($currentApp as $attributeName => $attributeValue) {
		echo $attributeName . ': ' . $attributeValue . '<br />';
	}
?>
<br />
<form action="mod_appset.php" method="post">
Set publish state:
<br />
<select name="publishstate" required>
<option value="">Select...</option>
<option value="0">[0] Pending approval</option>
<option value="1">[1] Published</option>
<option value="2">[2] Not approved</option>
<option value="3">[3] Hidden</option>
</select>
<br />
Message if "not approved" is selected (short, tell submitter why):
<br />
<input type="text" name="failpublishmessage" size="50" maxlength="32">
<input type="hidden" name="guid" value="<?php echo $currentApp['guid']; ?>">
<input type="hidden" name="token" value="<?php echo md5($_SESSION['appset_token']); ?>">
<br />
<input type="submit" value="Set">
</form>