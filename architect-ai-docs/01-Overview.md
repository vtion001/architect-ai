# Architect-AI Overview

## Project Summary

**Architect-AI** is a multi-tenant, AI-powered content creation and research platform built with Laravel 11.0, Vue.js, and Tailwind CSS. It enables agencies to manage multiple brands, generate AI-powered content, conduct research, and collaborate with team members through a secure, scalable architecture.

## Tech Stack

### Backend
- **Framework**: Laravel 11.0 (PHP 8.2+)
- **Authentication**: Laravel Sanctum
- **Database**: PostgreSQL (with UUID support)
- **AI Integration**:
  - OpenAI GPT-4o-mini (Content generation)
  - Google Gemini 1.5 Pro (Research engine)
  - Qdrant (Vector database for RAG)
- **Media Storage**: Cloudinary
- **Real-time**: Webhooks and Job Queues

### Frontend
- **Build Tool**: Vite 5.0
- **Styling**: Tailwind CSS 3.4
- **Icons**: Lucide
- **Charts**: Chart.js
- **HTTP Client**: Axios

### Infrastructure
- **Containerization**: Docker
- **Process Management**: Heroku Procfile
- **Security**: ngrok (SSL/TLS 1.3)

## Core Features

### 1. Multi-Tenancy & Brand Management
- Tenant isolation with global scopes
- Sub-tenant support for agencies
- Brand profiles with custom colors, typography, and voice
- Brand blueprints for templated content

### 2. AI-Powered Content Creation
- Social media posts with viral example analysis
- Blog posts
- Video scripts
- Framework calendars
- Content generation strategies with RAG integration

### 3. Research Engine
- Deep research using Google Gemini API
- Knowledge base integration
- Multiple model fallback support
- Structured report generation

### 4. Knowledge Base (RAG)
- Document upload and indexing
- Vector embeddings via Qdrant
- Context-aware content generation
- PDF text extraction

### 5. Social Media Integration
- Social planner and scheduling
- Multi-platform posting (Facebook, Instagram, LinkedIn)
- Encrypted social access tokens
- Campaign management

### 6. Document Builder
- Template-based document creation
- Brand-aware content generation
- Document registry and management

### 7. Task Management
- Task categories and assignments
- Status tracking
- Team collaboration

### 8. Analytics & Reporting
- Content performance analytics
- Usage metrics
- Token consumption tracking
- Custom report builder

### 9. Security & IAM
- Role-Based Access Control (RBAC)
- Multi-Factor Authentication (MFA) for admins
- Audit logging
- Session security
- Developer impersonation

## Architecture Patterns

### Multi-Tenancy
- Row-level data isolation using `BelongsToTenant` trait
- Tenant-scoped queries with global scopes
- Sub-tenant hierarchy support
- Encrypted metadata storage for sensitive credentials

### Content Generation Strategy
- Factory pattern for content generators
- Strategy pattern for different content types
- RAG (Retrieval-Augmented Generation) integration
- Context injection from knowledge base and viral examples

### API Design
- RESTful API routes in `/routes/api.php`
- Sanctum authentication
- Rate limiting on sensitive endpoints
- Middleware stack: auth → tenant → session_security → role checks

## Key Directories

```
app/
├── Models/              # Eloquent models (23 models)
├── Services/            # Business logic layer (14 services)
├── Http/Controllers/    # Request handlers (22 controllers)
├── DTOs/                # Data Transfer Objects
├── Enums/               # Enumerations
├── Jobs/                # Queued jobs
├── Observers/           # Model observers
├── Policies/            # Authorization policies
└── View/                # View components

resources/views/
├── layouts/             # Main layouts (auth, app, public)
├── dashboard/           # Dashboard components
├── content-creator/     # Content generation UI
├── research-engine/     # Research interface
├── knowledge-base/      # Knowledge base management
├── social-planner/      # Social media scheduling
├── document-builder/    # Document creation
├── brands/              # Brand management
├── tasks/               # Task management
├── analytics/           # Analytics dashboard
├── reports/             # Report generation
└── ai-agents/           # AI agent management

routes/
├── api.php              # API routes (43 lines)
├── web.php              # Web routes (400+ lines)
└── console.php          # CLI commands

config/
├── iam.php              # Identity & Access Management
├── services.php         # External service configs (OpenAI, Gemini, Qdrant, Cloudinary)
└── session.php          # Session configuration
```

## Database Models

### Core Models
- **Tenant**: Multi-tenant organization
- **User**: User accounts with MFA support
- **Role**: Role definitions
- **UserRole**: Pivot with scope and expiration
- **Permission**: Granular permissions
- **Brand**: Brand profiles with blueprints

### Content Models
- **Content**: Generated content
- **Research**: Research reports
- **Document**: Managed documents
- **KnowledgeBaseAsset**: RAG data sources

### AI Models
- **AiAgent**: AI agent configurations
- **AgentConversation**: Agent interaction history

### Management Models
- **Task**: Task management
- **TaskCategory**: Task categories
- **AccessPolicy**: Access control policies
- **AuditLog**: System audit trail

### Utility Models
- **MediaAsset**: Media registry
- **TokenAllocation**: Token quotas
- **TokenTransaction**: Usage tracking
- **Invitation**: User invitations
- **Waitlist**: Waitlist management

## Security Features

### Implemented
- TLS 1.3 encryption
- Multi-factor authentication (TOTP)
- Rate limiting (5 attempts/15 min for auth)
- CSRF protection
- CSP headers
- Session-based timeouts
- Developer impersonation (secure)
- Audit logging

### Configuration
- NIST 800-63B password policy (min 12 chars)
- Row-level data isolation
- Encrypted metadata for sensitive fields
- Anomaly detection (automatic suspension)

## API Endpoints

### Public
- `POST /api/auth/register-agency` - Agency registration
- `POST /api/auth/login` - User login

### Protected (Auth + Tenant)
- `GET /api/me` - Current user info
- `POST /api/content/generate` - Generate content
- `POST /api/research/start` - Start research
- `GET /api/search` - Global search

### Developer Only
- `POST /api/developer/impersonate` - Impersonate user
- `POST /api/developer/stop-impersonating` - Stop impersonation

## Environment Variables

### Required
- `APP_KEY` - Laravel encryption key
- `DB_*` - Database connection
- `OPENAI_API_KEY` - OpenAI API key
- `GEMINI_API_KEY` - Google Gemini API key
- `QDRANT_*` - Qdrant vector DB config
- `CLOUDINARY_*` - Cloudinary media storage

### Optional
- `HIKER_API_KEY` - Hiker API for viral content
- `DEVELOPER_EMAIL` - Developer account email

## Development Workflow

### Building Assets
```bash
npm run dev      # Development server
npm run build    # Production build
```

### Laravel Artisan
```bash
php artisan migrate          # Run migrations
php artisan serve            # Start dev server
php artisan queue:work       # Process jobs
```

### Docker
```bash
docker-compose up -d         # Start services
docker-compose down          # Stop services
```

## Deployment

- **Platform**: Heroku (via Procfile)
- **Buildpacks**: PHP, Node.js
- **Runtime**: PHP 8.2+
- **Web Server**: Nginx (via ngrok for dev)

---

**Documentation Generated**: January 17, 2026
