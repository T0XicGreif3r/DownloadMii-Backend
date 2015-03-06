<?php
	/*
		DownloadMii Notification Manager (WIP)
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	
	class notification {
		public $userId;
		public $timeCreated;
		public $summary;
		public $body;
		public $isRead;
	}
	
	class notification_manager {
		private $mysqlConn;
		private $connIsExternal;
		
		public function createNotification($userId, $summary, $body) {
			executePreparedSQLQuery($this->mysqlConn, 'INSERT INTO notifications (userId, summary, body)
													VALUES (?, ?, ?)',
													'iss', [$userId, escapeHTMLChars($summary), escapeHTMLChars($body)]);
		}
		
		public function getLatestNotifications() {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT userId, timeCreated, summary, body, isRead FROM notifications WHERE userId = ? ORDER BY notificationId DESC LIMIT 10', 'i', [$_SESSION['user_id']]);
			
			return $this->getNotificationObjectFromArray($notifications);
		}
		
		public function getLatestUnreadNotifications() {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT userId, timeCreated, summary, body, isRead FROM notifications WHERE userId = ? AND isRead = 0 ORDER BY notificationId DESC LIMIT 10', 'i', [$_SESSION['user_id']]);
			
			//Set "isRead" attribute
			executePreparedSQLQuery($this->mysqlConn, 'UPDATE notifications SET isRead = 1 WHERE userId = ? AND isRead = 0 ORDER BY notificationId DESC LIMIT 10', 'i', [$_SESSION['user_id']]);
			
			return $this->getNotificationObjectFromArray($notifications);
		}
		
		public function __construct($mysqlConn = null) {
			$this->connIsExternal = $mysqlConn !== null;
			
			if ($this->connIsExternal) {
				$this->mysqlConn = $mysqlConn;
			}
			else {
				$this->mysqlConn = connectToDatabase();
			}
		}
		
		public function __destruct() {
			if (!$this->connIsExternal) {
				$this->mysqlConn->close();
			}
		}
		
		private function getNotificationObjectFromArray($notifications) {
			//Convert notifications to object
			$notificationsObj = array();
			for ($i = 0; $i < count($notifications); $i++) {
				array_push($notificationsObj, new notification());
				foreach ($notifications[$i] as $key => $value)
				{
					$notificationsObj[$i]->$key = $value;
				}
			}
			
			return $notificationsObj;
		}
	}
?>