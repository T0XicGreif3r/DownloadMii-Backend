<?php
	$title = 'Mod CP';
	require_once('../../common/ucpheader.php');
	require_once('../../common/user.php');

	verifyGroup('Moderators');

	if (isset($_SESSION['mod_apps_token'])) {
		$appsToken = $_SESSION['mod_apps_token'];
	}

	sendResponseCodeAndExitIfTrue(!(isset($_GET['guid'], $_GET['token'])), 400);
	sendResponseCodeAndExitIfTrue(!isset($appsToken) || md5($appsToken) !== $_GET['token'], 422);
	
	$_SESSION['mod_appview_token' . $_GET['guid']] = uniqid(mt_rand(), true); //Generate token for moderator action
	
	$mysqlConn = connectToDatabase();
	
	$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.*,
														user.nick AS publisher, appver.number AS version, maincat.name AS category, subcat.name AS subcategory, appver.3dsx, appver.smdh, appver.appdata, appver.3dsx_md5, appver.smdh_md5, appver.appdata_md5, appver.largeIcon, group_concat(scr.url) AS screenshots FROM apps app
														LEFT JOIN users user ON user.userId = app.publisher
														LEFT JOIN appversions appver ON appver.versionId = (SELECT versionId FROM appversions WHERE appGuid = ? ORDER BY versionId DESC LIMIT 1)
														LEFT JOIN categories maincat ON maincat.categoryId = app.category
														LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
														LEFT JOIN screenshots scr ON scr.appGuid = app.guid
														WHERE app.guid = ? LIMIT 1', 'ss', [$_GET['guid'], $_GET['guid']]); //Get app with requested GUID
	
	printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID
	$currentApp = $matchingApps[0];
	
	$screenshots = explode(',', $currentApp['screenshots']);
	
	//Print all app attributes
	foreach ($currentApp as $attributeName => $attributeValue) {
		if ($attributeName == 'screenshots') {
			for ($i = 0; $i < count($screenshots); $i++) {
				echo $attributeName . ' (' . ($i + 1) . '): <a href="' . $screenshots[$i] . '">' . $screenshots[$i] . '</a><br />';
			}
		}
		else if ($attributeName == '3dsx' || $attributeName == 'smdh' || $attributeName == 'appdata' || $attributeName == 'largeIcon') {
			echo $attributeName . ': <a href="' . $attributeValue . '">' . $attributeValue . '</a><br />';
		}
		else {
			$safeValue = escapeHTMLChars($attributeValue);
			echo $attributeName . ': ' . $safeValue . '<br />';
		}
	}
	
	//Print icon
	echo '<img src="' . $currentApp['largeIcon'] . '" /><br />';
	
	for ($i = 0; $i < count($screenshots); $i++) {
		echo '<img src="' . $screenshots[$i] . '" /> ';
	}
?>
<br />
<br />

<form action="appset.php" method="post">
Set publish state:
<br />

<select name="publishstate" required>
<option value="">Select...</option>
<option value="0">[0] Pending approval</option>
<option value="1">[1] Published</option>
<option value="2">[2] Not approved</option>
<option value="3">[3] Hidden</option>
<option value="4">[4] Published - newer version pending approval</option>
<option value="5">[5] Published - newer version not approved</option>
</select>

<br />
<br />

<b>([2], [3])</b> Message (keep short, tell submitter reason):
<br />

<input type="text" name="failpublishmessage" size="50" maxlength="24">
<br />
<input type="checkbox" name="sendnotification" value="yes"<?php if ($currentApp['publishstate'] == 0 || $currentApp['publishstate'] > 3) echo ' checked'; ?>> <b>([1], [2], [3])</b> Send notification?
<input type="hidden" name="guid" value="<?php echo $currentApp['guid']; ?>">
<input type="hidden" name="token" value="<?php echo md5($_SESSION['mod_appview_token' . $_GET['guid']]); ?>">
<br />

<input type="submit" value="Set">
</form>

<?php
	require_once('../../common/ucpfooter.php');
?>
