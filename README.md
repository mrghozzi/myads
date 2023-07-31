# <b>MYADS v3.0.5</b> : ***php scripts*** for social networking and exchanging ads between website owners.

>
> ![MYADS](https://www.adstn.gq/bnr/myads.png)

* Program name: [MYADS](https://github.com/mrghozzi/myads)
* Official Site : [Kariya Host](http://www.kariya-host.gq)
* Demo Site : [ADStn](https://adstn.gq) ``Username: vvvbbb`` ``Password: vvvbbb`` 
* Author : [mrghozzi](https://github.com/mrghozzi "mrghozzi")


# Script features (for members)

- Exchange of visits between site owners.
- Exchange text ads.
- Exchange of advertising banners.
- Web Directory
- A store for downloading scripts, plugins and templates
- The knowledge base of the encyclopedia system (wiki) is for the script store to provide assistance from all members of the site
- Forum for communication between members and the exchange of services.
- Notification system.
- Messaging system for private communication with members.
- Report content in the forum.
- A member's personal profile, such as social networking sites.
- follow button
- Verified memberships (blue tick)
- Members online now
- Find out all the information about the appearance of your ad banners
  * Views
  * Number of clicks on ads
  * Find out where your ad appeared
  * Find out where your ad was clicked
  * Find out the IP address, browser and driver of who viewed your ad
  * Find out the IP address, browser and driver of the person who clicked on your ad
  * Find out which pages other people's ads appeared on your site
  
# Script features (for administrator)

- These are fully compatible with PHP version 7
- Automatic update of the latest "myads" updates ``NEW``
- Know the number of members and control their accounts
- Know the number of all ads of all sizes and have full control over them
- Full control of the forum settings
- The ability to add paid ads such as (AdSense) on the home page of the site
- Site news control
- Control smiles emojis
- Generate sitemap.xml from the main page of the admin panel
- Full control of forum topics when running admin mode
- Possibility to change the timing system from the admin panel
- Define verified memberships (blue tick) from member settings
- add-on system
- Find out all the information about the appearance of your ad banners
  * Views
  * Number of clicks on ads
  * Find out where all the ads appeared
  * Find out where all the ads were clicked
  * Knowing the IP addresses, browser and operating software of who viewed the ads
  * Knowing the IP addresses, browser and operating software of who clicked on the ads
  
**Note: For your site members. A member's ads will not appear on other sites unless an ad exchange code is added on their site, and for every two ads that appear on their site, their ad appears once on the other site.
   To post text ads, he must add the text ads code on his site, and to publish ad banners, he must add the banner exchange code on his site. 

# installation

- Download the script
- After you download the copy, unzip the package.
- Upload all files to your site.
- Give permission 777 for the following files

```
/upload
/sitemap.xml
```


- Create a new database.
- Edit the ``dbconfig.php`` file and add the rule name, password, and username.

```php
$db_host = "server name";
$db_name = "database name";
$db_user = "username";
$db_pass = "password";
```
- Run the installation wizard via the link.
``
http://siteurl/install/index.php
``
  * Replacing ``siteurl`` with your site link

- Click the "install" button and follow all the steps.

- Make sure that you have added your website link correctly and do not use the '/' at the end of the line

- Delete the install folder for security.
- [x] And the script was installed successfully.
