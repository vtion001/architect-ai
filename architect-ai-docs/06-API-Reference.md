# API Reference

## Base URL

**Development**: `https://[ngrok-url]/api`
**Production**: `https://architect-ai.com/api`

## Authentication

All protected endpoints require authentication via Laravel Sanctum.

### Authentication Header
```
Authorization: Bearer {token}
```

### Tenant Context
Authenticated requests automatically scope to the user's tenant via middleware.

---

## Authentication Endpoints

### Register Agency
Create a new agency tenant and admin user.

**Endpoint**: `POST /api/auth/register-agency`

**Rate Limit**: 3 requests per hour

**Request Body**:
```json
{
  "agency_name": "Creative Agency",
  "admin_name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!"
}
```

**Response** (201 Created):
```json
{
  "message": "Agency registered successfully",
  "tenant": {
    "id": "uuid",
    "name": "Creative Agency",
    "slug": "creative-agency"
  },
  "user": {
    "id": "uuid",
    "email": "john@example.com",
    "role": "admin"
  },
  "token": "sanctum_token"
}
```

---

### Login
Authenticate user and receive access token.

**Endpoint**: `POST /api/auth/login`

**Rate Limit**: 5 requests per 15 minutes

**Request Body**:
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123!"
}
```

**Response** (200 OK):
```json
{
  "message": "Login successful",
  "user": {
    "id": "uuid",
    "email": "john@example.com",
    "tenant": {
      "id": "uuid",
      "name": "Creative Agency"
    },
    "roles": [
      {
        "name": "Agency Admin",
        "permissions": [
          "content.create",
          "brands.manage",
          "users.manage"
        ]
      }
    ],
    "mfa_enabled": false
  },
  "token": "sanctum_token"
}
```

**If MFA Enabled** (200 OK):
```json
{
  "message": "MFA verification required",
  "requires_mfa": true,
  "mfa_challenge_url": "/api/auth/mfa/challenge"
}
```

---

### Logout
Invalidate current session token.

**Endpoint**: `POST /api/auth/logout`

**Authentication**: Required

**Response** (200 OK):
```json
{
  "message": "Logged out successfully"
}
```

---

## User Endpoints

### Get Current User
Get information about the authenticated user.

**Endpoint**: `GET /api/me`

**Authentication**: Required

**Response** (200 OK):
```json
{
  "id": "uuid",
  "email": "john@example.com",
  "tenant": {
    "id": "uuid",
    "name": "Creative Agency"
  },
  "roles": [
    {
      "id": "uuid",
      "name": "Agency Admin",
      "permissions": [
        "content.create",
        "content.update",
        "content.delete",
        "brands.manage"
      ]
    }
  ],
  "mfa_enabled": true,
  "last_login_at": "2026-01-17T10:30:00Z"
}
```

---

## Content Generation Endpoints

### Generate Content
Generate AI-powered content using specified type.

**Endpoint**: `POST /api/content/generate`

**Authentication**: Required

**Request Body**:
```json
{
  "topic": "5 tips for better productivity",
  "type": "social-media post",
  "brand_id": "optional-brand-uuid",
  "context": "Target audience: remote workers",
  "generator": "post",
  "options": {
    "tone": "professional",
    "length": "medium",
    "include_hashtags": true,
    "platform": "linkedin"
  }
}
```

**Content Types**:
- `social-media post`
- `blog post`
- `video script`
- `framework calendar`

**Response** (200 OK):
```json
{
  "id": "uuid",
  "type": "social-media post",
  "topic": "5 tips for better productivity",
  "content": "🚀 5 Productivity Tips for Remote Workers\n\n1. Set clear boundaries...",
  "brand_context": {
    "name": "TechBrand",
    "voice": "Professional & Friendly"
  },
  "tokens_used": 245,
  "generated_at": "2026-01-17T10:35:00Z"
}
```

**Response** (429 Too Many Requests):
```json
{
  "message": "Rate limit exceeded. Try again later.",
  "retry_after": 45
}
```

**Response** (402 Payment Required):
```json
{
  "message": "Token limit exceeded",
  "usage": {
    "limit": 100000,
    "used": 100000,
    "resets_at": "2026-02-01T00:00:00Z"
  }
}
```

---

## Research Endpoints

### Start Research
Initiate deep research on a topic.

**Endpoint**: `POST /api/research/start`

**Authentication**: Required

**Request Body**:
```json
{
  "topic": "The future of AI in marketing",
  "depth": "comprehensive",
  "include_sources": true
}
```

**Depth Options**:
- `basic` - Quick overview (2-3 minutes)
- `comprehensive` - Detailed analysis (5-10 minutes)
- `deep` - Extensive research (15-20 minutes)

**Response** (202 Accepted):
```json
{
  "id": "uuid",
  "topic": "The future of AI in marketing",
  "status": "processing",
  "estimated_completion": "2026-01-17T10:45:00Z",
  "check_status_url": "/api/research/{id}"
}
```

**Get Research Result**:
```json
{
  "id": "uuid",
  "topic": "The future of AI in marketing",
  "status": "completed",
  "content": "# The Future of AI in Marketing\n\n## Overview\n...",
  "sources": [
    {
      "title": "McKinsey AI Report",
      "url": "https://...",
      "relevance": 0.95
    }
  ],
  "tokens_used": 1250,
  "generated_at": "2026-01-17T10:43:00Z",
  "completion_time": 480
}
```

---

## Brand Endpoints

### List Brands
Get all brands for current tenant.

**Endpoint**: `GET /api/brands`

**Authentication**: Required

**Response** (200 OK):
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "TechBrand",
      "tagline": "Innovation simplified",
      "logo_url": "https://...",
      "is_default": true,
      "industry": "Technology"
    },
    {
      "id": "uuid",
      "name": "LifestyleBrand",
      "tagline": "Live your best life",
      "is_default": false
    }
  ]
}
```

