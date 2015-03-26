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
		public $url;
		public $isRead;
	}
	
	class notification_manager {
		private $mysqlConn;
		private $connIsExternal; //Whether the MySQL connection was supplied through the constructor

		/**
		 * Send a notification to a specific user
		 *
		 * @param int $userId The ID of the user to send the notification to
		 * @param string $summary Short notification summary (should be up to 10 words)
		 * @param string $body Full notification text
		 * @param string $url URL to related page
		 */
		public function createUserNotification($userId, $summary, $body, $url = null) {
			//Insert notification entry into database
			executePreparedSQLQuery($this->mysqlConn, 'INSERT INTO notifications (userId, summary, body, url)
														VALUES (?, ?, ?, ?)',
														'isss', [$userId, escapeHTMLChars($summary), escapeHTMLChars($body), $url]);
		}

		/**
		 * Send a notification to a specific group
		 *
		 * @param string $groupName The name of the group to send the notification to
		 * @param string $summary Short notification summary (should be up to 10 words)
		 * @param string $body Full notification text
		 * @param string $url URL to related page
		 */
		public function createGroupNotification($groupName, $summary, $body, $url = null) {
			//Insert notification entry into database
			executePreparedSQLQuery($this->mysqlConn, 'INSERT INTO notifications (groupId, summary, body, url)
														VALUES ((SELECT groupId FROM groups WHERE name = ?), ?, ?, ?)',
														'ssss', [$groupName, escapeHTMLChars($summary), escapeHTMLChars($body), $url]);
		}

		/**
		 * Get the objects of the latest notifications for the current user, both read and unread
		 *
		 * @param int $count How many notifications to return
		 * @param int $start Index of the first notification to get (zero-based)
		 *
		 * @return array
		 */
		public function getNotifications($count, $start = 0) {
			return $this->getNotificationObjects($count, $start, true);
		}

		/**
		 * Get the objects of the latest notifications for the current user, only unread
		 *
		 * @param int $count How many notifications to return
		 * @param int $start Index of the first notification to get (zero-based)
		 *
		 * @return array
		 */
		public function getUnreadNotifications($count, $start = 0) {
			return $this->getNotificationObjects($count, $start, false);
		}

		/**
		 * Get the summary strings of the latest notifications for the current user, both read and unread
		 *
		 * @param int $count How many strings to return
		 * @param int $start Index of the first string to get (zero-based)
		 *
		 * @return array
		 */
		public function getNotificationSummaries($count, $start = 0) {
			return $this->getNotificationSummaryStrings($count, $start, true);
		}

		/**
		 * Get the summary strings of the latest notifications for the current user, only unread
		 *
		 * @param int $count How many strings to return
		 * @param int $start Index of the first string to get (zero-based)
		 *
		 * @return array
		 */
		public function getUnreadNotificationSummaries($count, $start = 0) {
			return $this->getNotificationSummaryStrings($count, $start, false);
		}

		/**
		 * Get the total amount of notifications for the current user
		 *
		 * @return int
		 */
		public function getNotificationCount() {
			return $this->getNotificationCounts(true);
		}

		/**
		 * Get the total amount of unread notifications for the current user
		 *
		 * @return int
		 */
		public function getUnreadNotificationCount() {
			return $this->getNotificationCounts(false);
		}

		/**
		 * @param mysqli $mysqlConn If provided, the notification manager will use this MySQL connection (otherwise it will create its own connection)
		 */
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
		
		private function getNotificationObjects($count, $start, $includeRead) {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT notifications.notificationId, notifications.userId, notifications.groupId, timeCreated, summary, body, url,
																		CASE WHEN notificationreads.readId IS NOT NULL THEN 1 ELSE 0 END AS isRead
																		FROM notifications' .
																		$this->getJoinSQL() . $this->getWhereSQL($includeRead) . '
																		ORDER BY notifications.timeCreated DESC LIMIT ?, ?', 'ii', [$start, $count]);
			
			//Get notification objects
			$notificationObjects = $this->getObjectsFromNotificationArray($notifications);
			
			//Mark notifications as read
			foreach ($notificationObjects as $notification) {
				if (!$notification->isRead) {
					executePreparedSQLQuery($this->mysqlConn, 'INSERT IGNORE INTO notificationreads (userId, notificationId)
																VALUES (@curUserId, ?)', 'i', [$notification->notificationId]);
				}
			}
			
			return $notificationObjects;
		}
		
		private function getNotificationSummaryStrings($count, $start, $includeRead) {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT summary FROM notifications' .
																		$this->getJoinSQL() . $this->getWhereSQL($includeRead) . '
																		ORDER BY notifications.timeCreated DESC LIMIT ?, ?', 'ii', [$start, $count]);

			//Flatten array
			$summaries = array();
			for ($i = 0; $i < count($notifications); $i++) {
				array_push($summaries, $notifications[$i]['summary']);
			}

			return $summaries;
		}

		private function getNotificationCounts($includeRead) {
			return getArrayFromSQLQuery($this->mysqlConn, 'SELECT COUNT(*) FROM notifications' .
				$this->getJoinSQL() . $this->getWhereSQL($includeRead))[0]['COUNT(*)'];
		}
		
		private function getObjectsFromNotificationArray($notifications) {
			//Convert notifications to objects
			$notificationObjects = array();
			for ($i = 0; $i < count($notifications); $i++) {
				array_push($notificationObjects, new notification());
				foreach ($notifications[$i] as $key => $value) {
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