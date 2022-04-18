# Raw Score Viewing Progam

RSVP is a set of PHP scripts that are created for those running large sailing events.

Typically, in sailing events with a lot of on-the-water activity by the race committee, the teams use online applications such as WhatsApp to communicatate among themselves, and with support personnel onshore.

The process for of personnel on a finish boat typically goes like this:
1. On the water information such as finish order/times is recorder on paper.
2. A photograph is taken with a mobile phone
3. The information is sent via WhatsApp to an individual or group
4. Scorers onshore print the images (called "Raw Scores" as they have not been validated by anyone other than finish personnel)
5. Printed sheets are taken to a physical bulletin board where they are displayed for view by coaches and competitors.
6. Information is used to manually update scoring
7. Final scoring results are published either on web sites, on paper, or both.

The process is usually repeated every race day for each event and class.

Other information is also exchanged via image, such as boats that are UFD and BFD, protests that have been communicated to the Race Committee, etc. This information is also typically printed onshore and posted on the bulletin board. It is also used by scorers.

As more pressure is placed on racing events to reduce usage of paper and reduce turn-around time for display of images, processes for uploading images to a central server have been used. These typically require the scorer or event secretary to download images from WhatsApp and send them to a server via FTP, renaming the images so that they are somewhat descriptive of what they are.

RSVP is intended to allow personnel offshore to directly upload images to a server, including Event, Class, Date/Time, and a description. This removes the scorer or event secretary from the workflow in origination of images for online display.

**What is needed to make RSVP Work**
RSVP is provided as a set of PHP scripts that can be uploaded to a folder on an existing web hosting service with an existing website. The hosting service must also provide PHP (version higher than 7.30) and MySQL (MyriadB is also fine). Setup steps are:

1. Select a folder on the web server, or create a new one.
2. Copy all the PHP files to the folder
3. Create a new database. Just database name is sufficient - RSVP will create the necessary tables
4. Create a database user with appropriate rights for creation of tables, data, etc. (if you are unsure, just give the user all rights to that database)
5. Edit "config.php" with the following information:
- Name of the subfolder for storage of images and image thumbnails (if you don't care, "uploads/" is fine)
* The name of the database server (usually "localhost")
* The name of the database user you created above
* The password for the database user you created above

That is all that is needed. On first run, RSVP will create the necessary database tables and the folder for the images.

**Running RSVP for the first time**
Go to http://\<website root\>/\<RSVP folder\>. If all goes well, you will be presented with a login screen. Initially, RSVP starts with a login ID of "Admin" and a password of "Admin". There are three authority levels for RSVP, Administrator, Uploader, and Viewer. It is strongly advised that the first step be to change the administrator password.
  1. Only Administrators can create other Administrators and Uploaders. They can also delete images.
  2. Uploaders are allowed to upload images. They cannot delete images.
  3. Viewers can only view/download images.
  (Note: If someone comes across the initial login screen, they will be given the opportunity to register. While information like name, phone number, email address, etc. are collected, they will only be given the rights to view and download images. This feature was added for future enhancements.)
  
**Uploading Images**
Images can be uplodated from the Upload screen. Enter the name of the event, the class, a description of the image, change the date and time if necessary, and select the image to upload. If the name of the event and/or the class has been previously entered, it can be selected from the drop-down menu. When accessed from a mobile device, one of the options upon browsing for files is the ability to take a photo. This works on both Android and IOS (Apple) devices. Once the image is selected, or the photo is taken, click SUBMIT and the image will be uploaded to the server.

**Viewing or Downloading**
It is not necessary to log in to view or download images. From the login screen, just select the link to view ("Just want to view raw score images? You don't need to sign in. Click Here"). If you are in the Upload screen there is also a link at the bottom to view. You can choose event and class from the dropdown screen. 

**Permalinks**
If you wish to keep a particular download query (a particular event and/or particular class) you can make those selections from the dropdown menus and click the "Permalink" link. Your browser will be taken to a specific query, which can be copied and used again to choose those specific selections.
  
**Probems or issues**
Should you have any problems, issues, or suggestions for RSVP, you may contact me at andy@sailor.nu. An example of RSVP can be seen at http://www.sailor.nu/scorefiles/
