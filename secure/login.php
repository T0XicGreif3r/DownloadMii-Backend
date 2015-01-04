<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	$loginToken = generateRandomString();
	$_SESSION['login_token'] = md5(getConfigValue('salt_token') . $loginToken);
?>
		<div class="text-center">
			<h1>Login</h1>
			<form role="form" action="action_login.php" method="post" accept-charset="utf-8" style="max-width: 400px; margin-left: auto; margin-right: auto;">
				<label class="sr-only" for="name">Username:</label>
				<input type="text" class="form-control" id="user" name="user" placeholder="Username" style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;" required>
				<label class="sr-only" for="pass">Password:</label>
				<input type="password" class="form-control" id="pass" name="pass" placeholder="Password" style="border-radius: 0;" required>
				<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block" style="border-top-left-radius: 0; border-top-right-radius: 0;">Log in</button>
				<input type="hidden" name="logintoken" value="<?php echo $loginToken; ?>">
			</form>
			<a href="register.php">Register</a>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>