---

### Create Brand
Create a new brand.

**Endpoint**: `POST /api/brands`

**Authentication**: Required
**Permission**: `brands.create`

**Request Body**:
```json
{
  "name": "NewBrand",
  "tagline": "Your success partner",
  "description": "A tech consulting company",
  "colors": {
    "primary": "#3b82f6",
    "secondary": "#1e40af",
    "accent": "#f59e0b"
  },
  "typography": {
    "headings": "Inter",
    "body": "Open Sans"
  },
  "voice_profile": {
    "tone": "Professional",
    "personality": "Helpful",
    "keywords": "innovation, success, growth",
    "avoid_words": "cheap, fast, easy"
  },
  "industry": "Technology"
}
```

**Response** (201 Created):
```json
{
  "id": "uuid",
  "name": "NewBrand",
  "tagline": "Your success partner",
  "colors": {
    "primary": "#3b82f6",
    "secondary": "#1e40af",
    "accent": "#f59e0b",
    "background": "#ffffff",
    "text": "#1f2937"
  },
  "typography": {
    "headings": "Inter",
    "body": "Open Sans",
    "accent": "Inter"
  },
  "voice_profile": {
    "tone": "Professional",
    "personality": "Helpful",
    "keywords": "innovation, success, growth",
    "avoid_words": "cheap, fast, easy",
    "writing_style": "Balanced"
  },
  "industry": "Technology",
  "is_default": false
}
```

---

## Knowledge Base Endpoints

### Add Asset
Add a new asset to knowledge base.

**Endpoint**: `POST /api/knowledge-base/assets`

**Authentication**: Required
**Permission**: `knowledge-base.create`

**Request Body** (Text):
```json
{
  "name": "Company FAQ",
  "type": "text",
  "content": "Q: What services do we offer?\nA: We offer..."
}
```

**Request Body** (Document):
```json
{
  "name": "Product Guide",
  "type": "document",
  "file": "<binary file data>"
}
```

**Request Body** (URL):
```json
{
  "name": "Competitor Analysis",
  "type": "url",
  "url": "https://example.com/article"
}
```

**Response** (201 Created):
```json
{
  "id": "uuid",
  "name": "Company FAQ",
  "type": "text",
  "indexed": true,
  "vector_count": 15,
  "created_at": "2026-01-17T10:50:00Z"
}
```

