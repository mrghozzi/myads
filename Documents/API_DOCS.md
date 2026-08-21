# API Documentation

> **Note:** The MYADS v4.5.3 REST API is currently in **Beta**. It supports Internal Authentication (Sanctum), Mobile API tokens, and Third-Party Authorization (OAuth2).

The REST API allows external applications and third-party developers to interface with the MYADS core, authenticate users, retrieve content, and perform actions on behalf of users.

---

## 1. Authentication & Authorization

MYADS supports two methods of authentication depending on the use case:

### A. Internal API Tokens (Sanctum)
Best for first-party companion apps (e.g., mobile apps). Users provide credentials directly to the app.

**Security Requirements:**
All mobile API requests require a two-layer authentication:
1. `X-API-KEY` header: A global API key managed by the admin to prevent unauthorized client access. **Must** be sent as an HTTP header; query parameter `?api_key=` is **not** accepted (to prevent leakage via logs, referrer, and browser history).
2. `Authorization`: The user's Sanctum Bearer Token for identity.

**Login Endpoint:** `POST /api/login`  
**Payload:** `{"login": "username_or_email", "password": "..."}`  
**Header (Login):** `X-API-KEY: {YOUR_GLOBAL_API_KEY}`  
**Header (Subsequent Requests):** `Authorization: Bearer {token}` along with `X-API-KEY: {YOUR_GLOBAL_API_KEY}`. *(Note: You may also send `X-Authorization: Bearer {token}` as a fallback for shared hosts that strip the standard Authorization header).*

### Rate Limiting
Public API endpoints are rate-limited per IP to prevent brute-force attacks:
| Endpoint | Limit |
|----------|-------|
| `POST /api/login` | 5 requests / minute |
| `POST /api/register` | 3 requests / minute |
| `POST /api/license/verify` | 10 requests / minute |
| `GET/POST /api/developer/v1/*` | 30 requests / minute |

When rate-limited, the server responds with HTTP `429 Too Many Requests`.

### API Localization (Accept-Language)
The MYADS Mobile API supports dynamic localization for validation errors and API responses (such as notification text). To receive localized responses, the client must send the `Accept-Language` header.
**Supported Languages:** `en`, `ar` (more can be configured).
**Example Header:** `Accept-Language: ar`

### B. OAuth2 Authorization (Third-Party)
Best for third-party websites and integrations. Users authorize your app via the MYADS OAuth screen without sharing their password.

**Authorization URL:** `/oauth/authorize`  
**Token URL:** `/oauth/token`

---

## 4. Developer Platform & OAuth2 Scopes

To build third-party integrations, you must first register your application in the **Developer Platform** at `/developer`.

### Available OAuth2 Scopes Catalog

| Category | Scope ID | Description | Sensitive |
|---|---|---|---|
| **Identity & Profile** | `user.identity.read` | Read member account identifier and basic public identity fields. | No |
| | `user.profile.read` | Read public profile details and core member metadata. | No |
| | `user.email.read` | Access the primary verified email address of the member. | **Yes** |
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
| **Wallet & Rewards** | `user.wallet.read` | Read user points balance, rewards, and wallet details. | **Yes** |
| | `user.badges.read` | Read member badges, unlocked achievements, and quest status. | No |
| **Community & Media** | `user.clips.read` | Browse short video clips feed and saved clips in user account. | No |
| | `user.clips.write` | Save and unsave short video clips on behalf of the user. | No |
| | `user.forums.read` | Read forum categories, topics, discussions, and replies. | No |
| | `user.forums.write` | Create new topics and post replies in forums on behalf of the user. | **Yes** |
| **Store & Advertising** | `user.store.read` | Browse marketplace products, offerings, and store knowledgebase. | No |
| | `user.orders.read` | Read user purchase orders history and submitted offers. | **Yes** |
| | `user.ads.read` | Read ad impression counts, clicks, and campaign performance statistics. | No |
| **App Owner Integrations** | `owner.profile.read` | Read authorized owner profile through developer API. | No |
| | `owner.content.read` | Read authorized owner content feed and published updates. | No |
| | `owner.follow.write` | Follow or unfollow members on behalf of authorized owner. | **Yes** |
| | `owner.messages.read` | Read private message conversations belonging to authorized owner. | **Yes** |
| | `owner.messages.write` | Send private messages on behalf of authorized owner. | **Yes** |

---

## 3. OAuth2 Flow

### Step 1: Request Authorization
Redirect the user to the following URL:
```text
GET /oauth/authorize?
    client_id={CLIENT_ID}&
    redirect_uri={REDIRECT_URI}&
    response_type=code&
    scope=user.identity.read%20user.profile.read&
    state={RANDOM_STATE}
```

