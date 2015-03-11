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
			//Insert notification entry into database
			executePreparedSQLQuery($this->mysqlConn, 'INSERT INTO notifications (userId, summary, body)
													VALUES (?, ?, ?)',
													'iss', [$userId, escapeHTMLChars($summary), escapeHTMLChars($body)]);
		}
		
		public function getNotifications($count) {
			return $this->getNotificationObjects($count, true);
		}
		
		public function getUnreadNotifications($count) {
			return $this->getNotificationObjects($count, false);
		}
		
		public function getNotificationSummaries($count) {
			return $this->getNotificationSummaryStrings($count, true);
		}
		
		public function getUnreadNotificationSummaries($count) {
			return $this->getNotificationSummaryStrings($count, false);
		}
		
		public function getUnreadNotificationCount() {
			return getArrayFromSQLQuery($this->mysqlConn, 'SELECT COUNT(*) FROM notifications WHERE userId = ? AND isRead = 0', 'i', [$_SESSION['user_id']])[0]['COUNT(*)'];
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
		
		private function getNotificationObjects($count, $includeRead) {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT userId, timeCreated, summary, body, isRead FROM notifications WHERE userId = ?' .
																		(!$includeRead ? ' AND isRead = 0' : '') . ' ORDER BY notificationId DESC LIMIT ?', 'ii', [$_SESSION['user_id'], $count]);
			
			//Set "isRead" attribute
			executePreparedSQLQuery($this->mysqlConn, 'UPDATE notifications SET isRead = 1 WHERE userId = ?' .
														(!$includeRead ? ' AND isRead = 0' : '') . ' AND isRead = 0 ORDER BY notificationId DESC LIMIT ?', 'ii', [$_SESSION['user_id'], $count]);
			
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
		
		private function getNotificationSummaryStrings($count, $includeRead) {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT summary FROM notifications WHERE userId = ?' .
																		(!$includeRead ? ' AND isRead = 0' : '') . ' ORDER BY notificationId DESC LIMIT ?', 'ii', [$_SESSION['user_id'], $count]);
			
			$summariesObj = array();
			foreach ($notifications as $notification) {
				array_push($summariesObj, $notification['summary']);
			}
			
			return $summariesObj;
		}
	}
?>