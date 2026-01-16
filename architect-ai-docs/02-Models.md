# Models Reference

## Core Models

### Tenant
**Path**: `app/Models/Tenant.php`

Multi-tenant organization model with support for sub-tenants and encrypted metadata.

**Fields**:
- `type` - Tenant type
- `plan` - Subscription plan
- `plan_status` - Plan status (active, suspended, etc.)
- `parent_id` - Parent tenant ID (for sub-tenants)
- `name` - Tenant name
- `slug` - URL slug
- `status` - Tenant status
- `metadata` (array, encrypted) - Sensitive data (social tokens, API secrets)

**Relationships**:
- `users()` - Has many users
- `subAccounts()` - Has many sub-tenants
- `brands()` - Has many brands
- `parent()` - Belongs to parent tenant

**Features**:
- Auto-encryption of sensitive metadata fields
- UUID primary keys
- JSON metadata casting

---

### User
**Path**: `app/Models/User.php`

User authentication model with MFA support. Intentionally NOT scoped to tenant (to allow login).

**Fields**:
- `tenant_id` - Associated tenant
- `email` - User email
- `password` - Hashed password
- `status` - Account status
- `mfa_enabled` (boolean) - MFA enabled flag
- `mfa_secret` (hidden) - TOTP secret
- `last_login_at` (datetime) - Last login timestamp

**Relationships**:
- `tenant()` - Belongs to tenant
- `roles()` - Belongs to many with pivot data

**Methods**:
- `getIsDeveloperAttribute()` - Check if developer account

**Features**:
- Sanctum API tokens
- Email notifications
- Hashed passwords
- UUID primary keys

---

### Role
**Path**: `app/Models/Role.php`

Role definition for RBAC.

**Fields**:
- `name` - Role name
- `description` - Role description
- `is_system` (boolean) - System role flag

**Relationships**:
- `permissions()` - Belongs to many permissions
- `users()` - Belongs to many users

---

### Permission
**Path**: `app/Models/Permission.php`

Granular permission for RBAC.

**Fields**:
- `name` - Permission name
- `resource` - Resource identifier
- `action` - Action (create, read, update, delete)

**Relationships**:
- `roles()` - Belongs to many roles

---

### UserRole
**Path**: `app/Models/UserRole.php`

Pivot table for User-Role relationships with additional data.

**Pivot Fields**:
- `id` - Pivot ID
- `user_id` - User ID
- `role_id` - Role ID
- `scope_type` - Scope type (optional)
- `scope_id` - Scope ID (optional)
- `expires_at` - Role expiration

---

### Brand
**Path**: `app/Models/Brand.php`

Brand profile with customizable identity and content blueprints.

**Traits**: `BelongsToTenant`, `HasUuids`

**Fields**:
- `tenant_id` - Owner tenant
- `name` - Brand name
- `tagline` - Brand tagline
- `description` - Brand description
- `logo_url` - Logo URL
- `logo_public_id` - Cloudinary ID
- `favicon_url` - Favicon URL
- `colors` (array) - Color palette
- `typography` (array) - Typography settings
- `voice_profile` (array) - Brand voice guidelines
- `contact_info` (array) - Contact information
- `social_handles` (array) - Social media links
- `industry` - Industry sector
- `is_default` (boolean) - Default brand flag
- `blueprints` (array) - Content templates

**Relationships**:
- `tenant()` - Belongs to tenant

**Methods**:
- `getBlueprint(string $templateType)` - Get blueprint for template
- `defaultColors()` - Default color structure
- `defaultTypography()` - Default typography structure
- `defaultVoiceProfile()` - Default voice profile
- `getAIContext()` - Generate brand context for AI prompts

**Features**:
- Automatic merging with defaults
- JSON casting for array fields

---

## Content Models

### Content
**Path**: `app/Models/Content.php`

Generated content records.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `brand_id` - Associated brand
- `type` - Content type (post, blog, video, etc.)
- `topic` - Content topic
- `content` - Generated content
- `metadata` (array) - Additional data

---

