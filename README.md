<div align="center">

# 🚀 MYADS v4.6.3

### The Ultimate Open-Source Social Network, Traffic & Ad Exchange Platform

**Empower your digital community with cutting-edge social engagement, modern community forums with multi-editor suite, peer-to-peer ad exchange, cinema-grade video hub & shorts, a multi-vendor digital marketplace with reviews, and a first-party native Flutter mobile app.**

Built with passion on **Laravel 12**, **PHP 8.2+**, and **Bootstrap 5 / Flutter**.

---

[![Release Version](https://img.shields.io/badge/Release-v4.6.3-3b82f6.svg?style=for-the-badge&logo=github)](https://github.com/mrghozzi/myads/releases)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-ff2d20.svg?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777bb4.svg?style=for-the-badge&logo=php)](https://php.net)
[![Flutter Mobile App](https://img.shields.io/badge/Flutter-3.27%2B_v1.8.5-02569b.svg?style=for-the-badge&logo=flutter)](https://github.com/mrghozzi/myads_app)
[![CI Tests](https://img.shields.io/github/actions/workflow/status/mrghozzi/myads/tests.yml?branch=main&style=for-the-badge&logo=githubactions&label=Tests&color=22c55e)](https://github.com/mrghozzi/myads/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-10b981.svg?style=for-the-badge)](LICENSE)

[![Sponsor Ko-Fi](https://img.shields.io/badge/Sponsor-Ko--fi-ff5e5b.svg?style=for-the-badge&logo=ko-fi)](https://ko-fi.com/mrghozzi)
[![Sponsor Patreon](https://img.shields.io/badge/Sponsor-Patreon-f96854.svg?style=for-the-badge&logo=patreon)](https://www.patreon.com/MrGhozzi)
[![GitHub Stars](https://img.shields.io/github/stars/mrghozzi/myads?style=for-the-badge&logo=apachespark&color=eab308)](https://github.com/mrghozzi/myads/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/mrghozzi/myads?style=for-the-badge&logo=git&color=8b5cf6)](https://github.com/mrghozzi/myads/network/members)

<br/>

<a href="#-key-features"><strong>Explore Features</strong></a> •
<a href="#-quick-start--installation"><strong>Quick Start</strong></a> •
<a href="#-official-flutter-mobile-app"><strong>Mobile App</strong></a> •
<a href="#-developer-platform--rest-api"><strong>Developer API</strong></a> •
<a href="Documents/README.md"><strong>Documentation</strong></a> •
<a href="README_AR.md"><strong>🇸🇦 النسخة العربية</strong></a> •
<a href="#-community--support"><strong>Support</strong></a>

<br/><br/>

<img src="https://raw.githubusercontent.com/mrghozzi/myads_check_updates/main/myads.png" alt="MYADS Banner" width="100%" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);" />

</div>

---

## 🌟 Why Choose MYADS?

MYADS is an enterprise-ready, all-in-one ecosystem that eliminates the need for dozens of disjointed scripts or subscription services. It unifies high-throughput advertising exchange, real-time social networking, video streaming, digital commerce, and developer APIs under a modern, highly optimized Laravel 12 architecture.

<table>
  <tr>
    <td width="50%">
      <h3>📢 Monetization & Ad Network</h3>
      <p>Banner ads, text links, surf traffic exchange, YouTube views exchange, Smart Ads, and peer-to-peer custom ad deals with daily PTS settlements and fraud protection.</p>
    </td>
    <td width="50%">
      <h3>📺 Video Hub & Cinema Watch</h3>
      <p>YouTube-inspired video platform with Spotlight Hero, category pills, multi-criteria sorting, 16:9 grid, standalone Cinema Watch page with Ambient Glow & Theater Mode, and 9:16 vertical Shorts reels.</p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <h3>💬 Modern Forum & Social Community</h3>
      <p>Proprietary forum architecture with two-pane AJAX browsing, multi-editor suite (Markdown, Quill, SCEditor), threaded discussion tree, best answer badges, Groq AI moderation, and SSE live streams.</p>
    </td>
    <td width="50%">
      <h3>📱 Native Flutter Mobile App (v1.8.5)</h3>
      <p>First-party Android & iOS client (<code>myads_app</code>) with 100% parity: Video Hub & Watch routing, Modern Forum discussions & replies, Store reviews, universal bookmarks, and P2P service orders.</p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <h3>🛍️ Digital Store & Freelance Marketplace</h3>
      <p>Multi-vendor digital store with 5-star customer reviews, verified buyer badges, lightbox screenshot gallery, live demo previews, PTS checkout, and milestone-tracked P2P service orders.</p>
    </td>
    <td width="50%">
      <h3>⚡ Superdesign Admin & Extensibility</h3>
      <p>Duralux dark-mode admin suite with single-pass SQL aggregations (95%+ faster load), zero-reload AJAX plugin & theme management, and live theme customizer.</p>
    </td>
  </tr>
</table>

---

## 🏗️ System Architecture

```mermaid
graph TD
    classDef client fill:#3b82f6,stroke:#1d4ed8,stroke-width:2px,color:#ffffff;
    classDef core fill:#8b5cf6,stroke:#6d28d9,stroke-width:2px,color:#ffffff;
    classDef service fill:#10b981,stroke:#059669,stroke-width:2px,color:#ffffff;
    classDef storage fill:#f59e0b,stroke:#d97706,stroke-width:2px,color:#ffffff;

    subgraph Clients [" 🌐 Client Layer "]
        Web["🖥️ Web App (Blade + Themes + AJAX)"]:::client
        Mobile["📱 Mobile App (Flutter 3.27+ iOS/Android)"]:::client
        ThirdParty["🔌 3rd-Party Apps (OAuth 2.0 / REST API)"]:::client
    end

    subgraph CoreEngine [" ⚡ MYADS Core Engine (Laravel 12) "]
        Router["Sanctum Auth & Route Middleware"]:::core
        SSE["Real-Time Live SSE Stream (/live/stream)"]:::core
        PluginMgr["Dynamic Plugin & Theme Engine (AJAX + Migrations)"]:::core
    end

    subgraph Services [" 🚀 Modules & Micro-Engines "]
        AdExchange["📢 Ad Exchange & Anti-Fraud Shield v2"]:::service
        SocialEngine["💬 Social Feed, Video Hub & PIP Player"]:::service
        StoreEngine["🛍️ Digital Marketplace & Services Escrow"]:::service
        BillingEngine["💳 Multi-Gateway Billing & Subscriptions"]:::service
    end

    subgraph Persistence [" 💾 Storage & Data Layer "]
        MySQL[("🗄️ MySQL / MariaDB (InnoDB)")]:::storage
        CacheStorage[("⚡ Selective Cache Warmup")]:::storage
        MediaStorage[("📁 Media Manager Storage")]:::storage
    end

    Web --> Router
    Mobile --> Router
    ThirdParty --> Router

    Router --> SSE
    Router --> PluginMgr
    Router --> AdExchange
    Router --> SocialEngine
    Router --> StoreEngine
    Router --> BillingEngine

    AdExchange --> MySQL
    SocialEngine --> MySQL
    StoreEngine --> MySQL
    BillingEngine --> MySQL

    PluginMgr --> CacheStorage
    SocialEngine --> MediaStorage
```

---

## ✨ Key Features

### 📢 1. Advertising Exchange & Anti-Fraud Shield v2
- **Multi-Format Ads:** Standard image banners (728x90, 300x250, 160x600, 468x60), text links, native cards, and embeddable JavaScript widgets.
- **Traffic Exchange (Surf-to-Earn):** High-traffic surf bar with countdown timer, anti-cheat validation, and instant PTS rewards.
- **YouTube Views Exchange:** Watch-to-earn exchange engine boosting real views and engagement for video creators.
- **Custom Member Ads (`/ads/custom`):** Direct peer-to-peer ad marketplace. Publishers generate embed scripts (`/embed/custom.js`), negotiate deals, and receive automated daily PTS payouts.
- **Smart Ads with Geo-Targeting & A/B Testing:** Dynamic contextual ads tailored by country, device, and performance metrics with hourly click heatmaps.
- **Anti-Fraud Shield v2 (`ADS-05`):**
  - Compliant with **IAB Viewability Standards** (requires ≥ 50% viewport visibility for ≥ 1 continuous second).
  - Real-time **Human Behavior Fingerprinting** (`mousemove` trajectory velocity, mobile touch tap duration, headless browser detection via `navigator.webdriver`, and WebGL checks).
  - Rate limiting (24h per anonymous fingerprint) and reason-tagged impression protection (`RAPID_CLICK`, `BOT_FINGERPRINT`, `HIDDEN_TAB`, `DWELL_TOO_SHORT`).

### 📺 2. Cinema-Grade Video Hub & Shorts Engine
- **Next-Gen YouTube-Style Video Hub (`/video` & `/video/*`):** Rebuilt with `@.superdesign` standards, dynamic category filter pills, Spotlight Hero 16:9 marquee banner, multi-criteria sorting (Newest, Most Viewed, Top Rated, Oldest), Shorts/Clips shelf with silent hover video snippets, and smooth AJAX Load More pagination.
- **Cinema-Grade Watch Page (`/video/watch/{id}`, `/video/{id}`, `/t{id}`):**
  - **Cinema Theater Mode:** 1-tap full-width expander (`#btnTheater`, keyboard shortcut `T`) with `localStorage` state persistence.
  - **Dynamic Ambient Glow (YouTube Ambient Mode):** Immersive ambient color diffusion backdrop (`.v-ambient-backdrop`) behind the video stage.
  - **Advanced HTML5 Custom Video Player:** Auto-hiding controls bar on idle (2.5s timer), buffered progress scrubber with hover timestamp preview, playback speed flyout (0.5x–2.0x), volume slider with mute toggle, keyboard shortcuts (`Space`/`K`, `F`, `T`, `M`, `J`/`L`, arrows), and double-tap +/-10s seek.
  - **Dual Picture-in-Picture (PiP):** Native browser PiP API paired with an automatic floating mini player (`.v-mini-floating-player`) driven by `IntersectionObserver` when scrolling down past the stage.
  - **Creator Card & Social Actions:** Hexagonal avatars with verified badges, AJAX Follow/Unfollow, animated 8-emoji reactions dropdown, Watch Later / Save video toggle, rich Share flyout, and suggested videos sidebar with autoplay switch.
- **Shorts Clips Engine (`/clips`):** 9:16 vertical video reels with native touch swipe gestures, mouse wheel scrolling, silent hover playback snippets, and original audio disc animation (`messages.original_audio`).
- **Dedicated Video Hub API (`/api/video/feed`, `/api/video/watch/{id}`):** Content-isolated video feeds, category lists, and suggested video collections for companion apps.

### 💬 3. Next-Gen Social Networking & Modern Forum Architecture
- **Modern Community Forum Architecture (`/forum`, `/f{id}`, `/t{id}`):**
  - **Two-Pane Responsive Layout:** Sticky category navigation on desktop with Admin-customizable tag color bullets (`f_cat.color`) and live community stats.
  - **Zero-Reload AJAX Discussion Streams:** Instant filtering across **Latest Activity**, **Top** (most replied/active), **Newest**, and **Unanswered** with debounced live search and HTML5 `pushState` bookmarkable URLs.
  - **Multi-Editor Suite:** Seamless authoring across **MyAds Markdown** with live side-by-side preview, **Quill.js v1.3.7**, and **SCEditor** with dark-mode parity and AJAX image attachments.
  - **Docked Floating Discussion Composer:** Bottom drawer composer with minimize/fullscreen toggles, keeping drafts intact while browsing.
  - **Threaded & Nested Discussion Stream:** Chronological post cards (`#post-1`, `#post-2`, ...), 2-level hierarchical reply tree (`parent_id`) with visual connecting lines, parent mention badges (`@User`), and cascading deletion.
  - **Pinned Comments & Best Answer System:** Authors and moderators can pin important replies to the top or mark the verified "Best Answer" (awarding 10 PTS reputation and trigger alerts).
  - **Sticky Timeline Scrubber & Moderator Sidebar:** Sticky progression widget (`Post X of Y`) with 1-click Pin/Lock/Edit/Delete moderation tools.
  - **AI Moderation:** Optional Groq AI Forum Moderator plugin automatically analyzing topic quality and compliance.
- **High-Performance Social Feed:** Zero N+1 query overhead, supporting rich multimedia attachments (Video, Audio, Music, Image Galleries, Files, and Clips).
- **Universal Bookmarks & Autocomplete:** 1-tap post saving (`/statuses/save-toggle`) with dedicated `/statuses/saved` feed, plus real-time smart `@mentions` and `#tags` autocomplete.
- **Member Profile Privacy Suite:** Granular `about_visibility` controls (`public`, `followers`, `private`) preventing bio leakage, paired with hexagonal avatars and verification requests.
- **Real-Time Live Events Engine (`RT-04`):** Server-Sent Events (SSE) streaming (`/live/stream`) delivering instant message badges, notification toasts, and live community activity.
- **Gamification & Rewards:** 25+ dynamic achievement badges, daily/weekly quests, PTS transfers, and voucher codes.

### 🛍️ 4. Digital Store, Services Marketplace & Knowledgebase
- **Digital Products Marketplace:** Publish and sell scripts, themes, and plugins with PTS or fiat pricing, Arabic & Unicode slug support, and version management.
- **Customer Ratings & Reviews Engine:** 5-star customer ratings with overall averages, rating distribution percentage progress bars, verified buyer badges (`is_verified_buyer`), and customer review feeds.
- **Screenshots Lightbox & Previews:** Interactive screenshot carousels with zoomable full-screen modal lightbox, plus external launcher buttons for Live Demo web previews and video walkthroughs.
- **Wiki-Style Knowledgebase (`/kb`):** Rich documentation hub per product with Markdown rendering, interactive Mermaid diagrams, category filtering, and eager-loading relation isolation.
- **P2P Service Orders Marketplace (`/orders`):** Bulletin bidding marketplace with 5-step milestone progression (Matching → Awarded → In Progress → Delivered → Completed), requirement attachments, client revision rounds, deliverable downloads, and explicit non-escrow platform terms.
- **Web Directory:** Categorized webmaster directory with SEO backlinks, thumbnail scraping, and traffic statistics.

### 💳 5. Paid Subscriptions & Multi-Currency Billing
- **Tiered Membership Plans:** Custom plan durations, recurring benefits, ad credits, discount entitlements, and glowing tier badges.
- **8 Integrated Payment Gateways:**
  - `Stripe` • `PayPal` • `Lemon Squeezy` • `Paddle` • `Bank Transfer` (with manual receipt review) • `Tabby` (BNPL) • `Flouci` • `Apple Pay simulation`.
- **Multi-Currency Engine:** Configurable base currency, live exchange-rate snapshots, and custom decimal formatting.
- **Privacy-Centric:** Zero credit card data stored locally; all transactions processed via hosted PCI-compliant gateways.

### 🛠️ 6. Developer Platform & OAuth 2.0 Ecosystem
- **OAuth 2.0 Server (`/oauth/authorize`, `/oauth/token`):** Full RFC 6749 compliance, query parameter preservation, and HTTP Basic Auth client credential support.
- **Granular Permissions Catalog:** 27 scopes across 7 security categories (`Identity`, `Content`, `Messages`, `Wallet`, `Community`, `Store`, `Owner Integrations`).
- **Developer API v1 (`/api/developer/v1/*`):** Comprehensive REST endpoints for third-party publishing, identity retrieval, and store integration.
- **Reverse Proxy & Server Resilience:** Universal Bearer token extraction supporting Apache FastCGI, cPanel, Nginx, and Cloudflare reverse proxies.
- **WordPress Integration:** Ready for third-party auto-posters and plugins (such as ADStn Auto Poster).

### 🎛️ 7. Duralux Superdesign Admin Suite
- **Modern Dark-Mode Dashboard (`/admin`):** Glassmorphic UI with single-pass SQL aggregations delivering a **95%+ reduction in load times**.
- **Admin Advice Engine:** Rotating daily expert tips ("نصيحة اليوم للمدير") to help webmasters grow and secure their platform.
- **Zero-Reload AJAX Extension Hub:** Activate or deactivate plugins and themes instantly with live category filter chips and dynamic counters.
- **Automated Database Migrations & Schema Self-Healing:** Automatic on-the-fly table schema repair and defensive column inspections ensure zero crashes on legacy schemas.
- **Verification Request Management:** Administrative review dashboard with eligibility criteria controls, customizable multilingual terms, audit logs, and instant notifications.
- **Automated Safe Release Updater:** In-browser system upgrades with automatic release payload compaction, preflight safety verification, and zero-reload execution.
- **Live Theme Customizer (`THEME-07`):** Interactive visual customizer adjusting brand colors, typography (Inter, Cairo, Tajawal, Roboto), surface styles, and glassmorphic blur with responsive split-screen preview.
- **System Health Monitor:** Real-time diagnostics for table sizes, database index overhead (`OPTIMIZE TABLE`), plugin resource footprint, and selective cache pre-warming (`CacheWarmupService`).

---

## 📱 Official Flutter Mobile App

MYADS features a first-party, cross-platform mobile client built with **Flutter 3.27+** for Android and iOS (**`myads_app` v1.8.5+27**).

<div align="center">
  <a href="https://github.com/mrghozzi/myads_app">
    <img src="https://img.shields.io/badge/Repository-myads__app-02569b?style=for-the-badge&logo=github" alt="MYADS App Repository" />
  </a>
</div>

### Mobile Highlights:
- **Video Hub & Cinema Watch (`v1.8.5`):** Dedicated `VideoHubScreen` with dynamic category pills, multi-criteria sorting (Latest, Most Viewed, Most Reacted, Oldest), direct routing (`/video/watch/:id`), and Shorts shelf.
- **Modern Forum Engine (`v1.8.4`):** Hierarchical sub-forums with color badges, filter chips (Latest, Top, Newest, Unanswered), threaded discussion tree, pinned comments, best answers, and quick Markdown composer.
- **Profile Privacy Suite (`v1.8.3`):** Granular About tab privacy (`canViewAbout`, `aboutVisibility`), locked container view, and profile settings dropdown.
- **Store Reviews & Media Gallery (`v1.8.2`):** 5-star ratings, verified buyer badges, rating breakdown bars, tap-to-expand lightbox screenshot viewer, and Live Demo / Video launchers.
- **Service Orders Marketplace (`v1.8.1`):** P2P disclaimer banner, 5-step milestone stepper, requirement attachments, revision request modal, deliverable downloads, and complete contract lifecycle actions.
- **Universal Bookmarks & Smart Autocomplete (`v1.7.9`):** 1-tap post bookmarking with infinite pagination (`SavedPostsScreen`), and real-time `@mentions` and `#hashtags` autocomplete overlay in post composer.
- **Security & Privacy:** Hardware-backed encrypted keystore storage (`flutter_secure_storage`), Sanctum Bearer token validation, HTTPS enforcement, and member ID anti-enumeration.
- **Global Localization:** Native Arabic (RTL) and English support with dynamic device locale synchronization.

> 📖 Check out the complete [Mobile App Guide](Documents/MOBILE_APP_GUIDE.md) and [Mobile Repository](https://github.com/mrghozzi/myads_app).

---

## 💻 Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Backend Framework** | [Laravel 12.x](https://laravel.com/) |
| **PHP Runtime** | PHP 8.2 or higher (PHP 8.3 & 8.4 ready) |
| **Database Engine** | MySQL 5.7+ / 8.0+ or MariaDB 10.3+ (InnoDB) |
| **Web Frontend** | Blade Templates, Bootstrap 5, Vanilla JavaScript (Zero SPA overhead) |
| **Mobile Client** | [Flutter 3.27+](https://flutter.dev/) / Dart (Riverpod, Dio, SecureStorage) |
| **Real-Time Streaming**| Server-Sent Events (SSE) via `LiveEventStreamService` |
| **API & Auth** | Laravel Sanctum (Mobile), OAuth 2.0 (Developer Platform), Socialite (OAuth) |
| **Testing Suite** | PHPUnit 11 (Automated feature & unit test coverage) |
| **Internationalization**| 14 Locales (`ar`, `en`, `fr`, `es`, `de`, `it`, `pt`, `ru`, `sr`, `tr`, `ja`, `zh_CN`, `zh_TW`, `fa`) |

---

## ⚡ Quick Start & Installation

### Prerequisites
Before installing MYADS, ensure your server meets the following requirements:
- **PHP:** `^8.2` with extensions: `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PCRE`, `PDO MySQL`, `Tokenizer`, `XML`.
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **Tools:** Composer 2.x and Node.js 18+ (for local asset compilation)

### 1. Fast Setup (Web Installer)

1. **Clone or Download the repository:**
   ```bash
   git clone https://github.com/mrghozzi/myads.git
   cd myads
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Configure Web Server:**
   - Point your web server document root to the `public/` directory.
   - For Apache on shared hosting, the pre-configured `.htaccess` in the root will automatically handle redirection.

4. **Launch the Visual Setup Wizard:**
   Open your browser and navigate to:
   ```text
   http://your-domain.com/install
   ```
   Follow the 5-step installer to verify file permissions, configure your database, generate the application key, run database migrations, and create your super-admin account.

---

### 2. Manual CLI Setup (For Developers)

```bash
# 1. Clone repository
git clone https://github.com/mrghozzi/myads.git && cd myads

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure your database credentials in .env, then migrate:
php artisan migrate --force

# 5. Build frontend assets & launch server:
npm run build
php artisan serve
```

Your MYADS instance will now be live at `http://127.0.0.1:8000`! 🎉

---

## 🔌 Developer Platform & REST API

MYADS includes an enterprise-grade REST API and OAuth 2.0 authorization server.

### Example: Publish a Community Post via REST API
```bash
curl -X POST "https://your-domain.com/api/developer/v1/me/content" \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "content": "Excited to launch our new product on MYADS! 🚀 #innovation",
       "media_type": "text",
       "privacy": "public"
     }'
```

### Supported Scopes Overview:
- `user.identity.read` • `user.profile.read` • `user.profile.write`
- `user.content.read` • `user.content.write` (with aliases `content.write`, `posts.write`)
- `user.messages.read` • `user.messages.send`
- `user.wallet.read` • `user.community.read` • `store.products.read`

> 📖 For full API endpoints, query parameters, and code examples, consult the [API Documentation](Documents/API_DOCS.md).

---

## 📚 Complete Documentation Index

All in-depth technical manuals and guides are located in the [`Documents/`](Documents/) directory:

| Guide | Description |
| :--- | :--- |
| 📖 [**Manual & Overview**](Documents/README.md) | In-depth platform architecture and module overview. |
| ⚙️ [**Installation Guide**](Documents/INSTALLATION.md) | Detailed server installation guide for Apache, Nginx, and cPanel. |
| 📋 [**System Requirements**](Documents/SYSTEM_REQUIREMENTS.md) | Complete list of PHP extensions, database configs, and server sizing. |
| 🎨 [**Theme Guide**](Documents/THEME_GUIDE.md) | Guide to building custom Blade themes and Live Theme Customizer hooks. |
| 🔌 [**Plugin Guide**](Documents/PLUGIN_GUIDE.md) | How to build plugins, register action/filter hooks, and inject widgets. |
| 📱 [**Mobile App Guide**](Documents/MOBILE_APP_GUIDE.md) | Connecting and compiling the Flutter mobile client (`myads_app`). |
| 💳 [**Paid Subscriptions Guide**](Documents/PAID_SUBSCRIPTIONS_GUIDE.md) | Configuring subscription plans, payment gateways, and webhooks. |
| 📡 [**REST API Documentation**](Documents/API_DOCS.md) | Developer API v1, Mobile API, and OAuth 2.0 specifications. |
| 🚀 [**Upgrade Guide**](Documents/UPGRADE.md) | Safe, zero-downtime database upgrade steps across versions. |
| 📜 [**Changelog & Release Notes**](Documents/changelogs.md) | Detailed version history, patches, and security notes. |

---

## 🤝 Contributing & Community

Contributions are what make the open-source community an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**!

1. **Fork the Project**
2. **Create your Feature Branch** (`git checkout -b feature/AmazingFeature`)
3. **Commit your Changes** (`git commit -m 'Add some AmazingFeature'`)
4. **Push to the Branch** (`git push origin feature/AmazingFeature`)
5. **Open a Pull Request**

Please make sure to review our [Contributing Guidelines](CONTRIBUTING.md), [Code of Conduct](CODE_OF_CONDUCT.md), and [Security Policy](SECURITY.md).

---

## ⭐ Star History

If you love MYADS or find it helpful for your projects, **please give this repository a star!** ⭐  
Your support motivates continuous development and keeps the open-source spirit alive.

<div align="center">
  <a href="https://github.com/mrghozzi/myads/stargazers">
    <img src="https://api.star-history.com/svg?repos=mrghozzi/myads&type=Date" alt="Star History Chart" width="750" />
  </a>
</div>

---

## 💖 Sponsorship & Support

MYADS is free, open-source software maintained by **[mrghozzi](https://github.com/mrghozzi)**. If this project brings value to your business or community, consider supporting future development:

<div align="center">

[![Support on Ko-Fi](https://img.shields.io/badge/Ko--fi-Support_on_Ko--fi-ff5e5b?style=for-the-badge&logo=ko-fi&logoColor=white)](https://ko-fi.com/mrghozzi)
&nbsp;&nbsp;&nbsp;&nbsp;
[![Support on Patreon](https://img.shields.io/badge/Patreon-Become_a_Patron-f96854?style=for-the-badge&logo=patreon&logoColor=white)](https://www.patreon.com/MrGhozzi)

</div>

---

## 📄 License

MYADS is released under the **[MIT License](LICENSE)**. You are free to use, modify, and distribute this software for personal and commercial projects.

<div align="center">
  <sub>Crafted with ❤️ by <a href="https://github.com/mrghozzi">mrghozzi</a> and the open-source community.</sub>
</div>
