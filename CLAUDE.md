# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Architect AI** is a multi-tenant SaaS platform built with Laravel 11 that provides AI-powered content generation, document creation, and competitive research services.

### Tech Stack

- **Backend:** Laravel 11, PHP 8.2
- **Frontend:** Vite + Tailwind CSS + AlpineJS
- **Database:** MySQL 8.0 (Docker)
- **Vector Search:** Qdrant (Docker) - for knowledge base embeddings
- **File Storage:** Cloudinary
- **E-Signatures:** HelloSign
- **Auth:** Laravel Sanctum + Google2FA (MFA)

### Docker Services

```bash
docker-compose up -d          # Start all services
docker-compose up -d app     # Start only app
docker-compose up -d queue   # Start queue worker
docker-compose logs -f app    # View app logs
```

Services: `app` (Laravel), `queue` (background jobs), `web` (nginx), `db` (MySQL), `qdrant` (vector DB)

---

## Common Commands

```bash
# Development
php artisan serve                    # Start dev server
php artisan queue:work               # Run queue worker
npm run dev                          # Vite dev server
npm run build                        # Production build

# Code Quality
./vendor/bin/pint                    # Laravel Pint (code style)
./vendor/bin/pest                    # Run tests
./vendor/bin/pest --filter=TestName # Run specific test

# Database
php artisan migrate                  # Run migrations
php artisan db:seed                  # Seed database
php artisan db:seed --class=IAMSeeder

# Docker
docker-compose exec db mysql -u root -p    # MySQL shell
docker-compose exec qdrant curl localhost:6333/collections  # Qdrant API
```

---

## Architecture

### Multi-Tenant Model

```
Tenant (agency/account)
  └── SubAccount (client under agency)
  └── Brand (branding per client)
  └── User → Role → Permission
```

The `tenant` middleware automatically scopes queries to the current tenant. Models use the `BelongsToTenant` trait.

### AI Service Layer

**Primary:** MiniMax (`app/Services/AI/MiniMaxClient.php`)
**Fallback:** OpenAI, OpenRouter, Perplexity

The `AiChatProcessingService` handles context-aware conversations with conversation history stored in `agent_conversations` table.

### Document Generation (Refactored)

The `ReportService` uses a modular generator architecture:

```
ReportService (coordinator)
  ├── DocumentGeneratorInterface
  ├── BaseGenerator (common logic)
  └── Generators/
      ├── CvResumeGenerator
      ├── CoverLetterGenerator
      ├── ProposalGenerator
      ├── ContractGenerator
      └── ReportsGenerator (7 report types)
```

Each generator implements `generate()`, `buildSystemPrompt()`, `buildUserPrompt()`, etc. Generators that don't need research return `false` from `requiresResearch()` to skip API calls.

### Vector/Knowledge Base

`VectorService` manages embeddings via OpenAI API and stores in Qdrant. Collections: `knowledge_base` with 1536 dimensions.

`KnowledgeBaseService` syncs documents to vector store with parent-child hierarchy (`parent_id` for nested assets).

### Content Generation Pipeline

```
ContentGenerators/
  ├── BlogPostGenerator
  ├── SocialPostGenerator
  ├── VideoScriptGenerator
  └── FrameworkCalendarGenerator
```

Each generator extends `BaseContentGenerator` and is instantiated via `ContentGeneratorFactory`.

### Token/Billing System

Token-based quotas per tenant:
- `token_limits` table defines plan limits
- `token_allocations` tracks per-feature usage
- `token_transactions` for audit trail
- `feature_credits` for feature-based billing

Commands: `RefillGridTokens`, `RefillUserQuotas` (scheduled)

### Middleware Stack

- `tenant` - Sets current tenant context
- `session_security` - Session validation
- `mfa` - Google2FA enforcement
- `can` - Permission checks via roles

---

## Key Enums

| Enum | Location | Purpose |
|------|----------|---------|
| `FeatureType` | `app/Enums/FeatureType.php` | Content, Research, Documents |
| `PlanType` | `app/Enums/PlanType.php` | Token-based plans |
| `ReportTemplate` | `app/Enums/ReportTemplate.php` | CV, Cover Letter, Proposal, Contract, 7 Report types |

---

## External Integrations

All configured via `config/services.php` and `.env`:
- **AI:** MiniMax, OpenAI, OpenRouter, Perplexity, HikerAPI
- **Social:** LinkedIn, Twitter/X, Facebook, Instagram OAuth
- **Storage:** Cloudinary
- **E-Sign:** HelloSign (test mode by default)

---

## File Organization

```
app/
├── Console/Commands/       # Artisan commands (scheduled tasks, migrations)
├── Contracts/              # Interface definitions
├── DTOs/                   # Data Transfer Objects
├── Enums/                  # FeatureType, PlanType, ReportTemplate
├── Http/Controllers/       # Laravel controllers
│   ├── Auth/               # Auth, MFA, Invitation, Tenant, Developer
│   ├── Admin/              # AdminController, AuditController
│   └── [Feature]Controller # DocumentBuilder, ReportBuilder, ResearchEngine, etc.
├── Jobs/                   # Queue jobs (GenerateContent, RenderVideo, etc.)
├── Middleware/             # Tenant, MFA, SessionSecurity, CheckPermission
├── Models/                 # Eloquent models (Tenant, User, Content, Document, etc.)
├── Observers/              # Model observers for side effects
└── Services/               # Business logic
    ├── AI/                 # MiniMaxClient, OpenAIClient, PromptBuilder
    ├── ContentGenerators/  # Blog, Social, Video, Calendar generators
    ├── Generators/         # Document generators (CV, Cover Letter, etc.)
    └── [Feature]Service    # VectorService, ReportService, ResearchService, etc.

routes/
├── api.php      # API routes (auth, content, research)
├── web.php      # Web routes (session auth)
└── console.php  # Scheduled commands
```

---

## Database Notes

- UUIDs for primary keys on major tables (`users`, `contents`, `documents`, `research`)
- Soft deletes on `tasks` table
- `access_policies` table for IAM (Identity & Access Management)
- `audit_logs` table tracks all write operations
- `jobs` table for Laravel queue (database driver)
