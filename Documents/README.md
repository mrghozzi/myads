# Manual & Overview

Welcome to the **MYADS v4.3.5** documentation!

MYADS is an advanced PHP script designed for social networking and exchanging ads between website owners. Originally built with plain PHP, version 4.0 has been completely rewritten from scratch using the robust **Laravel Framework (v12, PHP 8.2+)**.

## Core Concepts

### 1. Ad Exchange System
MYADS allows users to exchange various types of traffic:
- **Banner Ads:** Traditional image banners placed in dedicated ad slots.
- **Text & Link Ads:** Contextual text snippets and direct URL linking.
- **Visit Exchange:** A mutual surf system where users view others' sites to earn points (PTS) to promote their own.
- **YouTube Views Exchange:** Watch-to-earn system for YouTube campaigns.
- **Smart Ads:** Contextual and native advertising.
- **Custom Member Ads:** Members create embeddable ad spaces, negotiate direct deals, track impressions/clicks, and settle daily PTS payouts.

### 2. Social Network & Community
The platform includes built-in social features to encourage user retention:
- **User Profiles:** Customizable profiles with avatars, cover photos, social links, and statistics.
- **Follow System:** Users can follow each other, creating a personalized feed.
- **Community Feed:** Support for posts, galleries, link previews, quote reposts, @mentions, and multimedia posts (Video, Audio, Files, Music, and Clips).
- **Private Messaging & Session Monitoring:** Real-time messaging and the ability to view/manage active member sessions.
- **Community Forum:** Discussions, tutorials, and support with a complete moderation system, category visibility controls, and attachments.
- **Gamification:** Points (PTS), badges, quests, and a point transactions ledger.

### 3. Marketplace (Store) & Services
The built-in store and services modules allow users to interact commercially:
- **Product Store:** Upload and download scripts, plugins, and templates (PTS-based pricing). Includes a Wiki-style Knowledgebase per product with Markdown support.
- **Services Marketplace:** Publish service requests, receive structured provider offers, award a provider, track delivery workflow, and exchange completion ratings.

### 4. Administrator Roles & Management
The system is managed via the **Duralux Dashboard**, a modern interface equipped with dark mode.
The admin (`id=1`) has absolute control over:
- Members, Content, & Admin Notifications
- Themes, Plugins, & Updates
- Media Manager (monitor, rename, and securely delete uploaded media files)
- Web Directory, News, and System Settings
- Centralized SEO Suite (admin dashboard, dynamic robots.txt, sitemap index, GA4 integration)

### 5. Mobile App API & Client
MYADS includes a foundational Flutter app for Android (`myads_app`) offering full community feed parity, forums, store, a native Clips System, and a premium Member Profile & Social Navigation experience powered by Laravel Sanctum and a secure Mobile API.

### Navigating the Documentation
Please refer to the specific guides for detailed instructions on managing different aspects of your MYADS installation:
- [Installation Guide](INSTALLATION.md)
- [System Requirements](SYSTEM_REQUIREMENTS.md)
- [Theme Guide](THEME_GUIDE.md)
- [Plugin Guide](PLUGIN_GUIDE.md)
- [API Documentation](API_DOCS.md)
