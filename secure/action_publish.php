<?php
	/*
		DownloadMii Publishing Handler
	*/
	
	require_once('../common/user.php');
	require_once('../common/smdh.php');
	require_once('../common/recaptchalib.php');
	require_once('../vendor/autoload.php');
	
	use WindowsAzure\Common\ServicesBuilder;
	
	class blob {
		public $md5;
		public $name;
		public $url;
		public $fileHandle;
		
		public function upload($blobRestProxy, $container, $filePath) {
			$this->md5 = md5_file($filePath); //Get file hash
			$this->name = generateRandomString(); //Generate file blob name
			$this->url = 'https://' . getConfigValue('azure_storage_account') . '.blob.core.windows.net/' . $container . '/' . $this->name; //Get Azure blob URL
			
			$this->fileHandle = fopen($filePath, 'r');
			$blobRestProxy->createBlockBlob($container, $this->name, $fileHandle); //Upload blob to Azure Blob Service
		}
		
		public function closeFileHandle() {
			fclose($this->fileHandle);
		}
	}
	
	if (isset($_POST['guidid'], $_SESSION['publish_app_guid' . $_POST['guidid']])) {
		$guid = $_SESSION['publish_app_guid' . $_POST['guidid']]; //Get GUID
		
		if (isset($_SESSION['publish_token' . $guid])) { //Check if session publishing token is set
			try {
				$publishToken = $_SESSION['publish_token' . $guid];
		
				sendResponseCodeAndExitIfTrue(!clientLoggedIn(), 403);
				printAndExitIfTrue($_SESSION['user_role'] < 1, 'You do not have permission to publish apps.');
				
				throwExceptionIfTrue(!(isset($_POST['name'], $_POST['version'], $_POST['category'], $_POST['description'], $_FILES['3dsx'], $_FILES['smdh'], $_POST["g-recaptcha-response"], $_POST['publishtoken'])), 'One or more required POST variables have not been set.'); //Check if all expected POST vars are set
				throwExceptionIfTrue(empty($_POST['name']) || empty($_POST['version']), 'Please fill all required fields.'); //Check if fields aren't empty
				throwExceptionIfTrue(md5($publishToken) !== $_POST['publishtoken'], 'Incorrect or invalid publishing token.'); //Check if POST publishing token is correct
				
				$subCategorySelected = isset($_POST['subcategory']) && $_POST['subcategory'] !== '';
				throwExceptionIfTrue(!is_numeric($_POST['category']) || ($subCategorySelected && !is_numeric($_POST['subcategory'])), 'Please select a category.'); //Check if category selected
				
				//Check POST var lengths
				throwExceptionIfTrue(mb_strlen($_POST['name']) > 32, 'App name is too long.');
				throwExceptionIfTrue(mb_strlen($_POST['version']) > 12, 'Version is too long.');
				throwExceptionIfTrue(mb_strlen($_POST['description']) > 300, 'Description is too long.');
				
				//Check captcha
				$reCaptcha = new ReCaptcha(getConfigValue('apikey_recaptcha_secret'));
				$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
				throwExceptionIfTrue($resp == null || !$resp->success, 'Invalid or no captcha response.');
				
				$appName = escapeHTMLChars($_POST['name']);
				$appVersion = escapeHTMLChars($_POST['version']);
				$appCategory = $_POST['category'];
				$appSubCategory = $subCategorySelected ? $_POST['subcategory'] : null;
				$appDescription = escapeHTMLChars(str_replace(['\r\n', '\r', '\n'], ' ', $_POST['description']));
				
				$app3dsxPath = $_FILES['3dsx']['tmp_name'];
				$appSmdhPath = $_FILES['smdh']['tmp_name'];
				$appDataPath = $_FILES['appdata']['tmp_name'];
				
				$isDeveloper = $_SESSION['user_role'] > 1;
				$updatingApp = isset($_SESSION['user_app_version' . $guid]);
				
				$updatingAppData = is_uploaded_file($appDataPath);
				if (!$updatingApp) {
					throwExceptionIfTrue(!is_uploaded_file($app3dsxPath) || !is_uploaded_file($appSmdhPath), 'Please upload the required files.');
				}
				else {
					$updating3dsx = is_uploaded_file($app3dsxPath);
					$updatingSmdh = is_uploaded_file($appSmdhPath);
					
					//Check that if 3dsx/appdata is changed, the version also is
					throwExceptionIfTrue($_SESSION['user_app_version' . $guid] === $_POST['version'] && ($updating3dsx || $updatingAppData), 'Please also update the version number when uploading a new 3dsx/appdata file.');
				}
				
				//Check which screenshots were uploaded
				$screenshotsUploaded = array();
				for ($i = 1; $i <= 4; $i++) {
					array_push($screenshotsUploaded, isset($_FILES['scr' . $i]) && is_uploaded_file($_FILES['scr' . $i]['tmp_name']));
				}
				
				$mysqlConn = connectToDatabase();
				
				//Check if categories exist and are valid
				$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId FROM categories WHERE categoryId = ? AND parent IS NULL', 'i', [$appCategory]);
				throwExceptionIfTrue(count($categories) != 1, 'Invalid category ID.');
				if ($subCategorySelected) {
					$subCategories = getArrayFromSQLQuery($mysqlConn, 'SELECT cat.categoryId FROM categories cat
																		LEFT JOIN categories parentcat ON cat.parent = parentcat.categoryId
																		WHERE cat.categoryId = ? AND parentcat.parent IS NULL', 'i', [$appSubCategory]);
					
					throwExceptionIfTrue(count($categories) != 1, 'Invalid subcategory ID.');
				}
				
				//Initialize Azure Blob Service if files will be uploaded
				if (!$updatingApp || $updating3dsx || $updatingSmdh || $updatingAppData || count($screenshotsUploaded) > 0) {
					$blobRestProxy = ServicesBuilder::getInstance()->createBlobService(getConfigValue('azure_connection_string'));
				}
				
				$app3dsxBlob = new blob();
				if (!$updatingApp || $updating3dsx) {
					$app3dsxBlob->upload($blobRestProxy, getConfigValue('azure_container_3dsx'), $app3dsxPath);
					$app3dsxBlob->closeFileHandle();
				}
				
				$appSmdhBlob = new blob();
				$appIconBlob = new blob();
				if (!$updatingApp || $updatingSmdh) {
					$appSmdhBlob->upload($blobRestProxy, getConfigValue('azure_container_smdh'), $appSmdhPath);
					
					//Upload large PNG icon (we don't include the small one because it's often improperly encoded(?))
					$smdhData = new smdh($appSmdhBlob->fileHandle);
					
					$appIcon = tmpfile(); //Create temporary file to save PNG
					imagepng($smdhData->getLargeIcon(), stream_get_meta_data($appIcon)['uri']);
					
					$appIconBlob->upload($blobRestProxy, getConfigValue('azure_container_largeicon'), stream_get_meta_data($appIcon)['uri']);
					
					$appIconBlob->closeFileHandle();
					$appSmdhBlob->closeFileHandle();
					$smdhData = null;
				}
				
				$appDataBlob = new blob();
				if ($updatingAppData) {
					$appDataBlob->upload($blobRestProxy, getConfigValue('azure_container_appdata'), $appDataPath);
					$appDataBlob->closeFileHandle();
				}
				else if (!$updatingApp) {
					$appDataBlob->url = null;
					$appDataBlob->md5 = null;
				}
				
				if ($updatingApp) {
					$currentVersion = getArrayFromSQLQuery($mysqlConn, 'SELECT appver.versionId, appver.3dsx, appver.smdh, appver.appdata, appver.largeIcon, appver.3dsx_md5, appver.smdh_md5, appver.appdata_md5 FROM appversions appver
																		LEFT JOIN apps app ON appver.versionId = app.version
																		WHERE app.guid = ? LIMIT 1', 's', [$guid])[0];
					
					if (!$updating3dsx && !$updatingAppData) {
						//Get current version ID
						$versionId = $currentVersion['versionId'];
					}
					if (!$updatingSmdh) {
						//Get current smdh URL and MD5, plus icon URL
						$appSmdhBlob->url = $currentVersion['smdh'];
						$appSmdhBlob->md5 = $currentVersion['smdh_md5'];
						$appIconBlob->url = $currentVersion['largeIcon'];
					}
					
					if (!$updating3dsx && $updatingAppData) {
						//Get current 3dsx URL and MD5
						$app3dsxBlob->url = $currentVersion['3dsx'];
						$app3dsxBlob->md5 = $currentVersion['3dsx_md5'];
					}
					else if (!$updatingAppData && $updating3dsx) {
						//Get current appdata URL and MD5
						$appDataBlob->url = $currentVersion['appdata'];
						$appDataBlob->md5 = $currentVersion['appdata_md5'];
					}
				}
				
				if (!$updatingApp || $updating3dsx || $updatingAppData) {
					//Insert app version
					$stmt = executePreparedSQLQuery($mysqlConn, 'INSERT INTO appversions (appGuid, number, 3dsx, smdh, appdata, largeIcon, 3dsx_md5, smdh_md5, appdata_md5)
																	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', 'sssssssss', [$guid, $appVersion, $app3dsxBlob->url, $appSmdhBlob->url, $appDataBlob->url, $appIconBlob->url, $app3dsxBlob->md5, $appSmdhBlob->md5, $appDataBlob->md5], true);
					$versionId = $stmt->insert_id;
					$stmt->close();
				}
				else if ($updatingSmdh) {
					//Update current app version with smdh URL and MD5
					$stmt = executePreparedSQLQuery($mysqlConn, 'UPDATE appversions appver INNER JOIN apps app ON appver.versionId = app.version
																	SET versionId = app.version, smdh = ?, largeIcon = ?, smdh_md5 = ?
																	WHERE app.guid = ? AND appver.versionId = app.version', 'ssss', [$guid, $appSmdhBlob->url, $appIconBlob->url, $appSmdhBlob->md5]);
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
				
				unset($_SESSION['publish_app_guid' . $_POST['guidid']]);
				
				for ($i = 1; $i <= 4; $i++) {
					//If screenshot is uploaded...
					if ($screenshotsUploaded[$i - 1]) {
						//...push it to storage and insert/update a database row for it
						$appScreenshotBlob = new blob();
						$appScreenshotBlob->upload($blobRestProxy, getConfigValue('azure_container_screenshots'), $_FILES['scr' . $i]['tmp_name']);
						$appScreenshotBlob->closeFileHandle();
						
						executePreparedSQLQuery($mysqlConn, 'INSERT INTO screenshots (appGuid, imageIndex, url)
																VALUES (?, ?, ?)
																ON DUPLICATE KEY UPDATE url=?',
																'siss', [$guid, $i, $appScreenshotBlob->url, $appScreenshotBlob->url]);
					}
				}
				
				unset($_SESSION['myapps_token' . $guid]);
				unset($_SESSION['publish_token' . $guid]);
				
				if ($isDeveloper) {
					echo 'Your application has been published.';
				}
				else {
					echo 'Your application has been submitted and is now pending approval from our staff.';
				}
				
				exit();
			}
			catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>