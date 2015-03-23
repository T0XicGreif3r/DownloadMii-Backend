<?php
	$title = 'Admin CP';
	require_once('../../common/ucpheader.php');
	require_once('../../common/user.php');

	verifyGroup('Administrators');

	if (isset($_SESSION['admin_users_token'])) {
		$usersToken = $_SESSION['admin_users_token'];
	}

	sendResponseCodeAndExitIfTrue(!(isset($_GET['nick'], $_GET['token'])), 400);
	sendResponseCodeAndExitIfTrue(!isset($usersToken) || md5($usersToken) !== $_GET['token'], 422);
	
	$mysqlConn = connectToDatabase();

	//Get list of all groups in the system
	$availableGroups = getArrayFromSQLQuery($mysqlConn, 'SELECT groupId, name FROM groups ORDER BY name ASC');

	//Get user data for requested name
	$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT userId, nick, email FROM users
														WHERE nick = ? LIMIT 1', 's', [$_GET['nick']]);

	//Verify that there is one user matching attempted nick
	printAndExitIfTrue(count($matchingUsers) !== 1, 'Invalid user nick.');
	$user = $matchingUsers[0];

	//Get user groups
	$assignedGroups = getGroupsForUser($mysqlConn, $user['userId'], false);
	$allGroupsForUser = getGroupsForUser($mysqlConn, $user['userId'], true);

	//Generate token for admin action
	$_SESSION['admin_userview_token' . $user['userId']] = uniqid(mt_rand(), true);

	//Print all user attributes
	foreach ($user as $attributeName => $attributeValue) {
		echo $attributeName . ': ' . $attributeValue . '<br />';
	}
	
	//Print user groups
	echo '<br />Groups (excluding inherited): ' . implode(', ', $assignedGroups);
	echo '<br />Groups (including inherited): ' . implode(', ', $allGroupsForUser);
?>
<br />
<br />

<form action="userset.php" method="post">
<select name="grouptoadd" required>
<option value="">Select...</option>
	<?php
		foreach ($availableGroups as $availableGroup) {
			echo '<option value="' . $availableGroup['groupId'] . '">' . $availableGroup['name'] . '</option>';
		}
	?>
</select>

<input type="checkbox" name="sendnotification" value="yes" checked> Send notification?
<input type="hidden" name="userid" value="<?php echo $user['userId']; ?>">
<input type="hidden" name="token" value="<?php echo md5($_SESSION['admin_userview_token' . $user['userId']]); ?>">
<input type="submit" name="addgroup" value="Add group">
</form>

<br />
<br />

<form action="userset.php" method="post">
<select name="grouptoremove" required>
<option value="">Select...</option>
<?php
	foreach ($assignedGroups as $assignedGroupName) {
		foreach ($availableGroups as $availableGroup) {
			if ($availableGroup['name'] === $assignedGroupName) {
				$assignedGroupId = $availableGroup['groupId'];
			}
		}
		echo '<option value="' . $assignedGroupId . '">' . $assignedGroupName . '</option>';
	}
?>
</select>

<input type="checkbox" name="sendnotification" value="yes" checked> Send notification?
<input type="hidden" name="userid" value="<?php echo $user['userId']; ?>">
<input type="hidden" name="token" value="<?php echo md5($_SESSION['admin_userview_token' . $user['userId']]); ?>">
<input type="submit" name="removegroup" value="Remove group">
</form>

<?php
	require_once('../../common/ucpfooter.php');
?>