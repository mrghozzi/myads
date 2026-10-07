# MYADS v4.6.3 REST & Real-Time API Documentation
> **Specification Version:** `v4.6.3` (Stable Release)  
> **Target Framework:** Laravel 12 (PHP 8.2+)  
> **Authentication Engines:** Laravel Sanctum (Mobile & Web API), OAuth 2.0 (Developer Platform), and Server-Sent Events (SSE Live Stream).  
> **Last Updated:** October 2026  

---

## 1. Overview & Architecture

The MYADS v4.6.3 API ecosystem delivers high-performance, secure, and extensible interfaces connecting web clients, companion mobile applications (Flutter), and third-party developer integrations.

### Primary API Subsystems
1. **Internal Mobile & Web API (`/api/*`):** Powered by Laravel Sanctum for mobile app companion clients (`myads_app` v1.8.0+22) and web AJAX workflows.
2. **Real-Time Events Engine (`/live/stream` & `/api/live/stream` — RT-04):** Zero-overhead Server-Sent Events (SSE) streaming engine delivering instant unread counters (synchronized with `MessageConversationService`), live toasts, and feed updates.
3. **Developer Platform & OAuth 2.0 (`/oauth/*` & `/api/developer/v1/*`):** 27 granular permissions across 7 categories for external applications registered at `/developer`.
4. **Service Orders Marketplace Lifecycle Engine (`/api/orders/*` & `/orders/*`):** Peer-to-peer bulletin model with full contract lifecycle management: requirement attachments, proposal offers, contract awarding, milestone progression, deliverable file uploads, client revision workflows (`requestRevision`), 5-star ratings, and protected file downloads.
5. **Store Marketplace, Ratings & Reviews Engine (`/api/store/*` & `/store/*`):** Rich digital marketplace with 5-star customer ratings, verified buyer badges, interactive screenshots lightbox gallery, live demo and video URL previews, Arabic & Unicode slug support, PTS checkout, and discount coupon codes.
6. **Software Licensing & Extension Marketplace Feed (`/api/marketplace/extensions/*`, `/api/license/verify`):** Plugin and theme discovery feeds, automated update checking for WordPress and third-party CMSs, domain activation verification, and secure package downloads.
7. **Ad Serving & Exchange Engine (`/ads/*`, `/bn.php`, `/link.php`, `/smart.php`, `/embed/*.js`):** High-throughput banner, link, smart, and custom member-to-member ads delivery, anti-click-farm validation, and conversion tracking.
8. **Real-Time Search & Intelligence (`/api/search/live`, `/api/ads/stats`):** Multi-entity live search across members, store products, forum topics, and feed posts, alongside real-time advertising analytics.
9. **Universal Media & Comments Ingestion Engine (`/comment/*`, `/api/statuses/*`):** Drag-and-drop & clipboard (`Ctrl+V`) media ingestion, binary MIME verification (`FileUploadSecurityService`), and automatic WebP image optimization.
10. **Smart Partitioned Sitemaps & Discovery (`/sitemap.xml`, `/sitemap/*.xml`):** Partitioned XML sitemaps with ETag conditional HTTP 304 caching for enterprise SEO discovery.

---

## 2. Authentication & Security Policy

### A. Internal API Tokens (Laravel Sanctum)
Designed for the first-party Flutter mobile application and official platform companions.

**Two-Layer Security Protocol:**
1. `X-API-KEY` (Header): Global API key managed in the Admin Control Panel (`mobile_api.api_key`). **Must** be sent as an HTTP header to prevent leakage in server access logs and browser referrer headers.
2. `Authorization: Bearer {token}` (Header): The user's Sanctum token issued upon authentication.  
   *(Note: `X-Authorization: Bearer {token}` is supported as a fallback on hosting environments where standard authorization headers are stripped by Apache/Nginx).*

**Login Endpoint:** `POST /api/login`  
- **Headers:** `X-API-KEY: {YOUR_GLOBAL_KEY}`, `Content-Type: application/json`  
- **Payload:** `{"login": "username_or_email", "password": "..."}`  
- **Response:**
  ```json
  {
      "status": "success",
      "token": "1|sanctum_token_string...",
      "user": {
          "id": 42,
          "username": "developer",
          "name": "Developer Name",
          "avatar_url": "https://domain.com/upload/avatar.png"
      }
  }
  ```

### B. Rate Limiting Rules
Public and authenticated API endpoints are protected with sliding-window rate limiters:

| Endpoint Group | Rate Limit | Protection Target |
|---|---|---|
| `POST /api/login` | 5 req / min / IP | Brute-force credential stuffing |
| `POST /api/register` | 3 req / min / IP | Automated spam registration |
| `POST /api/license/verify` | 10 req / min / IP | License enumeration & brute-force |
| `GET /api/search/live` & `GET /search/live` | 40 req / min / IP | Fulltext search DoS prevention |
| `POST /status/create` & file uploads | 20 req / min | Disk-filling & media flooding |
| `GET /share` | 15 req / min / IP | External link preview scraping & spam |
| `POST /post` (Forum) | 15 req / min | Forum spam topic generation |
| `GET/POST /api/developer/v1/*` | 30 req / min / IP | Developer API scraping & abuse |
| `GET /api/live/stream` | 1 connection / user | Persistent streaming session |

*When rate limits are exceeded, the server returns HTTP `429 Too Many Requests`.*

### C. Dynamic Localization (`Accept-Language`)
All API responses, notification payloads, and validation error messages support dynamic localization:
- **Header:** `Accept-Language: ar` (or `en`, `fr`, `es`, `de`, `tr`, etc.)
- **Default:** Site default locale (`ar` or `en`).

### D. Privacy & Member ID Obfuscation
When `public_member_ids_enabled` is active in Admin Security Settings, all API resources (`UserResource`, `UserProfileResource`, `StatusResource`, `SearchApiController`) automatically serve randomized public identifiers (`public_uid`) or usernames in place of numeric database IDs (`users.id`), neutralizing member enumeration attacks.

