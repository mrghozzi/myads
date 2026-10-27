# Mobile App Integration Guide

The official mobile client for the MYADS ecosystem is built using **Flutter (v3.27+)** and is available as an open-source project.

- **Repository:** [https://github.com/mrghozzi/myads_app](https://github.com/mrghozzi/myads_app)
- **Supported Platforms:** Android (SDK 21+) & iOS (iOS 13+)
- **Target App Version:** `v1.8.7+29` (Synchronized with MYADS Web Core `v4.6.4`)

---

## 1. Feature Parity & Architecture

The Flutter mobile application (`myads_app`) provides 100% feature parity with the MYADS v4.6.4 web script across all core modules:

### 1.1 Community & Social Experience
- **Full Social Feed Parity (`portal/feed`):** High-speed feed supporting 9 post kinds: Text, Image Gallery (up to 10 photos with grid preview), Video, Audio (with waveform visualizer), Music, File Attachments, Clips, Live Link Previews, and Quote Reposts.
- **Universal Bookmarks / Saved Posts (`SavedPostsScreen`):** 1-tap bookmarking on post cards (`/api/statuses/save-toggle`) with optimistic UI feedback, infinite scroll pagination (`/api/statuses/saved`), and quick shortcuts in Explore and Settings.
- **Smart Mentions & Hashtags Autocomplete:** Interactive autocomplete overlay in post composer detecting `@` (`/api/mentions/users`) and `#` (`/api/tags/suggest`) in real-time right before the cursor.
- **Expandable Long Content:** Collapsible text cards with smooth animations, bottom gradient fade mask, and bilingual "See more" / "See less" ("رؤية المزيد" / "رؤية أقل") toggle buttons.
- **Optimistic UI Reactions (< 1ms):** Instant visual reaction feedback across 8 supported types (`like`, `love`, `funny`, `wow`, `sad`, `angry`, `care`) with automatic error rollback on network disconnection.
- **Member Profiles & Privacy Suite (`UserProfileModel`):** Hexagonal avatars (`HexagonAvatar`) matching web theme tiers, public identifier resolution (`public_uid`), and granular About tab privacy (`canViewAbout`, `aboutVisibility`) with locked container fallback.

### 1.2 Video Hub & Cinema Watch (`v1.8.5+27`)
- **Dedicated Video Hub (`VideoHubScreen`):**
  - **Dynamic Category Filter Pills:** Horizontal scrollable category pills loaded dynamically from `/api/video/feed` with 1-tap active gradient states.
  - **Multi-Criteria Video Sorting:** Interactive sort menu supporting **Latest**, **Most Viewed** (`views`), **Most Reacted** (`reactions`), and **Oldest** (`oldest`).
  - **Spotlight Hero Video Banner:** 16:9 featured video card with channel metadata, verified checkmark, and quick play action.
  - **Shorts Clips Shelf:** Horizontal YouTube Shorts carousel with 9:16 vertical cards, creator avatars, and view counters.
  - **Content Isolation:** Strict backend scoping excluding non-video posts from video feeds.
- **Cinema Watch & Direct Routing (`PostDetailsScreen`):**
  - Direct GoRoute declarations for `/video/watch/:id` and `/video/:id`.
  - Resilient video loading by ID via `/api/video/watch/:id` or fallback `/api/statuses/:id`.
  - Dark ambient cinema watch stage with creator hexagon avatar, 1-tap follow toggle, expandable description, and suggested related videos feed.

### 1.3 Modern Forum Engine (`v1.8.4+26`)
- **Forums Directory (`ForumsScreen`):**
  - Hierarchical sub-forums with custom color badges (`color`), sub-category chips, and recursive topic counters.
  - Pull-to-refresh synchronization and privacy visibility handling (`0`=public, `1`=members, `2`=moderators).
- **Topics Stream & Filtering (`ForumTopicsScreen`):**
  - Live filter chips: **Latest**, **Top** (most replies), **Newest**, and **Unanswered**.
  - Debounced search bar with instant query execution.
  - Pinned (`is_pinned`) and locked (`is_locked`) status indicators on topic cards.
  - Interactive Markdown discussion creation modal with title, category selector, and syntax helpers.
- **Discussion Details & Threaded Replies (`ForumTopicDetailScreen`):**
  - Circular and hexagonal member avatars with online presence indicators and role badges.
  - Distinct highlighted cards for **Pinned Comments** and **Best Answers**.
  - True 2-level threaded replies tree with author indentations and timestamp formatting.
  - Contextual "Replying to @username" banner with 1-click cancel button.
  - Quick Markdown formatting chips (`Bold`, `Italic`, `Code`, `Quote`, `Link`) above the reply composer.
- **Moderation Actions:** In-app actions for toggling pin/lock on topics, pinning comments, marking Best Answers, and deleting comments.

### 1.4 Store Marketplace, Ratings & Reviews (`v1.8.2+24`)
- **Product Catalog (`StoreProductsScreen`):** Browse digital scripts, plugins, and themes with active red discount badges ("خصم"), strikethrough original prices, star rating badges, and review counts.
- **Customer Reviews & Rating Distribution (`StoreProductDetailScreen`):**
  - 5-star rating summary card with overall average score and total review counts.
  - Rating breakdown percentage progress bars (5, 4, 3, 2, 1 stars).
  - Verified Buyer badge (`is_verified_buyer`) highlighting confirmed purchasers.
  - Customer review feed displaying reviewer avatars, dates, star scores, and feedback text.
- **Screenshots Lightbox & Previews:**
  - Interactive screenshots preview carousel with tap-to-expand full-screen modal lightbox supporting pinch-to-zoom and swipe navigation.
  - "Live Demo" external launcher button allowing users to test SaaS/web applications.
  - "Video Preview" launcher button for watching video walkthroughs.
- **Wiki-Style Knowledgebase:** Markdown-rendered documentation articles associated with products.

### 1.5 Service Orders P2P Marketplace (`v1.8.1+23`)
- **P2P Platform Disclaimer Banner:** Embedded disclaimer banners on both the marketplace list and request detail screens stating non-escrow peer-to-peer accountability.
- **Milestone Progress Stepper:** Visual 5-step milestone tracker (Matching → Awarded → In Progress → Delivered → Completed) with due dates and overdue badge indicators.
- **Requirements & Deliverables:** Project requirement attachments and completed deliverable file downloads.
- **Contract Revisions:** Interactive revision request alerts and bottom sheet for client revision notes and iteration counters.
- **Full Order Lifecycle Management:** Complete actions for awarding proposals, commencing work, delivering deliverables, requesting revisions, completing with 5-star reviews, and mutual cancellations.

### 1.6 Communication, Settings & Security
- **Live Notifications & Private Messaging:** Real-time push updates, chat bubble polling, and SSE live stream (`/api/live/stream`) synchronizing unread message badge counters.
- **Bidirectional Settings Synchronization:** Comprehensive Settings Hub for Account, Profile, Privacy (`about_visibility`), Social Links, Email/Push preferences, Badges Showcase, and FCM Push Device Token registration.
- **Gamification Quests & Wallet:** Daily and weekly quests progression, PTS transfers between members, and voucher code generation/redemption.
- **Sanctum Multi-Device Session Control:** Inspect active device tokens and revoke remote API sessions on demand (`/api/settings/tokens/{id}/revoke`).
- **Network Resilience & WAF Evasion:** Automatic injection of `X-Authorization` and `X-Api-Token` headers alongside standard `Authorization` headers to bypass restrictive shared hosting ModSecurity rules.
- **Global Localization:** Native **Arabic (RTL)** and **English (LTR)** with dynamic device locale synchronization.

---

## 2. Requirements

1. **MYADS Website:** Version **v4.6.4** or higher running over HTTPS.
2. **Flutter SDK:** Version `3.27.0` or higher (Dart 3.6+).
3. **Android Studio / Android SDK:** Platform SDK 34 / 35 with Kotlin Gradle Plugin `2.2.20`.
4. **Xcode:** For compiling and testing iOS applications (macOS only, iOS 13+).

---

## 3. Connecting the App to Your Website

### Step 1: Obtain the Mobile API Key
1. Log in to your MYADS website Admin Panel as Super Admin (`id=1`).
2. Navigate to **Settings -> API Settings** (or Mobile Settings).
3. Copy your platform's secure **Mobile API Key**.

### Step 2: Configure Environment Variables
Inside the root directory of the `myads_app` Flutter repository:

1. Copy `.env.example` to create `.env`:
   ```bash
   cp .env.example .env
   ```
2. Open `.env` and set your API endpoint and key:
   ```env
   # Must end with /api without a trailing slash
   BASE_URL=https://yourdomain.com/api
   API_KEY=your_secure_mobile_api_key_here
   ```

### Step 3: Install Dependencies & Run
Execute the following commands in the mobile app root:

```bash
# Fetch Flutter packages
flutter pub get

# Run on connected device or emulator
flutter run
```

---

## 4. Building Production Release Packages

### Android APK:
```bash
flutter build apk --release --split-per-abi
```
Output files will be generated in `build/app/outputs/flutter-apk/`.

### Android App Bundle (Google Play Store):
```bash
flutter build appbundle --release
```
Output bundle will be located at `build/app/outputs/bundle/release/app-release.aab`.

---

## 5. Mobile API Endpoints Summary

All mobile endpoints require the `X-API-KEY: {key}` header, and authenticated endpoints require `Authorization: Bearer {token}`:

| Domain | Endpoint | Method | Description |
| :--- | :--- | :---: | :--- |
| **Auth** | `/api/login` | `POST` | Authenticate member and issue Sanctum token. |
| **Auth** | `/api/register` | `POST` | Register a new member account. |
| **Auth** | `/api/logout` | `POST` | Invalidate active Sanctum token session. |
| **Auth** | `/api/user` | `GET` | Get current authenticated user object. |
| **Feed** | `/api/portal/feed` | `GET` | Paginated community timeline with media. |
| **Feed** | `/api/statuses` | `POST` | Publish new status post with multimedia uploads. |
| **Feed** | `/api/statuses/{id}` | `GET` | Detailed post payload with relations. |
| **Feed** | `/api/statuses/saved` | `GET` | Paginated saved/bookmarked statuses feed. |
| **Feed** | `/api/statuses/save-toggle` | `POST` | Toggle post bookmark state. |
| **Feed** | `/api/statuses/link-preview`| `POST` | Fetch rich link metadata preview. |
| **Feed** | `/api/composer/options` | `GET` | Get categories and post kinds for composer. |
| **Autocomplete** | `/api/mentions/users` | `GET` | Autocomplete user mentions (`?q=keyword`). |
| **Autocomplete** | `/api/tags/suggest` | `GET` | Autocomplete hashtag suggestions (`?q=keyword`). |
| **Video Hub** | `/api/video/feed` | `GET` | Video feed with category pills, sorting & clips. |
| **Video Hub** | `/api/video/watch/{id}` | `GET` | Cinema watch payload with suggested videos. |
| **Clips** | `/api/clips` | `GET` | 9:16 vertical short video clips reel. |
| **Clips** | `/api/clips/saved` | `GET` | List user saved clips. |
| **Clips** | `/api/clips/{id}/save` | `POST` | Save clip to bookmarks. |
| **Forums** | `/api/forums/categories` | `GET` | Hierarchical categories with color badges. |
| **Forums** | `/api/forums/categories/{id}/topics` | `GET` | Filtered topics stream (`latest`, `top`, `newest`, `unanswered`). |
| **Forums** | `/api/forums/categories/{id}/topics` | `POST` | Create new discussion topic. |
| **Forums** | `/api/forums/topics/{id}` | `GET` | Topic details with 2-level threaded comments tree. |
| **Forums** | `/api/forums/topics/{id}/replies` | `POST` | Submit threaded reply (`parent_id`). |
| **Forums** | `/api/forums/topics/{id}/pin` | `POST` | Toggle pinned state on topic. |
| **Forums** | `/api/forums/topics/{id}/lock`| `POST` | Toggle locked state on topic. |
| **Forums** | `/api/forums/comments/{id}/pin` | `POST` | Toggle pin on specific comment. |
| **Forums** | `/api/forums/comments/{id}/best-answer` | `POST` | Mark comment as Best Answer. |
| **Forums** | `/api/forums/comments/{id}`| `DELETE` | Delete discussion comment or reply. |
| **Store** | `/api/store/products` | `GET` | Store catalog with sale discount prices. |
| **Store** | `/api/store/products/{id}` | `GET` | Product details with reviews, ratings & previews. |
| **Store** | `/api/store/products/{id}/knowledgebase` | `GET` | Documentation articles for product. |
| **Orders** | `/api/orders` | `GET` | Service orders marketplace requests feed. |
| **Orders** | `/api/orders/{id}` | `GET` | Complete order contract state & milestones. |
| **Orders** | `/api/orders/{id}/offers` | `POST` | Submit provider proposal offer. |
| **Orders** | `/api/orders/{id}/award` | `POST` | Client awards offer to provider. |
| **Orders** | `/api/orders/{id}/start` | `POST` | Provider begins work milestone. |
| **Orders** | `/api/orders/{id}/deliver`| `POST` | Provider submits completed deliverables. |
| **Orders** | `/api/orders/{id}/revision`| `POST`| Client requests deliverable revisions. |
| **Orders** | `/api/orders/{id}/complete`| `POST`| Client completes order with star review. |
| **Orders** | `/api/orders/{id}/cancel` | `POST` | Cancel service contract. |
| **Messages** | `/api/messages` | `GET` | Active conversations list with unread counts. |
| **Messages** | `/api/messages/updates` | `GET` | Poll incoming message updates. |
| **Messages** | `/api/messages/{id}` | `GET` | Conversation message history. |
| **Messages** | `/api/messages/{id}` | `POST` | Send direct message. |
| **Messages** | `/api/messages/{id}/read` | `POST` | Mark conversation as read. |
| **Real-Time** | `/api/live/stream` | `GET` | Persistent Server-Sent Events (SSE) stream. |
| **Notifications** | `/api/notifications` | `GET` | User notifications feed. |
| **Notifications** | `/api/notifications/unread-count` | `GET` | Unread notifications badge count. |
| **Notifications** | `/api/notifications/read-all` | `POST` | Mark all notifications as read. |
| **Profile** | `/api/profile/{id}` | `GET` | Profile details with About tab privacy. |
| **Profile** | `/api/profile/{id}/statuses` | `GET` | Member published posts stream. |
| **Profile** | `/api/profile/{id}/follow` | `POST` | Toggle follow status. |
| **Profile** | `/api/profile/{id}/block` | `POST` | Block user account. |
| **Settings** | `/api/settings/overview` | `GET` | High-level account overview. |
| **Settings** | `/api/settings/profile` | `GET/POST`| Fetch or update profile information. |
| **Settings** | `/api/settings/privacy` | `GET/POST`| Fetch or update privacy (`about_visibility`). |
| **Settings** | `/api/settings/social` | `GET/POST`| Fetch or update social profile links. |
| **Settings** | `/api/settings/notifications` | `GET/POST`| Fetch or update notification preferences. |
| **Settings** | `/api/settings/sessions` | `GET` | List active web & Sanctum device sessions. |
| **Settings** | `/api/settings/tokens/{id}/revoke` | `POST` | Revoke a specific Sanctum API token. |
| **Settings** | `/api/settings/badges` | `GET/POST`| Get or reorder showcased badges. |
| **Settings** | `/api/settings/device-token` | `POST` | Register FCM push notification token. |
| **Gamification** | `/api/wallet/balance` | `GET` | Current PTS balance and currencies. |
| **Gamification** | `/api/quests` | `GET` | Daily and weekly quests progression. |
| **Gamification** | `/api/quests/{id}/claim` | `POST` | Claim completed quest rewards. |
| **Gamification** | `/api/pts/transfer` | `POST` | Transfer PTS to another user. |
| **Gamification** | `/api/pts/vouchers/create` | `POST` | Generate PTS voucher code. |
| **Gamification** | `/api/pts/vouchers/claim` | `POST` | Redeem PTS voucher code. |
| **Search** | `/api/search/live` | `GET` | Multi-entity live search across platform. |