### Step 2: Exchange Code for Token
After the user approves, they will be redirected to your `redirect_uri` with a `code`. Exchange it via a server-to-server request:
```http
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "authorization_code",
    "client_id": "{CLIENT_ID}",
    "client_secret": "{CLIENT_SECRET}",
    "redirect_uri": "{REDIRECT_URI}",
    "code": "{CODE}"
}
```

**Response:**
```json
{
    "access_token": "...",
    "refresh_token": "...",
    "expires_in": 3600,
    "token_type": "Bearer"
}
```

---

## 4. Developer API v1

All Developer API requests require an `Authorization: Bearer {access_token}` header.  
**Rate Limit:** 30 requests per minute per IP.

### User Identity & Profile Endpoints
- `GET /api/developer/v1/me`: Returns basic identity (ID, Username, Email).  
  *Scope: `user.identity.read`*
- `GET /api/developer/v1/me/profile`: Returns profile details (Avatar, Points).  
  *Scope: `user.profile.read`*
- `GET /api/developer/v1/me/email`: Returns verified email address and verification status.  
  *Scope: `user.email.read`*
- `GET /api/developer/v1/me/social-links`: Returns user's configured social media links.  
  *Scope: `user.social_links.read`*
- `GET /api/developer/v1/me/follows`: Returns following and followers count.  
  *Scope: `user.follows.read`*
- `POST /api/developer/v1/me/follows`: Follow or unfollow a target user (`target_user_id`, `action`).  
  *Scope: `user.follows.write`*

### Content, Engagement & Messages Endpoints
- `GET /api/developer/v1/me/content`: Returns latest posts authored by the user.  
  *Scope: `user.content.read`*
- `POST /api/developer/v1/me/content`: Publishes a new status update on user's behalf (`content`, `privacy`).  
  *Scope: `user.content.write`*
- `POST /api/developer/v1/me/reactions`: Toggles reaction/like on a post (`status_id`).  
  *Scope: `user.reactions.write`*
- `GET /api/developer/v1/me/messages`: Returns user's recent direct message conversations.  
  *Scope: `user.messages.read`*
- `POST /api/developer/v1/me/messages`: Sends a direct message on behalf of the user (`receiver_id`, `content`).  
  *Scope: `user.messages.write`*
- `GET /api/developer/v1/me/notifications`: Returns user's notification list and unread counter.  
  *Scope: `user.notifications.read`*

### Economy, Gamification & Media Endpoints
- `GET /api/developer/v1/me/wallet`: Returns user points (PTS) balance.  
  *Scope: `user.wallet.read`*
- `GET /api/developer/v1/me/badges`: Returns unlocked badges and achievements.  
  *Scope: `user.badges.read`*
- `GET /api/developer/v1/me/clips`: Returns public video clips feed.  
  *Scope: `user.clips.read`*
- `GET /api/developer/v1/forums`: Returns list of forum categories with topic counts.  
  *Scope: `user.forums.read`*
- `GET /api/developer/v1/store/products`: Returns marketplace store catalog.  
  *Scope: `user.store.read`*
- `GET /api/developer/v1/me/orders`: Returns user's service orders and requests.  
  *Scope: `user.orders.read`*
- `GET /api/developer/v1/me/ads/stats`: Returns banner and ad performance metrics.  
  *Scope: `user.ads.read`*

### App Owner Endpoints
These endpoints allow interaction with the developer who owns the application.
- `GET /api/developer/v1/owner/profile`: Get app owner's public profile.  
  *Scope: `owner.profile.read`*
- `GET /api/developer/v1/owner/content`: Get app owner's latest public posts.  
  *Scope: `owner.content.read`*
- `POST /api/developer/v1/owner/follow`: Follow the app owner.  
  *Scope: `owner.follow.write`*
- `POST /api/developer/v1/owner/messages`: Send a private message to the app owner.  
  *Scope: `owner.messages.write`*

---

## 5. Embed Widgets

MYADS provides ready-to-use JS widgets for your website. You can find your specific widget codes in your App Dashboard at `/developer/apps/{id}`.

- **Follow Button:** `/embed/developer/{app_id}/follow.js`
- **Profile Card:** `/embed/developer/{app_id}/profile.js`
- **Latest Content:** `/embed/developer/{app_id}/content.js`

---

## 6. External Share API (Public)

Allows any website to pre-fill the MYADS post composer.

**Endpoint:** `GET /share`  
**Query Parameters:**
- `text`: URL-encoded string for the post content.

**Example:**
`https://myads.com/share?text=Check+this+out!+https://example.com`

