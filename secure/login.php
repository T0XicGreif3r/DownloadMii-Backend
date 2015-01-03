<?php
	/*
		TEST/TEMPORARY
	*/
	
	require_once('../common/user.php');
	require_once('../common/functions.php');
	
	$token = generateRandomString();
	$_SESSION['login_token'] = md5(getConfigValue('salt_token') . $token);
?>

<form action="action_login.php" method="post" accept-charset="utf-8">
<input type="text" name="user" size="40" required>
<input type="password" name="pass" size="40" required>
<input type="hidden" name="logintoken" value="<?php echo $token; ?>">
<input type="submit" name="submit" value="Login">
</form>