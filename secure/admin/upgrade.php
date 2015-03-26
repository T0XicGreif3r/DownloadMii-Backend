<?php
	require_once('../../common/user.php');
	
	verifyRole(4);

	echo 'Connecting to database...';
	$mysqlConn = connectToDatabase();

	echo 'Creating tables...';
	executePreparedSQLQuery($mysqlConn, '
	CREATE TABLE groups(
		groupId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		name VARCHAR(30) NOT NULL,
		inheritedGroup INT NULL,

		UNIQUE KEY uq_name(name),

		FOREIGN KEY (inheritedGroup) REFERENCES groups(groupId)
	);

	CREATE TABLE groupconnections(
		groupConnectionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		userId INT NOT NULL,
		groupId INT NOT NULL,

		UNIQUE KEY uq_user_group(userId, groupId),

		FOREIGN KEY (userId) REFERENCES users(userId),
		FOREIGN KEY (groupId) REFERENCES groups(groupId)
	);

	CREATE TABLE notifications(
		notificationId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		userId INT NULL,
		groupId INT NULL,
		timeCreated TIMESTAMP NOT NULL,
		summary TEXT NOT NULL,
		body TEXT NOT NULL,
		url VARCHAR(255) NULL,

		FOREIGN KEY (userId) REFERENCES users(userId),
		FOREIGN KEY (groupId) REFERENCES groups(groupId)
	);

	CREATE TABLE notificationreads(
		readId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		userId INT NOT NULL,
		notificationId INT NOT NULL,

		UNIQUE KEY uq_user_notification(userId, notificationId),

		FOREIGN KEY (userId) REFERENCES users(userId),
		FOREIGN KEY (notificationId) REFERENCES notifications(notificationId)
	);
	');

	echo 'Creating groups...';
	executePreparedSQLQuery($mysqlConn, '
	INSERT INTO groups (groupId, name, inheritedGroup)
	VALUES (1, "Users", NULL), (2, "Developers", 1), (3, "Moderators", 2), (4, "Administrators", 3);
	');

	echo 'Upgrading roles...';
	executePreparedSQLQuery($mysqlConn, '
	INSERT INTO groupconnections (userId, groupId)
	SELECT userId, role FROM users;
	');

	echo 'Removing old columns and tables...';
	executePreparedSQLQuery($mysqlConn, '
	ALTER TABLE users
	DROP COLUMN role,
	ADD UNIQUE KEY uq_name(name);

	DROP TABLE developers;
	');

	echo 'Upgrade complete.';
?>