# Mobile App Integration Guide

The official mobile client for the MYADS ecosystem is built using **Flutter (v3.27+)** and is available as an open-source project.

- **Repository:** [https://github.com/mrghozzi/myads_app](https://github.com/mrghozzi/myads_app)
- **Supported Platforms:** Android (SDK 21+) & iOS (iOS 13+)
- **Target App Version:** `v1.7.5+17`

---

## 1. Feature Parity & Architecture

The Flutter mobile application provides native parity with the web platform:

### 1.1 Community & Social Experience
- **Community Feed:** Browse posts, photo galleries, link previews, quote reposts, and @mentions.
- **Reactions & Comments:** React to posts with animated emoji icons (`like`, `love`, `funny`, `wow`, `sad`, `angry`) with instant point ledger and notification synchronization.
- **Member Profiles & Social Tabs:** Hexagonal avatars matching the web theme, user cover artwork, profile stats, and dedicated media tabs (Videos, Clips, Audio).
- **Member ID Privacy:** Full compatibility with MYADS anti-enumeration security suite, resolving randomized `public_uid` strings and usernames.

### 1.2 Video Hub & Shorts (`v1.7.5+17`)
- **Dedicated Video Hub (`VideoHubScreen`):** Category filter pills (`All`, `Videos`, `Shorts Clips`, `Trending`, `Latest`), 16:9 Spotlight Hero Video Card, and responsive video grid.
- **YouTube Shorts Shelf & Vertical Clips:** 9:16 vertical reel playback with touch swipe navigation, audio badges (`original_audio`), and instant reaction overlays.
- **YouTube-Style Watch Page:** Immersive video player with suggested related videos and author subscription shortcuts.

### 1.3 Store & Marketplace
- **Product Catalog:** Browse digital goods with active discount badges ("خصم") and strikethrough original prices.
- **Knowledgebase Integration:** Rich Markdown articles and wiki-style documentation associated with store items.

### 1.4 Communication & Real-Time Engine
- **Live Notifications & Messaging:** Real-time push updates and SSE live streaming (`/api/live/stream`) synchronizing unread message badge counts.
- **Sanctum Multi-Device Session Control:** Inspect active device tokens and revoke remote sessions on demand (`/api/settings/tokens/{id}/revoke`).
- **Localization:** Bilingual support for **Arabic (RTL)** and **English (LTR)**.

---

## 2. Requirements

1. **MYADS Website:** Version **v4.5.5** or higher running with HTTPS.
2. **Flutter SDK:** Version `3.27.0` or higher.
3. **Android Studio / Android SDK:** Platform SDK 34 / 35.
4. **Xcode:** For compiling and testing iOS applications (macOS only).

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

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/auth/login` | `POST` | Authenticate user and issue Sanctum token. |
| `/api/auth/register` | `POST` | Create new member account. |
| `/api/status` | `GET` | Paginated community feed timeline. |
| `/api/video/feed` | `GET` | Isolated video and clips feed. |
| `/api/live/stream` | `GET` | Real-time Server-Sent Events (SSE) stream. |
| `/api/store/products` | `GET` | Store catalog with active sale prices. |
| `/api/settings/sessions` | `GET` | List active web sessions and Sanctum API device tokens. |
| `/api/settings/tokens/{id}/revoke` | `POST` | Revoke a specific Sanctum API session token. |
