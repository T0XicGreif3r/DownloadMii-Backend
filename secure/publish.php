<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	if (isset($_SESSION['myapps_token'])) {
		$myappsToken = $_SESSION['myapps_token'];
		unset($_SESSION['myapps_token']);
	}
	
	if (isset($_SESSION['saved_desc'])) {
		$savedDesc = $_SESSION['saved_desc'];
		unset($_SESSION['saved_desc']);
	}
	
	unset($_SESSION['user_app_guid']); //Unset GUID setting
	
	if (clientLoggedIn()) {
		printAndExitIfTrue($_SESSION['user_role'] < 1, 'You do not have permission to publish apps.');
		
		$_SESSION['publish_token'] = uniqid(mt_rand(), true);
		
		$mysqlConn = connectToDatabase();
		
		
		if (isset($_GET['guid'], $_GET['token'], $myappsToken) && md5($myappsToken) === $_GET['token']) {
			$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.publisher, app.version, app.description, app.category, app.subcategory, app.rating, app.downloads, app.publishstate,
																appver.number AS version FROM apps app
																LEFT JOIN appversions appver ON appver.versionId = app.version
																WHERE app.guid = ? AND app.publisher = ? LIMIT 1', 'ss', [$_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
			
			printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination
			
			$appToEdit = $matchingApps[0];
			printAndExitIfTrue($matchingApps[0]['publishstate'] === 0, 'This app is pending approval and can not be updated at this time.'); //Check if app not pending approval
			
			$_SESSION['user_app_guid'] = $appToEdit['guid'];
			$_SESSION['user_app_version'] = $appToEdit['version'];
		}
		
		$editing = isset($appToEdit);
?>
		<h1 class="text-center"><?php if (isset($appToEdit)) echo 'Updating ' . escapeHTMLChars($appToEdit['name']); else echo 'Add a new application'; ?></h1>
		<br />
		<div class="well">
			<form role="form" action="action_publish.php" method="post" enctype="multipart/form-data" accept-charset="utf-8">
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="name">Name:</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="e.g. My Application" maxlength="32" value="<?php if ($editing) echo escapeHTMLChars($appToEdit['name']); ?>" required>
					</div>
					<div class="col-md-6 form-group">
						<label for="version">Version:</label>
						<input type="text" class="form-control" id="version" name="version" placeholder="e.g. 1.0.0.0" maxlength="12" value="<?php if ($editing) echo escapeHTMLChars($appToEdit['version']); ?>" required>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="category">Category:</label>
						<select class="form-control" id="category" name="category" required>
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
								if ($editing) {
									$subCategories = getArrayFromSQLQuery($mysqlConn, 'SELECT cat.categoryId, cat.name FROM categories cat
																						LEFT JOIN categories parentcat ON cat.parent = parentcat.categoryId
																						WHERE parentcat.categoryId = ? AND parentcat.parent IS NULL ORDER BY cat.name ASC', 'i', [$appToEdit['category']]);
									
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
					<textarea class="form-control" id="description" name="description" rows="6" maxlength="300"><?php if (isset($savedDesc)) echo $savedDesc; else if ($editing) echo escapeHTMLChars($appToEdit['description']); ?></textarea>
				</div>
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="3dsx">3dsx file<?php if ($editing) echo ' (only upload if you want to update)'; ?>:</label>
						<input type="file" class="filestyle" id="3dsx" name="3dsx" accept=".3dsx"<?php if (!$editing) echo ' required'; ?>>
					</div>
					<div class="col-md-6 form-group">
						<label for="smdh">smdh file<?php if ($editing) echo ' (only upload if you want to update)'; ?>:</label>
						<input type="file" class="filestyle" id="smdh" name="smdh" accept=".smdh,.bin,.icn"<?php if (!$editing) echo ' required'; ?>>
					</div>
				</div>
				<div class="form-group">
					<div class="g-recaptcha" data-sitekey="<?php echo getConfigValue('apikey_recaptcha_site'); ?>"></div>
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
				<input type="hidden" name="publishtoken" value="<?php echo md5($_SESSION['publish_token']); ?>">
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
				var httpRequest = new XMLHttpRequest();
				httpRequest.onreadystatechange = function() {
					if (httpRequest.readyState == 4 && httpRequest.status == 200) {
						var categoriesObject = JSON.parse(httpRequest.responseText);
						
						categoriesObject.Subcategories.sort(function(a, b) {
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
					}
				}
				
				addOption(subCategorySelectElement, 'Loading subcategories...', '');
				httpRequest.open('GET', '/api/categories/' + categorySelectElement.options[categorySelectElement.selectedIndex].text, false);
				httpRequest.send();
			}
		}
		
		document.getElementById('category').onchange = updateSubCategories;
		<?php
			if ($editing) {
?>

			document.getElementById('category').value = <?php echo $appToEdit['category']; ?>;
			<?php
				if ($appToEdit['subcategory'] !== null) {
?>

			document.getElementById('subcategory').value = <?php echo $appToEdit['subcategory']; ?>;
			<?php
				}
?>

		<?php
			}
?>
		</script>
		
	<?php
		}
	require_once('../common/ucpfooter.php');
?>
