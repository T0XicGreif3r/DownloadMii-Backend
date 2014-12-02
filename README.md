DownloadMii-Website
===================

DownloadMii is an online store for homebrew applications.

PLEASE TEST BEFORE COMMITING!

**Server Info**
- Python: 3.4
- PHP: 5.5
- Platform: 64bit
- Server Software: IIS
- Server Platform: 64bit

ROADMAP
========
- [ ] Implament a simple but secure account system and SSL support
- [ ] Implament roles
- [ ] Add MySQL DB
- [ ] Implment app publishing for the role "developer" which the data will be saved to the DB and the files to an Azure blob storage. (Containg a appname, developer, category, subcategory, description, unquie app GUID, link to 3dsx & smdh file, link to icon ans publish date).
- [ ] Implament app reviewing by moderators an allow them to change the "allowPublish" to true. (Should be done for new releases and updates)
- [ ] Allow the "developer" role to publish their application manually at any time they want.
- [ ] Implament a getJson.php feature which will folow these standards:
- "getJson.php?category=[category]&subcategory=[subcategory]&subcategoryother=[subcategoryother]&bydeveloper=[bydeveloper]"
- Valid values for [category]: TopDownloaded, StaffPicks,  Applications, Games. (Required)
- Valid values for [subcategory]: (Games) Retro, Platformer, Fight, subcategoryother. (Applications) Utils, Web, HBLauncher, subcategoryother. (Required unless the category is TopDownloaded, StaffPicks, if [bydeveloper] contains a value or if [byid] contains a value)
- Valid values for [subcategoryother]: Any valid 16 char string(including "\0") (Optional)
- Valid values for [bydeveloper]: Any valid 16 char string(including "\0") (Optional)
- [ ] Implamemt a rateApp.php which folow the these standards:
- "rateApp?securetoken=[securetoken]&appguid=[appguid]&rating=[rating]"
- [securetoken] is the temporary token returned after the user has logged in.
- [appguid] is the app GUID value.
- [rating] is a value between 1 and 5 (Five best, One worst)
- The function will return a blank webpagw with the number "0" if successful, "1" if invalid GUID, "2" if invalid secure Token, "3" for server/script error.
- [ ] With more.




Error codes
===========
To Do!