### Research
**Path**: `app/Models/Research.php`

Research reports.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `topic` - Research topic
- `content` - Research findings
- `status` - Research status
- `metadata` (array) - Additional data

---

### Document
**Path**: `app/Models/Document.php`

Managed documents.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `brand_id` - Associated brand
- `name` - Document name
- `content` - Document content
- `template_id` - Template ID
- `status` - Document status
- `metadata` (array) - Additional data

---

### KnowledgeBaseAsset
**Path**: `app/Models/KnowledgeBaseAsset.php`

Knowledge base assets for RAG.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `name` - Asset name
- `type` - Asset type (document, text, etc.)
- `content` - Asset content
- `metadata` (array) - Additional data

---

## AI Models

### AiAgent
**Path**: `app/Models/AiAgent.php`

AI agent configurations.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `name` - Agent name
- `description` - Agent description
- `system_prompt` - System prompt
- `model` - AI model to use
- `temperature` - Sampling temperature
- `max_tokens` - Max tokens
- `metadata` (array) - Additional configuration

**Relationships**:
- `conversations()` - Has many conversations

---

### AgentConversation
**Path**: `app/Models/AgentConversation.php`

AI agent conversation history.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `agent_id` - Associated agent
- `user_id` - User who initiated
- `messages` (array) - Conversation messages
- `status` - Conversation status
- `metadata` (array) - Additional data

---

## Management Models

### Task
**Path**: `app/Models/Task.php`

Task management.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `category_id` - Task category
- `assigned_to` - Assigned user
- `title` - Task title
- `description` - Task description
- `status` - Task status
- `due_date` - Due date
- `metadata` (array) - Additional data

---

### TaskCategory
**Path**: `app/Models/TaskCategory.php`

Task categories.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `name` - Category name
- `color` - Category color
- `icon` - Category icon

---

### AccessPolicy
**Path**: `app/Models/AccessPolicy.php`

Access control policies.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `resource` - Resource identifier
- `action` - Allowed action
- `conditions` (array) - Policy conditions

---

### AuditLog
**Path**: `app/Models/AuditLog.php`

System audit trail.

**Fields**:
- `actor_type` - Actor model type
- `actor_id` - Actor ID
- `action` - Action performed
- `resource_type` - Resource model type
- `resource_id` - Resource ID
- `changes` (array) - Changed data
- `ip_address` - Request IP
- `user_agent` - User agent

---

## Utility Models

### MediaAsset
**Path**: `app/Models/MediaAsset.php`

Media registry.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `name` - Asset name
- `type` - Asset type
- `url` - Asset URL
- `public_id` - Cloudinary ID
- `metadata` (array) - Additional data

---

### TokenAllocation
**Path**: `app/Models/TokenAllocation.php`

Token quotas.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `limit` - Token limit
- `used` - Tokens used
- `reset_at` - Reset date

---

### TokenTransaction
**Path**: `app/Models/TokenTransaction.php`

Token usage tracking.

**Fields**:
- `tenant_id` - Owner tenant
- `amount` - Token amount
- `type` - Transaction type (usage, refill)
- `description` - Transaction description

---

### Invitation
**Path**: `app/Models/Invitation.php`

User invitations.

**Traits**: `BelongsToTenant`

**Fields**:
- `tenant_id` - Owner tenant
- `email` - Invitee email
- `role_id` - Role to assign
- `token` - Invitation token
- `expires_at` - Expiration date
- `accepted_at` - Acceptance date

---

### Waitlist
**Path**: `app/Models/Waitlist.php`

Waitlist entries.

**Fields**:
- `email` - Email address
- `metadata` (array) - Additional data

---

## Traits

### BelongsToTenant
**Path**: `app/Models/Traits/BelongsToTenant.php`

Global scope trait for tenant isolation.

**Functionality**:
- Automatically filters queries by tenant_id
- Prevents cross-tenant data access
- Applied to all tenant-scoped models

---

**Note**: All models use UUID primary keys via `HasUuids` trait unless otherwise specified.

---

**Documentation Generated**: January 17, 2026