### E. Peer-to-Peer Service Orders Non-Escrow Policy
Under the platform's Terms of Service and Bulletin Architecture (v4.6.3), MYADS functions strictly as a peer-to-peer advertising, discovery, and networking platform. The platform does not act as an escrow agent, financial intermediary, or guarantor of deliverables for service orders negotiated at `/orders` or `/api/orders`.

---

## 3. Real-Time Events Engine (SSE Stream — RT-04)

The **Real-Time Events Engine** provides persistent, low-overhead event streaming via standard Server-Sent Events (SSE), eliminating the need for periodic client polling.

### Endpoints
- **Web Client Stream:** `GET /live/stream` (Authenticated via Web Session)
- **Mobile / API Client Stream:** `GET /api/live/stream` (Authenticated via Sanctum Bearer Token)

### Protocol Headers
```http
HTTP/1.1 200 OK
Content-Type: text/event-stream; charset=UTF-8
Cache-Control: no-cache, no-store, must-revalidate
X-Accel-Buffering: no
Connection: keep-alive
```

### Event Channels Catalog

#### 1. `handshake` (Connection Initialization)
Sent immediately upon connection establishment:
```text
event: handshake
data: {
    "status": "connected",
    "user_id": 42,
    "username": "developer",
    "unread_notifications": 3,
    "unread_messages": 1,
    "timestamp": 1787534000,
    "server_time": "2026-10-07T22:00:00+00:00"
}
```

#### 2. `notifications` (Unread Alerts & New Notifications)
Dispatched whenever the user's unread notification count changes:
```text
event: notifications
data: {
    "unread_count": 4,
    "has_new": true,
    "latest": {
        "id": 105,
        "name": "قام أحمد بالتعليق على منشورك",
        "url": "/post/123#comment-45",
        "logo": "comment",
        "time": 1787534015
    }
}
```

#### 3. `messages` (Direct Messages & Unread Counters)
Dispatched when an incoming private message arrives or unread state changes:
```text
event: messages
data: {
    "unread_count": 2,
    "has_new": true,
    "latest": {
        "id": 204,
        "sender_id": 15,
        "sender_name": "Sarah",
        "sender_avatar": "https://example.com/upload/avatar.jpg",
        "text_preview": "مرحباً، هل يمكنك مراجعة العرض؟",
        "time": 1787534020
    }
}
```
*Note: `unread_count` reflects active unread conversation threads (`state != 0`) matching `MessageConversationService::unreadConversationCount()`, ensuring full parity across Blade views, polling, and SSE live streams.*

#### 4. `feed` (Community Feed Updates Counter)
Dispatched when other users publish new public posts:
```text
event: feed
data: {
    "new_posts_count": 3,
    "timestamp": 1787534030
}
```

#### 5. `admin` (System Monitoring Alerts — Super Admin `id=1` Only)
Dispatched to administrative sessions:
```text
event: admin
data: {
    "pending_reports": 2,
    "timestamp": 1787534030
}
```

#### 6. `ping` & `reconnect`
- `ping`: Periodic heartbeat sent every few seconds to keep proxies and NAT gateways alive.
- `reconnect`: Clean connection close notice sent after the micro-loop duration (e.g. 20s), prompting the browser's native `EventSource` to automatically and transparently reconnect.

### JavaScript Client Integration
```javascript
const eventSource = new EventSource('/live/stream');

eventSource.addEventListener('notifications', (e) => {
    const data = JSON.parse(e.data);
    window.updateNotificationIndicators(data.unread_count);
});

eventSource.addEventListener('messages', (e) => {
    const data = JSON.parse(e.data);
    document.querySelectorAll('[data-message-unread-count]').forEach(el => {
        el.textContent = data.unread_count > 0 ? data.unread_count : '';
        el.hidden = data.unread_count === 0;
    });
});
```

---

## 4. Developer Platform & OAuth 2.0 Ecosystem

Developers can register external applications at `/developer` to build third-party integrations using OAuth 2.0 Authorization Code Flow.

### Authorization URLs
- **Authorization Screen:** `GET /oauth/authorize`
- **Token Exchange:** `POST /oauth/token`

### OAuth 2.0 Permissions Catalog (27 Scopes across 7 Categories)

| Category | Scope Identifier | Description | Sensitive |
|---|---|---|:---:|
| **Identity & Profile** | `user.identity.read` | Read member account identifier and basic public identity fields. | No |
| | `user.profile.read` | Read public profile details, cover, points, and core member metadata. | No |
| | `user.email.read` | Access the verified primary email address of the member. | **Yes** |
| | `user.social_links.read` | Read public social links attached to a member profile. | No |
| | `user.follows.read` | Read follower and following relationships for visible members. | No |
| | `user.follows.write` | Follow or unfollow other members on behalf of the user. | **Yes** |
| **Content & Interactions** | `user.content.read` | Read public posts and status updates authored by the user. | No |
| | `user.content.write` | Create, update, and publish posts on behalf of the user. | **Yes** |
| | `user.reactions.write` | Add or toggle likes and reactions to content on behalf of the user. | No |
| | `user.comments.write` | Publish comments and replies on posts on behalf of the user. | **Yes** |
| **Messages & Notifications** | `user.messages.read` | Read private direct message conversations belonging to the user. | **Yes** |
| | `user.messages.write` | Send private direct messages on behalf of the user. | **Yes** |
| | `user.notifications.read` | Read account notifications, alerts, and unread counters. | No |
| **Wallet & Rewards** | `user.wallet.read` | Read user points (PTS) balance, transactions, and wallet details. | **Yes** |
| | `user.badges.read` | Read member badges, unlocked achievements, and quest status. | No |
| **Community & Media** | `user.clips.read` | Browse short video clips feed and saved clips in user account. | No |
| | `user.clips.write` | Save and unsave short video clips on behalf of the user. | No |
| | `user.forums.read` | Read forum categories, topics, discussions, and replies. | No |
| | `user.forums.write` | Create new topics and post replies in forums on behalf of the user. | **Yes** |
| **Store & Advertising** | `user.store.read` | Browse marketplace products, offerings, and store knowledgebase. | No |
| | `user.orders.read` | Read user purchase orders history and submitted offers. | **Yes** |
| | `user.ads.read` | Read ad impression counts, clicks, and campaign performance statistics. | No |
| **App Owner Integrations** | `owner.profile.read` | Read authorized application owner profile. | No |
| | `owner.content.read` | Read authorized application owner content feed and updates. | No |
| | `owner.follow.write` | Follow or unfollow members on behalf of authorized owner. | **Yes** |
| | `owner.messages.read` | Read private message conversations belonging to authorized owner. | **Yes** |
| | `owner.messages.write` | Send private messages on behalf of authorized owner. | **Yes** |

