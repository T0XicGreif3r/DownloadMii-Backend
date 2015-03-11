<?php
	/*
		DownloadMii User Control Panel (WIP)
	*/
	
	$title = 'User CP';
	$printNotificationsInHeader = false;
	require_once('../../common/ucpheader.php');
	
	$notificationManager = new notification_manager();
	$notifications = $notificationManager->getNotifications(10);
	
	foreach ($notifications as $notification) {
		echo '<b>' . $notification->summary . '</b> (' . $notification->timeCreated . ')<br>' . $notification->body . '<br><br>';
	}
	
	require_once('../../common/ucpfooter.php');
?>