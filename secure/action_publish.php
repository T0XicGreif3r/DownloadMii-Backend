<?php
	/*
		DownloadMii Publishing Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	require_once('../common/recaptchalib.php');
	require_once('../vendor/autoload.php');
	
	use WindowsAzure\Common\ServicesBuilder;
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['publish_token']), 422); //Check if session publishing token is set
	$publishToken = $_SESSION['publish_token'];
	unset($_SESSION['publish_token']);
	
	sendResponseCodeAndExitIfTrue(!clientLoggedIn(), 403);
	printAndExitIfTrue($_SESSION['user_role'] < 1, 'You do not have permission to publish apps.');
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['name'], $_POST['version'], $_POST['category'], $_POST['description'], $_FILES['3dsx'], $_FILES['smdh'], $_POST["g-recaptcha-response"], $_POST['publishtoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue(md5($publishToken) !== $_POST['publishtoken'], 422); //Check if POST publishing token is correct
	sendResponseCodeAndExitIfTrue(!is_numeric($_POST['category']), 422); //Check if category selected
	
	//Check POST var lengths
	printAndExitIfTrue(mb_strlen($_POST['name']) > 50, 'App name is too long.');
	printAndExitIfTrue(mb_strlen($_POST['version']) > 12, 'Version is too long.');
	printAndExitIfTrue(mb_strlen($_POST['description']) > 3000, 'Description is too long.');
	
	//Check captcha
	$reCaptcha = new ReCaptcha(getConfigValue('apikey_recaptcha_secret'));
	$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
	printAndExitIfTrue($resp == null || !$resp->success, 'Invalid or no captcha response.');
	
	$appName = htmlspecialchars($_POST['name']);
	$appVersion = htmlspecialchars($_POST['version']);
	$appCategory = $_POST['category'];
	$appDescription = str_replace(['\r\n', '\r', '\n'], ' ', htmlspecialchars($_POST['description']);
	
	$isDeveloper = $_SESSION['user_role'] > 1;
	$updatingApp = isset($_SESSION['user_app_guid']);
	
	if (!$updatingApp) {
		$guid = generateGUID();
	}
	else {
		$guid = $_SESSION['user_app_guid'];
		$updating3dsx = is_uploaded_file($_FILES['3dsx']['tmp_name']);
		$updatingSmdh = is_uploaded_file($_FILES['smdh']['tmp_name']);
		
		//Check that if one of 3dsx, version is changed, the other also is
		if ($_SESSION['user_app_version'] !== $_POST['version'] || $updating3dsx) {
			printAndExitIfTrue($_SESSION['user_app_version'] === $_POST['version'] || !$updating3dsx, 'Please update both the version number and 3dsx file at the same time.');
		}
	}
	
	//TODO: Check if files are valid 3dsx/smdh
	
	$mysqlConn = connectToDatabase();
	
	//Check if category exists
	$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId FROM categories WHERE categoryId = ? AND type = 0', 'i', [$_POST['category']]);
	printAndExitIfTrue(count($categories) != 1, 'Invalid category ID.');
	
	//Initialize Azure Blob Service if files will be uploaded
	if (!$updatingApp || $updating3dsx || $updatingSmdh) {
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService(getConfigValue('azure_connection_string'));
	}
	
	if (!$updatingApp || $updating3dsx) {
		$app3dsxMD5 = md5_file($_FILES['3dsx']['tmp_name']); //Get file hash
		$app3dsxBlobName = generateRandomString(); //Generate file blob name
		$app3dsxBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_3dsx') . '/' . $app3dsxBlobName; //Get Azure blob URL
		$blobRestProxy->createBlockBlob(getConfigValue('azure_container_3dsx'), $app3dsxBlobName, fopen($_FILES['3dsx']['tmp_name'], 'r')); //Upload blob to Azure Blob Service
	}
	
	if (!$updatingApp || $updatingSmdh) {
		$appSmdhMD5 = md5_file($_FILES['smdh']['tmp_name']);
		$appSmdhBlobName = generateRandomString();
		$appSmdhBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_smdh') . '/' . $appSmdhBlobName;
		$blobRestProxy->createBlockBlob(getConfigValue('azure_container_smdh'), $appSmdhBlobName, fopen($_FILES['smdh']['tmp_name'], 'r'));
	}
	
	if ($updatingApp) {
		$currentVersion = getArrayFromSQLQuery($mysqlConn, 'SELECT appver.versionId, appver.smdh, appver.smdh_md5 FROM appversions appver
															LEFT JOIN apps app ON appver.versionId = app.version
															WHERE app.guid = ? LIMIT 1', 's', [$guid])[0];
		
		if ($updating3dsx && !$updatingSmdh) {
			//Get current smdh URL and MD5
			$appSmdhBlobURL = $currentVersion['smdh'];
			$appSmdhMD5 = $currentVersion['smdh_md5'];
		}
		else if (!$updating3dsx) {
			//Get version ID
			$versionId = $currentVersion['versionId'];
		}
	}
	
	if (!$updatingApp || $updating3dsx) {
		//Insert app version
		$stmt = executePreparedSQLQuery($mysqlConn, 'INSERT INTO appversions (appGuid, number, 3dsx, smdh, 3dsx_md5, smdh_md5)
												VALUES (?, ?, ?, ?, ?, ?)', 'ssssss', [$guid, $appVersion, $app3dsxBlobURL, $appSmdhBlobURL, $app3dsxMD5, $appSmdhMD5], true);
		$versionId = $stmt->insert_id;
		$stmt->close();
	}
	else if ($updatingSmdh) {
		//Update current app version with smdh URL and MD5
		$stmt = executePreparedSQLQuery($mysqlConn, 'UPDATE appversions appver INNER JOIN apps app ON appver.versionId = app.version
														SET versionId = app.version, smdh = ?, smdh_md5 = ?
														WHERE app.guid = ? AND appver.versionId = app.version', 'sss', [$guid, $appSmdhBlobURL, $appSmdhMD5], true);
	}
	
	if (!$updatingApp) {
		//Insert app
		executePreparedSQLQuery($mysqlConn, 'INSERT INTO apps (guid, name, publisher, version, description, category, publishstate)
												VALUES (?, ?, ?, ?, ?, ?, ?)',
												'ssiisii', [$guid, $appName, $_SESSION['user_id'], $versionId, $appDescription, $appCategory, $isDeveloper ? 1 : 0]);
	}
	else {
		//Update app row
		executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET name = ?, version = ?, description = ?, category = ?, publishstate = ?
												WHERE guid = ?',
												'sisiis', [$appName, $versionId, $appDescription, $appCategory, $isDeveloper ? 1 : 0, $guid]);
	}
	
	if ($isDeveloper) {
		echo 'Your application has been published.';
	}
	else {
		echo 'Your application has been submitted and is now waiting approval from our staff.';
	}
?>