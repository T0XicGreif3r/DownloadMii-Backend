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
	$notificationsPerPage = 10;

	$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1; //Get current page

	$notificationManager = new notification_manager();
	$totalNotificationCount = $notificationManager->getNotificationCount();
	$notificationsToDisplay = $notificationManager->getNotifications($notificationsPerPage, $notificationsPerPage * ($page - 1));
	
	foreach ($notificationsToDisplay as $notification) {
?>

		<div class="well">
			<div class="pull-left">
				<h4><strong>
<?php
				if (!$notification->isRead) {
					echo '<span class="badge">!</span> ';
				}

				if (!empty($notification->url)) {
					echo '<a href="' . $notification->url . '">' . $notification->summary . '</a>';
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
?>
		<div style="text-align: center;">
<?php
	$pageCount = ceil($totalNotificationCount / $notificationsPerPage);
	for ($i = 1; $i < $pageCount + 1; $i++) {
		echo $i == $page ? '<button type="button" class="btn btn-primary">' . $i . '</button>' : '<a href="?page=' . $i . '"><button type="button" class="btn">' . $i . '</button></a>';
	}
?>
		</div>
<?php
	require_once('../../common/ucpfooter.php');
?>