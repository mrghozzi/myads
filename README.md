# <b>MYADS v3.2.0</b> : ***PHP Script*** for social networking and exchanging ads between website owners.

> ![MYADS](https://raw.githubusercontent.com/mrghozzi/myads_check_updates/main/myads.png)

* Program name: [MYADS](https://github.com/mrghozzi/myads)
* Demo Site : [ADStn](https://adstn.is-best.net)  
  ``Username: vvvbbb`` ``Password: vvvbbb`` 
* Author : [mrghozzi](https://github.com/mrghozzi "mrghozzi")

---

# 🚀 Script Features (Members)

- Exchange of visits between website owners.  
- Exchange of **text ads**.  
- Exchange of **advertising banners**.  
- Web Directory.  
- A **store** for downloading scripts, plugins, and templates.  
- **Knowledge base / wiki system** for sharing guides and documentation.  
- **Forum** for communication and service exchange.  
- **Notification system** (live via AJAX).  
- **Private messaging** between members.  
- **Report system** for forum content.  
- **Personal profile system** (like social networks).  
- **Follow system** (social/friend connections).  
- **Verified memberships** (blue tick).  
- **Members online now** section.  
- **Dark/Light mode** support.  
- **Ad customization** (different ad styles).  
- **Ad filtering & sorting**.  
- **User levels & points system** for active members (gamification).  
- **Detailed analytics for ads**:  
  * Impressions (views).  
  * Click tracking.  
  * Ad placement tracking (where it appeared).  
  * Click source tracking (where it was clicked).  
  * Viewer/Clicker IP, browser, and OS detection.  
  * Pages where other people’s ads appeared on your site.  

---

# 🛠 Script Features (Administrator)

- Fully compatible with PHP 7+.  
- Automatic update system for the latest **myads** version.  
- Dashboard with modern design and ad statistics.  
- Manage members and control their accounts.  
- Manage and control all ads of all types and sizes.  
- Full control over **forum settings** and topics.  
- Ability to add external ads (e.g., **AdSense**) on the homepage.  
- Manage **site news** and announcements.  
- Manage **emojis/smilies**.  
- Auto-generate **sitemap.xml** and **robots.txt**.  
- Define **verified memberships** (blue tick).  
- **Add-on / plugin system** (extensible).  
- Full advanced statistics for ad banners:  
  * Total views.  
  * Total clicks.  
  * Ads appearance locations.  
  * Ads click locations.  
  * IP/browser/OS info for all views and clicks.  

---

# ⚡ Security & Technology

- Database connection via **PDO**.  
- Secure passwords using **password_hash()** and **password_verify()**.  
- Input/Output sanitization to prevent **XSS & SQL Injection**.  
- Responsive design using **Bootstrap 5**.  
- Modern **SVG icons**.  
- Optimized **SEO (Meta Tags & OpenGraph)**.  
- Automatic **sitemap.xml** & **robots.txt** generation.  

---

# 📌 Important Notes

- Ads will only appear on partner websites if the **ad exchange code** is installed.  
- For every **2 ads displayed** on a member’s site, **1 of their ads** will appear on other sites.  
- To post text ads → add the text ads code.  
- To publish banner ads → add the banner exchange code.  

---

# ⚙️ Installation

1. Download the script.  
2. Unzip the package.  
3. Upload all files to your hosting.  
4. Set permission **777** for the following:  

```
/upload
/sitemap.xml
```

5. Create a new database.  
6. Edit the `dbconfig.php` file with your credentials:  

```php
$db_host = "server name";
$db_name = "database name";
$db_user = "username";
$db_pass = "password";
```
7. Run the installation wizard:
```
http://siteurl/install/index.php
```

8. (replace siteurl with your domain, without trailing slash)

9. Click Install and follow all steps.

10. Delete the /install folder for security.

✅ Installation complete!

# 🗺 Roadmap (Planned Features)

We are actively developing MYADS. Upcoming improvements include:

🔹 Plugin System Upgrade → More flexibility and developer-friendly API.

🔹 Enhanced Logs & Security → Admin logs, advanced CSRF protection, 2FA for login.

🔹 Smart Ads / Auto-Rotation → Automatically optimize ads with higher CTR.

🔹 Advanced Targeting → Show ads based on time, device type, or language.

🔹 Leaderboard & Badges → More gamification features for active members.

🔹 REST API → Integrations with external platforms (WordPress, etc.).

🔹 WebSocket Support → Faster real-time notifications (instead of AJAX).

🔹 Exportable Reports → Download campaign stats in CSV/PDF.

🔹 Machine Learning (future) → Predict ad performance and auto-optimize.

# 📢 License

This script is freeware. You are free to use and contribute.