### Application Management & Form Architecture
- **Application Registration:** `POST /developer/apps`
  - **Headers:** `Content-Type: application/json`, `X-CSRF-TOKEN: {token}`, `Accept: application/json`
  - **Payload:**
    ```json
    {
      "name": "My WordPress Sync App",
      "domain": "https://example.com",
      "description": "Auto-publish articles and sync updates.",
      "redirect_uris": "https://example.com/oauth/callback",
      "requested_scopes": ["user.identity.read", "user.content.write"]
    }
    ```
- **Application Update:** `PUT /developer/apps/{id}`
- **Client Secret Rotation:** `POST /developer/apps/{id}/rotate-secret`
- **Application Submission:** `POST /developer/apps/{id}/submit`
- **Application Self-Service Deletion:** `DELETE /developer/apps/{id}`

### OAuth 2.0 Authorization Flow & RFC 6749 Compliance

#### 1. Authorization Request
```http
GET /oauth/authorize?client_id={client_id}&redirect_uri={redirect_uri}&response_type=code&scope={scope}&state={state}
```
- **`client_id`** *(required)*: The 32-character hexadecimal Client ID.
- **`redirect_uri`** *(required)*: Must match one of the registered URIs.
- **`response_type`** *(required)*: Must be `code`.
- **`scope`** *(optional)*: Space- or comma-delimited permissions (e.g. `user.identity.read user.content.write`).
- **`state`** *(recommended)*: CSRF protection token.

> **WAF / ModSecurity Rule 930120 Bypass & Extended Scope Aliases:** The platform normalizes sensitive dotfile patterns (`profile.read`, `user_profile.read`, `user_profile_read` &rarr; `user.profile.read`) and developer aliases (`content.write`, `posts.write`, `publish_posts` &rarr; `user.content.write`; `messages.write`, `dm.send` &rarr; `user.messages.write`).

#### 2. Token Exchange (RFC 6749 Section 4.1.3)
```http
POST /oauth/token
Content-Type: application/x-www-form-urlencoded (or application/json)
Authorization: Basic {base64(client_id:client_secret)}
```
Or via body payload:
```json
{
  "grant_type": "authorization_code",
  "client_id": "{client_id}",
  "client_secret": "{client_secret}",
  "redirect_uri": "{redirect_uri}",
  "code": "{authorization_code}"
}
```

**Successful Response (HTTP 200 OK):**
```json
{
  "access_token": "DbMYZk4zAq5iZ4KNRBCceUyhnwZw5SbYp4fRIMRAXz40JzMjalN6XjsuuCAU",
  "token_type": "Bearer",
  "expires_in": 2592000,
  "refresh_token": "KONxbh1KItXybhrMTiikoy0YBAUzkrb3rk3iywZT4fvEx7zRTcpRgtHuaxfD",
  "scope": "user.identity.read user.content.write"
}
```

#### 3. Refreshing Tokens (RFC 6749 Section 6)
```json
{
  "grant_type": "refresh_token",
  "client_id": "{client_id}",
  "client_secret": "{client_secret}",
  "refresh_token": "{refresh_token}"
}
```

#### 4. Token Validity Lifecycles
- **Authorization Code:** 10 minutes (single-use).
- **Access Token:** 30 days (`2592000` seconds).
- **Refresh Token:** 90 days.

---

## 5. Developer API v1 Endpoints

### A. Authorization Protocol & Multi-Environment Token Extraction
All requests require an active OAuth 2.0 access token issued via `/oauth/token`. Tokens are extracted resiliently via:
1. `Authorization: Bearer {access_token}`
2. Server environment headers (`HTTP_AUTHORIZATION`, `REDIRECT_HTTP_AUTHORIZATION`)
3. Fallback Query Parameter: `?access_token={access_token}` or JSON payload key `access_token`.

### B. Rate Limiting & Error Handling
- Rate limited to 30 requests per minute per IP (`throttle:30,1`).
- Structured JSON error envelopes (`401`, `403`, `422`, `500`) with backend logging in `storage/logs/laravel.log`.

### C. Endpoints Catalog
- **Identity & Profile:**
  - `GET /api/developer/v1/me`: Basic member identity. *(Scope: `user.identity.read`)*
  - `GET /api/developer/v1/me/profile`: Display name, bio, points, avatar. *(Scope: `user.profile.read`)*
  - `GET /api/developer/v1/me/email`: Verified email address. *(Scope: `user.email.read`)*
  - `GET /api/developer/v1/me/social-links`: External social links. *(Scope: `user.social_links.read`)*
  - `GET /api/developer/v1/me/follows`: Followers and following counts and list. *(Scope: `user.follows.read`)*
  - `POST /api/developer/v1/me/follows`: Follow or unfollow member (`{"target_user_id": 123, "action": "follow"}`). *(Scope: `user.follows.write`)*
- **Content & Interaction:**
  - `GET /api/developer/v1/me/content`: Recent authored posts. *(Scope: `user.content.read`)*
  - `POST /api/developer/v1/me/content`: Publish post (`{"text": "...", "title": "...", "privacy": 0}`). Automatically links `ForumTopic` record for integrity. *(Scope: `user.content.write`)*
  - `POST /api/developer/v1/me/reactions`: Add or toggle reaction (`{"status_id": 456, "reaction_name": "like"}`). *(Scope: `user.reactions.write`)*
  - `GET /api/developer/v1/me/messages`: Active conversations. *(Scope: `user.messages.read`)*
  - `POST /api/developer/v1/me/messages`: Send direct message (`{"receiver_id": 789, "content": "..."}`). *(Scope: `user.messages.write`)*
  - `GET /api/developer/v1/me/notifications`: Alerts feed. *(Scope: `user.notifications.read`)*
  - `GET /api/developer/v1/forums`: Categories with counters. *(Scope: `user.forums.read`)*
  - `GET /api/developer/v1/me/clips`: Vertical video clips feed. *(Scope: `user.clips.read`)*
