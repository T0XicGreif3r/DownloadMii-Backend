<?php
	/*
		DownloadMii Publishing Handler
	*/
	
	require_once('../../common/user.php');
	require_once('../../common/smdh.php');
	require_once('../../common/recaptchalib.php');
	require_once('../../vendor/autoload.php');
	
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
			$blobRestProxy->createBlockBlob($container, $this->name, $this->fileHandle); //Upload blob to Azure Blob Service
		}
		
		public function closeFileHandle() {
			fclose($this->fileHandle);
		}
	}
	
	function processScreenshot($filename) {
		$originalSizeInfo = getimagesize($filename); //Get input image size and MIME information
		$originalWidth = $originalSizeInfo[0];
		$originalHeight = $originalSizeInfo[1];
		$originalMIME = $originalSizeInfo['mime'];
		
		if ($originalWidth !== 400 || ($originalHeight !== 240 && $originalHeight !== 480)) { //If width isn't 400, and height isn't 240 or 480...
			//...do some resizing
			
			$image = null;
			switch ($originalMIME) {
				case 'image/jpeg':
					$image = imagecreatefromjpeg($filename);
					break;
				
				case 'image/png':
					$image = imagecreatefrompng($filename);
					break;
			}
			
			$scale = 400 / $originalWidth; //Calculate horizontal scaling
			$newWidth = 400;
			$newHeight = $originalHeight * $scale / 480 >= 0.75 ? 480 : 240; //Estimate if the image is of the top screen only or both screens, and adjust the height for that
			
			$resizedImage = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight); //Resize the image
			
			$screenshot = tmpfile(); //Create temporary file to save PNG
			imagepng($resizedImage, stream_get_meta_data($screenshot)['uri']); //Save processed screenshot
			imagedestroy($resizedImage);
			
			return $screenshot; //Return temporary screenshot file handle
		}
		else {
			return fopen($filename, 'r'); //We didn't do anything, return the file handle of the original screenshot
		}
	}
	
	if (isset($_POST['guidid'], $_SESSION['publish_app_guid' . $_POST['guidid']])) {
		$guid = $_SESSION['publish_app_guid' . $_POST['guidid']]; //Get GUID
		
		if (isset($_SESSION['publish_token' . $guid])) { //Check if session publishing token is set
			try {
				$publishToken = $_SESSION['publish_token' . $guid];
		
				sendResponseCodeAndExitIfTrue(!clientLoggedIn(), 403);
				verifyRole(1);
				
				throwExceptionIfTrue(!(isset($_POST['name'], $_POST['version'], $_POST['category'], $_POST['description'], $_FILES['3dsx'], $_FILES['smdh'], $_POST["g-recaptcha-response"], $_POST['publishtoken'])), 'One or more required POST variables have not been set.'); //Check if all expected POST vars are set
				throwExceptionIfTrue(empty($_POST['name']) || empty($_POST['version']), 'Please fill all required fields.'); //Check if fields aren't empty
				throwExceptionIfTrue(md5($publishToken) !== $_POST['publishtoken'], 'Incorrect or invalid publishing token.'); //Check if POST publishing token is correct
				
				$subCategorySelected = isset($_POST['subcategory']) && $_POST['subcategory'] !== '';
				throwExceptionIfTrue(!is_numeric($_POST['category']) || ($subCategorySelected && !is_numeric($_POST['subcategory'])), 'Please select a category.'); //Check if category selected
				
				//Check POST var lengths
				throwExceptionIfTrue(mb_strlen($_POST['name']) > 32, 'App name is too long.');
				throwExceptionIfTrue(mb_strlen($_POST['version']) > 12, 'Version is too long.');
				throwExceptionIfTrue(mb_strlen($_POST['description']) > 300, 'Description is too long.');
				
				//Check file upload errors
				foreach ($_FILES as $file) {
					throwExceptionIfTrue($file['error'] === 1 || $file['error'] === 2, $file['name'] . ' exceeds the file size limit.');
					throwExceptionIfTrue($file['error'] === 3, $file['name'] . ' wasn\'t fully uploaded.');
					throwExceptionIfTrue($file['error'] > 4, $file['name'] . ' encountered an internal error upon upload: ' . $file['error']);
				}
				
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
				for ($i = 1; $i <= getConfigValue('downloadmii_max_screenshots'); $i++) {
					array_push($screenshotsUploaded, isset($_FILES['scr' . $i]) && is_uploaded_file($_FILES['scr' . $i]['tmp_name']));
					
					if ($screenshotsUploaded[$i - 1]) {
						//Verify that image is JPEG/PNG
						$imageMIME = getimagesize($_FILES['scr' . $i]['tmp_name'])['mime'];
						throwExceptionIfTrue(!($imageMIME && ($imageMIME === 'image/jpeg' || $imageMIME === 'image/png')), 'Invalid screenshot file type. Screenshots must be in JPEG or PNG format.');
					}
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
					
					$currentVersionId = $currentVersion['versionId'];
					
					$currentPublishState = getArrayFromSQLQuery($mysqlConn, 'SELECT publishstate FROM apps WHERE guid = ? LIMIT 1', 's', [$guid])[0]['publishstate'];
					
					if (!$updatingSmdh) {
						//Get current smdh URL and MD5, plus icon URL
						$appSmdhBlob->url = $currentVersion['smdh'];
						$appSmdhBlob->md5 = $currentVersion['smdh_md5'];
						$appIconBlob->url = $currentVersion['largeIcon'];
					}
					
					if (!$updating3dsx) {
						//Get current 3dsx URL and MD5
						$app3dsxBlob->url = $currentVersion['3dsx'];
						$app3dsxBlob->md5 = $currentVersion['3dsx_md5'];
					}
					
					if (!$updatingAppData) {
						//Get current appdata URL and MD5
						$appDataBlob->url = $currentVersion['appdata'];
						$appDataBlob->md5 = $currentVersion['appdata_md5'];
					}
				}
				
				if (!$updatingApp || $updating3dsx || $updatingSmdh || $updatingAppData) {
					//Insert app version
					$stmt = executePreparedSQLQuery($mysqlConn, 'INSERT INTO appversions (appGuid, number, 3dsx, smdh, appdata, largeIcon, 3dsx_md5, smdh_md5, appdata_md5)
																	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', 'sssssssss', [$guid, $appVersion, $app3dsxBlob->url, $appSmdhBlob->url, $appDataBlob->url, $appIconBlob->url, $app3dsxBlob->md5, $appSmdhBlob->md5, $appDataBlob->md5], true);
					$versionId = $stmt->insert_id;
					$stmt->close();
				}
				
				if (!$updatingApp) {
					//Insert app
					
					$publishState = $isDeveloper ? 1 : 0;
					
					executePreparedSQLQuery($mysqlConn, 'INSERT INTO apps (guid, name, publisher, version, description, category, subcategory, publishstate)
															VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
															'ssiisiii', [$guid, $appName, $_SESSION['user_id'], $versionId, $appDescription, $appCategory, $appSubCategory, $publishState]);
				}
				else if ($updating3dsx || $updatingSmdh || $updatingAppData) {
					//Update app row, including version and publish state
					
					if ($currentPublishState !== 0) {
						if ($isDeveloper || ($updatingSmdh && !$updating3dsx && !updatingAppData)) {
							$publishState = 1; //Published
						}
						else {
							$publishState = 4; //Published, new version pending approval
						}
					}
					else {
						$publishState = 0; //Pending approval
					}
					
					executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET name = ?, version = ?, description = ?, category = ?, subcategory = ?, publishstate = ?
															WHERE guid = ?',
															'sisiiis', [$appName, $publishState === 1 ? $versionId : $currentVersionId, $appDescription, $appCategory, $appSubCategory, $publishState, $guid]);
				}
				else {
					//Update app row, but keep current version and publish state
					
					$publishState = $currentPublishState;
					
					executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET name = ?, description = ?, category = ?, subcategory = ?
															WHERE guid = ?',
															'ssiis', [$appName, $appDescription, $appCategory, $appSubCategory, $guid]);
				}
				
				unset($_SESSION['publish_app_guid' . $_POST['guidid']]);
				
				for ($i = 1; $i <= getConfigValue('downloadmii_max_screenshots'); $i++) {
					//If screenshot is uploaded...
					if ($screenshotsUploaded[$i - 1]) {
						//...push it to storage and insert/update a database row for it
						$appScreenshotBlob = new blob();
						$processedScreenshotHandle = processScreenshot($_FILES['scr' . $i]['tmp_name']);
						$appScreenshotBlob->upload($blobRestProxy, getConfigValue('azure_container_screenshots'), stream_get_meta_data($processedScreenshotHandle)['uri']);
						$appScreenshotBlob->closeFileHandle();
						
						executePreparedSQLQuery($mysqlConn, 'INSERT INTO screenshots (appGuid, imageIndex, url)
																VALUES (?, ?, ?)
																ON DUPLICATE KEY UPDATE url = ?',
																'siss', [$guid, $i, $appScreenshotBlob->url, $appScreenshotBlob->url]);
					}
				}
				
				unset($_SESSION['myapps_token' . $guid]);
				unset($_SESSION['publish_token' . $guid]);
				
				if ($isDeveloper || ($updatingApp && $currentPublishState === 1 && !$updating3dsx && !$updatingAppData)) {
					echo 'Your application has been published.';
				}
				else {
					if (!$updatingApp) {
						echo 'Your application has been submitted and is now pending approval from our staff.';
					}
					else {
						echo 'Your update has been submitted and is now pending approval from our staff. The current version is still available.';
					}
				}
				
				exit();
			}
			catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>