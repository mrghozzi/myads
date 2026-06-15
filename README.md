# MYADS v4.3.7

Advanced Laravel-based social networking and ad exchange platform for website owners.

![MYADS](https://raw.githubusercontent.com/mrghozzi/myads_check_updates/main/myads.png)

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
- Ad analytics with **Hourly Click Heatmaps**
- Member-to-member custom ad placements with embed codes, deal requests, PTS settlement, and external agreement tracking
- Community feed with **Multimedia Posts** (Video, Audio, Music, Files, Reels), reactions, comments, reposts, mentions, and messaging
- Forum with moderation tools and attachments
- Store and product knowledgebase
- Web directory
- News publishing
- Gamification with PTS, PTS transfers and vouchers, badges, quests, and ledgers
- SEO suite with sitemap, robots.txt, and analytics support
- Security suite with IP bans, session monitoring, and protected public identifiers
- Optional paid subscriptions and billing

---

## Key Features

### Member Features

- Ad exchange tools for banners, text ads, visit exchange, Smart Ads with **Geo-Targeting**, and Custom Ads
- **A/B Testing** optimization and **Performance Heatmaps** for advertisers
- Custom ad spaces that members can publish in the marketplace, invite advertisers into, or monetize with daily PTS deals
- Social profiles with follows, badges, privacy controls, and social links
- Community feed with **Multimedia Posts** (Video, Audio, Music, Files, Reels), comments, reactions, reposts, and mentions
- Forum participation with categories, topics, and attachments
- Marketplace and knowledgebase (with categories) access
- Direct PTS transfers and vouchers system for members
- Session monitoring and device revocation
- Optional paid plans with a billing dashboard and hosted checkout flow

### Administrator Features

- Duralux admin panel with module-based ACL
- Full content, knowledgebase categories, and member management
- Dedicated PTS Activities monitoring dashboard
- Plugin and theme management
- SEO dashboard and indexing tools
- Maintenance mode and updater workflows
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

- Continued platform polish for v4.3.x
- Expansion of billing and monetization tooling
- More Custom Ads targeting, reporting, and moderation capabilities
- More API coverage
- More real-time and marketplace capabilities

---

## License

MYADS is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
