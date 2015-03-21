<?php
	/*
		DownloadMii Notification Manager (WIP)
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	
	class notification {
		public $notificationId;
		public $userId;
		public $groupId;
		public $timeCreated;
		public $summary;
		public $body;
		public $rootRelativeURL;
	}
	
	class notification_manager {
		private $mysqlConn;
		private $connIsExternal;
		
		public function createUserNotification($userId, $summary, $body) {
			//Insert notification entry into database
			executePreparedSQLQuery($this->mysqlConn, 'INSERT INTO notifications (userId, summary, body)
														VALUES (?, ?, ?)',
														'iss', [$userId, escapeHTMLChars($summary), escapeHTMLChars($body)]);
		}
		
		public function createGroupNotification($groupName, $summary, $body) {
			//Insert notification entry into database
			executePreparedSQLQuery($this->mysqlConn, 'INSERT INTO notifications (groupId, summary, body)
														VALUES ((SELECT groupId FROM groups WHERE name = ?), ?, ?)',
														'sss', [$groupName, escapeHTMLChars($summary), escapeHTMLChars($body)]);
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
			return getArrayFromSQLQuery($this->mysqlConn, 'SELECT COUNT(*) FROM notifications' .
															$this->getJoinSQL() . $this->getWhereSQL(false))[0]['COUNT(*)'];
		}
		
		public function __construct($mysqlConn = null) {
			$this->connIsExternal = $mysqlConn !== null;
			
			if ($this->connIsExternal) {
				$this->mysqlConn = $mysqlConn; //Use existing MySQL connection if provided
			}
			else {
				$this->mysqlConn = connectToDatabase(); //Otherwise, create a new one
			}
			
			executePreparedSQLQuery($this->mysqlConn, 'SET @curUserId = ?', 'i', [$_SESSION['user_id']]); 
		}
		
		public function __destruct() {
			if (!$this->connIsExternal) {
				$this->mysqlConn->close();
			}
		}
		
		private function getNotificationObjects($count, $includeRead) {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT notifications.notificationId, notifications.userId, notifications.groupId, timeCreated, summary, body, rootRelativeURL FROM notifications' .
																		$this->getJoinSQL() . $this->getWhereSQL($includeRead) . '
																		ORDER BY notifications.notificationId DESC LIMIT ?', 'i', [$count]);
			
			//Get notification objects
			$notificationObjects = $this->getObjectsFromNotificationArray($notifications);
			
			//Mark notifications as read
			foreach ($notificationObjects as $notification) {
				executePreparedSQLQuery($this->mysqlConn, 'INSERT IGNORE INTO notificationreads (userId, notificationId)
															VALUES (@curUserId, ?)',
															'i', [$notification->notificationId]);
			}
			
			return $notificationObjects;
		}
		
		private function getNotificationSummaryStrings($count, $includeRead) {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT summary, rootRelativeURL FROM notifications' .
																		$this->getJoinSQL() . $this->getWhereSQL($includeRead) . '
																		ORDER BY notifications.notificationId DESC LIMIT ?', 'i', [$count]);
			
			return $this->getObjectsFromNotificationArray($notifications);
		}
		
		private function getObjectsFromNotificationArray($notifications) {
			//Convert notifications to object
			$notificationObjects = array();
			for ($i = 0; $i < count($notifications); $i++) {
				array_push($notificationObjects, new notification());
				foreach ($notifications[$i] as $key => $value)
				{
					$notificationObjects[$i]->$key = $value;
				}
			}
			
			return $notificationObjects;
		}
		
		private function getJoinSQL() {
			//Get SQL for joining groups into notification queries
			$sqlParts = array();
			for ($i = 0; $i < count($_SESSION['user_groups']); $i++) {
				array_push($sqlParts, 'LEFT JOIN groups group' . $i . ' ON group' . $i . '.name = "' . $_SESSION['user_groups'][$i] . '"');
			}
			
			return ' ' . implode(' ', $sqlParts) . '
					LEFT JOIN notificationreads ON notificationreads.userId = @curUserId AND notificationreads.notificationId = notifications.notificationId ';
		}
		
		private function getWhereSQL($includeRead) {
			//Get SQL for selecting notifications from groups into notification queries
			$sqlParts = array();
			for ($i = 0; $i < count($_SESSION['user_groups']); $i++) {
				array_push($sqlParts, 'notifications.groupId = group' . $i . '.groupId');
			}
			$whereSql = implode(' OR ', $sqlParts);
			
			return ' WHERE (notifications.userId = @curUserId ' . (!empty($whereSql) ? ' OR ' . $whereSql : '') . ') ' . 
					(!$includeRead ? ' AND notificationreads.readId IS NULL' : '') . ' ';
		}
	}
?>