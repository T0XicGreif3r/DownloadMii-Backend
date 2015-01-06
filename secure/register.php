<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/ucpheader.php');
	
	$_SESSION['register_token'] = uniqid(mt_rand(), true);
?>
		<h1 class="text-center">Create an account</h1>
		<br />
		<form role="form" class="small-width" action="action_register.php" method="post" accept-charset="utf-8">
			<label class="sr-only" for="name">Username (3-24 characters):</label>
			<input type="text" class="form-control no-bottom-border-radius" id="user" name="user" placeholder="Username (3-24 characters)" maxlength="24" required>
			
			<label class="sr-only" for="pass">Password (at least 8 characters):</label>
			<input type="password" class="form-control no-border-radius" id="pass" name="pass" placeholder="Password (at least 8 characters)" required>
			
			<label class="sr-only" for="pass2">Confirm password:</label>
			<input type="password" class="form-control no-border-radius" id="pass2" name="pass2" placeholder="Confirm password" required>
			
			<label class="sr-only" for="email">Email address:</label>
			<input type="email" class="form-control no-border-radius" id="email" name="email" placeholder="Email address" maxlength="255" required>
			
			<div class="g-recaptcha" data-sitekey="<?php echo getConfigValue('apikey_recaptcha_site'); ?>"></div>
			
			<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block no-top-border-radius">Sign up</button>
			
			<input type="hidden" name="registertoken" value="<?php echo md5($_SESSION['register_token']); ?>">
		<form>
		<script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>
<?php
	require_once('../common/ucpfooter.php');
?>