- **Economy, Store & Advertising:**
  - `GET /api/developer/v1/me/wallet`: Points (PTS) wallet details. *(Scope: `user.wallet.read`)*
  - `GET /api/developer/v1/me/badges`: Unlocked badges and progress. *(Scope: `user.badges.read`)*
  - `GET /api/developer/v1/store/products`: Marketplace products catalog. *(Scope: `user.store.read`)*
  - `GET /api/developer/v1/me/orders`: Service order requests and history. *(Scope: `user.orders.read`)*
  - `GET /api/developer/v1/me/ads/stats`: Ad impression and click metrics. *(Scope: `user.ads.read`)*
- **Application Owner Endpoints:**
  - `GET /api/developer/v1/owner/profile`: Owner public profile. *(Scope: `owner.profile.read`)*
  - `GET /api/developer/v1/owner/content`: Owner posts feed. *(Scope: `owner.content.read`)*
  - `POST /api/developer/v1/owner/follow`: Follow owner. *(Scope: `owner.follow.write`)*
  - `POST /api/developer/v1/owner/messages`: Message owner. *(Scope: `owner.messages.write`)*

---

## 6. Embed Widgets Catalog

MYADS provides ready-to-use JavaScript embed widgets. Snippets with your specific `app_id` are available in `/developer/apps/{id}`:
- **Follow Button Widget:** `GET /embed/developer/{app_id}/follow.js`
- **Profile Card Widget:** `GET /embed/developer/{app_id}/profile.js`
- **Content Stream Widget:** `GET /embed/developer/{app_id}/content.js`

### Advertising Embeds
- **Banner Ad Embed:** `GET /embed/banner.js` (or `/bn.php`)
- **Link Ad Embed:** `GET /embed/link.js` (or `/link.php`)
- **Smart Ad Embed:** `GET /embed/smart.js` (or `/smart.php`)
- **Custom Member-to-Member Ad Embed:** `GET /embed/custom.js` (or `/ads/custom/serve`)

---

## 7. External Web Share API (Public)

Allows any external website to pre-fill the MYADS post composer with text and links.

**Endpoint:** `GET /share`  
**Rate Limit:** 15 requests / min / IP  
**Query Parameters:**
- `text`: URL-encoded string for the post content.

**Example:**
```text
https://myads.com/share?text=Check+out+this+awesome+platform!+https://example.com
```

---

## 8. Mobile App API & REST Subsystems (Sanctum Endpoints)

All endpoints in this section are prefixed with `/api/` (unless explicitly noted as web/AJAX routes) and require the global header:
- `X-API-KEY: {YOUR_GLOBAL_KEY}`
- `Authorization: Bearer {token}`

---

### A. Settings & Account Management
All settings mutation endpoints accept `POST`, `PUT`, and `PATCH` HTTP verbs for maximum client compatibility.

- `GET /api/settings/overview`: Retrieve high-level authenticated member overview:
  ```json
  {
      "user": {
          "id": 42,
          "name": "Jane Doe",
          "username": "janedoe",
          "email": "jane@example.com",
          "pts": 2500,
          "avatar": "https://domain.com/upload/avatar.png",
          "is_verified": true
      }
  }
  ```
- `GET /api/settings/profile`: Retrieve editable profile information (`email`, `about_me`, `avatar`).
- `POST|PUT|PATCH /api/settings/profile`: Update user profile details.
  - *Payload:* `{"about_me": "User signature/bio", "email": "optional_email@example.com", "password": "...", "avatar": file, "cover": file}`. The `email` parameter is optional; when omitted, the backend preserves the active email.
- `POST /api/settings/2fa/enable`: Enable Two-Factor Authentication via email confirmation. Returns 8 emergency recovery codes (`recovery_codes: ["CODE1", "CODE2", ...]`).
- `POST /api/settings/2fa/disable`: Disable Two-Factor Authentication.
- `GET /api/settings/privacy`: Retrieve current privacy configuration. Returns full schema strings (`profile_visibility`, `allow_direct_messages`, `allow_mentions`, etc.) and mobile shorthand integers (`visibility`: 0=Public, 1/2=Followers, 3=Private; `dm`: 2=Disabled; `mention`: 2=Disabled).
- `POST|PUT|PATCH /api/settings/privacy`: Update privacy configuration. Accepts canonical schema strings or mobile integer shorthands.
- `GET /api/settings/social`: Retrieve connected social profile URLs. Returns both `links` dictionary map and `socials` list of objects.
- `POST|PUT|PATCH /api/settings/social`: Update social profile URLs. Supports flat platform keys (`{"facebook": "..."}`) or nested `{"socials": {"facebook": "..."}}`.
- `GET /api/settings/notification-preferences` & `GET /api/settings/notifications`: Retrieve push/email notification preferences. Returns nested `settings`, canonical boolean keys, and mobile integer flags (`email_mentions`, `email_messages`, `email_follows`, `email_comments`).
- `POST|PUT|PATCH /api/settings/notification-preferences` & `POST|PUT|PATCH /api/settings/notifications`: Update notification preferences. Accepts mobile plural keys or canonical singular keys.
- `GET /api/settings/sessions`: Retrieve active web sessions and active Sanctum device tokens.
- `POST /api/settings/sessions/{id}/revoke`: Revoke a specific web session by ID.
- `POST /api/settings/tokens/{id}/revoke`: Revoke a specific Sanctum API device token by ID.
- `POST /api/settings/device-token`: Register FCM device token for mobile push notifications (`{"token": "fcm_token_string"}`).
- `GET /api/settings/badges`: Retrieve user earned badges and showcase progress (`earned`, `showcase`, `badges`).
- `POST|PUT|PATCH /api/settings/badges`: Update badge showcase order and display (`{"showcase": [1, 2, 3]}` or `{"badge_ids": [1, 2, 3]}`).
- `GET /api/settings/history`: Retrieve paginated member Points (PTS) transaction ledger history with dynamic timestamp sorting.
- `GET /api/settings/apps`: Retrieve authorized third-party OAuth applications.
- `POST /api/settings/apps/{id}/revoke`: Revoke authorization for a third-party application.
- `GET /api/settings/blocks`: Retrieve list of blocked users.

