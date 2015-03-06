<?php
	/*
		DownloadMii User Control Panel (WIP)
	*/
	
	$title = 'User CP';
	require_once('../../common/ucpheader.php');
	
	foreach ($unreadNotifications as $notification) {
		echo '<b>' . $notification->summary . '</b> (' . $notification->timeCreated . ')<br>' . $notification->body . '<br><br>';
	}
	
	require_once('../../common/ucpfooter.php');
?>