---

## 7. Mobile App API
  
  These endpoints are designed for the first-party mobile application and require Sanctum Bearer Token authentication.
  
  ### Settings & Hub (Phase 5)
  - `GET /api/settings/account`: Returns the current user's profile and account data (`UserProfileResource`).
  - `POST /api/settings/account`: Updates the user's name, bio, location, website, etc.
  - `GET /api/settings/privacy`: Returns the user's privacy settings.
  - `POST /api/settings/privacy`: Updates privacy settings.
  - `GET /api/settings/social`: Returns user social links.
  - `POST /api/settings/social`: Updates social links.
  - `GET /api/settings/mail`: Returns email notification preferences.
  - `POST /api/settings/mail`: Updates email preferences.
  - `GET /api/settings/sessions`: Returns active authenticated sessions for the user.
  - `DELETE /api/settings/sessions/{id}`: Revokes a specific session.
  - `GET /api/settings/apps`: Returns third-party apps authorized via OAuth (Socialite).
  - `DELETE /api/settings/apps/{id}`: Revokes a specific third-party app authorization.
  - `GET /api/settings/badges`: Returns gamification badges earned by the user.
  - `GET /api/settings/history`: Returns login history logs.
  - `GET /api/settings/points-ledger`: Returns the user's point transaction history.
  
  ### Community Feed & Statuses
- `GET /api/portal/feed`: Retrieves the community feed (paginated). Optional query parameter `filter` (`all` or `me`). Returns a paginated collection of `StatusResource` (with properly eager-loaded relationships including `user`) which includes:
  - `user`: User details (`UserResource`) containing `id` (returns `public_uid` when `public_member_ids_enabled` setting is active; returns raw integer `id` otherwise), `username`, `name`, `avatar` URL, `verified` status, and `profile_badge_color` (the hex color corresponding to their active paid plan or Super Admin status).
  - `display_content`, `display_title`, `display_image`: Pre-rendered HTML/attributes for diverse post types.
  - `video_title`, `video_thumbnail`: Resolved custom video title and video cover thumbnail image URL for video posts.
  - `media`: Primary media object for multimedia posts (Video, Audio, File, Music, Clips) containing `type`, `url`, `mime_type`, `name`, and `size`. Returns `null` for text-only posts.
  - `gallery`: Array of image URLs for multi-image posts. Empty array for non-image posts.
  - `attachments`: Array of all file attachments, each with `url`, `mime_type`, `name`, and `size`.
  - `repost_record`: Details of the original status if the post is a share/repost. Returns `null` for regular posts. Includes:
    - `id`: Unique repost record ID.
    - `status_id`: ID of the share status.
    - `original_status_id`: ID of the original post.
    - `user_id`: ID of the user who reposted.
    - `original_status`: A nested `StatusResource` representing the original post (containing its own `user`, `display_content`, `media`, `gallery`, `attachments`, etc.).
  - `grouped_reactions`: Map of reaction types to counts.
  - `has_liked`, `user_reaction`: Current user's reaction state.
  - `is_promoted_ad`: Boolean flag indicating if the post is a promoted ad campaign injected into the feed.
  - `s_type`: Status type integer code (`0` community post, `1` directory listing, `2` regular forum topic, `4` forum image post, `5` news, `6` order request, `10` video, `11` audio, `12` file, `13` music, `14` clips, `100` forum text post, `205` KB article, `7867` store product).
- `GET /api/video/feed`: Retrieves Video Hub content for mobile clients (paginated). Enforces strict `s_type` scoping (`whereIn('s_type', [10, 2, 4, 100])` for main videos, `14` for clips), completely excluding web directory listings (`s_type = 1`), store products (`s_type = 7867`), news, orders, and KB posts.
  - *Query Parameters:*
    - `filter`: Category filter (`all` default, `videos`, `clips`, `trending`, `latest`).
    - `q`: Optional search query string.
    - `page`: Page number (default `1`).
    - `per_page`: Items per page (default `12`).
  - *Response:*
    ```json
    {
        "filter": "all",
        "search_query": "",
        "spotlight_video": StatusResource | null,
        "clips": [ StatusResource, ... ],
        "videos": {
            "data": [ StatusResource, ... ]
        },
        "meta": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 12,
            "total": 54
        }
    }
    ```
