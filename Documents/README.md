# Manual & Overview

Welcome to the **MYADS v4.2.0** documentation!

MYADS is an advanced PHP script designed for social networking and exchanging ads between website owners. Originally built with plain PHP, version 4.0 has been completely rewritten from scratch using the robust **Laravel Framework (v10+, PHP 8.2+)**.

## Core Concepts

### 1. Ad Exchange System
MYADS allows users to exchange various types of traffic:
- **Banner Ads:** Traditional image banners placed in dedicated ad slots.
- **Text Ads:** Contextual text snippets.
- **Link Ads:** Direct URL linking.
- **Visit Exchange:** A mutual surf system where users view others' sites to earn points (PTS) to promote their own.

### 2. Social Network & Community
The platform includes built-in social features to encourage user retention:
- **User Profiles:** Customizable profiles with avatars, cover photos, and statistics.
- **Follow System:** Users can follow each other, creating a personalized feed.
- **Community Forum:** A space for discussions, tutorials, and support, heavily integrated with a reaction system (Like, Love, Wow, Angry, etc.). Includes a complete moderation system (pin/lock topics, assign moderators per category) and file attachment support.
- **Private Messaging:** Real-time (AJAX) messaging between members.
- **Order Request System:** A standalone module for hiring service providers (developers, designers, writers) directly from the community feed with integrated bids and ratings.
- **Standardized Deletion UI:** A robust, theme-integrated 'in-place' confirmation system for all user content (posts, comments, products, orders).

### 3. Marketplace (Store)
The built-in store allows users to upload and share (or sell for PTS):
- Scripts
- Plugins
- Templates

### 4. Administrator Roles & The Duralux Panel
The system is managed via the **Duralux Dashboard**, a modern interface equipped with dark mode.
The admin (`id=1`) has absolute control over:
- Members & Content
- Themes & Plugins
- System Settings & Localization
- Categories & Directory Listings

### Navigating the Documentation
Please refer to the specific guides for detailed instructions on managing different aspects of your MYADS installation:
- [Installation Guide](INSTALLATION.md)
- [System Requirements](SYSTEM_REQUIREMENTS.md)
- [Theme Guide](THEME_GUIDE.md)
- [Plugin Guide](PLUGIN_GUIDE.md)
- [API Documentation](API_DOCS.md)
