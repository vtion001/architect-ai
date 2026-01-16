# Services Reference

## Core Services

### AuthorizationService
**Path**: `app/Services/AuthorizationService.php`

Handles role-based and attribute-based access control.

**Methods**:
- `can(User $user, string $permission, ?Model $resource = null)` - Check permission
- `canImpersonate(User $impersonator, User $target)` - Check impersonation eligibility
- `isTenantAdmin(User $user)` - Check if user is tenant admin
- `assignRole(User $user, Role $role, ?array $scope = null)` - Assign role with optional scope
- `hasPermission(User $user, string $permission)` - Check user permissions

**Features**:
- RBAC/ABAC hybrid model
- Scope-based role assignments
- Permission inheritance
- Tenant isolation

---

### BrandResolverService
**Path**: `app/Services/BrandResolverService.php`

Resolves active brand for content generation.

**Methods**:
- `resolve(User $user, ?string $brandId = null)` - Resolve brand from request or user default
- `getDefaultBrand(User $user)` - Get user's default brand
- `validateBrandAccess(User $user, Brand $brand)` - Validate user can access brand

**Features**:
- Request parameter fallback
- User default brand
- Access validation

---

### CloudinaryService
**Path**: `app/Services/CloudinaryService.php`

Handles media upload and management via Cloudinary.

**Methods**:
- `upload(UploadedFile $file, array $options = [])` - Upload file
- `uploadFromUrl(string $url, array $options = [])` - Upload from URL
- `delete(string $publicId)` - Delete asset
- `transform(string $publicId, array $transformations)` - Transform image
- `getAssets(?string $folder = null)` - List assets

**Features**:
- Automatic optimization
- Responsive transformations
- Folder organization
- Public ID management

---

## Content Generation Services

### ContentService
**Path**: `app/Services/ContentService.php`

Main content generation service with RAG integration.

**Dependencies**:
- `ContentGeneratorFactory` - Factory for content generators
- `KnowledgeBaseService` - RAG context provider

**Methods**:
- `generateText(string $topic, string $type, ?string $context = null, array $options = [])` - Generate content
- `generateWithBrand(string $topic, string $type, Brand $brand, array $options = [])` - Generate with brand context

**Content Types**:
- Social media posts
- Blog posts
- Video scripts
- Framework calendars

**Features**:
- RAG integration with knowledge base
- Viral post example analysis (Hiker API)
- Brand context injection
- Generator strategy pattern

---

### ContentGenerators

#### BaseContentGenerator
**Path**: `app/Services/ContentGenerators/BaseContentGenerator.php`

Abstract base class for content generators.

**Methods**:
- `generate(string $topic, ?string $context, array $options)` - Abstract generate method
- `buildSystemPrompt(Brand $brand)` - Build system prompt from brand
- `buildUserPrompt(string $topic, ?string $context, array $options)` - Build user prompt
- `callOpenAI(array $messages, array $options)` - Call OpenAI API

---

#### SocialPostGenerator
**Path**: `app/Services/ContentGenerators/SocialPostGenerator.php`

Generates social media posts with viral examples.

**Methods**:
- `generate(string $topic, ?string $context, array $options)` - Generate social post
- `injectViralExamples(string $prompt, array $examples)` - Inject viral post examples
- `getViralPosts(string $topic)` - Fetch viral posts from Hiker API

**Features**:
- Platform-specific formatting
- Hashtag optimization
- Viral content analysis
- Engagement optimization

---

#### BlogPostGenerator
**Path**: `app/Services/ContentGenerators/BlogPostGenerator.php`

Generates blog posts with SEO structure.

**Methods**:
- `generate(string $topic, ?string $context, array $options)` - Generate blog post
- `buildSeoStructure(string $topic, string $content)` - Add SEO structure

---

#### VideoScriptGenerator
**Path**: `app/Services/ContentGenerators/VideoScriptGenerator.php`

Generates video scripts with scene breakdowns.

**Methods**:
- `generate(string $topic, ?string $context, array $options)` - Generate video script
- `formatScenes(string $content)` - Format content into scenes

---

#### FrameworkCalendarGenerator
**Path**: `app/Services/ContentGenerators/FrameworkCalendarGenerator.php`

Generates content calendars.

**Methods**:
- `generate(string $topic, ?string $context, array $options)` - Generate calendar
- `buildCalendarStructure(array $topics)` - Build calendar structure