- `GET /api/statuses/{status_id}`: Retrieves details for a specific status. For video posts (`s_type == 10` or `post_kind == 'video'`), the response includes `StatusResource` attributes alongside `suggested_videos` (collection of recommended video posts), `is_following` (whether viewer follows publisher), and `is_saved` (whether status is saved by viewer).
- `GET /api/v1/composer/options`: Retrieves post composer options including user's joined groups (`groups`), web directory categories (`directory_categories`), and supported post kinds (`supported_kinds`).
- `POST /api/v1/statuses/link-preview`: Generates live metadata preview for a target URL.
  *Payload:* `{"link_url": "https://example.com"}`  
  *Response:* `{"url": "...", "normalized_url": "...", "title": "...", "description": "...", "image_url": "...", "site_name": "...", "domain": "..."}`
- `POST /api/statuses`: Create a new status post.
  *Payload (Multipart Form-Data):*
  - `text`: Post text content (supports @mentions).
  - `post_kind`: Post kind (`text`, `gallery`, `video`, `audio`, `music`, `file`, `clips`, `link`, `repost`).
  - `video_title`: Optional custom video title string (for `video` posts).
  - `video_thumbnail`: Optional video cover thumbnail image file upload (for `video` posts).
  - `publish_mode`: `post` (default) or `directory_only` (publishes link as a Web Directory entry).
  - `images[]`: Array of image files (for `gallery` posts up to 10 images).
  - `videos[]`: Array of video files (for `video` or `clips` posts).
  - `audios[]`: Array of audio files (for `audio` or `music` posts).
  - `files[]`: Array of document files (for `file` posts up to 5 documents).
  - `link_url`: URL string for link posts.
  - `directory_name`, `directory_category_id`, `directory_tags`: Directory fields required when `publish_mode` is `directory_only`.
  - `group_id`: Optional target group ID when posting inside a community group.
  - `repost_status_id`: Target status ID when `post_kind` is `repost`.
  *Error Response (HTTP 422):*
  ```json
  {
      "message": "First validation error message",
      "errors": {
          "videos.0": ["The videos.0 file size must not exceed 10MB."]
      }
  }
  ```
- `POST /api/statuses/{status_id}/update`: Update an existing status (supports `video_title` and `video_thumbnail` parameters). Requires post ownership or admin rights.
- `DELETE /api/statuses/{status_id}`: Delete a status (requires ownership or admin rights).

### Comments & Reactions
- `GET /api/statuses/{status_id}/comments`: Retrieve comments for a specific status.
- `POST /api/statuses/{status_id}/comments`: Post a new comment.
  *Payload:* `{"text": "My comment"}`
- `POST /api/reactions/toggle`: Toggle a reaction on a subject (post, topic, store product, directory listing, comment, order request, clips, KB article).
  *Payload:* `{"subject_id": 123, "type": 2, "reaction_name": "love"}` *(Supported reactions: like, love, haha, wow, sad, angry, care)*
  *Response (HTTP 200):*
  ```json
  {
      "message": "Reaction added",
      "action": "added",
      "reacted": true,
      "reaction": "love"
  }
  ```
  **Note:** Clients must extract `subject_id` and `type` dynamically from the `interaction_subject_id` and `reaction_type` properties provided in the `StatusResource` to ensure reactions are logged against the correct parent topic or media format (e.g., `type 14` for Clips, `type 2` for statuses, `type 22` for Directory, `type 3` for Store, `type 4` for Forum comments, `type 44` for Directory comments, `type 444` for Store comments) and to ensure notifications and gamification points are awarded to the correct owner.

### Profile & Follow (Phase 2)
- `GET /api/profile/{identifier}`: Retrieve user profile details and stats.
  - **Note:** `{identifier}` can be `'me'` to fetch the currently authenticated user, or a `username`, or a public user ID.
  - **Response Fields:** Returns `UserProfileResource` which includes:
    - `id` (returns randomized `public_uid` when `public_member_ids_enabled` setting is active; returns raw integer `id` otherwise), `username`, `name`, `bio`, `online` (boolean), `verified` (boolean), `pts` (private, hidden or zeroed for privacy if viewed by others), `created_at` (formatted date).
    - `cover`: Resolved cover image URL.
    - `followers_count`, `following_count`, `posts_count`.
    - `is_following`: Whether the current viewer is following this user (boolean).
    - `subscription_badge`: Premium level badge containing `label` and `color` (or `null` if none).
    - `social_links`: Key-value map of configured social media platform URLs.
    - `badges`: List of unlocked badges, each containing `name` and `icon` URL.
    - `profile_badge_color`: Color hex string representing user level/tier styling.