---

### B. Community Feed, Media Posts & Video Hub

- `GET /api/portal/feed`: Retrieve the community feed (paginated).
  - *Attributes:* `user` (with `profile_badge_color`), `display_content`, `display_title`, `video_title`, `video_thumbnail`, `media`, `gallery`, `attachments`, `repost_record`, `grouped_reactions`, `has_liked`, `user_reaction`, `is_promoted_ad`, `s_type`.
- `GET /api/video/feed`: Retrieve Video Hub content strictly scoped to video items (`whereIn('s_type', [10, 2, 4, 100])` for main videos and `14` for clips).
  - *Parameters:* `filter` (`all`, `trending`, `latest`, `videos`, `clips`), search query `q`, `per_page` (default: 12).
  - *Response:*
    ```json
    {
        "filter": "all",
        "search_query": "",
        "spotlight_video": { ... },
        "clips": [ ... ],
        "videos": [ ... ],
        "meta": { "current_page": 1, "last_page": 5, "total": 60 }
    }
    ```
- `GET /api/statuses/saved`: Retrieve paginated saved/bookmarked posts for the authenticated user.
- `POST /api/statuses/save-toggle` and `POST /api/statuses/{id}/save-toggle`: Toggle bookmarked state for a status.
  - *Response:* `{"success": true, "saved": true, "action": "added", "count": 12, "message": "Post saved successfully"}`
- `GET /api/tags/suggest`: Query hashtag autocomplete suggestions (`?q=tag_keyword`).
- `GET /api/mentions/users`: Query user mention autocomplete suggestions (`?q=username_keyword`). Returns user avatar, username, and name.
- `GET /api/statuses/{id}`: Retrieve detailed post payload. For video posts, includes `suggested_videos` collection, `is_following`, and `is_saved`.
- `GET /api/composer/options`: Retrieve post composer options (`groups`, `directory_categories`, `supported_kinds`).
- `POST /api/statuses/link-preview`: Generate live metadata preview for a target URL (`{"link_url": "..."}`).
- `POST /api/statuses`: Publish a new post (Multipart Form-Data supporting `text`, `post_kind`, `video_title`, `video_thumbnail`, `images[]`, `videos[]`, `audios[]`, `files[]`, `link_url`, `group_id`).
- `POST /api/statuses/{id}/update`: Update an existing status (requires post ownership).
- `DELETE /api/statuses/{id}`: Delete a status (requires post ownership or admin permissions).

---

### C. Comments & Reactions

- `GET /api/statuses/{id}/comments`: Retrieve paginated comments for a status.
- `POST /api/statuses/{id}/comments`: Post a new comment (`{"text": "..."}`).
- `POST /api/reactions/toggle`: Toggle reaction on any supported entity.
  - *Payload:* `{"subject_id": 123, "type": 2, "reaction_name": "love"}`  
  - *Allowed Reactions:* `like`, `love`, `funny`, `wow`, `sad`, `angry`, `care`.

---

### D. Profiles & Social Relationships

- `GET /api/profile/{identifier}`: Fetch member profile details (`identifier` can be `'me'`, username, or `public_uid`).
- `GET /api/profile/{identifier}/statuses`: Fetch user's published statuses.
- `POST /api/profile/{identifier}/follow`: Toggle follow status.
- `POST /api/profile/{identifier}/block`: Block user (`{"block_type": "full_platform|messages_only", "duration": 30}`).
- `DELETE /api/profile/{identifier}/unblock`: Unblock user.

---

### E. Private Messaging

- `GET /api/messages`: List active direct message conversations with latest preview and unread counters.
- `GET /api/messages/updates`: Poll message updates (`?conversation={route_key}&after_id={id}`).
- `GET /api/messages/{identifier}`: Fetch message history with a specific conversation partner.
- `POST /api/messages/{identifier}`: Send a direct message (`{"text": "..."}`).
- `POST /api/messages/{identifier}/read`: Mark unread messages in conversation as read.

---

### F. Notifications & Gamification

- `GET /api/notifications`: Retrieve user notifications (paginated).
- `GET /api/notifications/unread-count`: Get integer count of unread notifications.
- `POST|GET /api/notifications/{id}/read` & `POST|GET /api/notifications/{id}/mark-read`: Mark specific notification as read.
- `POST|GET /api/notifications/read-all` & `POST|GET /api/notifications/mark-all-read`: Mark all notifications as read.
- `GET /api/wallet/balance`: Get current Points (PTS) balance and credit balances.
- `GET /api/quests` (and `/api/gamification/quests`): Retrieve active gamification quests.
  - *Returns:*
    ```json
    {
        "success": true,
        "user_pts": 1500,
        "daily_quests": [ ... ],
        "weekly_quests": [ ... ],
        "data": {
            "user_pts": 1500,
            "daily_quests": [ ... ],
            "weekly_quests": [ ... ],
            "quests": [ ... ]
        }
    }
    ```
  - *Quest Fields:* `id`, `title`, `name`, `description`, `period`, `reward` (`reward_pts`), `goal` (`target_value`), `progress` (`current_value`), `completed` (`is_completed`), `claimed` (`is_claimed`).
- `POST /api/quests/{id}/claim`: Claim quest completion points and credit PTS balance.
- `POST /api/pts/transfer`: Transfer PTS to another member (`{"username": "recipient", "amount": 100}`).
- `POST /api/pts/vouchers/create`: Create a PTS voucher code (`{"amount": 50}`). Returns 10-character code.
- `POST /api/pts/vouchers/claim`: Redeem a PTS voucher code (`{"code": "ABC123XYZ4"}`).

---

### G. Store Marketplace, Ratings & Reviews Engine (v4.6.3)

MYADS v4.6.3 provides an overhauled digital goods marketplace supporting 5-star customer ratings, verified buyer badges, screenshot gallery lightboxes, live demo previews, video embeds, and native Arabic & Unicode slug URL routing.