---

## Research Services

### ResearchService
**Path**: `app/Services/ResearchService.php`

Performs deep research using Google Gemini API.

**Dependencies**:
- `KnowledgeBaseService` - RAG context provider

**Methods**:
- `performResearch(string $topic)` - Perform research on topic
- `attemptResearchWithModel(string $model, string $enhancedTopic)` - Try specific model

**Models (fallback order)**:
1. `gemini-1.5-pro` (configured default)
2. `gemini-1.5-flash`
3. `gemini-pro`

**Features**:
- RAG integration
- Model fallback
- Comprehensive reports
- Source citations

---

### ReportService
**Path**: `app/Services/ReportService.php`

Generates various types of reports.

**Methods**:
- `generateContentReport(Tenant $tenant, array $filters)` - Generate content performance report
- `generateUsageReport(Tenant $tenant, DatePeriod $period)` - Generate usage report
- `generateTokenReport(Tenant $tenant, DatePeriod $period)` - Generate token consumption report
- `exportToPdf(string $reportId)` - Export report to PDF

**Features**:
- Custom report builder
- Date range filtering
- Multiple export formats
- Chart generation

---

## Knowledge Base Services

### KnowledgeBaseService
**Path**: `app/Services/KnowledgeBaseService.php`

Manages knowledge base assets for RAG.

**Dependencies**:
- `VectorService` - Qdrant integration

**Methods**:
- `addAsset(KnowledgeBaseAsset $asset)` - Add asset to knowledge base
- `removeAsset(string $assetId)` - Remove asset
- `search(string $query, int $limit = 5)` - Semantic search
- `getContext(string $topic)` - Get context for AI generation

**Features**:
- Vector embeddings via Qdrant
- Semantic search
- Context retrieval
- Asset management

---

### VectorService
**Path**: `app/Services/VectorService.php`

Handles vector operations with Qdrant.

**Methods**:
- `createCollection(string $name)` - Create vector collection
- `insertVector(string $collection, array $vector, array $payload)` - Insert vector
- `searchVectors(string $collection, array $queryVector, int $limit)` - Search similar vectors
- `deleteVector(string $collection, string $id)` - Delete vector

**Features**:
- Qdrant integration
- Embedding generation
- Similarity search
- Collection management

---

### PdfToTextService
**Path**: `app/Services/PdfToTextService.php`

Extracts text from PDF files.

**Methods**:
- `extractText(string $filePath)` - Extract text from PDF
- `extractTextFromUrl(string $url)` - Download and extract from URL

**Features**:
- PDF parsing
- Text extraction
- URL download support

---

## Social Media Services

### SocialPublishingService
**Path**: `app/Services/SocialPublishingService.php`

Handles social media publishing and scheduling.

**Methods**:
- `publishToInstagram(Brand $brand, string $content, string $mediaUrl)` - Publish to Instagram
- `publishToFacebook(Brand $brand, string $content, ?string $mediaUrl)` - Publish to Facebook
- `publishToLinkedIn(Brand $brand, string $content)` - Publish to LinkedIn
- `schedulePost(Brand $brand, array $platforms, string $content, DateTime $scheduleDate)` - Schedule post

**Features**:
- Multi-platform support
- Encrypted access tokens
- Scheduling
- Media upload

---

## Utility Services

### TokenService
**Path**: `app/Services/TokenService.php`

Manages token allocation and tracking.

**Methods**:
- `checkLimit(Tenant $tenant, int $required)` - Check if tenant has tokens
- `consume(Tenant $tenant, int $amount, string $description)` - Consume tokens
- `allocate(Tenant $tenant, int $amount)` - Allocate tokens
- `getUsage(Tenant $tenant, DatePeriod $period)` - Get usage statistics

**Features**:
- Token quota management
- Usage tracking
- Monthly resets
- Transaction logging

---

## Service Factories

### ContentGeneratorFactory
**Path**: `app/Services/Factories/ContentGeneratorFactory.php`

Factory for creating content generators.

**Methods**:
- `make(string $type)` - Create generator instance

**Supported Types**:
- `post` → `SocialPostGenerator`
- `blog` → `BlogPostGenerator`
- `video` → `VideoScriptGenerator`
- `calendar` → `FrameworkCalendarGenerator`

---

**Documentation Generated**: January 17, 2026