---

### Search Assets
Search knowledge base assets semantically.

**Endpoint**: `GET /api/knowledge-base/search`

**Authentication**: Required

**Query Parameters**:
- `q` - Search query (required)
- `limit` - Number of results (default: 5, max: 20)

**Request**:
```
GET /api/knowledge-base/search?q=product pricing&limit=10
```

**Response** (200 OK):
```json
{
  "query": "product pricing",
  "results": [
    {
      "id": "uuid",
      "name": "Pricing Guide",
      "type": "text",
      "relevance": 0.95,
      "excerpt": "Our pricing starts at $99/month for the basic plan...",
      "asset_url": "/api/knowledge-base/assets/{id}"
    },
    {
      "id": "uuid",
      "name": "FAQ",
      "type": "text",
      "relevance": 0.87,
      "excerpt": "Q: How much does the service cost?\nA: Pricing depends...",
      "asset_url": "/api/knowledge-base/assets/{id}"
    }
  ]
}
```

---

## Global Search

### Global Search
Search across all tenant resources.

**Endpoint**: `GET /api/search`

**Authentication**: Required

**Query Parameters**:
- `q` - Search query (required)
- `type` - Filter by type (optional: content, document, task, brand)

**Request**:
```
GET /api/search?q=product launch&type=content
```

**Response** (200 OK):
```json
{
  "query": "product launch",
  "results": [
    {
      "type": "content",
      "id": "uuid",
      "title": "Product Launch Social Posts",
      "excerpt": "🚀 Exciting news! We're launching...",
      "url": "/content/{id}"
    },
    {
      "type": "document",
      "id": "uuid",
      "title": "Product Launch Plan",
      "excerpt": "This document outlines the strategy for...",
      "url": "/documents/{id}"
    }
  ]
}
```

---

## Developer Endpoints

### Impersonate User
Start impersonating another user (developer only).

**Endpoint**: `POST /api/developer/impersonate`

**Authentication**: Required
**Permission**: `is-developer`

**Request Body**:
```json
{
  "user_id": "uuid"
}
```

**Response** (200 OK):
```json
{
  "message": "Impersonation started",
  "impersonating": {
    "id": "uuid",
    "email": "user@example.com",
    "tenant": {
      "id": "uuid",
      "name": "Client Tenant"
    }
  },
  "original_user": {
    "id": "uuid",
    "email": "developer@archit-ai.io"
  }
}
```

---

### Stop Impersonating
Stop current impersonation session.

**Endpoint**: `POST /api/developer/stop-impersonating`

**Authentication**: Required
**Permission**: `is-developer`

**Response** (200 OK):
```json
{
  "message": "Impersonation stopped",
  "user": {
    "id": "uuid",
    "email": "developer@archit-ai.io"
  }
}
```

---

## Error Responses

### Standard Error Format

```json
{
  "message": "Error description",
  "errors": {
    "field": ["Error message 1", "Error message 2"]
  }
}
```

### Common HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 202 | Accepted - Request accepted for processing |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Permission denied |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |

---

## Rate Limits

| Endpoint | Limit | Period |
|----------|-------|--------|
| POST /api/auth/login | 5 | 15 minutes |
| POST /api/auth/register-agency | 3 | 1 hour |
| POST /api/content/generate | 20 | 1 minute |
| POST /api/research/start | 5 | 1 minute |
| GET /api/search | 30 | 1 minute |

Rate limit headers included in responses:
```
X-RateLimit-Limit: 20
X-RateLimit-Remaining: 15
X-RateLimit-Reset: 1737123456
```

---

## Webhooks

### Content Generation Complete

**Webhook URL**: Configured per tenant in settings

**Payload**:
```json
{
  "event": "content.generated",
  "data": {
    "id": "uuid",
    "type": "social-media post",
    "topic": "...",
    "status": "completed"
  },
  "timestamp": "2026-01-17T10:35:00Z"
}
```

---

**Documentation Generated**: January 17, 2026