#### 1. Store Catalog & Details API
- `GET /api/store/products`: Browse products with multi-criteria filtering and sorting:
  - **Query Parameters:**
    - `category`: Filter by category slug or name (`all` for all categories).
    - `q` or `search`: Search keyword across product name and description.
    - `sort`: `latest` (default), `price_asc`, `price_desc`, `free`, `paid`.
    - `per_page`: Products per page (default: 20, max: 100).
- `GET /api/store/products/{id}`: Detailed product payload with seller info and media gallery:
  ```json
  {
      "id": 12,
      "title": "قالب الإعلانات الاحترافي",
      "description": "قالب متكامل ومميز...",
      "price": 100,
      "original_price": 100,
      "sale_price": 80,
      "current_price": 80,
      "is_on_sale": true,
      "sales": 45,
      "downloads": 45,
      "downloads_count": 45,
      "is_pending": false,
      "moderation_status": "approved",
      "thumbnail": "upload/store/thumb.jpg",
      "rating": 4.8,
      "average_rating": 4.8,
      "reviews_count": 15,
      "live_demo_url": "https://demo.example.com",
      "video_preview_url": "https://youtube.com/watch?v=...",
      "screenshots": [
          {
              "id": 101,
              "url": "upload/screenshots/ss_1.jpg",
              "full_url": "https://domain.com/upload/screenshots/ss_1.jpg",
              "caption": "لوحة التحكم الرئيسية"
          }
      ],
      "seller": {
          "id": 7,
          "username": "ahmed",
          "name": "أحمد",
          "avatar": "https://domain.com/upload/avatar.png"
      },
      "category_id": 3,
      "created_at": "2026-10-01T12:00:00Z"
  }
  ```
- `GET /api/store/products/{id}/knowledgebase`: Get product-associated documentation articles.

#### 2. Customer Reviews & Ratings Engine (Web & AJAX Endpoints)
- `POST /store/{id}/reviews`: Submit or update a 5-star review:
  - **Headers:** `X-CSRF-TOKEN: {token}` or `Authorization: Bearer {token}`, `Accept: application/json`
  - **Payload:**
    ```json
    {
        "rating": 5,
        "title": "منتج ممتاز ودعم رائع",
        "comment": "تم تثبيت القالب ويعمل بسرعة فائقة وبدون أي أخطاء."
    }
    ```
  - **Verified Buyer Badge:** If the member holds a license in `product_licenses`, the backend automatically marks `is_verified_buyer: true`.
  - **Response (HTTP 200):**
    ```json
    {
        "success": true,
        "message": "تم إرسال التقييم بنجاح",
        "review": {
            "id": 31,
            "rating": 5,
            "title": "منتج ممتاز ودعم رائع",
            "comment": "تم تثبيت القالب...",
            "is_verified_buyer": true,
            "created_at": "منذ دقيقة",
            "user": { "id": 42, "username": "developer", "avatar": "..." }
        },
        "average_rating": 4.9,
        "reviews_count": 16,
        "rating_breakdown": {
            "5": 90,
            "4": 10,
            "3": 0,
            "2": 0,
            "1": 0
        }
    }
    ```
- `DELETE /store/reviews/{id}`: Delete a customer review (allowed for review author, product owner, or administrator).

#### 3. Product Media & Previews Management
- `POST /store/upload-screenshot`: Upload a screenshot image asset via AJAX (Multipart Form-Data, max 10MB).
  - *Response:* `{"success": true, "url": "upload/screenshots/ss_....jpg", "full_url": "..."}`
- `POST /store/{name}/media`: Attach media item to a product:
  - *Payload:* `{"media_type": "screenshot|video|demo_url", "url": "...", "caption": "...", "sort_order": 0}`
- `DELETE /store/{name}/media/{id}`: Remove an attached media asset.

#### 4. Instant Purchase & License Generation
- `POST /store/{id}/purchase`: Instant product checkout with Points (PTS):
  - *Payload:* `{"code": "OPTIONAL_DISCOUNT_COUPON"}`
  - *Process:* Verifies point balance, applies discount percentage/fixed reduction, transfers PTS to seller, increments sales counter, generates a unique license key (`ADSTN-XXXX-XXXX-XXXX`), and returns the secure download URL.
  - *Response:*
    ```json
    {
        "success": true,
        "message": "تم الشراء بنجاح!",
        "download_url": "https://domain.com/download/a1b2c3d4"
    }
    ```
- `POST /store/discounts/validate`: Check validity of a discount code before checkout (`{"code": "SAVE20", "product_id": 12}`).
- `GET /download/{hash}`: Authenticated file download streaming based on verified license or ownership.

---

### H. Service Orders Marketplace (Peer-to-Peer Bulletin Overhaul — v4.6.3)

The Service Orders engine at `/orders` and `/api/orders` provides end-to-end contract progression for freelance requests, custom software development, and design services based on a direct peer-to-peer bulletin model with explicit non-escrow disclaimers.

#### Workflow Milestones Stepper
$$\text{Open} \longrightarrow \text{Awarded} \longrightarrow \text{In Progress} \longrightarrow \text{Delivered} \longrightarrow \text{Completed}$$
*(Alternative branches: `Cancelled` or `Revision Requested` reverting from Delivered back to In Progress).*

#### 1. Browse & Search Orders
- `GET /api/orders`:
  - **Query Parameters:**
    - `search`: Keyword search across request title and description.
    - `category`: Filter by service category.
    - `status`: `all` (default), `open`, `under_review`, `awarded`, `in_progress`, `delivered`, `completed`, `cancelled`.
    - `sort`: `newest` (default), `active` (last activity), `popular` (offers count), `budget_high`, `budget_low`.
  - **Item Payload Attributes:** Includes `buyer` object, `budget_min`, `budget_max`, `currency`, `offers_count`, `max_delivery_days`, `has_attachment`, `is_revision_requested`.

