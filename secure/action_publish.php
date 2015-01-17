<?php
	/*
		DownloadMii Publishing Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	require_once('../common/smdh.php');
	require_once('../common/recaptchalib.php');
	require_once('../vendor/autoload.php');
	
	use WindowsAzure\Common\ServicesBuilder;
	
	$_SESSION['saved_desc'] = $_POST['description']; //temporary solution
	
	sendResponseCodeAndExitIfTrue(!isset($_SESSION['publish_token']), 422); //Check if session publishing token is set
	$publishToken = $_SESSION['publish_token'];
	unset($_SESSION['publish_token']);
	
	sendResponseCodeAndExitIfTrue(!clientLoggedIn(), 403);
	printAndExitIfTrue($_SESSION['user_role'] < 1, 'You do not have permission to publish apps.');
	
	sendResponseCodeAndExitIfTrue(!(isset($_POST['name'], $_POST['version'], $_POST['category'], $_POST['description'], $_FILES['3dsx'], $_FILES['smdh'], $_POST["g-recaptcha-response"], $_POST['publishtoken'])), 400); //Check if all expected POST vars are set
	printAndExitIfTrue(empty($_POST['name']) || empty($_POST['version']), 'Please fill all required fields.'); //Check if fields aren't empty
	sendResponseCodeAndExitIfTrue(md5($publishToken) !== $_POST['publishtoken'], 422); //Check if POST publishing token is correct
	
	$subCategorySelected = isset($_POST['subcategory']) && $_POST['subcategory'] !== '';
	sendResponseCodeAndExitIfTrue(!is_numeric($_POST['category']) || ($subCategorySelected && !is_numeric($_POST['subcategory'])), 422); //Check if category selected
	
	//Check POST var lengths
	printAndExitIfTrue(mb_strlen($_POST['name']) > 32, 'App name is too long.');
	printAndExitIfTrue(mb_strlen($_POST['version']) > 12, 'Version is too long.');
	printAndExitIfTrue(mb_strlen($_POST['description']) > 300, 'Description is too long.');
	
	//Check captcha
	$reCaptcha = new ReCaptcha(getConfigValue('apikey_recaptcha_secret'));
	$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
	printAndExitIfTrue($resp == null || !$resp->success, 'Invalid or no captcha response.');
	
	$appName = escapeHTMLChars($_POST['name']);
	$appVersion = escapeHTMLChars($_POST['version']);
	$appCategory = $_POST['category'];
	$appSubCategory = $subCategorySelected ? $_POST['subcategory'] : null;
	$appDescription = escapeHTMLChars(str_replace(['\r\n', '\r', '\n'], ' ', $_POST['description']));
	
	$app3dsxPath = $_FILES['3dsx']['tmp_name'];
	$appSmdhPath = $_FILES['smdh']['tmp_name'];
	
	$isDeveloper = $_SESSION['user_role'] > 1;
	$updatingApp = isset($_SESSION['user_app_guid']);
	
	if (!$updatingApp) {
		$guid = generateGUID(); //Generate GUID if not updating app
		printAndExitIfTrue(!is_uploaded_file($app3dsxPath) || !is_uploaded_file($appSmdhPath), 'Please upload the required files.');
	}
	else {
		$guid = $_SESSION['user_app_guid'];
		$updating3dsx = is_uploaded_file($app3dsxPath);
		$updatingSmdh = is_uploaded_file($appSmdhPath);
		
		//Check that if one of 3dsx, version is changed, the other also is
		if ($_SESSION['user_app_version'] !== $_POST['version'] || $updating3dsx) {
			printAndExitIfTrue($_SESSION['user_app_version'] === $_POST['version'] || !$updating3dsx, 'Please update both the version number and 3dsx file at the same time.');
		}
	}
	
	$mysqlConn = connectToDatabase();
	
	//Check if categories exist and are valid
	$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId FROM categories WHERE categoryId = ? AND parent IS NULL', 'i', [$appCategory]);
	printAndExitIfTrue(count($categories) != 1, 'Invalid category ID.');
	if ($subCategorySelected) {
		$subCategories = getArrayFromSQLQuery($mysqlConn, 'SELECT cat.categoryId FROM categories cat
															LEFT JOIN categories parentcat ON cat.parent = parentcat.categoryId
															WHERE cat.categoryId = ? AND parentcat.parent IS NULL', 'i', [$appSubCategory]);
		
		printAndExitIfTrue(count($categories) != 1, 'Invalid subcategory ID.');
	}
	
	//Initialize Azure Blob Service if files will be uploaded
	if (!$updatingApp || $updating3dsx || $updatingSmdh) {
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService(getConfigValue('azure_connection_string'));
	}
	
	if (!$updatingApp || $updating3dsx) {
		$app3dsxMD5 = md5_file($app3dsxPath); //Get file hash
		$app3dsxBlobName = generateRandomString(); //Generate file blob name
		$app3dsxBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_3dsx') . '/' . $app3dsxBlobName; //Get Azure blob URL
		
		$app3dsxFile = fopen($app3dsxPath, 'r');
		$blobRestProxy->createBlockBlob(getConfigValue('azure_container_3dsx'), $app3dsxBlobName, $app3dsxFile); //Upload blob to Azure Blob Service
		fclose($app3dsxFile);
	}
	
	if (!$updatingApp || $updatingSmdh) {
		$appSmdhMD5 = md5_file($appSmdhPath);
		$appSmdhBlobName = generateRandomString();
		$appSmdhBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_smdh') . '/' . $appSmdhBlobName;
		
		$appSmdhFile = fopen($appSmdhPath, 'r');
		$blobRestProxy->createBlockBlob(getConfigValue('azure_container_smdh'), $appSmdhBlobName, $appSmdhFile);
		
		//Upload PNG icon
		try {
			$smdhData = new smdh($appSmdhFile);
		}
		catch (Exception $e) {
			printAndExit($e->getMessage());
		}
		
		$appPNG = tmpfile(); //Create temporary file to save PNG
		imagepng($smdhData->getLargeIcon(), stream_get_meta_data($appPNG)['uri']);
		
		$appPNGBlobName = generateRandomString();
		$appPNGBlobURL = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . getConfigValue('azure_container_largeicon') . '/' . $appPNGBlobName;
		$blobRestProxy->createBlockBlob(getConfigValue('azure_container_largeicon'), $appPNGBlobName, $appPNG);
		
		fclose($appPNG);
		fclose($appSmdhFile);
		$smdhData = null;
	}
	
	if ($updatingApp) {
		$currentVersion = getArrayFromSQLQuery($mysqlConn, 'SELECT appver.versionId, appver.smdh, appver.largeIcon, appver.smdh_md5 FROM appversions appver
															LEFT JOIN apps app ON appver.versionId = app.version
															WHERE app.guid = ? LIMIT 1', 's', [$guid])[0];
		
		if ($updating3dsx && !$updatingSmdh) {
			//Get current smdh URL and MD5
			$appSmdhBlobURL = $currentVersion['smdh'];
			$appPNGBlobURL = $currentVersion['largeIcon'];
			$appSmdhMD5 = $currentVersion['smdh_md5'];
		}
		else if (!$updating3dsx) {
			//Get version ID
			$versionId = $currentVersion['versionId'];
		}
	}
	
	if (!$updatingApp || $updating3dsx) {
		//Insert app version
		$stmt = executePreparedSQLQuery($mysqlConn, 'INSERT INTO appversions (appGuid, number, 3dsx, smdh, largeIcon, 3dsx_md5, smdh_md5)
												VALUES (?, ?, ?, ?, ?, ?, ?)', 'sssssss', [$guid, $appVersion, $app3dsxBlobURL, $appSmdhBlobURL, $appPNGBlobURL, $app3dsxMD5, $appSmdhMD5], true);
		$versionId = $stmt->insert_id;
		$stmt->close();
	}
	else if ($updatingSmdh) {
		//Update current app version with smdh URL and MD5
		$stmt = executePreparedSQLQuery($mysqlConn, 'UPDATE appversions appver INNER JOIN apps app ON appver.versionId = app.version
														SET versionId = app.version, smdh = ?, largeIcon = ?, smdh_md5 = ?
														WHERE app.guid = ? AND appver.versionId = app.version', 'ssss', [$guid, $appSmdhBlobURL, $appPNGBlobURL, $appSmdhMD5], true);
	}
	
	if (!$updatingApp) {
		//Insert app
		executePreparedSQLQuery($mysqlConn, 'INSERT INTO apps (guid, name, publisher, version, description, category, subcategory, publishstate)
												VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
												'ssiisiii', [$guid, $appName, $_SESSION['user_id'], $versionId, $appDescription, $appCategory, $appSubCategory, $isDeveloper ? 1 : 0]);
	}
	else {
		//Update app row
		executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET name = ?, version = ?, description = ?, category = ?, subcategory = ?, publishstate = ?
												WHERE guid = ?',
												'sisiiis', [$appName, $versionId, $appDescription, $appCategory, $appSubCategory, $isDeveloper ? 1 : 0, $guid]);
	}
	
	unset($_SESSION['saved_desc']);
	
	if ($isDeveloper) {
		echo 'Your application has been published.';
	}
	else {
		echo 'Your application has been submitted and is now pending approval from our staff.';
	}
?>