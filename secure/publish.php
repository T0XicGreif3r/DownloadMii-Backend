<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	if (isset($_SESSION['myapps_token'])) {
		$myappsToken = $_SESSION['myapps_token'];
		unset($_SESSION['myapps_token']);
	}
	
	unset($_SESSION['user_app_guid']); //Unset GUID setting
	
	if (isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])) {
		$publishToken = generateRandomString(); //Generate token for publish action
		$_SESSION['publish_token'] = md5(getConfigValue('salt_token') . $publishToken);
		
		$mysqlConn = connectToDatabase();
		if (isset($_GET['guid'], $_GET['token'], $myappsToken) && $myappsToken == md5(getConfigValue('salt_token') . $_GET['token'])) {
			$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.*, appver.number AS version FROM apps app
																LEFT JOIN appversions appver ON appver.versionId = app.version
																WHERE app.guid = ? AND app.publisher = ? LIMIT 2', 'is', [$_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
			
			printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination
				
			$appToEdit = $matchingApps[0];
			$_SESSION['user_app_guid'] = $_GET['guid'];
		}
		
		$editing = isset($appToEdit);
		$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId, name FROM categories');
?>

		<div class="well">
			<h3><?php if (isset($appToEdit)) print('Updating ' . $appToEdit['name']); else print('Adding new application'); ?></h3>
			<br />
			<form role="form" action="action_publish.php" method="post" accept-charset="utf-8">
				<div class="row">
					<div class="col-md-4 form-group">
						<label for="name">Name:</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="e.g. My Application" maxlength="50" value="<?php if ($editing) echo $appToEdit['name']; ?>" required>
					</div>
					<div class="col-md-4 form-group">
						<label for="version">Version:</label>
						<input type="text" class="form-control" id="version" name="version" placeholder="e.g. 1.0.0.0" maxlength="25" value="<?php if ($editing) echo $appToEdit['version']; ?>" required>
					</div>
					<div class="col-md-4 form-group">
						<label for="category">Category:</label>
						<select class="form-control" id="category" name="category" required>
							<option value="">Select a category...</option>
							<?php
								foreach ($categories as $category) {
									echo '<option value="' . $category['categoryId'] . '">' . $category['name'] . '</option>';
								}
?>

						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="description">Description (optional):</label>
					<textarea class="form-control" id="description" name="description" maxlength="255"><?php if ($editing) echo $appToEdit['description']; ?></textarea>
				</div>
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="3dsx">3dsx file<?php if ($editing) echo ' (only upload if you want to update)'; ?>:</label>
						<input type="file" class="form-control" id="3dsx" name="3dsx"<?php if (!$editing) echo ' required'; ?>>
					</div>
					<div class="col-md-6 form-group">
						<label for="smdh">smdh file<?php if ($editing) echo ' (only upload if you want to update)'; ?>:</label>
						<input type="file" class="form-control" id="smdh" name="smdh"<?php if (!$editing) echo ' required'; ?>>
					</div>
				</div>
				<div class="form-group">
					<div class="g-recaptcha" data-sitekey="<?php echo getConfigValue('apikey_recaptcha_site'); ?>"></div>
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
				<input type="hidden" name="publishtoken" value="<?php echo $publishToken; ?>">
			</form>
		</div>
		<script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>
		<?php
			if ($editing) {
?>

		<script type="text/javascript">
			document.getElementById('category').value = <?php echo $appToEdit['category']; ?>;
		</script>
<?php
			}
		}
	require_once('../common/ucpfooter.php');
?>