<?php
	require_once('../../common/user.php');
	
	if (isset($_SESSION['mod_appview_token'])) {
		$appsetToken = $_SESSION['mod_appview_token'];
		unset($_SESSION['mod_appview_token']);
	}
	
	verifyRole(3);
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['guid'], $_POST['publishstate'], $_POST['failpublishmessage'], $_POST['token'])), 400);
	sendResponseCodeAndExitIfTrue(!isset($appsetToken) || md5($appsetToken) !== $_POST['token'] || !is_numeric($_POST['publishstate']) || $_POST['publishstate'] < 0 || $_POST['publishstate'] > 5, 422);
	
	$appGuid = $_POST['guid'];
	$appPublishState = $_POST['publishstate'];
	$appFailPublishMessage = $_POST['publishstate'] == 2 || $_POST['publishstate'] == 5 ? escapeHTMLChars($_POST['failpublishmessage']) : '';
	
	$mysqlConn = connectToDatabase();
	executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET version = (SELECT versionId FROM appversions WHERE appGuid = ? ORDER BY versionId DESC LIMIT 1),
											publishstate = ?, failpublishmessage = ?
											WHERE guid = ? LIMIT 1', 'siss', [$appGuid, $appPublishState, $appFailPublishMessage, $appGuid]); //Update publish state in database
	$mysqlConn->close();
	
	echo 'App publish state set.';
?>