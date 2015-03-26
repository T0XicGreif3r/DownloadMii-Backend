<?php
	$title = 'Admin CP';
	require_once('../../common/ucpheader.php');
	require_once('../../common/user.php');

	verifyGroup('Administrators');

	sendResponseCodeAndExitIfTrue(!(isset($_POST['userid'], $_POST['token'])) || (!isset($_POST['grouptoadd']) && !isset($_POST['grouptoremove'])), 400);

	$userId = $_POST['userid'];
	if (isset($_SESSION['admin_userview_token' . $userId])) {
		$userViewToken = $_SESSION['admin_userview_token' . $userId];
	}

	//Verify token
	sendResponseCodeAndExitIfTrue(!isset($userViewToken) || md5($userViewToken) !== $_POST['token'], 422);

	$mysqlConn = connectToDatabase();

	if (isset($_POST['grouptoadd'])) {
		$groupToAdd = $_POST['grouptoadd'];

		//Insert group connection
		executePreparedSQLQuery($mysqlConn, 'INSERT IGNORE INTO groupconnections (userId, groupId)
												VALUES (?, ?)', 'ii', [$userId, $groupToAdd]);

		//Get group name
		$groupName = getArrayFromSQLQuery($mysqlConn, 'SELECT name FROM groups WHERE groupId = ?', 'i', [$groupToAdd])[0]['name'];

		//Create notification summary and body
		$notificationSummary = 'You are now part of "' . $groupName . '".';
		$notificationBody = 'You have been added to the group "' . $groupName . '" by an administrator.';
	}
	if (isset($_POST['grouptoremove'])) {
		$groupToRemove = $_POST['grouptoremove'];

		//Get group name
		$groupName = getArrayFromSQLQuery($mysqlConn, 'SELECT name FROM groups WHERE groupId = ?', 'i', [$groupToRemove])[0]['name'];

		//Remove group connection
		executePreparedSQLQuery($mysqlConn, 'DELETE FROM groupconnections
												WHERE userId = ? AND groupId = ?', 'ii', [$userId, $groupToRemove]);

		//Create notification summary and body
		$notificationSummary = 'You are no longer part of "' . $groupName . '".';
		$notificationBody = 'You have been removed from the group "' . $groupName . '" by an administrator.';
	}

	//Send notification if corresponding checkbox was checked
	if (isset($_POST['sendnotification']) && $_POST['sendnotification'] === 'yes') {
		$notificationManager = new notification_manager($mysqlConn);
		$notificationManager->createUserNotification($userId, $notificationSummary, $notificationBody);
	}

	$mysqlConn->close();

	unset($_SESSION['admin_userview_token' . $userId]);
	unset($_SESSION['admin_users_token']);

	echo 'User group settings set.';

	require_once('../../common/ucpfooter.php');
?>