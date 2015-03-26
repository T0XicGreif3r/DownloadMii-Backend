<?php
	/*
		DownloadMii User Control Panel (WIP)
	*/
	
	$title = 'Notifications';
	$printNotificationsInHeader = false;
	require_once('../../common/ucpheader.php');

	function getPageButtonHTML($page, $currentPage) {
		return $page == $currentPage ? '<button type="button" class="btn btn-primary">' . $page . '</button>' : '<a href="?page=' . $page . '"><button type="button" class="btn">' . $page . '</button></a>';
	}
?>	

		<h1 class="animated bounceInDown text-center">Notifications</h1>
		<br />

<?php
	$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1; //Get current page

	$notificationsPerPage = getConfigValue('notifications_per_page');
	$pagesBehind = getConfigValue('notifications_pages_behind');
	$pagesAhead = getConfigValue('notifications_pages_ahead');

	$notificationManager = new notification_manager();
	$notificationCount = $notificationManager->getNotificationCount();
	$notificationsToDisplay = $notificationManager->getNotifications($notificationsPerPage, $notificationsPerPage * ($currentPage - 1));
	
	foreach ($notificationsToDisplay as $notification) {
?>

		<div class="well clearfix">
			<div class="pull-left">
				<h4><strong>
<?php
				if (!$notification->isRead) {
					echo '<span class="badge">!</span> '; //Display an indicator for unread notifications
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
			<div class="app-vertical-center-outer pull-right">
				<div class="app-vertical-center-inner">
<?php
			echo $notification->timeCreated;
?>
				</div>
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
	$pageCount = ceil($notificationCount / $notificationsPerPage);

	if (1 < $currentPage - getConfigValue('notifications_pages_behind')) {
		$dots = 2 != $currentPage - $pagesBehind ? ' ... ' : ''; //Show dots if there's no "2" button
		echo getPageButtonHTML(1, $currentPage) . $dots;
	}

	for ($i = $currentPage - $pagesBehind; $i < $currentPage + $pagesAhead + 1; $i++) {
		if ($i > 0 && $i <= $pageCount) {
			echo getPageButtonHTML($i, $currentPage);
		}
	}

	if ($pageCount > $currentPage + getConfigValue('notifications_pages_ahead')) {
		$dots = $pageCount - 1 != $currentPage + $pagesAhead ? ' ... ' : ''; //Show dots if there's no "($pageCount - 1)" button
		echo $dots  . getPageButtonHTML($pageCount, $currentPage);
	}
?>
		</div>
<?php
	require_once('../../common/ucpfooter.php');
?>