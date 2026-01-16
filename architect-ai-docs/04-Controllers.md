# Controllers Reference

## Auth Controllers

### AuthController
**Path**: `app/Http/Controllers/Auth/AuthController.php`

Handles authentication flows.

**Methods**:
- `registerAgency(Request $request)` - Register new agency tenant
- `login(Request $request)` - User login
- `logout()` - User logout

**Middleware**:
- `throttle:3,60` on register-agency (3 attempts per hour)
- `throttle:5,15` on login (5 attempts per 15 minutes)

---

### MfaController
**Path**: `app/Http/Controllers/Auth/MfaController.php`

Handles Multi-Factor Authentication.

**Methods**:
- `challenge()` - Show MFA challenge page
- `verify(Request $request)` - Verify TOTP code
- `setup()` - Show MFA setup page
- `enable(Request $request)` - Enable MFA for user
- `disable()` - Disable MFA for user

**Middleware**:
- `auth` - Must be logged in
- `throttle:5,15` on verify (5 attempts per 15 minutes)

**Features**:
- TOTP using Google Authenticator
- QR code generation
- Recovery codes (planned)

---

### InvitationController
**Path**: `app/Http/Controllers/Auth/InvitationController.php`

Handles user invitations.

**Methods**:
- `show(string $token)` - Show invitation page
- `accept(Request $request, string $token)` - Accept invitation
- `send(Request $request)` - Send invitation (tenant admin)

**Features**:
- Secure token generation
- Role assignment
- Email notifications

---

### DeveloperController
**Path**: `app/Http/Controllers/Auth/DeveloperController.php`

Developer tools and impersonation.

**Methods**:
- `impersonate(Request $request)` - Impersonate another user
- `stopImpersonating()` - Stop impersonation

**Middleware**:
- `can:is-developer` - Developer role required

**Features**:
- Secure impersonation
- Audit logging
- Original user preservation

---

## Core Controllers

### DashboardController
**Path**: `app/Http/Controllers/DashboardController.php`

Main dashboard controller.

**Methods**:
- `index()` - Show dashboard
- `stats()` - Get dashboard statistics

**Features**:
- Overview metrics
- Recent activity
- Quick actions

---

### LandingPageController
**Path**: `app/Http/Controllers/LandingPageController.php`

Public landing page.

**Methods**:
- `index()` - Show landing page
- `waitlist()` - Show waitlist page
- `joinWaitlist(Request $request)` - Add to waitlist

---

## Content Controllers

### ContentCreatorController
**Path**: `app/Http/Controllers/ContentCreatorController.php`

Handles content generation.

**Methods**:
- `index()` - Show content creator
- `store(Request $request)` - Generate content
- `history()` - Show generation history
- `regenerate(Request $request)` - Regenerate content

**Validation Rules**:
- `topic` - required|string|max:500
- `type` - required|in:post,blog,video,calendar
- `brand_id` - sometimes|exists:brands,id
- `context` - sometimes|string|max:5000
- `generator` - sometimes|in:post,blog,video,calendar

**Features**:
- Multiple content types
- Brand context injection
- History tracking
- RAG integration

---

### ResearchEngineController
**Path**: `app/Http/Controllers/ResearchEngineController.php`

Handles research requests.

**Methods**:
- `index()` - Show research interface
- `store(Request $request)` - Start research
- `show(Research $research)` - Show research result
- `export(Research $research)` - Export research

**Validation Rules**:
- `topic` - required|string|max:500
- `depth` - sometimes|in:basic,comprehensive,deep

**Features**:
- Deep research
- Multi-model fallback
- Export options
- Save to knowledge base

---

### SocialPlannerController
**Path**: `app/Http/Controllers/SocialPlannerController.php`

Handles social media planning and publishing.

**Methods**:
- `index()` - Show social planner
- `store(Request $request)` - Create scheduled post
- `publishNow(Request $request)` - Publish immediately
- `update(Request $request, int $id)` - Update scheduled post
- `destroy(int $id)` - Delete scheduled post

**Validation Rules**:
- `platforms` - required|array|min:1
- `content` - required|string|max:5000
- `media_url` - sometimes|url
- `scheduled_at` - sometimes|date|after:now

**Supported Platforms**:
- Instagram
- Facebook
- LinkedIn

---

## Document Controllers

### DocumentBuilderController
**Path**: `app/Http/Controllers/DocumentBuilderController.php`

Handles document creation and editing.

**Methods**:
- `index()` - Show document builder
- `create()` - Create new document
- `store(Request $request)` - Save document
- `edit(Document $document)` - Edit document
- `update(Request $request, Document $document)` - Update document
- `destroy(Document $document)` - Delete document
- `preview(Document $document)` - Preview document

**Validation Rules**:
- `name` - required|string|max:255
- `content` - required
- `brand_id` - sometimes|exists:brands,id
- `template_id` - sometimes|exists:templates,id

**Features**:
- Template support
- Brand context
- Live preview
- Version history

---

### DocumentsController
**Path**: `app/Http/Controllers/DocumentsController.php`

Document management.

**Methods**:
- `index()` - List documents
- `show(Document $document)` - Show document
- `download(Document $document)` - Download document

---

## Knowledge Base Controllers

### KnowledgeBaseController
**Path**: `app/Http/Controllers/KnowledgeBaseController.php`

Handles knowledge base management.