- `GET /api/profile/{identifier}/statuses`: Retrieve user's feed statuses. Response includes enriched `StatusResource` fields (`display_title`, `display_content`, `media`, `gallery`, `attachments`, `activity_card`). Knowledgebase status posts (`s_type = 205`) return the complete formatted article body (`o_valuer`) in `display_content` with strict `s_type` isolation in `related_content`.
- `POST /api/profile/{identifier}/follow`: Toggle follow status for a user.
- `POST /api/profile/{identifier}/block`: Block a user. Payload: `{"block_type": "messages_only|full_platform", "duration": 30}` (duration is optional).
- `GET /api/settings/blocks`: Retrieve the authenticated user's list of blocked users.
- `GET /api/settings/sessions`: Retrieve active web sessions (`sessions`) and active Sanctum API device tokens (`sanctum_tokens`).
- `POST /api/settings/sessions/{id}/revoke`: Revoke a specific web session by session ID.
- `POST /api/settings/tokens/{id}/revoke`: Revoke a specific Sanctum API token device session by token ID.



### Private Messages (Phase 2)
- `GET /api/messages`: Retrieve the list of active conversations (latest message per partner).
  - **Response Fields:** Each conversation contains:
    - `user`: Partner details (`id`, `username`, `name`, `img`, `online`, `verified`).
    - `last_message`: Object with `text` (string) and `time` (unix timestamp).
    - `unread_count`: Integer count of unread messages from this partner.
    - `route_key`: Encrypted conversation identifier for navigating to the chat (preferred over username).
  - **Response also includes:** `unread_count` (total across all conversations), `has_more` (pagination flag).
- `GET /api/messages/updates`: Poll for new messages and unread count updates.
  - *Query:* `?conversation={route_key_or_username}&after_id={last_message_id}`
  - *Response:* `unread_count`, `latest_id`, `active_messages` (new messages since `after_id`).
- `GET /api/messages/{identifier}`: Retrieve message history with a specific user.
  - **Note:** `{identifier}` can be an encrypted `route_key` (preferred) or a `username` (fallback).
- `POST /api/messages/{identifier}`: Send a private message to a user.
  *Payload:* `{"text": "Hello!"}`
- `POST /api/messages/{identifier}/read`: Mark all unread messages from a user as read.


### Notifications & Wallet (Phase 3)
- `GET /api/notifications`: Retrieve user notifications (paginated).
- `GET /api/notifications/unread-count`: Get the number of unread notifications.
- `POST /api/notifications/{id}/read`: Mark a specific notification as read.
- `POST /api/notifications/read-all`: Mark all notifications as read.
- `GET /api/wallet/balance`: Retrieve the user's current points (PTS) and ad credits balance.

### Forums & Store (Phase 4)
- `GET /api/forums/categories`: Retrieve forum categories.
- `GET /api/forums/categories/{categoryId}/topics`: Retrieve topics in a specific category.
- `POST /api/forums/categories/{categoryId}/topics`: Create a new topic. Payload: `{"title": "...", "content": "..."}`
- `GET /api/forums/topics/{topicId}`: Retrieve a specific topic and its replies.
- `POST /api/forums/topics/{topicId}/replies`: Add a reply to a topic. Payload: `{"content": "..."}`
- `GET /api/store/products`: Retrieve store products (paginated). Products are ordered chronologically by status promotion date (`s_type = 7867`), `updated_at`, then `id DESC`. Returns `ProductResource` containing `price`, `original_price`, `sale_price`, `current_price`, `is_on_sale`, `thumbnail`, `seller`, `updated_at`, and `date_formatted`.
- `GET /api/store/products/{id}`: Retrieve a specific product's details with complete sale pricing and seller metadata.
- `GET /api/store/products/{id}/knowledgebase`: Retrieve the knowledgebase articles for a product (paginated).

### Clips & Shorts
- `GET /api/clips/saved`: Retrieve a list of saved clips for the authenticated user.
- `POST /api/clips/save`: Toggle the saved state of a specific reel. Payload: `{"status_id": 123}`

### Custom Member Ads & Anti-Click-Farm Serving
- `GET /ads/custom/embed`: Returns placement loader JavaScript script.
- `GET /ads/custom/serve?placement={key}&slot={slot_id}&vt={visitor_token}`: Serves HTML creative markup for custom ad placement. Automatically records impression with 24-hour fingerprint rate limit check (`sha256(IP + UserAgent + AcceptLanguage)`).
- `GET /ads/custom/click/{token}`: Tracks click event and redirects visitor to target URL. Enforces minimum 1.5-second session dwell check. Repeated or rapid clicks are marked `is_flagged = true` without deducting advertiser PTS or incrementing publisher earnings.

---

## 7. Response Format

All API responses follow a consistent JSON structure:

**Success:**
```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}
```

**Error:**
```json
{
    "success": false,
    "message": "Error description.",
    "data": null
}
```

