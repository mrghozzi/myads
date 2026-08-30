# MYADS v4.5.5 REST & Real-Time API Documentation
> **Specification Version:** `v4.5.5` (Stable / Maintenance Release)  
> **Target Framework:** Laravel 12 (PHP 8.2+)  
> **Authentication Engines:** Laravel Sanctum (Mobile & Web API), OAuth 2.0 (Developer Platform), and Server-Sent Events (SSE Live Stream).  
> **Last Updated:** August 2026  

---

## 1. Overview & Architecture

The MYADS v4.5.5 API ecosystem delivers high-performance, secure, and extensible interfaces connecting web clients, companion mobile applications (Flutter), and third-party developer integrations.

### Primary API Subsystems
1. **Internal Mobile & Web API (`/api/*`):** Powered by Laravel Sanctum for mobile app companion clients and web AJAX workflows.
2. **Real-Time Events Engine (`/live/stream` & `/api/live/stream` — RT-04):** Zero-overhead Server-Sent Events (SSE) streaming engine delivering instant unread counters (synchronized with `MessageConversationService`), live toasts, and feed updates.
3. **Developer Platform & OAuth 2.0 (`/oauth/*` & `/api/developer/v1/*`):** 27 granular permissions for external applications registered at `/developer`.
4. **Ad Serving & Exchange Engine (`/ads/*`):** High-throughput ad delivery, anti-click-farm validation, and conversion tracking.

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
      "success": true,
      "message": "Login successful",
      "token": "1|sanctum_token_string...",
      "user": { ... }
  }
  ```

### B. Rate Limiting Rules
Public and authenticated API endpoints are protected with sliding-window rate limiters:

| Endpoint Group | Rate Limit | Protection Target |
|---|---|---|
| `POST /api/login` | 5 req / min / IP | Brute-force credential stuffing |
| `POST /api/register` | 3 req / min / IP | Automated spam registration |
| `POST /api/license/verify` | 10 req / min / IP | License enumeration |
| `GET/POST /api/developer/v1/*` | 30 req / min / IP | Developer API scraping & abuse |
| `GET /api/live/stream` | 1 connection / user | Persistent streaming session |

*When rate limits are exceeded, the server returns HTTP `429 Too Many Requests`.*

### C. Dynamic Localization (`Accept-Language`)
All API responses, notification payloads, and validation error messages support dynamic localization:
- **Header:** `Accept-Language: ar` (or `en`, `fr`, `es`, `de`, `tr`, etc.)
- **Default:** Site default locale (`ar` or `en`).

### D. Privacy & Member ID Obfuscation
When `public_member_ids_enabled` is active in Admin Security Settings, all API resources (`UserResource`, `UserProfileResource`, `StatusResource`) automatically serve randomized public identifiers (`public_uid`) or usernames in place of numeric database IDs (`users.id`), neutralizing member enumeration attacks.

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
    "server_time": "2026-08-24T01:13:20+00:00"
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

---

## 5. Developer API v1 Endpoints

All Developer API requests require the header: `Authorization: Bearer {access_token}`.  
**Rate Limit:** 30 requests per minute per IP.

### User Identity & Profile
- `GET /api/developer/v1/me`: Returns member ID, username, and identity metadata (`user.identity.read`).
- `GET /api/developer/v1/me/profile`: Returns full profile details, avatar URL, and points (`user.profile.read`).
- `GET /api/developer/v1/me/email`: Returns verified email address (`user.email.read`).
- `GET /api/developer/v1/me/social-links`: Returns configured social media links (`user.social_links.read`).
- `GET /api/developer/v1/me/follows`: Returns following and followers counts (`user.follows.read`).
- `POST /api/developer/v1/me/follows`: Follow or unfollow target member (`target_user_id`, `action`) (`user.follows.write`).

### Content, Community & Messages
- `GET /api/developer/v1/me/content`: Returns recent posts authored by the user (`user.content.read`).
- `POST /api/developer/v1/me/content`: Publishes a new status post (`content`, `privacy`) (`user.content.write`).
- `POST /api/developer/v1/me/reactions`: Toggles reaction/like on a post (`status_id`) (`user.reactions.write`).
- `GET /api/developer/v1/me/messages`: Returns user's private message conversations (`user.messages.read`).
- `POST /api/developer/v1/me/messages`: Sends a private message (`receiver_id`, `content`) (`user.messages.write`).
- `GET /api/developer/v1/me/notifications`: Returns notifications list and unread count (`user.notifications.read`).
- `GET /api/developer/v1/forums`: Returns list of forum categories with topic counts (`user.forums.read`).
- `GET /api/developer/v1/me/clips`: Returns public video clips feed (`user.clips.read`).

### Economy, Store & Advertising
- `GET /api/developer/v1/me/wallet`: Returns user points (PTS) wallet balance (`user.wallet.read`).
- `GET /api/developer/v1/me/badges`: Returns earned badges and achievements (`user.badges.read`).
- `GET /api/developer/v1/store/products`: Returns marketplace product listings (`user.store.read`).
- `GET /api/developer/v1/me/orders`: Returns service orders history (`user.orders.read`).
- `GET /api/developer/v1/me/ads/stats`: Returns banner and custom ad performance metrics (`user.ads.read`).

### Application Owner Endpoints
- `GET /api/developer/v1/owner/profile`: Get app owner's public profile (`owner.profile.read`).
- `GET /api/developer/v1/owner/content`: Get app owner's published updates (`owner.content.read`).
- `POST /api/developer/v1/owner/follow`: Follow the app owner (`owner.follow.write`).
- `POST /api/developer/v1/owner/messages`: Send a private message to the app owner (`owner.messages.write`).

---

## 6. Embed Widgets Catalog

MYADS provides ready-to-use JavaScript embed widgets. Snippets with your specific `app_id` are available in `/developer/apps/{id}`:
- **Follow Button Widget:** `GET /embed/developer/{app_id}/follow.js`
- **Profile Card Widget:** `GET /embed/developer/{app_id}/profile.js`
- **Content Stream Widget:** `GET /embed/developer/{app_id}/content.js`

---

## 7. External Web Share API (Public)

Allows any external website to pre-fill the MYADS post composer with text and links.

**Endpoint:** `GET /share`  
**Query Parameters:**
- `text`: URL-encoded string for the post content.

**Example:**
```text
https://myads.com/share?text=Check+out+this+awesome+platform!+https://example.com
```

---

## 8. Mobile App API (Sanctum Endpoints)

### A. Settings & Account Management
- `GET /api/settings/profile`: Retrieve user's editable profile information.
- `POST /api/settings/profile`: Update user profile details.
- `GET /api/settings/privacy`: Retrieve current privacy settings.
- `PATCH /api/settings/privacy`: Update privacy configuration.
- `GET /api/settings/social`: Retrieve connected social profile URLs.
- `PATCH /api/settings/social`: Update social profile URLs.
- `GET /api/settings/notification-preferences`: Retrieve push/email notification toggles.
- `PATCH /api/settings/notification-preferences`: Update notification toggles.
- `GET /api/settings/sessions`: Retrieve active web sessions and active Sanctum device tokens.
- `POST /api/settings/sessions/{id}/revoke`: Revoke a specific web session by ID.
- `POST /api/settings/tokens/{id}/revoke`: Revoke a specific Sanctum API device token by ID.
- `POST /api/settings/device-token`: Register FCM device token for mobile push notifications.
- `GET /api/settings/badges`: Retrieve user earned badges and progress.
- `GET /api/settings/history`: Retrieve account activity and login history.
- `GET /api/settings/blocks`: Retrieve list of blocked users.

### B. Community Feed & Multimedia Posts
- `GET /api/portal/feed`: Retrieve the community feed (paginated).
  - *Attributes:* `user` (with `profile_badge_color`), `display_content`, `display_title`, `video_title`, `video_thumbnail`, `media`, `gallery`, `attachments`, `repost_record`, `grouped_reactions`, `has_liked`, `user_reaction`, `is_promoted_ad`, `s_type`.
- `GET /api/video/feed`: Retrieve Video Hub content strictly scoped to video items (`whereIn('s_type', [10, 2, 4, 100])` for main videos and `14` for clips). Supports `filter` (`all`, `videos`, `clips`, `trending`, `latest`), search `q`, and pagination.
- `GET /api/statuses/{id}`: Retrieve detailed post payload. For video posts, includes `suggested_videos` collection, `is_following`, and `is_saved`.
- `GET /api/composer/options`: Retrieve post composer options (`groups`, `directory_categories`, `supported_kinds`).
- `POST /api/statuses/link-preview`: Generate live metadata preview for a target URL (`{"link_url": "..."}`).
- `POST /api/statuses`: Publish a new post (Multipart Form-Data supporting `text`, `post_kind`, `video_title`, `video_thumbnail`, `images[]`, `videos[]`, `audios[]`, `files[]`, `link_url`, `group_id`).
- `POST /api/statuses/{id}/update`: Update an existing status (requires post ownership).
- `DELETE /api/statuses/{id}`: Delete a status (requires post ownership or admin permissions).

### C. Comments & Reactions
- `GET /api/statuses/{id}/comments`: Retrieve paginated comments for a status.
- `POST /api/statuses/{id}/comments`: Post a new comment (`{"text": "..."}`).
- `POST /api/reactions/toggle`: Toggle reaction on any supported entity.
  - *Payload:* `{"subject_id": 123, "type": 2, "reaction_name": "love"}`  
  - *Allowed Reactions:* `like`, `love`, `funny`, `wow`, `sad`, `angry`, `care`.

### D. Profiles & Social Relationships
- `GET /api/profile/{identifier}`: Fetch member profile details (`identifier` can be `'me'`, username, or `public_uid`).
- `GET /api/profile/{identifier}/statuses`: Fetch user's published statuses.
- `POST /api/profile/{identifier}/follow`: Toggle follow status.
- `POST /api/profile/{identifier}/block`: Block user (`{"block_type": "full_platform|messages_only", "duration": 30}`).
- `DELETE /api/profile/{identifier}/unblock`: Unblock user.

### E. Private Messaging
- `GET /api/messages`: List active direct message conversations with latest preview and unread counters.
- `GET /api/messages/updates`: Poll message updates (`?conversation={route_key}&after_id={id}`).
- `GET /api/messages/{identifier}`: Fetch message history with a specific conversation partner.
- `POST /api/messages/{identifier}`: Send a direct message (`{"text": "..."}`).
- `POST /api/messages/{identifier}/read`: Mark unread messages in conversation as read.

### F. Notifications & Gamification
- `GET /api/notifications`: Retrieve user notifications (paginated).
- `GET /api/notifications/unread-count`: Get integer count of unread notifications.
- `POST /api/notifications/{id}/read`: Mark specific notification as read.
- `POST /api/notifications/read-all`: Mark all notifications as read.
- `GET /api/wallet/balance`: Get current Points (PTS) balance and credit balances.
- `GET /api/quests`: Retrieve active gamification quests.
- `POST /api/quests/{id}/claim`: Claim quest completion points.
- `POST /api/pts/transfer`: Transfer PTS to another member.
- `POST /api/pts/vouchers/create`: Create a PTS voucher code.
- `POST /api/pts/vouchers/claim`: Redeem a PTS voucher code.

### G. Forum & Marketplace Store
- `GET /api/forums/categories`: Get forum categories with topic counts.
- `GET /api/forums/categories/{id}/topics`: Get topics within a category.
- `POST /api/forums/categories/{id}/topics`: Create a new forum topic.
- `GET /api/forums/topics/{id}`: Get topic details and replies.
- `POST /api/forums/topics/{id}/replies`: Post a reply to a forum topic.
- `GET /api/store/products`: Browse marketplace products ordered chronologically by status promotion date, modification date, and ID.
- `GET /api/store/products/{id}`: Get product details with original/sale pricing.
- `GET /api/store/products/{id}/knowledgebase`: Get product knowledgebase articles.
- `GET /api/orders`: Browse service requests.
- `POST /api/orders/{id}/offers`: Submit an offer on a service request.

### H. Clips System
- `GET /api/clips`: Retrieve vertical short video clips feed.
- `GET /api/clips/saved`: Retrieve user's saved clips list.
- `POST /api/clips/{id}/save`: Save a clip.
- `DELETE /api/clips/{id}/save`: Unsave a clip.

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
| `401` | Unauthorized | Missing or invalid API key / Bearer token |
| `403` | Forbidden | Insufficient OAuth scope or access permissions |
| `404` | Not Found | Target resource, post, or member does not exist |
| `422` | Unprocessable Entity | Form validation error (payload details in `errors`) |
| `429` | Too Many Requests | Rate limit threshold exceeded |
| `500` | Server Error | Internal error (masked for security) |