#### 2. Order Details & Full Contract State
- `GET /api/orders/{id}`:
  - Returns complete contract payload including:
    - `buyer`: `{ "id": 10, "name": "...", "username": "...", "avatar": "..." }`
    - `has_attachment`: boolean indicating presence of client requirement files (PDF, DOCX, ZIP up to 25MB).
    - `attachment_download_url`: secure link (`/orders/{id}/attachment`).
    - `offers`: list of submitted proposals with `provider`, `price`, `delivery_days`, and proposal message (`txt` / `content`).
    - `contract`: milestone stepper state:
      - `workflow_status`: current contract state.
      - `deadline`: calculated ISO 8601 deadline timestamp based on agreement start date and agreed delivery days.
      - `is_overdue`: boolean flag indicating overdue status.
      - `revision_count`: total iterations of requested revisions.
      - `revision_note`: latest client revision feedback notes.
      - `has_delivery_attachment`: boolean indicating deliverable files.
      - `delivery_attachment_name`: original filename of deliverable.
    - `viewer_offer`: current authenticated member's proposal if already submitted.

#### 3. Submitting Proposals & Offers
- `POST /api/orders/{id}/offers`:
  - **Headers:** `Authorization: Bearer {token}`, `Content-Type: application/json`
  - **Payload Parameters:**
    | Parameter | Type | Required | Description |
    |---|---|---|---|
    | `content` / `txt` | string | **Yes** | Proposal description and deliverables explanation (max 5000 chars). Accepts either `content` or `txt`. |
    | `price` | numeric | Optional | Quoted amount for the project. |
    | `currency` | string | Optional | Currency symbol or ISO code (e.g. `USD`, `PTS`). |
    | `delivery_days` | integer | Optional | Agreed execution duration in days (1–365). |
  - **Response (HTTP 200):**
    ```json
    {
        "success": true,
        "message": "تم تقديم العرض بنجاح",
        "data": { "id": 88, "quoted_amount": 150, "delivery_days": 5 }
    }
    ```

#### 4. Awarding & Contracting Lifecycle
- `POST /api/orders/{id}/award`: Client awards contract to a specific provider proposal (`{"offer_id": 88}`). Transitions order to `awarded`.
- `POST /api/orders/{id}/start`: Provider commences work. Transitions order to `in_progress` and starts the deadline timer.
- `POST /api/orders/{id}/deliver`: Provider submits completed deliverables:
  - **Request Type:** `multipart/form-data`
  - **Payload:**
    - `delivery_note` (string, optional): Delivery summary notes (max 5000 chars).
    - `delivery_attachment` (file, optional): Deliverable package (ZIP, RAR, PDF, images up to **25 MB**).
  - Transitions order to `delivered`.
- `POST /api/orders/{id}/revision`: Client requests contract revisions:
  - **Payload:** `{"revision_note": "يرجى تعديل ألوان الواجهة وإصلاح استجابة الهاتف..."}`
  - Automatically reverts status back to `in_progress`, increments `revision_count`, and notifies the provider.
- `POST /api/orders/{id}/complete`: Client accepts final delivery and completes the contract:
  - **Payload:** `{"rating": 5, "review": "عمل متقن وتسليم في الموعد المحدد."}` (rating: 1 to 5).
  - Transitions order to `completed`.
- `POST /api/orders/{id}/cancel`: Cancel contract with reason note (`{"note": "تم الاتفاق على الإلغاء بالتراضي."}`).

#### 5. Protected File Downloads
- `GET /orders/{order}/attachment`: Download project specifications attachment provided by client during order creation.
- `GET /orders/{order}/deliverable`: Download completed deliverable uploaded by provider. Strictly restricted to contract participants (client, provider, or administrator).

---

### I. Clips System (Shorts)

- `GET /api/clips`: Retrieve vertical short video clips feed.
- `GET /api/clips/saved`: Retrieve user's saved clips list.
- `POST /api/clips/{id}/save`: Save a clip.
- `DELETE /api/clips/{id}/save`: Unsave a clip.

---

### J. Forums API (Mobile Specific)

- `GET /api/forums/categories`: Get forum categories with topic counts.
- `GET /api/forums/categories/{categoryId}/topics`: Get topics within a category.
- `POST /api/forums/categories/{categoryId}/topics`: Create a new forum topic.
- `GET /api/forums/topics/{topicId}`: Get topic details and replies.
- `POST /api/forums/topics/{topicId}/replies`: Post a reply to a forum topic.

---

### K. Live Search & Advertising Intelligence

- `GET /api/search/live`: Unified real-time search across 4 platform entities:
  - **Rate Limit:** 40 req / min / IP (`throttle:40,1`)
  - **Query Parameters:** `q` (minimum 2 characters).
  - **Entities Searched:**
    1. Members (`type: "user"`): Matches usernames, returns `id` (or public UID), `identifier`, `title`, `img`, and `@username`.
    2. Store Products (`type: "product"`): Boolean full-text search against product names and descriptions.
    3. Forum Topics (`type: "forum"`): Boolean full-text search against topic titles and content.
    4. Community Posts (`type: "post"`): Search across public text posts.
  - **Response:**
    ```json
    {
        "success": true,
        "data": [
            {
                "type": "user",
                "id": 42,
                "identifier": "developer",
                "title": "developer",
                "img": "https://domain.com/upload/avatar.png",
                "subtitle": "@developer"
            },
            {
                "type": "product",
                "id": 12,
                "title": "قالب الإعلانات الاحترافي",
                "img": "https://domain.com/upload/store/thumb.jpg",
                "subtitle": "قالب متكامل ومميز..."
            }
        ]
    }
    ```
- `GET /api/ads/stats`: Retrieve member advertising performance metrics:
  - *Response:*
    ```json
    {
        "success": true,
        "data": {
            "visits": { "today": 14, "total": 240 },
            "ads": { "banner_impressions": 1250, "smart_impressions": 840 },
            "wallet": { "pts": 3500 }
        }
    }
    ```

---

### L. Software Licensing & Extension Marketplace Feed API

Provides automated software licensing verification and update feeds for extensions, WordPress plugins, and CMS themes.

#### 1. Software License Verification
- **Endpoint:** `POST /api/license/verify`
- **Rate Limit:** 10 requests / min / IP (`throttle:10,1`)
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Payload:**
  ```json
  {
      "license_key": "ADSTN-XXXX-XXXX-XXXX",
      "domain": "client-site.com",
      "plugin": "adstn-auto-poster"
  }
  ```
