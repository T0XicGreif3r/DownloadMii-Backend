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
	
	if (clientLoggedIn() && isset($_GET['guid'], $_GET['token'], $myappsToken) && md5($myappsToken) === $_GET['token']) {
		$_SESSION['remove_token'] = uniqid(mt_rand(), true);
		
		$mysqlConn = connectToDatabase();
		
		$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT guid, name, publishstate FROM apps
															WHERE guid = ? AND publisher = ? LIMIT 1', 'ss', [$_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
		
		$mysqlConn->close();
		
		printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination
		printAndExitIfTrue($matchingApps[0]['publishstate'] === 2 || $matchingApps[0]['publishstate'] === 3, 'This app is rejected or already hidden.');
			
		$appToRemove = $matchingApps[0];
			
		$_SESSION['user_app_guid'] = $appToRemove['guid'];
?>
		<h1 class="text-center"><?php echo 'Hiding ' . $appToRemove['name']; ?></h1>
		<br />
		<form role="form" class="small-width" action="action_hide.php" method="post" accept-charset="utf-8">
			<label for="pass">Enter your password and an exclamation mark to confirm hiding the app:</label>
			<input type="password" class="form-control no-bottom-border-radius" id="pass" name="pass" placeholder="Password" required>
			
			<button type="submit" name="submit" class="btn btn-lg btn-danger btn-block no-top-border-radius">Hide</button>
			
			<input type="hidden" name="removetoken" value="<?php echo md5($_SESSION['remove_token']); ?>">
		</form>
<?php
		if ($_SESSION['user_role'] < 2) {
			echo '<div class="text-center" style="color: red; font-weight: bold;">WARNING: You\'ll have to republish the app to unhide it.</div>';
		}
	}
	
	require_once('../common/ucpfooter.php');
?>