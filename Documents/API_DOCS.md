# API Documentation

> **Note:** The MYADS v4.3 REST API is currently in **Beta**. It now supports both Internal Authentication (Sanctum) and Third-Party Authorization (OAuth2).

The REST API allows external applications and third-party developers to interface with the MYADS core, authenticate users, retrieve content, and perform actions on behalf of users.

---

## 1. Authentication & Authorization

MYADS supports two methods of authentication depending on the use case:

### A. Internal API Tokens (Sanctum)
Best for first-party companion apps (e.g., mobile apps). Users provide credentials directly to the app.

**Security Requirements:**
All mobile API requests require a two-layer authentication:
1. `x-api-key`: A global API key managed by the admin to prevent unauthorized client access.
2. `Authorization`: The user's Sanctum Bearer Token for identity.

**Login Endpoint:** `POST /api/login`  
**Payload:** `{"login": "username_or_email", "password": "..."}`  
**Header (Login):** `x-api-key: {YOUR_GLOBAL_API_KEY}`  
**Header (Subsequent Requests):** `Authorization: Bearer {token}` along with `x-api-key: {YOUR_GLOBAL_API_KEY}`

### B. OAuth2 Authorization (Third-Party)
Best for third-party websites and integrations. Users authorize your app via the MYADS OAuth screen without sharing their password.

**Authorization URL:** `/oauth/authorize`  
**Token URL:** `/oauth/token`

---

## 4. Developer Platform

To build third-party integrations, you must first register your application in the **Developer Platform**.

1. Go to `/developer`.
2. Click **Create New App**.
3. Provide your App Name, Website, and **Redirect URIs**.
4. Obtain your `Client ID` and `Client Secret`.

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

### User Endpoints (Scope: `user.*`)
- `GET /api/developer/v1/me`: Returns basic identity (ID, Username, Email).  
  *Scope: `user.identity.read`*
- `GET /api/developer/v1/me/profile`: Returns profile details (Avatar, Points).  
  *Scope: `user.profile.read`*

### App Owner Endpoints (Scope: `owner.*`)
These endpoints allow interaction with the developer who owns the app.
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

## 7. Mobile App API (Phase 1)

These endpoints are designed for the first-party mobile application and require Sanctum Bearer Token authentication.

### Community Feed & Statuses
- `GET /api/portal/feed`: Retrieves the community feed (paginated). Optional query parameter `filter` (`all` or `me`). Returns a collection of `StatusResource` which includes:
  - `user`: User details (`UserResource`) containing `id`, `username`, `name`, `avatar` URL, `verified` status, and `profile_badge_color` (the hex color corresponding to their active paid plan or Super Admin status).
  - `display_content`, `display_title`, `display_image`: Pre-rendered HTML/attributes for diverse post types.
  - `media`: Primary media object for multimedia posts (Video, Audio, File, Music, Reels) containing `type`, `url`, `mime_type`, `name`, and `size`. Returns `null` for text-only posts.
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
- `POST /api/statuses`: Create a new status.
  *Payload:* `{"text": "Hello world!"}`
- `DELETE /api/statuses/{status_id}`: Delete a status (requires ownership or admin rights).

### Comments & Reactions
- `GET /api/statuses/{status_id}/comments`: Retrieve comments for a specific status.
- `POST /api/statuses/{status_id}/comments`: Post a new comment.
  *Payload:* `{"text": "My comment"}`
- `POST /api/reactions/toggle`: Toggle a reaction on a subject.
  *Payload:* `{"subject_id": 123, "type": 2, "reaction_name": "Love"}` *(Supported reactions: Like, Love, Haha, Wow, Sad, Angry)*
  **Note:** Clients must extract `subject_id` and `type` dynamically from the `interaction_subject_id` and `reaction_type` properties provided in the `StatusResource` to ensure reactions are logged against the correct parent topic or media format (e.g., `type 14` for Reels, `type 2` for statuses, `type 3` for groups) and to ensure notifications and gamification points are awarded to the correct owner.

### Profile & Follow (Phase 2)
- `GET /api/profile/{identifier}`: Retrieve user profile details and stats.
  - **Note:** `{identifier}` can be `'me'` to fetch the currently authenticated user, or a `username`, or a public user ID.
  - **Response Fields:** Returns `UserProfileResource` which includes:
    - `id`, `username`, `name`, `bio`, `online` (boolean), `verified` (boolean), `pts` (private, hidden or zeroed for privacy if viewed by others), `created_at` (formatted date).
    - `cover`: Resolved cover image URL.
    - `followers_count`, `following_count`, `posts_count`.
    - `is_following`: Whether the current viewer is following this user (boolean).
    - `subscription_badge`: Premium level badge containing `label` and `color` (or `null` if none).
    - `social_links`: Key-value map of configured social media platform URLs.
    - `badges`: List of unlocked badges, each containing `name` and `icon` URL.
    - `profile_badge_color`: Color hex string representing user level/tier styling.
- `GET /api/profile/{identifier}/statuses`: Retrieve user's feed statuses. Response includes the same enriched `StatusResource` fields (`media`, `gallery`, `attachments`) as the community feed, with full `related_content` hydration.
- `POST /api/profile/{identifier}/follow`: Toggle follow status for a user.


### Private Messages (Phase 2)
- `GET /api/messages`: Retrieve the list of active conversations (latest message per partner).
- `GET /api/messages/{identifier}`: Retrieve message history with a specific user.
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
- `GET /api/forums/topics/{topicId}`: Retrieve a specific topic and its replies.
- `GET /api/store/products`: Retrieve store products (paginated).
- `GET /api/store/products/{id}`: Retrieve a specific product's details.

### Reels
- `GET /api/reels/saved`: Retrieve a list of saved reels for the authenticated user.
- `POST /api/reels/save`: Toggle the saved state of a specific reel. Payload: `{"status_id": 123}`

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

