<?php
	require_once('../common/user.php');
	
	if (isset($_SESSION['mod_appview_token'])) {
		$appsetToken = $_SESSION['mod_appview_token'];
		unset($_SESSION['mod_appview_token']);
	}
	
	printAndExitIfTrue(!clientLoggedIn() || $_SESSION['user_role'] < 3, 'You do not have permission to access this page.');
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['guid'], $_POST['publishstate'], $_POST['failpublishmessage'], $_POST['token'])), 400);
	sendResponseCodeAndExitIfTrue(!isset($appsetToken) || md5($appsetToken) !== $_POST['token'] || !is_numeric($_POST['publishstate']) || $_POST['publishstate'] < 0 || $_POST['publishstate'] > 4, 422);
	
	$appGuid = $_POST['guid'];
	$appPublishState = $_POST['publishstate'];
	$appFailPublishMessage = $_POST['publishstate'] == 2 ? escapeHTMLChars($_POST['failpublishmessage']) : '';
	
	$mysqlConn = connectToDatabase();
	executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET publishstate = ?, failpublishmessage = ? WHERE guid = ? LIMIT 1', 'iss', [$appPublishState, $appFailPublishMessage, $appGuid]); //Update publish state in database
	$mysqlConn->close();
	
	echo 'App publish state set.';
?>