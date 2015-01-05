#Download Mii DB Table description

##Users
Column|Description|
---|:---|
`userId`|Unique id for the user|
`nick` | nickname choosed by the user|
`password` | pasword for the user. Is MDhased|
`role` | Composed role: 1- User can login into system and rate(val 0:1:2[2=mod]:3[3= admin]), 2- Is a developer and can audit/edit/create apps into repo Ex: [3,1] Developer admin user.|
`email` | Email of the user |
`token` | Token of the user, it will be regenerated every time the user relog into the 3ds app |

##Categories
Column|Description|
---|:---|
`categoryId` | id of the category |
`type` | 0 - top level, 1 - subcategory, 2 - other category
`name` | name of the category |

##AppVersions
Column|Description|
---|:---|
`versionId` | Id of the version |
`number` | Version number, ex: '1.0.0.0' |
`3dsx` | 3dsx url |
`smdh` | smdh url |

##Apps
Column|Description|
---|:---|
`guid` | GUID of the app |
`name` | name of the app |
`version` | id of the current version, reference into appversions table |
`publisher` | id of the user who can audit the app details, reference into users table |
`description` | description  of the app, accept HTML |
`category` | id of the category, reference into categories table |
`subcategory`| id of the category, reference into categories table |
`othercategory`| id of the category, reference into categories table |
`rating` | Average app rating |
`downloads` | Download count |
`publishstate` | Publish state: 0 - pending approval, 1 - published, 2 - rejected |

##Ratings
Column|Description|
---|:---|
`ratingId` | Id of the rating |
`appGuid` | Guid of the rated app |
`userId` | Id of the user who rated the app |
`rate` | User's rating of the app (values between 1 & 5) |

##Developers
Column|Description|
---|:---|
`developerId` | Id of the association |
`appGuid` | Guid of the app |
`userId` | Id of the user who had worked in the develop of the app(appGuid). If the user isn't registred on the system then this column will be NULL and his nick will be in the next column. |
`nick` | Nick of a developer if isn't registred on the system |