**Methods**:
- `index()` - Show knowledge base
- `store(Request $request)` - Add asset
- `show(KnowledgeBaseAsset $asset)` - Show asset
- `update(Request $request, KnowledgeBaseAsset $asset)` - Update asset
- `destroy(KnowledgeBaseAsset $asset)` - Delete asset
- `search(Request $request)` - Search assets

**Validation Rules**:
- `name` - required|string|max:255
- `type` - required|in:document,text,url
- `content` - required_if:type,text|string
- `file` - required_if:type,document|file
- `url` - required_if:type,url|url

**Features**:
- File upload
- URL import
- Vector indexing
- Semantic search

---

## Brand Controllers

### BrandController
**Path**: `app/Http/Controllers/BrandController.php`

Handles brand management.

**Methods**:
- `index()` - List brands
- `create()` - Create brand form
- `store(Request $request)` - Save brand
- `edit(Brand $brand)` - Edit brand
- `update(Request $request, Brand $brand)` - Update brand
- `destroy(Brand $brand)` - Delete brand
- `setAsDefault(Brand $brand)` - Set as default brand
- `blueprint(Brand $brand, string $type)` - Get blueprint

**Validation Rules**:
- `name` - required|string|max:255
- `tagline` - sometimes|string|max:255
- `description` - sometimes|string
- `colors` - sometimes|array
- `typography` - sometimes|array
- `voice_profile` - sometimes|array
- `blueprints` - sometimes|array

**Features**:
- Brand customization
- Voice profiles
- Color/typography management
- Blueprint configuration

---

## Task Controllers

### TaskController
**Path**: `app/Http/Controllers/TaskController.php`

Handles task management.

**Methods**:
- `index()` - List tasks
- `store(Request $request)` - Create task
- `update(Request $request, Task $task)` - Update task
- `destroy(Task $task)` - Delete task
- `updateStatus(Request $request, Task $task)` - Update task status
- `assign(Request $request, Task $task)` - Assign task

**Validation Rules**:
- `title` - required|string|max:255
- `description` - sometimes|string
- `category_id` - sometimes|exists:task_categories,id
- `assigned_to` - sometimes|exists:users,id
- `due_date` - sometimes|date

---

## Analytics Controllers

### AnalyticsController
**Path**: `app/Http/Controllers/AnalyticsController.php`

Analytics and reporting.

**Methods**:
- `index()` - Show analytics dashboard
- `content()` - Content performance
- `usage()` - Usage statistics
- `tokens()` - Token consumption

---

## AI Agent Controllers

### AiAgentController
**Path**: `app/Http/Controllers/AiAgentController.php`

Handles AI agent configuration and conversations.

**Methods**:
- `index()` - List agents
- `create()` - Create agent form
- `store(Request $request)` - Save agent
- `show(AiAgent $agent)` - Show agent details
- `edit(AiAgent $agent)` - Edit agent
- `update(Request $request, AiAgent $agent)` - Update agent
- `destroy(AiAgent $agent)` - Delete agent
- `chat(AiAgent $agent)` - Start chat
- `message(Request $request, AiAgent $agent)` - Send message to agent

**Validation Rules**:
- `name` - required|string|max:255
- `description` - sometimes|string
- `system_prompt` - sometimes|string
- `model` - sometimes|string
- `temperature` - sometimes|numeric|min:0|max:2

**Features**:
- Custom AI agents
- Conversation history
- Model configuration
- Prompt engineering

---

## Utility Controllers

### MediaRegistryController
**Path**: `app/Http/Controllers/MediaRegistryController.php`

Media asset management.

**Methods**:
- `index()` - List media assets
- `store(Request $request)` - Upload asset
- `show(MediaAsset $asset)` - Show asset
- `destroy(MediaAsset $asset)` - Delete asset

---

### ReportBuilderController
**Path**: `app/Http/Controllers/ReportBuilderController.php`

Custom report builder.

**Methods**:
- `index()` - Show report builder
- `build(Request $request)` - Generate custom report
- `preview(Request $request)` - Preview report

---

### GlobalSearchController
**Path**: `app/Http/Controllers/GlobalSearchController.php`

Global search across resources.

**Methods**:
- `index(Request $request)` - Perform search

**Searchable Resources**:
- Content
- Documents
- Knowledge base assets
- Tasks
- Brands

---

### GodViewController
**Path**: `app/Http/Controllers/GodViewController.php`

Developer view for platform administration.

**Methods**:
- `index()` - Show all tenants
- `showTenant(Tenant $tenant)` - Show tenant details
- `showUser(User $user)` - Show user details

**Middleware**:
- `can:is-developer`

---

## Admin Controllers

### Admin Controllers
**Path**: `app/Http/Controllers/Admin/`

Tenant administration controllers.

- `TenantController` - Tenant management
- `UserController` - User management
- `SystemSettingsController` - Platform settings

---

## Controller Middleware Stack

### Common Middleware Applied:
1. `auth:sanctum` - Sanctum authentication
2. `tenant` - Tenant scope resolution
3. `session_security` - Session timeout check
4. `can:permission` - Permission check (where applicable)

### Rate Limiting:
- Auth endpoints: 5 attempts per 15 minutes
- Agency registration: 3 attempts per hour
- MFA verification: 5 attempts per 15 minutes

---

**Documentation Generated**: January 17, 2026
