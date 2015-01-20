<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	if (isset($_GET['guid']) && isset($_SESSION['myapps_token' . $_GET['guid']])) {
		$myappsToken = $_SESSION['myapps_token' . $_GET['guid']];
	}
	
	if (clientLoggedIn() && isset($_GET['guid'], $_GET['token'], $myappsToken) && md5($myappsToken) === $_GET['token']) {
		$guidId = uniqid(mt_rand(), true);
		
		$mysqlConn = connectToDatabase();
		
		$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT guid, name, publishstate FROM apps
															WHERE guid = ? AND publisher = ? LIMIT 1', 'ss', [$_GET['guid'], $_SESSION['user_id']]); //Get app with user/GUID combination
		
		$mysqlConn->close();
		
		printAndExitIfTrue(count($matchingApps) != 1, 'Invalid app GUID.'); //Check if there is one app matching attempted GUID/user combination
		
		$appToRemove = $matchingApps[0];
		printAndExitIfTrue($appToRemove['publishstate'] === 2 || $appToRemove['publishstate'] === 3, 'This app is rejected or already hidden.');
			
		$_SESSION['hide_app_guid' . $guidId] = $appToRemove['guid'];
		$_SESSION['remove_token' . $appToRemove['guid']] = uniqid(mt_rand(), true);
?>
		<h1 class="text-center"><?php echo 'Hiding ' . $appToRemove['name']; ?></h1>
		<br />
		<form role="form" class="small-width" action="action_hide.php" method="post" accept-charset="utf-8">
			<label for="pass">Enter your password and an exclamation mark to confirm hiding the app:</label>
			<input type="password" class="form-control no-bottom-border-radius" id="pass" name="pass" placeholder="Password" required>
			
			<button type="submit" name="submit" class="btn btn-lg btn-danger btn-block no-top-border-radius">Hide</button>
			
			<input type="hidden" name="removetoken" value="<?php echo md5($_SESSION['remove_token' . $appToRemove['guid']]); ?>">
			<input type="hidden" name="guidid" value="<?php echo $guidId; ?>">
		</form>
<?php
		if ($_SESSION['user_role'] < 2) {
			echo '<div class="text-center" style="color: red; font-weight: bold;">WARNING: You\'ll have to republish the app to unhide it.</div>';
		}
	}
	
	require_once('../common/ucpfooter.php');
?>