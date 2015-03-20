<?php
	/*
		DownloadMii User Handler
		This file automatically includes functions.php and notifications.php
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\functions.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '\common\notifications.php');
	
	function getFlattenedGroupArray($array) {
		for ($i = 0; $i < count($array); $i++) {
			$array[$i] = $array[$i]['name'];
		}
		return $array;
	}
	
	session_start();
	
	$now = time();
	
	if (isset($_SESSION['last_active']) && ($now > $_SESSION['last_active'] + 60 * 60)) { //If the user has been inactive for 1 hour...
		//...their session expires
		session_unset();
		session_destroy();
	}
	
	if (isset($_SESSION['user_id'], $_SESSION['user_token'])) {
		$mysqlConn = connectToDatabase();
		$matchingUsers = getArrayFromSQLQuery($mysqlConn, 'SELECT nick FROM users WHERE userId = ? AND token = ? LIMIT 1', 'ss', [$_SESSION['user_id'], $_SESSION['user_token']]); //Get user nickname
		
		if (count($matchingUsers) === 1) {
			$_SESSION['user_nick'] = $matchingUsers[0]['nick'];
			
			//Get user groups
			$matchingGroups = getArrayFromSQLQuery($mysqlConn, 'SELECT groups.groupId, name FROM groups
																LEFT JOIN groupconnections groupcon ON groupcon.userId = ?
																WHERE groupcon.groupId = groups.groupId', 'i', [$_SESSION['user_id']]);
			
			if (count($matchingGroups) > 0) {
				//Get inherited groups for each user group
				$inheritedGroups = array();
				foreach ($matchingGroups as $group) {
					$inheritedGroups = array_merge($inheritedGroups, getArrayFromSQLQuery($mysqlConn, 'SELECT groups.name, @subGroup:=groups.inheritedGroup FROM groups
																										JOIN (SELECT * FROM groups ORDER BY ISNULL(inheritedGroup), groupId ASC) orderedGroups
																										JOIN (SELECT @subGroup:=?) topGroup
																										WHERE groups.groupId=@subGroup', 'i', [$group['groupId']]));
				}
				
				array_shift($matchingGroups);
				
				//Combine group arrays
				$_SESSION['user_groups'] = array_values(array_unique(getFlattenedGroupArray(array_merge($matchingGroups, $inheritedGroups))));
			}
			else {
				$_SESSION['user_groups'] = array();
			}
			
			//Get information about unread notifications
			$notificationManager = new notification_manager($mysqlConn);
			$unreadNotificationCount = $notificationManager->getUnreadNotificationCount();
			$unreadNotificationSummaries = $notificationManager->getUnreadNotificationSummaries(2);
		}
		else {
			session_unset();
			session_destroy();
		}
		
		$mysqlConn->close();
	}
	
	
	$_SESSION['last_active'] = $now; //Set last activity time to now
?>