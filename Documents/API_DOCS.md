# API Documentation

> **Note:** The MYADS v4.3 REST API is currently in **Beta**. It now supports both Internal Authentication (Sanctum) and Third-Party Authorization (OAuth2).

The REST API allows external applications and third-party developers to interface with the MYADS core, authenticate users, retrieve content, and perform actions on behalf of users.

---

## 1. Authentication & Authorization

MYADS supports two methods of authentication depending on the use case:

### A. Internal API Tokens (Sanctum)
Best for first-party companion apps (e.g., mobile apps). Users provide credentials directly to the app.

**Endpoint:** `POST /api/login`  
**Payload:** `{"identity": "username_or_email", "password": "..."}`  
**Header:** `Authorization: Bearer {token}`

### B. OAuth2 Authorization (Third-Party)
Best for third-party websites and integrations. Users authorize your app via the MYADS OAuth screen without sharing their password.

**Authorization URL:** `/oauth/authorize`  
**Token URL:** `/oauth/token`

---

## 2. Developer Platform

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

