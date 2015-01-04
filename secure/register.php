<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	$registerToken = generateRandomString();
	$_SESSION['register_token'] = md5(getConfigValue('salt_token') . $registerToken);
?>
		<div class="text-center">
			<h1>Create an account</h1>
			<form role="form" action="action_register.php" method="post" accept-charset="utf-8" style="max-width: 400px; margin-left: auto; margin-right: auto;">
				<label class="sr-only" for="name">Username:</label>
				<input type="text" class="form-control" id="user" name="user" placeholder="Username" style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;" required>
				<label class="sr-only" for="pass">Password:</label>
				<input type="password" class="form-control" id="pass" name="pass" placeholder="Password" style="border-radius: 0;" required>
				<label class="sr-only" for="pass">Email:</label>
				<input type="email" class="form-control" id="pass" name="pass" placeholder="Email" style="border-radius: 0;" required>
				<div class="g-recaptcha" data-sitekey="<?php echo getConfigValue('apikey_recaptcha_site'); ?>"></div>
				<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block" style="border-top-left-radius: 0; border-top-right-radius: 0;">Sign up</button>
				<input type="hidden" name="registertoken" value="<?php echo $registerToken; ?>">
			</form>
		</div>
<?php
	require_once('../common/ucpfooter.php');
?>