- **Validation Rules:**
  - Verifies existence of the product in the store matching `plugin` slug.
  - Verifies existence and validity of `license_key` in `product_licenses`.
  - Normalizes target domain (strips `https://`, `http://`, `www.`, and trailing slashes).
  - If license domain is empty, automatically binds and activates domain on first verification (`activated_at = now()`).
  - If license is already registered to a different domain, returns HTTP `400 Bad Request`.
- **Success Response (HTTP 200):**
  ```json
  {
      "success": true,
      "message": "License successfully verified and activated.",
      "license_key": "ADSTN-XXXX-XXXX-XXXX",
      "domain": "client-site.com"
  }
  ```

#### 2. Extensions Feed & Automatic Update Checker
- `GET|POST /api/marketplace/extensions/plugins`:
  - **Feed Mode (No query params):** Returns JSON catalog of available plugins for external CMS marketplaces.
  - **Update Check Mode (`slug` and `version` provided):**
    - Parameters: `slug`, `version`, `license_key` (required for paid items), `domain`.
    - If product is paid, validates license key and checks domain activation.
    - Locates latest release file in `options` (`o_type = 'store_file'`).
    - *Response:*
      ```json
      {
          "success": true,
          "version": "1.2.0",
          "download_url": "https://domain.com/api/marketplace/extensions/download?slug=...&license_key=...&domain=...",
          "changelog": "Added Arabic slugs and reviews support."
      }
      ```
- `GET /api/marketplace/extensions/themes`: Returns marketplace themes catalog.
- `GET /api/marketplace/extensions/download`: Authenticated extension package download stream. Validates license key and domain binding before streaming ZIP package.

---

### M. Unified Comments & Media Attachment API

Unified AJAX/REST endpoints managing contextual discussions across all platform entities (Forum Topics, Directory Listings, Store Products, Knowledgebase Articles, and Service Orders):

- `POST /comment/store`: Post a comment with optional inline image attachment.
  - **Headers:** `X-CSRF-TOKEN: {token}` (Web) or `Authorization: Bearer {token}` (Mobile), `Accept: application/json`
  - **Request Type:** `multipart/form-data`
  - **Payload Parameters:**
    | Parameter | Type | Required | Description |
    |---|---|---|---|
    | `id` | integer | **Yes** | Target entity ID (e.g. topic ID, product ID, order ID). |
    | `type` | string | **Yes** | Entity domain (`forum`, `directory`, `store`, `knowledgebase`, `order`). |
    | `comment` | string | Conditional | Comment text. Optional if an `attachment` is provided. |
    | `attachment` | file | Optional | Image attachment (`.jpg`, `.jpeg`, `.png`, `.gif`, `.webp`, `.bmp`). Max size: **5 MB**. |
  - **Security & Media Pipeline:**
    - Real binary signature (Magic Bytes) inspection via `FileUploadSecurityService`.
    - Auto-conversion to compressed `WebP` (85% quality, alpha channel preserved).
    - Auto-embedding as Markdown image `![image](upload/comments/comment-...)` directly into the comment text for universal backward compatibility.
  - **Response (HTTP 200):**
    ```json
    {
        "status": "success",
        "html": "<div class=\"... comment...\">...</div>",
        "comment_id": 142,
        "media_url": "https://domain.com/upload/comments/comment-142.webp"
    }
    ```
- `POST /comment/delete`: Remove an existing comment (`{"trashid": 142, "type": "forum"}`).
- `POST /reaction/toggle`: Toggle emoji reaction on a post or comment (`{"id": 142, "type": "forum_comment", "reaction": "like"}`).

---

### N. Smart Partitioned XML Sitemaps

MYADS v4.6.3 provides scalable, partitioned XML Sitemaps compliant with Google Sitemaps Protocol 0.9 and Schema.org standards:

| Endpoint | Content | Cache Strategy |
|---|---|---|
| `GET /sitemap.xml` | Master Sitemap Index referencing all partition endpoints | Dynamic Cache (1 hr) + ETag |
| `GET /sitemap/pages.xml` | Core static & CMS landing pages | Dynamic Cache (24 hrs) + ETag |
| `GET /sitemap/topics.xml` | Public forum discussion topics | Dynamic Cache (1 hr) + ETag |
| `GET /sitemap/products.xml` | Marketplace store listings & products | Dynamic Cache (2 hrs) + ETag |
| `GET /sitemap/directory.xml` | Web directory links and categories | Dynamic Cache (6 hrs) + ETag |
| `GET /sitemap/knowledgebase.xml` | Help center and documentation articles | Dynamic Cache (12 hrs) + ETag |

- **HTTP 304 Conditional Support:** Endpoints calculate an `ETag` and check incoming `If-None-Match` request headers. If content has not changed, the server returns an immediate `HTTP 304 Not Modified` without transferring payload bytes.
- **Content-Type:** `application/xml; charset=utf-8` with XML declaration and standard `<urlset>` / `<sitemapindex>` roots.

---

## 9. Standard Response Envelopes & Error Codes

### Success Response Envelope (HTTP 200 / 201)
```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}
```

### Error Response Envelope (HTTP 4xx / 5xx)
```json
{
    "success": false,
    "message": "Validation failed / Unauthorized access.",
    "errors": {
        "field_name": [
            "Detailed error description."
        ]
    }
}
```

### Common HTTP Status Codes
| Code | Meaning | Typical Scenario |
|:---:|---|---|
| `200` | OK | Request succeeded |
| `201` | Created | Resource successfully created |
| `304` | Not Modified | Conditional ETag match (sitemaps, static assets) |
| `401` | Unauthorized | Missing or invalid API key / Bearer token |
| `403` | Forbidden | Insufficient OAuth scope or access permissions |
| `404` | Not Found | Target resource, post, or member does not exist |
| `422` | Unprocessable Entity | Form validation error (payload details in `errors`) |
| `429` | Too Many Requests | Rate limit threshold exceeded |
| `500` | Server Error | Internal server exception (masked for security) |
