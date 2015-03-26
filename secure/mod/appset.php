<?php
	$title = 'Mod CP';
	require_once('../../common/ucpheader.php');
	require_once('../../common/user.php');
	
	verifyGroup('Moderators');
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['guid'], $_POST['publishstate'], $_POST['failpublishmessage'], $_POST['token'])), 400);

	if (isset($_SESSION['mod_appview_token' . $_POST['guid']])) {
		$appViewToken = $_SESSION['mod_appview_token' . $_POST['guid']];
	}

	sendResponseCodeAndExitIfTrue(!isset($appViewToken) || md5($appViewToken) !== $_POST['token'] || !is_numeric($_POST['publishstate']) || $_POST['publishstate'] < 0 || $_POST['publishstate'] > 5, 422);

	$appGuid = $_POST['guid'];
	$appPublishState = $_POST['publishstate'];
	$appFailPublishMessage = $_POST['publishstate'] == 2 || $_POST['publishstate'] == 5 ? escapeHTMLChars($_POST['failpublishmessage']) : '';
	
	$mysqlConn = connectToDatabase();
	if ($appPublishState == 1) {
		executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET version = (SELECT versionId FROM appversions WHERE appGuid = ? ORDER BY versionId DESC LIMIT 1),
												publishstate = ?, failpublishmessage = ?
												WHERE guid = ? LIMIT 1', 'siss', [$appGuid, $appPublishState, $appFailPublishMessage, $appGuid]); //Update latest version and publish state in database
	}
	else {
		executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET publishstate = ?, failpublishmessage = ?
												WHERE guid = ? LIMIT 1', 'iss', [$appPublishState, $appFailPublishMessage, $appGuid]); //Update publish state in database
	}
	
	if (isset($_POST['sendnotification']) && $_POST['sendnotification'] === 'yes') {
		$currentApp = getArrayFromSQLQuery($mysqlConn, 'SELECT name, publisher FROM apps WHERE guid = ?', 's', [$appGuid])[0];
		$notificationUserId = $currentApp['publisher'];
		
		//Generate notification summary
		$notificationSummary = '"' . $currentApp['name'] . '" has been';
		switch ($appPublishState) {
			case 1: //Published
				$notificationSummary .= ' approved.';
				break;
			
			case 2: //Not approved
				$notificationSummary .= ' rejected.';
				break;
			
			case 3: //Hidden
				$notificationSummary .= ' hidden.';
				break;
		}
		
		//Generate notification body
		$notificationBody = 'Your submitted application ' . $notificationSummary;
		if ($appPublishState == 1) {
			$notificationBody .= ' It is now viewable on the website and in the 3DS DownloadMii application.';
		}
		else if ($appPublishState > 1 && !empty($appFailPublishMessage)) {
			$notificationBody .= ' The reason specified was: "' . $appFailPublishMessage . '"';
		}
		
		//Create notification
		$notificationManager = new notification_manager($mysqlConn);
		$notificationManager->createUserNotification($notificationUserId, $notificationSummary, $notificationBody);
	}
	
	$mysqlConn->close();

	unset($_SESSION['mod_appview_token' . $_POST['guid']]);
	echo 'App publish state set.';

	require_once('../../common/ucpfooter.php');
?>