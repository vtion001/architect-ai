# Architect-AI Documentation

Welcome to the Architect-AI documentation. This comprehensive guide covers all aspects of the multi-tenant AI-powered content creation platform.

## Table of Contents

### 📚 Core Documentation

1. **[[01-Overview]]** - Project overview, tech stack, features, and architecture
2. **[[02-Models]]** - Complete reference for all Eloquent models
3. **[[03-Services]]** - Business logic layer and service documentation
4. **[[04-Controllers]]** - HTTP controllers and endpoint reference
5. **[[05-Security]]** - Security features, multi-tenancy, and compliance
6. **[[06-API-Reference]]** - RESTful API endpoints and usage
7. **[[07-Getting-Started]]** - Installation, setup, and development guide

---

## Quick Links

### Architecture
- Multi-tenancy implementation
- RAG (Retrieval-Augmented Generation) architecture
- Content generation strategy pattern
- Service layer architecture

### Key Features
- AI Content Creation (OpenAI GPT-4o-mini)
- Research Engine (Google Gemini)
- Knowledge Base with Qdrant
- Social Media Integration
- Brand Management
- Task Management
- Analytics & Reporting

### Security
- Multi-tenant data isolation
- RBAC/ABAC hybrid access control
- Multi-factor authentication
- Encrypted metadata
- Audit logging
- GDPR/CCPA compliance

### Development
- Laravel 11.0 with PHP 8.2+
- Vue.js frontend with Vite
- Tailwind CSS styling
- PostgreSQL database
- Docker support

---

## Project Structure

```
architect-ai/
├── app/
│   ├── Models/              # Eloquent models (23 models)
│   ├── Services/            # Business logic (14 services)
│   ├── Http/Controllers/    # Request handlers (22 controllers)
│   ├── DTOs/                # Data Transfer Objects
│   ├── Enums/               # Enumerations
│   ├── Jobs/                # Queued jobs
│   ├── Observers/           # Model observers
│   ├── Policies/            # Authorization policies
│   └── View/                # View components
├── config/                  # Configuration files
├── database/                # Migrations and seeders
├── public/                  # Public assets
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Tailwind CSS
│   └── js/                  # Vue components
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── console.php          # CLI commands
├── tests/                   # Test suites
└── vendor/                  # Composer dependencies
```

---

## Key Technologies

### Backend
- **Laravel 11.0** - PHP framework
- **PHP 8.2+** - Programming language
- **PostgreSQL** - Database
- **Laravel Sanctum** - API authentication
- **Qdrant** - Vector database for RAG

### AI Services
- **OpenAI GPT-4o-mini** - Content generation
- **Google Gemini 1.5 Pro** - Research engine
- **Hiker API** - Viral content analysis (optional)

### Frontend
- **Vite 5.0** - Build tool
- **Tailwind CSS 3.4** - Styling
- **Lucide** - Icons
- **Chart.js** - Data visualization
- **Axios** - HTTP client

### Infrastructure
- **Docker** - Containerization
- **Cloudinary** - Media storage
- **Redis** - Queue/caching
- **Heroku** - Deployment (or custom)

---

## Getting Started

### New Developers

1. Read [[01-Overview]] to understand the project
2. Follow [[07-Getting-Started]] for installation
3. Review [[02-Models]] to understand data structure
4. Study [[03-Services]] for business logic
5. Check [[06-API-Reference]] for available endpoints

### For Feature Development

1. Identify which [[04-Controllers]] handles the feature
2. Understand related [[03-Services]]
3. Check associated [[02-Models]]
4. Review [[05-Security]] for access control requirements

### For API Integration

1. Review [[06-API-Reference]] for endpoints
2. Understand authentication flow
3. Check rate limits
4. Review error responses

---

## Security Notes

⚠️ **Important Security Information**

- All tenant-scoped models automatically filter by `tenant_id` via `BelongsToTenant` trait
- Sensitive credentials stored encrypted in `Tenant.metadata`
- MFA required for admin roles
- All critical actions logged to `audit_logs`
- Rate limiting enforced on sensitive endpoints

See [[05-Security]] for complete security documentation.

---

## Multi-Tenancy

### Core Concept
Architect-AI uses row-level multi-tenancy to ensure complete data isolation between organizations.

### Implementation
- `BelongsToTenant` trait applies global scope
- User model intentionally NOT scoped (allows login)
- Sub-tenant support via `parent_id`
- Encrypted social media tokens

### Use Cases
- Agencies managing multiple client accounts
- Reseller/partner models
- Departmental separation within organizations

---

## AI Integration

### Content Generation
- **Primary Model**: OpenAI GPT-4o-mini
- **Fallback**: Configured alternative models
- **RAG**: Context from knowledge base and brand profile
- **Strategy Pattern**: Different generators for different content types

### Research Engine
- **Primary Model**: Google Gemini 1.5 Pro
- **Fallbacks**: Gemini 1.5 Flash, Gemini Pro
- **RAG**: Context from internal knowledge base
- **Fallback Chain**: Automatic model switching on errors

---

## Common Tasks

### Adding a New Content Type
1. Create generator in `app/Services/ContentGenerators/`
2. Extend `BaseContentGenerator`
3. Register in `ContentGeneratorFactory`
4. Add to frontend content type selector

### Adding New Permission
1. Add to `permissions` table
2. Assign to appropriate role
3. Check in controller middleware
4. Document in API reference

### Adding New Model
1. Create model: `php artisan make:model`
2. Add `BelongsToTenant` trait if tenant-scoped
3. Create migration
4. Add to `audit_logs` observer if needed

---

## Troubleshooting

### Common Issues
- **Authentication failures**: Check Sanctum token, verify tenant scope
- **Data not appearing**: Verify `BelongsToTenant` trait is applied
- **RAG not working**: Check Qdrant connection and collection exists
- **AI generation fails**: Verify API keys are configured and have credits
- **MFA not working**: Ensure Google Authenticator time is synced

### Debug Mode
Enable in `.env`:
```env
APP_DEBUG=true
```

View logs:
```bash
tail -f storage/logs/laravel.log
```

---

## Contributing

### Code Style
- Follow Laravel coding standards
- Use Laravel Pint for formatting
- Write tests for new features
- Update documentation for changes

### Git Workflow
1. Create feature branch from `main`
2. Make changes with atomic commits
3. Run tests: `php artisan test`
4. Run linting: `./vendor/bin/pint`
5. Submit pull request with description

---

## Glossary

- **Tenant**: Organization/account in the system
- **Sub-tenant**: Child organization (used by agencies)
- **RAG**: Retrieval-Augmented Generation - AI technique using external knowledge
- **RBAC**: Role-Based Access Control
- **ABAC**: Attribute-Based Access Control
- **TOTP**: Time-based One-Time Password (MFA)
- **Vector Database**: Database optimized for similarity search (Qdrant)
- **Blueprint**: Brand-specific template configuration

---

## External Resources

- [Laravel Documentation](https://laravel.com/docs)
- [OpenAI API](https://platform.openai.com/docs)
- [Google Gemini API](https://ai.google.dev/docs)
- [Qdrant Documentation](https://qdrant.tech/documentation/)
- [Tailwind CSS](https://tailwindcss.com/docs)

---

## Version

**Current Version**: 1.0.0
**Laravel Version**: 11.0
**PHP Version**: 8.2+
**Documentation Updated**: January 17, 2026

---

## License

MIT License - See project LICENSE file for details.

---

*This documentation is part of the Architect-AI project. For the most up-to-date information, always refer to the codebase and inline comments.*
