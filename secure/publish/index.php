<?php
	/*
		DownloadMii App Publishing Page
	*/
	
	$title = 'Publish App';
	require_once('../../common/ucpheader.php');
	require_once('action.php');

	function generateDeleteButtonHTML($fileId) {
		echo '<input type="checkbox" id="del_' . $fileId . '" name="del_' . $fileId . '" value="yes" onclick="updateFileButton(\'' . $fileId . '\')">
				<label for="del_' . $fileId . '">Delete</label>';
	}
	
	if (isset($_GET['guid']) && isset($_SESSION['myapps_token' . $_GET['guid']])) {
		$myappsToken = $_SESSION['myapps_token' . $_GET['guid']];
	}
	
	if (clientLoggedIn()) {
		verifyGroup('Users');
		
		$guidId = uniqid(mt_rand(), true);
		$mysqlConn = connectToDatabase();
		
		$appToEdit = null;
		if (isset($_GET['guid'], $_GET['token'], $myappsToken) && md5($myappsToken) === $_GET['token']) {
			$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT guid, name, description, category, subcategory, rating, downloads, webicon, publishstate,
																appversions.number AS version, appversions.appdata, group_concat(screenshots.imageIndex) AS screenshots FROM apps
																LEFT JOIN appversions ON appversions.versionId = (SELECT versionId FROM appversions appver WHERE appver.appGuid = ? ORDER BY appver.versionId DESC LIMIT 1)
																LEFT JOIN screenshots ON screenshots.appGuid = ?
																WHERE guid = ? AND publisher = ?
																GROUP BY guid LIMIT 1', 'ssss', [$_GET['guid'], $_GET['guid'], $_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
			
			printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination

			$appToEdit = $matchingApps[0];
			 
			$_SESSION['publish_app_guid' . $guidId] = $appToEdit['guid'];
			$_SESSION['user_app_version' . $appToEdit['guid']] = $appToEdit['version'];
		}
		else {
			$_SESSION['publish_app_guid' . $guidId] = generateGUID();
		}
		
		if (!isset($_SESSION['publish_token' . $_SESSION['publish_app_guid' . $guidId]])) {
			$_SESSION['publish_token' . $_SESSION['publish_app_guid' . $guidId]] = uniqid(mt_rand(), true);
		}
		
		$editing = isset($appToEdit);
?>
		<h1 class="animated bounceInDown text-center"><?php if (isset($appToEdit)) echo 'Updating ' . escapeHTMLChars($appToEdit['name']); else echo 'Add a new application'; ?></h1>
		<br />
		
		<?php if (isset($errorMessage)) {
?>
		<div class="animated shake alert alert-danger">
			<a class="close" href="#" data-dismiss="alert">&times;</a>
			<strong>Error!</strong> <?php echo $errorMessage; ?>
		</div>

		<?php
		}
?>

		<div class="well">
			<form role="form" action="index.php<?php if (!empty($_SERVER['QUERY_STRING'])) echo '?' . $_SERVER['QUERY_STRING']; ?>" method="post" enctype="multipart/form-data" accept-charset="utf-8">
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="name">Name:</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="e.g. My Application" maxlength="32"<?php printAttributeValueFromChoices(@$_POST['name'], $appToEdit['name']); ?> required>
					</div>
					<div class="col-md-6 form-group">
						<label for="version">Version:</label>
						<input type="text" class="form-control" id="version" name="version" placeholder="e.g. 1.0.0.0" maxlength="12"<?php printAttributeValueFromChoices(@$_POST['version'], $appToEdit['version']); ?> required>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="category">Category:</label>
						<select class="form-control" id="category" name="category" onchange="updateSubCategories()" required>
							<option value="">Select a category...</option>
							<?php
								$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId, name FROM categories WHERE parent IS NULL ORDER BY name ASC');
								foreach ($categories as $category) {
									echo '<option value="' . $category['categoryId'] . '">' . $category['name'] . '</option>';
								}
?>

						</select>
					</div>
					<div class="col-md-6 form-group">
						<label for="subcategory">Subcategory (optional):</label>
						<select class="form-control" id="subcategory" name="subcategory">
							<option value=""></option>
							<?php
								if (isset($_POST['category']) || $editing) {
									echo 'yes';
									$subCategories = getArrayFromSQLQuery($mysqlConn, 'SELECT cat.categoryId, cat.name FROM categories cat
																						LEFT JOIN categories parentcat ON cat.parent = parentcat.categoryId
																						WHERE parentcat.categoryId = ? AND parentcat.parent IS NULL ORDER BY cat.name ASC', 'i', [getValueFromChoices(@$_POST['category'], $appToEdit['category'])]);
									
									foreach ($subCategories as $subCategory) {
										echo '<option value="' . $subCategory['categoryId'] . '">' . $subCategory['name'] . '</option>';
									}
								}
?>

						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="description">Description (300 character limit):</label>
					<textarea class="form-control" id="description" name="description" rows="6" maxlength="300"><?php printAttributeValueFromChoices(@$_POST['description'], $appToEdit['description'], false); ?></textarea>
				</div>

				<div class="row" style="margin-top: 48px;">
					<div class="col-md-6 form-group">
						<label for="3dsx">3dsx file:</label>
						<input type="file" class="filestyle <?php if ($editing) echo 'alreadyuploaded'; ?> " id="3dsx" name="3dsx" accept=".3dsx" <?php if (!$editing) echo 'required'; ?> >
					</div>
					<div class="col-md-6 form-group">
						<label for="smdh">smdh/icon file:</label>
						<input type="file" class="filestyle <?php if ($editing) echo 'alreadyuploaded'; ?> " id="smdh" name="smdh" accept=".smdh,.bin,.icn" <?php if (!$editing) echo 'required'; ?> >
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="appdata">Additional files ZIP archive (optional):</label>
						<input type="file" class="filestyle <?php if ($editing && $appToEdit['appdata'] !== null) echo 'alreadyuploaded'; ?> " id="appdata" name="appdata" accept=".zip">

						<?php if ($editing) generateDeleteButtonHTML('appdata'); ?>
					</div>
					<div class="col-md-6 form-group">
						<label for="webicon">Hi-res app icon (optional):</label>
						<input type="file" class="filestyle <?php if ($editing && $appToEdit['webicon'] !== null) echo 'alreadyuploaded'; ?> " id="webicon" name="webicon" accept=".jpg,.jpeg,.png">

						<?php if ($editing) generateDeleteButtonHTML('webicon'); ?>
					</div>
				</div>

				<div class="form-group" style="margin-bottom: 48px;">
					Additional files will be unpacked in the same directory as the 3dsx will be placed. The hi-res icon will automatically be resized to 400x400.
				</div>

				<?php
					$uploadedScreenshots = explode(',', $appToEdit['screenshots']);

					for ($i = 0; $i < ceil(getConfigValue('downloadmii_max_screenshots') / 2); $i++) {
						echo '<div class="row">';
						for ($j = 1; $j <= 2; $j++) {
							$imageIndex = $i * 2 + $j;
							
							if ($imageIndex < getConfigValue('downloadmii_max_screenshots') + 1) {
								echo '<div class="col-md-6 form-group">
										<label for="scr' . $imageIndex . '">Screenshot ' . $imageIndex . ' (optional):</label>
										<input type="file" class="filestyle ' . ($editing && in_array($imageIndex, $uploadedScreenshots) ? 'alreadyuploaded' : '') . '" id="scr' . $imageIndex . '" name="scr' . $imageIndex . '" accept=".jpg,.jpeg,.png">';

								if ($editing) {
									generateDeleteButtonHTML('scr' . $imageIndex);
								}

								echo '</div>';
							}
						}
						echo '</div>';
					}
				?>
				
				<div class="form-group">
					Please include either only the top screen or both screens in each screenshot. They should also be 1:1 to the 3DS screen resolution(s); 400x240 or 400x480.
				</div>

				<div class="form-group" style="margin-top: 48px;">
					<div class="g-recaptcha" data-sitekey="<?php echo getConfigValue('apikey_recaptcha_site'); ?>"></div>
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
				<input type="hidden" name="publishtoken" value="<?php echo md5($_SESSION['publish_token' . $_SESSION['publish_app_guid' . $guidId]]); ?>">
				<input type="hidden" name="guidid" value="<?php echo $guidId; ?>">
			</form>
		</div>
		<script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>
		<?php
			$mysqlConn->close();
?>

		<script type="text/javascript">
		var addOption = function(selectElement, text, value) {
			var option = document.createElement('option');
			option.text = text;
			option.value = value;
			selectElement.add(option);
		}
		
		var removeAllOptions = function(selectElement) {
			while (selectElement.options.length > 0) {
				selectElement.remove(0);
			}
		}
		
		var updateSubCategories = function() {
			var categorySelectElement = document.getElementById('category');
			var subCategorySelectElement = document.getElementById('subcategory');
			
			removeAllOptions(subCategorySelectElement);
			
			if (categorySelectElement.value !== '') {
				addOption(subCategorySelectElement, 'Loading subcategories...', '');

				$.ajax('/newapi/categories/' + categorySelectElement.options[categorySelectElement.selectedIndex].text, {
					type: "GET",
					dataType: "json",
					success: function (categoriesObject) {
						categoriesObject.Subcategories.sort(function (a, b) {
							var nameA = a.name.toLowerCase();
							var nameB = b.name.toLowerCase();

							if (nameA > nameB) {
								return 1;
							}
							if (nameA < nameB) {
								return -1;
							}
							return 0;
						});

						removeAllOptions(subCategorySelectElement);

						if (categoriesObject.Subcategories.length > 0) {
							addOption(subCategorySelectElement, 'Select a category...', '');
							for (var i = 0; i < categoriesObject.Subcategories.length; i++) {
								addOption(document.getElementById('subcategory'), categoriesObject.Subcategories[i].name, categoriesObject.Subcategories[i].categoryId);
							}
						}
					},
					error: function() {
						subCategorySelectElement.options[0].text = 'Failed to get subcategories.';
					}
				});
			}
		}

		var updateFileButton = function(fileId) {
			var disable = $('#del_' + fileId).prop('checked');
			$('#' + fileId).prop('disabled', disable);

			//Enable/disable upload button
			var fileButton = $('#' + fileId).nextUntil('label').find('label');
			if (disable) {
				fileButton.addClass('disabled');
			}
			else {
				fileButton.removeClass('disabled');
			}
		}

		document.addEventListener("DOMContentLoaded", function(event) {
			$('.alreadyuploaded').nextUntil('bootstrap-filestyle').find('label').each(function(index) {
				$(this).contents().last().replaceWith(' Choose replacement');
			});

		<?php
			$categoryValue = getValueFromChoices(@$_POST['category'], $appToEdit['category']);
			if ($categoryValue) {
				echo "$('#category').val(" . $categoryValue . ");";
				
				$subCategoryValue = getValueFromChoices(@$_POST['subcategory'], $appToEdit['subcategory']);
				if ($subCategoryValue) {
					echo "$('#subcategory').val(" . $subCategoryValue . ");";
				}
			}
?>

		});
		</script>
		
	<?php
		}
	require_once('../../common/ucpfooter.php');
?>
