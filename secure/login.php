<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	$logintoken = generateRandomString();
	$_SESSION['login_token'] = md5(getConfigValue('salt_token') . $logintoken);
?>

		<div class="well text-center">
			<form role="form" action="action_login.php" method="post" accept-charset="utf-8" style="max-width: 400px; margin-left: auto; margin-right: auto;">
				<label class="sr-only" for="name">Username:</label>
				<input type="text" class="form-control" id="user" name="user" placeholder="Username" style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;" required>
				<label class="sr-only" for="pass">Password:</label>
				<input type="password" class="form-control" id="pass" name="pass" placeholder="Password" style="border-radius: 0;" required>
				<input type="hidden" name="logintoken" value="<?php echo $logintoken; ?>">
				<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block" style="border-top-left-radius: 0; border-top-right-radius: 0;">Login</button>
			</form>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>