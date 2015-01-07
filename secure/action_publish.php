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
	$publishToken = md5($_SESSION['publish_token']);
	unset($_SESSION['publish_token']);
	
	sendResponseCodeAndExitIfTrue(!clientLoggedIn(), 403);
	printAndExitIfTrue($_SESSION['user_role'] < 1, 'You do not have permission to publish apps.');
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['name'], $_POST['version'], $_POST['category'], $_POST['description'], $_FILES['3dsx'], $_FILES['smdh'], $_POST["g-recaptcha-response"], $_POST['publishtoken'])), 400); //Check if all expected POST vars are set
	sendResponseCodeAndExitIfTrue($publishToken !== $_POST['publishtoken'], 422); //Check if POST publishing token is correct
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
	$appDescription = htmlspecialchars($_POST['description']);
	
	if (isset($_SESSION['user_app_guid'])) {
		printAndExitIfTrue(true, 'Updating apps is not supported yet.');
		//TODO: Check that if one of 3dsx, smdh, version is changed, the others also are.
		//TODO: Do everything else necessary to update app
	}
	
	//TODO: Check if files are valid
	
	$mysqlConn = connectToDatabase();
	
	//Check if category exists
	$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId FROM categories WHERE categoryId = ? AND type = 0', 'i', [$_POST['category']]);
	printAndExitIfTrue(count($categories) != 1, 'Invalid category ID.');
	
	//Upload files to Azure Blob Service
	$app3dsxBlobName = generateRandomString();
	$appSmdhBlobName = generateRandomString();
	
	$blobRestProxy = ServicesBuilder::getInstance()->createBlobService(getConfigValue('azure_connection_string'));
	$blobRestProxy->createBlockBlob(getConfigValue('azure_container_3dsx'), $app3dsxBlobName, fopen($_FILES['3dsx']['tmp_name'], 'r'));
	$blobRestProxy->createBlockBlob(getConfigValue('azure_container_smdh'), $appSmdhBlobName, fopen($_FILES['smdh']['tmp_name'], 'r'));
	
	$app3dsxBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_3dsx') . '/' . $app3dsxBlobName;
	$appSmdhBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_smdh') . '/' . $appSmdhBlobName;
	
	//Insert app version
	$stmt = executePreparedSQLQuery($mysqlConn, 'INSERT INTO appversions (number, 3dsx, smdh)
											VALUES (?, ?, ?)', 'sss', [$appVersion, $app3dsxBlobURL, $appSmdhBlobURL], true);
	$versionId = $stmt->insert_id;
	$stmt->close();
	
	//Insert app
	if (!isset($_SESSION['user_app_guid'])) {
		executePreparedSQLQuery($mysqlConn, 'INSERT INTO apps (guid, name, publisher, version, description, category, publishstate)
												VALUES (?, ?, ?, ?, ?, ?, ?)',
												'ssiisii', [generateGUID(), $appName, $_SESSION['user_id'], $versionId, $appDescription, $appCategory, $_SESSION['user_role'] < 2 ? 0 : 1]);
	}
	
	echo 'Your application has been submitted and is now waiting approval from our staff.';
?>