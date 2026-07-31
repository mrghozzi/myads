# MYADS v4.5.0

Advanced Laravel-based social networking and ad exchange platform for website owners.

![MYADS](https://raw.githubusercontent.com/mrghozzi/myads_check_updates/main/myads.png)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Sponsor Ko-Fi](https://img.shields.io/badge/Sponsor-Ko--fi-ff5e5b.svg)](https://ko-fi.com/mrghozzi)
[![Sponsor Patreon](https://img.shields.io/badge/Sponsor-Patreon-f96854.svg)](https://www.patreon.com/MrGhozzi)

- Project: [MYADS](https://github.com/mrghozzi/myads)
- Author: [mrghozzi](https://github.com/mrghozzi)
- Framework: Laravel 12
- Runtime: PHP 8.2+
- License: MIT

---

## Overview

MYADS combines ad exchange, community, marketplace, directory, forum, and admin tooling in one platform.

Core areas include:

- Banner ads, text ads, visit exchange, YouTube Views Exchange, Smart Ads with **Geo-Targeting** and **A/B Testing**, and **Custom Member Ads**
- Dedicated **YouTube-Style Video Watch Page** (`/t{id}`) and **Video Hub** (`/video`) with Shorts shelf, HTML5 custom player, publisher Hexagon avatars, and cover image composer
- Ad analytics with **Hourly Click Heatmaps**
- Member-to-member custom ad placements with embed codes, deal requests, PTS settlement, and external agreement tracking
- High-performance community feed (N+1 query optimized) with **Multimedia Posts** (Video, Audio, Music, Files, Reels/Clips), reactions, comments, reposts, mentions, and fast low-memory messaging
- Forum with moderation tools and attachments
- Store and dense power-user **Knowledgebase** dashboard with categories
- Web directory and **Services Marketplace**
- News publishing
- Gamification engine with PTS, PTS transfers and vouchers, 25+ badges (including Video Star, Clips Master, Audio Maestro), daily/weekly quests, and ledgers
- SEO suite with sitemap, robots.txt, performance integration, and Free SEO Checker for webmasters
- Permission-gated **Smart Widget Management** (`<x-widget-column>`) with drag-and-drop location reordering (`/admin/widgets`)
- High-performance Admin Dashboard powered by a **Single-Pass SQL Aggregation Engine** (95%+ query reduction)
- Security suite with IP bans, session monitoring, and protected public identifiers
- Full Flutter mobile app integration (`myads_app`) with native video watch screen, Hexagon avatars, and localized i18n in 14 languages
- Optional paid subscriptions and billing

---

## Key Features

### Member Features

- Ad exchange tools for banners, text ads, visit exchange, Smart Ads with **Geo-Targeting**, and Custom Ads
- Dedicated **YouTube-Style Video Watch Page** (`/t{id}`) and **YouTube-Style Video Hub** (`/video`) with Shorts shelf, 16:9 video grid, Hexagon publisher avatars, standalone popover flyouts, and suggested video recommendations
- Video Title (`video_title`) and Cover Thumbnail (`video_thumbnail`) post composer inputs with 256MB upload optimization
- **A/B Testing** optimization and **Performance Heatmaps** for advertisers
- Custom ad spaces that members can publish in the marketplace, invite advertisers into, or monetize with daily PTS deals
- Social profiles with follows, dynamic badges, privacy controls, and 12-platform social links
- Community feed with **Multimedia Posts** (Video, Audio, Music, Files, Reels/Clips), comments, reactions, reposts, and mentions
- Forum participation with categories, topics, attachments, and Markdown support
- Marketplace and Knowledgebase access (with article categories)
- Direct PTS transfers, PTS vouchers system, 25+ unlockable dynamic badges, and daily/weekly multimedia quests
- Session monitoring and device revocation
- Optional paid plans with a billing dashboard and hosted checkout flow

### Administrator Features

- Duralux admin panel with module-based ACL and `@.superdesign` aesthetics
- High-performance Admin Dashboard (`/admin`) with **Single-Pass SQL Aggregations** (95%+ load time reduction) and dynamic **Admin Advice/Tips Engine**
- **Smart Widget Management** hub (`/admin/widgets`) with target location pre-selection, category filter chips, drag-and-drop reordering, and permission-gated `<x-widget-column>` prompts
- **Admin SEO Suite** (`/admin/seo/*`) with real-time retention & database cleanup integration
- Dense power-user **Knowledgebase Hub** (`/admin/knowledgebase`) with live KPI stat cards, single-row filter bar, and category management
- Media Manager hub (`/admin/media`) with drag-and-drop uploads, code/video/audio previewers, and bulk cleanup
- Dedicated PTS Activities monitoring dashboard
- Unified Master Settings control panel (`/admin/settings`) and dedicated Mobile App settings (`/admin/settings/mobile`)
- Plugin and theme management
- Maintenance mode, database cleanup, daily log rotation (`LOG_STACK=daily`), and updater workflows
- Security dashboard for IP bans and member sessions
- Custom Ads administration for placements, deals, creative review, settlement limits, and marketplace settings
- Billing workspace for:
  - paid plans
  - currencies
  - orders
  - transaction logs
  - Stripe
  - PayPal
  - Bank Transfer
  - Lemon Squeezy
  - Paddle

---

## Custom Member Ads

MYADS includes a member-to-member Custom Ads module at `/ads/custom`.

Current Custom Ads capabilities:

- Publishers create ad placements and generate safe embed code for their own websites
- Supported v1 formats: Banner, Text Ad, and Native Card
- Advertisers can request public marketplace placements
- Publishers can send private invitations to specific members
- `pts_daily` deals reserve advertiser PTS and release publisher payouts daily only when impressions exist
- `external` deals record amount, currency, and notes without processing payment or storing bank/card data
- Independent analytics track impressions, clicks, CTR, referrers, devices, countries, hourly heatmaps, and payout state
- Admins can review creatives, pause/resume/cancel/complete deals, and configure service limits

Public embed endpoints:

- `/embed/custom.js`
- `/ads/custom/serve`
- `/ads/custom/click/{token}`

---

## Paid Subscriptions and Billing

MYADS includes an optional paid subscriptions system that can be enabled or disabled from `/admin/billing/settings`.

Current billing capabilities:

- Paid plans with durations, pricing, highlights, and entitlements
- Manual currency management with a base currency and exchange-rate snapshots
- Hosted checkout via `Stripe`, `PayPal`, `Lemon Squeezy`, and `Paddle`
- Manual review flow for `Bank Transfer`
- Subscription extension for same-plan repurchase
- Queued subscriptions for different-plan upgrades
- Profile badge and ad-credit entitlements
- Promotion discount support through subscription entitlements

Privacy rules:

- No card details are stored inside MYADS
- No unnecessary personal payment data is collected
- Only minimal payment metadata is stored

Developer documentation:

- [Paid Subscriptions Guide](Documents/PAID_SUBSCRIPTIONS_GUIDE.md)

---

## Technology Stack

- Backend: Laravel 12
- Database: MySQL 5.7+ / MariaDB 10.3+
- Frontend: Blade, Bootstrap 5, vanilla JavaScript
- Auth: Laravel auth, Sanctum, Socialite
- Testing: PHPUnit 11

---

## Documentation

Project documentation is available in `Documents/`:

- [Manual and Overview](Documents/README.md)
- [Installation Guide](Documents/INSTALLATION.md)
- [System Requirements](Documents/SYSTEM_REQUIREMENTS.md)
- [Upgrade Guide](Documents/UPGRADE.md)
- [Theme Guide](Documents/THEME_GUIDE.md)
- [Plugin Guide](Documents/PLUGIN_GUIDE.md)
- [API Documentation](Documents/API_DOCS.md)
- [Paid Subscriptions Guide](Documents/PAID_SUBSCRIPTIONS_GUIDE.md)
- [Changelog](Documents/changelogs.md)

### Mobile App

The official open-source MYADS mobile app is available at: [https://github.com/mrghozzi/myads_app](https://github.com/mrghozzi/myads_app)

To learn how to connect the app with your MYADS website, please read the [Mobile App Guide](Documents/MOBILE_APP_GUIDE.md).

---

## Installation

### Fresh Install

1. Make sure the server meets the requirements in `Documents/SYSTEM_REQUIREMENTS.md`.
2. Upload the project files to your server.
3. Point the document root to `public`, or use the provided root `.htaccess` for shared hosting setups.
4. Open:

   ```text
   http://your-domain.com/install
   ```

5. Follow the installer to:
   - verify requirements and writable paths
   - configure the database
   - generate `.env`
   - run migrations
   - create the first admin account

### Upgrade Notes

- Always back up files and database before any upgrade.
- Read `Documents/UPGRADE.md` before applying a new release.
- Billing features depend on the billing tables, so incomplete upgrades will surface fallback notices through `V420SchemaService`.
- Custom Ads require the `custom_ad_*` tables; the public embed/serve endpoints degrade gracefully until migrations are applied.

---

## Development Notes

- Public views use `theme::`
- Admin views use `admin::`
- Admin UI lives in `admin_themes/`
- Custom Ads logic lives under `app/Services/CustomAds/`
- Custom Ads settings live in `options` through `App\Support\CustomAdsSettings`
- Billing logic lives under `app/Services/Billing/`
- Billing settings live in `options` through:
  - `App\Support\SubscriptionSettings`
  - `App\Support\SubscriptionGatewaySettings`
- Any billing-dependent code must respect `V420SchemaService::supports('subscriptions_billing')`

---

## Testing

- Tests must run only against the isolated testing database
- Never run destructive test or migration commands against a live site database
- Billing coverage lives in:
  - `tests/Feature/BillingFeatureTest.php`
- Custom Ads coverage lives in:
  - `tests/Feature/CustomAdsFeatureTest.php`

---

## Roadmap Direction

Current platform direction includes:

- Continued platform polish and feature expansion for v4.5.x
- Expansion of billing and monetization tooling
- More Custom Ads targeting, reporting, and moderation capabilities
- More API coverage
- More real-time and marketplace capabilities

## Community & Contributing

We welcome community contributions, bug reports, and suggestions! Please review the following resources:

- 📖 [Contributing Guidelines](CONTRIBUTING.md)
- 🤝 [Code of Conduct](CODE_OF_CONDUCT.md)
- 🔒 [Security Policy](SECURITY.md)
- 💖 [Sponsorship & Support](.github/FUNDING.yml)

---

## License

MYADS is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
