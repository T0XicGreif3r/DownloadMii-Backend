<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	unset($_SESSION['user_app_guid']); //Unset GUID setting
	sendResponseCodeAndExitIfTrue(!(isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_token'])), 403); //Check if logged in
	
	$mysqlConn = connectToDatabase();
	if (isset($_GET['guid'])) {
		$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT * FROM apps WHERE guid = ? AND publisher = ? LIMIT 2', 'is', [$_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
		printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination
		
		$appToEdit = $matchingApps[0];
		$_SESSION['user_app_guid'] = $_GET['guid'];
	}
	
	$categories = getArrayFromSQLQuery($mysqlConn, 'SELECT categoryId, name FROM categories');
?>

		<div class="well">
			<h3><?php if (isset($appToEdit)) print('Updating ' . $appToEdit['name']); else print('Adding new application'); ?></h3>
			<br />
			<form role="form" action="action_publish.php" method="post" accept-charset="utf-8">
				<div class="row">
					<div class="col-md-4 form-group">
						<label for="name">Name:</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="e.g. My Application" maxlength="50" required>
					</div>
					<div class="col-md-4 form-group">
						<label for="version">Version:</label>
						<input type="text" class="form-control" id="version" name="version" placeholder="e.g. 1.0.0.0" maxlength="25" required>
					</div>
					<div class="col-md-4 form-group">
						<label for="maincat">Category:</label>
						<select class="form-control" id="maincat" name="maincat" required>
							<option value="">Select a category...</option>
							<?php
								foreach ($categories as $category) {
									print('<option value="' . $category['categoryId'] . '">' . $category['name'] . '</option>');
								}
?>

						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="description">Description (optional):</label>
					<textarea class="form-control" id="description" name="description" maxlength="255"></textarea>
				</div>
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="3dsx">3dsx file:</label>
						<input type="file" class="form-control" class="form-control" id="3dsx" name="3dsx" required>
					</div>
					<div class="col-md-6 form-group">
						<label for="smdh">smdh file:</label>
						<input type="file" class="form-control" class="form-control" id="smdh" name="smdh" required>
					</div>
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>