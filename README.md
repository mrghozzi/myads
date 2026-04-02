# MYADS v4.2.4 (Laravel Edition)

***Advanced PHP Script*** for social networking and exchanging ads between website owners, now powered by **Laravel**.

> ![MYADS](https://raw.githubusercontent.com/mrghozzi/myads_check_updates/main/myads.png)

* **Program name:** [MYADS](https://github.com/mrghozzi/myads)
* **Author:** [mrghozzi](https://github.com/mrghozzi "mrghozzi")
* **Framework:** Laravel 12

---

## 🚀 Features

### 👤 Members
- **Ad Exchange:** Native **Smart Ads**, visits exchange, text ads, and advertising banners.
- **Social presence:** Integrated **Social Links Hub** supporting 10+ platforms and **verified memberships**.
- **Marketplace:** Digital **Store** for scripts, plugins, and templates with PTS-based pricing.
- **Community:** **Forum** with visibility controls, **Wiki-style Knowledgebase**, and private messaging.
- **UI/UX:** Glassmorphic **Superdesign** modules with smooth animations and **Dark/Light mode**.
- **Analytics:** Detailed tracking for impressions, clicks, and geo-targeting.

### 🛠 Administrator
- **Duralux Dashboard:** Modern, responsive admin panel with detailed statistics and dark mode support.
- **Plugin System:** **Advanced WordPress-like plugin system** allowing installation via ZIP, activation/deactivation, and automatic updates.
- **Compliance & Privacy:** Built-in **GDPR/CCPA Cookie Consent Manager** and customizable legal pages (Terms & Privacy).
- **Control:** Manage members, ads, forum categories, directory listings, and site news.
- **System:** Auto-generate **sitemap.xml** and **robots.txt**.
- **Advanced Stats:** Full tracking for banners (IP, Browser, OS).
- **Hooks & Filters:** Extensible architecture using hooks and filters for deep customization.

---

## ⚡ Technology Stack

- **Backend:** Laravel Framework (PHP 8.2+)
- **Database:** MySQL / MariaDB (PDO)
- **Frontend:** Bootstrap 5, Blade Templates, IntersectionObserver for animations
- **Security:** CSRF Protection, XSS Sanitization, Bcrypt + Adaptive Hashing (for legacy MD5 compatibility)

---

## 📚 Documentation

Detailed documentation is available in the `Documents` directory:

- [Manual & Overview](Documents/README.md)
- [Installation Guide](Documents/INSTALLATION.md)
- [System Requirements](Documents/SYSTEM_REQUIREMENTS.md)
- [Theme Guide](Documents/THEME_GUIDE.md)
- [Plugin Guide](Documents/PLUGIN_GUIDE.md)
- [API Documentation](Documents/API_DOCS.md)

---

## ⚙️ Installation

### Fresh Installation

1.  **Requirements:** Make sure your server meets the requirements (PHP 8.2+, MySQL/MariaDB, PDO, cURL, OpenSSL).
2.  **Upload:** Upload all project files to your server.
3.  **Document Root:** Ensure your domain points to the `public` directory, or use the provided `.htaccess` in the root folder for shared hosting.
4.  **Run Installer:** Open your browser and navigate to:
    ```
    http://your-domain.com/install
    ```
5.  **Follow the Wizard:**
    - The wizard will verify server requirements and folder permissions (`storage`, `bootstrap/cache`).
    - Enter your Database credentials. The installer will automatically generate the `.env` file and APP configuration.
    - The system will migrate all database tables and seed default settings.
    - Finally, create your primary **Admin account**.

### 🔄 Upgrading from Legacy (v3.2.x) to v4.0.0

Since v4.0.0 is a complete rewrite onto the Laravel framework, the database structure has changed. The built-in installer handles this migration seamlessly.

1.  **Backup:** **CRITICAL:** Backup your entire v3.2 database and files.
2.  **Upload:** Upload the new v4.0.0 files over your existing installation. Keep your existing `upload` or `assets` folders if you have custom user images.
3.  **Run Installer:** Go to `http://your-domain.com/install` (The system will detect if you are upgrading or if tables are completely missing).
4.  **Database Configuration:** Enter your *existing* v3.2 database credentials.
5.  **Migration & Compatibility:** The installer will map your old tables to the new Laravel schema, inject missing tables (like `setting`, `menu`, `notifications`), and enable MD5-to-Bcrypt password compatibility so your members don't lose access.
6.  **Finalize:** Complete the wizard to verify your Admin account.


---

## 🗺 Roadmap

We have reached **100% Feature Parity** with the legacy version. Continuous improvements include:

- ✅ **Social Login:** Integration with Facebook/Google (via Socialite).
- ✅ **Compliance:** Full GDPR Cookie Notice & Legal Agreement Manager.
- ✅ **New UI:** Redesigned Landing Page and Admin Dashboard.
- ✅ **Smart Ads:** Contextual Native Ads with Auto-Rotation.
- ✅ **Social Hub:** Integrated Social Links System for member profiles.
- ✅ **Security Suite:** IP Bans, Session Audit Log, and at-rest PM Encryption.
- ✅ **SEO Engine:** Centralized SEO dashboard, dynamic sitemaps, and GA4.
- 🔹 **API:** Full REST API for mobile apps (Internal Alpha).
- 🔹 **Real-time:** WebSocket support for live notifications.
- 🔹 **Payment Gateways:** Integration with PayPal/Stripe for store.

---

## 📢 License

This script is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
