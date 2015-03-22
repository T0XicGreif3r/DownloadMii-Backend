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
	$currentPage = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1; //Get current page

	$notificationsPerPage = getConfigValue('notifications_per_page');
	$pagesBehind = getConfigValue('notifications_pages_behind');
	$pagesAhead = getConfigValue('notifications_pages_ahead');

	$notificationManager = new notification_manager();
	$totalNotificationCount = $notificationManager->getNotificationCount();
	$notificationsToDisplay = $notificationManager->getNotifications($notificationsPerPage, $notificationsPerPage * ($currentPage - 1));
	
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
	$increasedPagesBehind = false;
	$increasedPagesAhead = false;

	if (2 == $currentPage - $pagesBehind) {
		$pagesBehind += 1;
		$increasedPagesBehind = true;
	}
	if ($pageCount - 1 == $currentPage + $pagesAhead) {
		$pagesAhead += 1;
		$increasedPagesAhead = true;
	}

	if (!$increasedPagesBehind && 2 < $currentPage - getConfigValue('notifications_pages_behind')) {
		echo getPageButtonHTML(1, $currentPage) . ' ... ';
	}

	for ($i = $currentPage - $pagesBehind; $i < $currentPage + $pagesAhead + 1; $i++) {
		if ($i > 0 && $i <= $pageCount) {
			echo getPageButtonHTML($i, $currentPage);
		}
	}

	if (!$increasedPagesAhead && $pageCount > $currentPage + getConfigValue('notifications_pages_ahead')) {
		echo '...' . getPageButtonHTML($pageCount, $currentPage);
	}
?>
		</div>
<?php
	require_once('../../common/ucpfooter.php');
?>