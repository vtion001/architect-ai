# Getting Started Guide

## Prerequisites

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **npm**: Latest version
- **Docker**: Optional, for development environment
- **PostgreSQL**: 14 or higher (or Docker)

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-org/architect-ai.git
cd architect-ai
```

### 2. Install Dependencies

**PHP Dependencies**:
```bash
composer install
```

**Node Dependencies**:
```bash
npm install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure the following:

```env
# App Configuration
APP_NAME="Architect-AI"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=architect_ai
DB_USERNAME=postgres
DB_PASSWORD=your_password

# OpenAI
OPENAI_API_KEY=sk-your-openai-key
OPENAI_MODEL=gpt-4o-mini

# Google Gemini
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-1.5-pro

# Qdrant (Vector Database)
QDRANT_HOST=http://localhost:6333
QDRANT_COLLECTION=architect_ai_kb

# Cloudinary
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret

# Optional: Hiker API (for viral examples)
HIKER_API_KEY=your-hiker-api-key

# Developer Account
DEVELOPER_EMAIL=developer@yourdomain.com
```

**Generate Application Key**:
```bash
php artisan key:generate
```

### 4. Database Setup

**Using Local PostgreSQL**:
```bash
createdb architect_ai
```

**Using Docker**:
```bash
docker-compose up -d
```

**Run Migrations**:
```bash
php artisan migrate
```

**Seed Database (Optional)**:
```bash
php artisan db:seed
```

### 5. Start Development Servers

**Laravel Server**:
```bash
php artisan serve
```
Access at: `http://localhost:8000`

**Vite Dev Server**:
```bash
npm run dev
```

### 6. Create Admin Account

**Via Artisan Command**:
```bash
php artisan tinker
```

```php
use App\Models\Tenant;
use App\Models\User;

$tenant = Tenant::create([
    'name' => 'Agency',
    'slug' => 'agency',
    'type' => 'agency',
    'status' => 'active'
]);

$user = User::create([
    'tenant_id' => $tenant->id,
    'email' => 'admin@example.com',
    'password' => bcrypt('SecurePassword123!')
]);
```

---

## Development Workflow

### Building Assets

**Development** (with hot reload):
```bash
npm run dev
```

**Production Build**:
```bash
npm run build
```

### Running Tests

**PHPUnit**:
```bash
php artisan test
```

**With Coverage**:
```bash
php artisan test --coverage
```

### Code Quality

**Laravel Pint (Code Styling)**:
```bash
./vendor/bin/pint
```

**Static Analysis**:
```bash
composer require larastan/larastan --dev
./vendor/bin/phpstan analyse
```

---

## Docker Setup (Optional)

### Using Docker Compose

**Start Services**:
```bash
docker-compose up -d
```

**Services Included**:
- PostgreSQL (database)
- Redis (queue/caching)
- Qdrant (vector database)

**Run Migrations**:
```bash
docker-compose exec app php artisan migrate
```

**View Logs**:
```bash
docker-compose logs -f
```

**Stop Services**:
```bash
docker-compose down
```

---

## Configuration

### Multi-Tenancy Setup

Tenant resolution is handled automatically via subdomain or slug:

**Subdomain Pattern**:
```
tenant.architect-ai.com
```

**Slug Pattern**:
```
architect-ai.com/tenant/tenant-name
```

### Qdrant Vector Database Setup

**Create Collection**:
```bash
php artisan tinker
```

```php
use App\Services\VectorService;

$vectorService = app(VectorService::class);
$vectorService->createCollection('architect_ai_kb', 1536);
```

**Parameters**:
- Collection name: `architect_ai_kb`
- Vector dimension: `1536` (OpenAI embeddings)

### MFA Configuration

MFA uses TOTP (Google Authenticator compatible).

**Enable for User**:
```bash
php artisan tinker
```

```php
$user = App\Models\User::find('uuid');
$user->update(['mfa_enabled' => true]);
```

---

## Common Commands

### Queue Management

**Process Queues**:
```bash
php artisan queue:work
```

**Queue in Background**:
```bash
php artisan queue:work --daemon &
```

**Clear Queued Jobs**:
```bash
php artisan queue:flush
```

### Cache Management

**Clear Application Cache**:
```bash
php artisan cache:clear
```

**Clear Configuration Cache**:
```bash
php artisan config:clear
```

**Clear View Cache**:
```bash
php artisan view:clear
```

**Clear Route Cache**:
```bash
php artisan route:clear
```

**Cache All**:
```bash
php artisan config:cache
php artisan route:cache
```

### Database Operations

**Create Migration**:
```bash
php artisan make:migration create_table_name
```

**Run Migrations**:
```bash
php artisan migrate
```

**Rollback Last Migration**:
```bash
php artisan migrate:rollback
```

**Reset Database**:
```bash
php artisan migrate:fresh
```

### Model Generation

**Create Model with Migration**:
```bash
php artisan make:model ModelName -m
```

**Create Controller**:
```bash
php artisan make:controller ControllerName
```

**Create Service**:
```bash
php artisan make:service ServiceName
```

---

## Troubleshooting

### Common Issues

**Port Already in Use**:
```bash
# Change port
php artisan serve --port=8001
```

**Migration Errors**:
```bash
# Rollback and retry
php artisan migrate:rollback
php artisan migrate
```

**NPM Install Errors**:
```bash
# Clear cache
npm cache clean --force
rm -rf node_modules
rm package-lock.json
npm install
```

**Composer Dependency Issues**:
```bash
# Update lock file
composer update
```

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

## Production Deployment

### Preparation

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Generate app key: `php artisan key:generate`
4. Run migrations: `php artisan migrate --force`
5. Optimize: `php artisan config:cache && php artisan route:cache`
6. Build assets: `npm run build`

### Environment Variables Required for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_* (production database credentials)

# API Keys
OPENAI_API_KEY=...
GEMINI_API_KEY=...
QDRANT_* (production Qdrant instance)
CLOUDINARY_* (production Cloudinary account)

# Developer Email
DEVELOPER_EMAIL=your-email@example.com
```

### Deployment Platforms

**Heroku**:
```bash
heroku create your-app-name
git push heroku main
heroku run php artisan migrate
```

**AWS/Azure/GCP**:
- Deploy to appropriate platform (ECS, App Service, Cloud Run)
- Configure environment variables
- Set up managed PostgreSQL
- Set up managed Qdrant instance

---

## IDE Recommendations

### VS Code Extensions

- **PHP Intelephense** - PHP intelligence
- **Laravel Extra Intellisense** - Laravel specific features
- **Tailwind CSS IntelliSense** - Tailwind autocompletion
- **Blade Formatter** - Blade formatting
- **Prettier** - Code formatting

### Recommended VS Code Settings

```json
{
  "php.validate.executablePath": "/usr/bin/php",
  "emmet.includeLanguages": {
    "blade": "html"
  },
  "files.associations": {
    "*.blade.php": "blade"
  }
}
```

---

## Learning Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Vite](https://vitejs.dev/guide/)

### Architecture
- Multi-Tenancy with Laravel
- Repository Pattern
- Service Layer Architecture
- RAG (Retrieval-Augmented Generation)

### AI Integration
- [OpenAI API](https://platform.openai.com/docs)
- [Google Gemini API](https://ai.google.dev/docs)
- [Qdrant Vector Database](https://qdrant.tech/documentation/)

---

## Support

For issues or questions:
- Check existing GitHub issues
- Review documentation files in `/docs` or root directory
- Contact development team

---

**Documentation Generated**: January 17, 2026
