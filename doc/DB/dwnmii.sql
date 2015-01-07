CREATE TABLE users(
	userId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nick VARCHAR(24) NOT NULL UNIQUE,
	password VARCHAR(60) NOT NULL,
	role TINYINT NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	token VARCHAR(40) NULL UNIQUE
);

CREATE TABLE categories(
	categoryId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	type TINYINT NOT NULL,
	name VARCHAR(50) NOT NULL
);

CREATE TABLE appversions(
	versionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appGuid CHAR(36) NOT NULL,								-- Not a very nice solution...
	number VARCHAR(12) NOT NULL,
	3dsx VARCHAR(255) NOT NULL,
	smdh VARCHAR(255) NOT NULL,
	3dsx_md5 VARCHAR(32) NOT NULL,
	smdh_md5 VARCHAR(32) NOT NULL
);

CREATE TABLE apps(
	guid CHAR(36) NOT NULL PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	publisher INT NOT NULL,
	version INT NOT NULL,
	description TEXT NULL,
	category INT NOT NULL,
	subcategory INT NULL,
	othercategory INT NULL,
	rating TINYINT NOT NULL DEFAULT 0,
	downloads INT NOT NULL DEFAULT 0,
	publishstate TINYINT NOT NULL DEFAULT 0,

	FOREIGN KEY (publisher) REFERENCES users(userId),
	FOREIGN KEY (version) REFERENCES appversions(versionId),
	FOREIGN KEY (category) REFERENCES categories(categoryId),
	FOREIGN KEY (subcategory) REFERENCES categories(categoryId),
	FOREIGN KEY (othercategory) REFERENCES categories(categoryId)
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

CREATE TABLE developers(
	developerId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appGuid CHAR(36) NOT NULL,
	userId INT NOT NULL,
	nick VARCHAR(50) NULL,

	FOREIGN KEY (appGuid) REFERENCES apps(guid),
	FOREIGN KEY (developerId) REFERENCES users(userId)
);