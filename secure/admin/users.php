<?php
	$title = 'Admin CP';
	require_once('../../common/ucpheader.php');
	require_once('../../common/user.php');

	verifyGroup('Administrators');

	$_SESSION['admin_users_token'] = uniqid(mt_rand(), true); //Generate token for admin action
?>
<form action="userview.php" method="get">
	Query user by nickname:
	<br />
	<input type="text" name="nick" size="50">
	<input type="hidden" name="token" value="<?php echo md5($_SESSION['admin_users_token']); ?>">
	<input type="submit" value="Query">
</form>

<?php
	require_once('../../common/ucpfooter.php');
?>