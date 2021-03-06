<?php
	/*
		DownloadMii Login Page
	*/
	
	$title = 'Log In';
	require_once('../../common/ucpheader.php');
	
	$_SESSION['login_token'] = uniqid(mt_rand(), true);
?>
		<h1 class="animated bounceInDown text-center">Log in</h1>
		<br />
		<form role="form" class="small-width" action="action.php" method="post" accept-charset="utf-8">
			<label class="sr-only" for="name">Username:</label>
			<input type="text" class="form-control no-bottom-border-radius" id="user" name="user" placeholder="Username" maxlength="24" required>
			
			<label class="sr-only" for="pass">Password:</label>
			<input type="password" class="form-control no-border-radius" id="pass" name="pass" placeholder="Password" required>
			
			<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block no-top-border-radius">Log in</button>
			
			<input type="hidden" name="logintoken" value="<?php echo md5($_SESSION['login_token']); ?>">
		</form>
		<div class="text-center"><a href="/secure/register/">Create a DownloadMii account</a></div>
		<br />
		<div class="text-center" style="font-weight: bold;">If you already are logged in on another device/browser, you will automatically get logged out there.</div>
<?php
	require_once('../../common/ucpfooter.php');
?>
