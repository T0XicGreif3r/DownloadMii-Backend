<?php
	/*
		DownloadMii User Control Panel (WIP)
	*/
	
	$title = 'Notifications';
	$printNotificationsInHeader = false;
	require_once('../../common/ucpheader.php');
?>	

		<h1 class="animated bounceInDown text-center">Notifications</h1>
		<br />

<?php	
	$notificationManager = new notification_manager();
	$notifications = $notificationManager->getNotifications(10);
	
	foreach ($notifications as $notification) {
?>

		<div class="well">
			<div class="pull-left">
				<h4><strong>
<?php
				if (!empty($notification->rootRelativeURL)) {
					echo '<a href="' . $notification->rootRelativeURL . '">' . $notification->summary . '</a>';
				}
				else {
					echo $notification->summary;
				}
?>
				</strong></h4>
			</div>
			<div class="pull-right">
<?php
			echo $notification->timeCreated;
?>
			</div>
			<div class="clear-float">
<?php
			echo $notification->body;
?>
			</div>
		</div>

<?php	
	}
	
	require_once('../../common/ucpfooter.php');
?>