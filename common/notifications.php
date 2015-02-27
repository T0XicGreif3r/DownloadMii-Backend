<?php
	/*
		DownloadMii Notification Manager (WIP)
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	
	class notification {
		public $userId;
		public $timeCreated;
		public $shortDescription;
		public $longDescription;
		public $isRead;
	}
	
	class notification_manager {
		private $mysqlConn;
		private $connIsExternal;
		
		//function createNotification()
		
		function getUnreadNotifications() {
			//Get notifications from database
			$notifications = getArrayFromSQLQuery($this->mysqlConn, 'SELECT userId, timeCreated, shortDescription, longDescription, isRead FROM notifications WHERE userId = ? AND isRead = 0', 'i', [$_SESSION['user_id']]);
			
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
	}
?>