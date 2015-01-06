<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	$_SESSION['login_token'] = uniqid(mt_rand(), true);
?>
		<div class="text-center">
			<h1>Log in</h1>
			<form role="form" action="action_login.php" method="post" accept-charset="utf-8" style="max-width: 400px; margin-left: auto; margin-right: auto;">
				<label class="sr-only" for="name">Username:</label>
				<input type="text" class="form-control no-bottom-border-radius" id="user" name="user" placeholder="Username" maxlength="24" required>
				
				<label class="sr-only" for="pass">Password:</label>
				<input type="password" class="form-control no-border-radius" id="pass" name="pass" placeholder="Password" maxlength="32" required>
				
				<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block no-top-border-radius">Log in</button>
				
				<input type="hidden" name="logintoken" value="<?php echo md5($_SESSION['login_token']); ?>">
			</form>
			<a href="register.php">Create a DownloadMii account</a>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>