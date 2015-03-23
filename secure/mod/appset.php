<?php
	$title = 'Mod CP';
	require_once('../../common/ucpheader.php');
	require_once('../../common/user.php');
	
	verifyRole(3);
	
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
	
	$mysqlConn->close();

	unset($_SESSION['mod_appview_token' . $_POST['guid']]);
	echo 'App publish state set.';

	require_once('../../common/ucpfooter.php');
?>