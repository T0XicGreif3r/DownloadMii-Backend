![DownloadMii](https://raw.githubusercontent.com/DownloadMii/DownloadMii-Website/master/img/logo.png "Logo")
[![Donation Status](https://img.shields.io/gratipay/filfat.svg)](https://gratipay.com/filfat/)
===================

DownloadMii is an online store for homebrew applications.

**Server Info**
- Python: 3.4
- PHP: 5.4
- Platform: 64bit
- Server Software: IIS
- Server Platform: 64bit
- The server automatically pulls any changes from the "master" branch.

ROADMAP 
========
some of the following things are horrebly outdated please take a look at api.php first.
- [ ] Add support for multiple consoles.
- [x] Implment app publishing for the role "developer" which the data will be saved to the DB and the files to an Azure blob storage. (Containg a appname, developer, category, subcategory, description, unquie app GUID, link to 3dsx & smdh file, link to icon ans publish date).
- [x] Implament app reviewing by moderators an allow them to change the "allowPublish" to true. (Should be done for new releases and updates)
- [x] Allow the "developer" role to publish their application manually at any time they want.
- [x] Public page that displays all the current apps (paged with 50 apps per page) and allows the user to search for apps.
- [x] App data support, allow the user to attach an zip file that inside the app will get extracted inside the same dir as the 3dsx file.
