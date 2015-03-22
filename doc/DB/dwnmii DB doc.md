#Download Mii DB Table description

##Groups
Column|Description|
---|:---|
`groupId`| id of the group |
`name` | group name |
`inheritedGroup` | optional ID of inherited group |

##Users
Column|Description|
---|:---|
`userId`| Unique id for the user |
`nick` | nickname choosed by the user |
`password` | bcrypted password for the user |
`email` | Email of the user |
`token` | Token of the user, it will be regenerated every time the user relog into the 3ds app |

##GroupConnections
Column|Description|
---|:---|
`groupConnectionId`| Unique id for the connection |
`userId`| id of the user |
`groupId`| id of the group |

##Categories
Column|Description|
---|:---|
`categoryId` | id of the category |
`parent` | Parent category ID
`name` | name of the category |

##AppVersions
Column|Description|
---|:---|
`versionId` | Id of the version |
`appGuid` | Guid of the app |
`number` | Version number, ex: '1.0.0.0' |
`3dsx` | 3dsx url |
`smdh` | smdh url |
`appdata` | appdata url |
`largeIcon` | large icon url |
`3dsx_md5` | 3dsx md5 |
`smdh_md5` | smdh md5 |
`appdata_md5` | appdata md5 |

##Apps
Column|Description|
---|:---|
`guid` | GUID of the app |
`name` | name of the app |
`version` | id of the current version, reference into appversions table |
`publisher` | id of the user who can audit the app details, reference into users table |
`description` | description  of the app, don't accept HTML NOR newline("\n") |
`category` | id of the category, reference into categories table |
`subcategory`| id of the category, reference into categories table |
`rating` | Average app rating |
`downloads` | Download count |
`publishstate` | Publish state: 0 - pending approval, 1 - published, 2 - rejected, 3 - hidden |
`failpublishmessage` | If app rejected, reason why |

##Screenshots
Column|Description|
---|:---|
`ratingId` | Id of the sceenshot |
`appGuid` | id of the app the screenshot belongs to, reference into apps table |
`imageIndex` | Image index for app (starts at 1) |
`url` | Screenshot url |

##Ratings
Column|Description|
---|:---|
`ratingId` | Id of the rating |
`appGuid` | Guid of the rated app |
`userId` | Id of the user who rated the app |
`rate` | User's rating of the app (values between 1 & 5) |

##Downloads
Column|Description|
---|:---|
`downloadId` | Id of the app/IP download connection (for unique download counting) |
`appGuid` | Guid of the app |
`ipHash` | hashed IP address |

##Notifications
Column|Description|
---|:---|
`notificationId` | Id of the notification |
`userId` | Receiver user |
`groupId` | Receiver group |
`timeCreated` | When the notification was sent |
`summary` | Very short summary of the notification |
`body` | Full notification text |
`url`| URL to relating page |

##NotificationReads
Column|Description|
---|:---|
`readId`| Unique id for the user/notification connection |
`userId`| id of the user who read the notification |
`notificationId`| id of the notification |