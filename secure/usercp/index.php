<?php
	/*
		DownloadMii User Control Panel (WIP)
	*/
	
	$title = 'User CP';
	require_once('../../common/ucpheader.php');
	
	foreach ($unreadNotifications as $notification) {
		echo '<b>' . $notification->shortDescription . '</b> (' . $notification->timeCreated . ')<br>' . $notification->longDescription . '<br><br>';
	}
	
	require_once('../../common/ucpfooter.php');
?>