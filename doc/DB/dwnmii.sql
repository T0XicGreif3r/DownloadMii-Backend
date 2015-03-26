CREATE TABLE groups(
	groupId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	inheritedGroup INT NULL,
	
	FOREIGN KEY (inheritedGroup) REFERENCES groups(groupId)
);

CREATE TABLE users(
	userId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nick VARCHAR(24) NOT NULL UNIQUE,
	password VARCHAR(60) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	token VARCHAR(40) NULL UNIQUE,

	UNIQUE KEY uq_nick(nick)
);

CREATE TABLE groupconnections(
	groupConnectionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	userId INT NOT NULL,
	groupId INT NOT NULL,
	
	UNIQUE KEY uq_user_group(userId, groupId),
	
	FOREIGN KEY (userId) REFERENCES users(userId),
	FOREIGN KEY (groupId) REFERENCES groups(groupId)
);

CREATE TABLE categories(
	categoryId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	parent INT NULL,
	name VARCHAR(50) NOT NULL UNIQUE,
	
	FOREIGN KEY (parent) REFERENCES categories(categoryId)
);

CREATE TABLE appversions(
	versionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appGuid CHAR(36) NOT NULL,								-- Not a very nice solution...
	number VARCHAR(12) NOT NULL,
	3dsx VARCHAR(255) NOT NULL,
	smdh VARCHAR(255) NOT NULL,
	appdata VARCHAR(255) NULL,
	largeIcon VARCHAR(255) NOT NULL,
	3dsx_md5 VARCHAR(32) NOT NULL,
	smdh_md5 VARCHAR(32) NOT NULL,
	appdata_md5 VARCHAR(32) NULL
);

CREATE TABLE apps(
	guid CHAR(36) NOT NULL PRIMARY KEY,
	name VARCHAR(32) NOT NULL,
	publisher INT NOT NULL,
	version INT NOT NULL,
	description VARCHAR(300) NULL,
	category INT NOT NULL,
	subcategory INT NULL,
	rating TINYINT NOT NULL DEFAULT 0,
	downloads INT NOT NULL DEFAULT 0,
	publishstate TINYINT NOT NULL DEFAULT 0,				-- Use bitwise operations in the future?
	failpublishmessage VARCHAR(24) NULL,
	
	FULLTEXT ft_name_desc(name, description),

	FOREIGN KEY (publisher) REFERENCES users(userId),
	FOREIGN KEY (version) REFERENCES appversions(versionId),
	FOREIGN KEY (category) REFERENCES categories(categoryId),
	FOREIGN KEY (subcategory) REFERENCES categories(categoryId)
);

CREATE TABLE screenshots(
	screenshotId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appGuid CHAR(36) NOT NULL,
	imageIndex TINYINT NOT NULL,
	url VARCHAR(255) NOT NULL,
	
	UNIQUE KEY uq_guid_index(appGuid, imageIndex),
	
	FOREIGN KEY (appGuid) REFERENCES apps(guid)
);

CREATE TABLE ratings(
	ratingId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appGuid CHAR(36) NOT NULL,
	userId INT NOT NULL,
	rate TINYINT NOT NULL DEFAULT 0,

	FOREIGN KEY (appGuid) REFERENCES apps(guid),
	FOREIGN KEY (userId) REFERENCES users(userId)
);

CREATE TABLE downloads(
	downloadId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appGuid CHAR(36) NOT NULL,
	ipHash CHAR(32) NOT NULL
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