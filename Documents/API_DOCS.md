# API Documentation

> **Note:** The MYADS v4.0 REST API is currently in **Internal Alpha**.

The REST API allows external applications (like companion mobile apps) to interface directory with the MYADS core, authenticate users, retrieve posts, and manage directories.

## Authentication

Authentication is handled via **Sanctum** tokens.
All secure endpoints require an `Authorization: Bearer {token}` header.

### Generating a Token
To obtain a token, the external app must send a `POST` request to `/api/login` with the user's credentials.

```http
POST /api/login
Content-Type: application/json

{
    "identity": "admin@myads.com",
    "password": "secretpassword"
}
```

**Success Response `200 OK`:**
```json
{
    "token": "1|abcdef123456789...",
    "user": {
        "id": 1,
        "username": "admin",
        "email": "admin@myads.com"
    }
}
```

## Available Endpoints (Draft)

### 1. User Profile `GET /api/user/{id}`
Returns public information regarding a user profile.

### 2. Community Feed `GET /api/community`
Returns a paginated list of the latest status updates and directory submissions.

**Query Parameters:**
- `page`: Integer (default 1).
- `limit`: Integer (default 15).

### 3. Submit Directory Site `POST /api/directory`
Submit a new site to the Web Directory.

**Payload:**
```json
{
    "name": "Example Domain",
    "url": "https://example.com",
    "description": "An example site.",
    "category_id": 12
}
```

### 4. External Share API (Public) `GET /share`
Allows third-party websites to pre-fill the MYADS post composer.

**Query Parameters:**
- `text`: URL-encoded string containing the content to share.

**Response:**
Redirects to the login page (if not authenticated) or directly to the `/share` page with the pre-filled post composer.

## Developer Documentation
A full guide on implementing the Share API is available on-site at `/developer`.

### Response Format
All REST API responses follow a consistent JSON format:
```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}
```
If an error occurs, the API returns the appropriate HTTP code (400, 401, 403, 404, 422, 500) and:
```json
{
    "success": false,
    "error": "Validation failed.",
    "details": {
        "url": ["The url field is required."]
    }
}
```
