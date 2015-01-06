<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	$_SESSION['login_token'] = uniqid(mt_rand(), true);
?>
		<h1 class="text-center">Log in</h1>
		<br />
		<form role="form" class="small-width" action="action_login.php" method="post" accept-charset="utf-8">
			<label class="sr-only" for="name">Username:</label>
			<input type="text" class="form-control no-bottom-border-radius" id="user" name="user" placeholder="Username" maxlength="24" required>
			
			<label class="sr-only" for="pass">Password:</label>
			<input type="password" class="form-control no-border-radius" id="pass" name="pass" placeholder="Password" required>
			
			<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block no-top-border-radius">Log in</button>
			
			<input type="hidden" name="logintoken" value="<?php echo md5($_SESSION['login_token']); ?>">
		</form>
		<a class="text-center" href="register.php">Create a DownloadMii account</a>
<?php
	require_once('../common/ucpfooter.php');
?>