# Security & Multi-Tenancy

## Multi-Tenancy Architecture

### Overview

Architect-AI implements strict multi-tenancy to ensure complete data isolation between organizations (tenants) while supporting sub-tenant hierarchies for agencies.

### Tenant Isolation Model

#### Row-Level Security
All tenant-scoped models use the `BelongsToTenant` trait which applies a global scope:

```php
// app/Models/Traits/BelongsToTenant.php
protected static function bootBelongsToTenant()
{
    static::addGlobalScope('tenant', function ($query) {
        if (auth()->check()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }
    });
}
```

**Applied to Models**:
- Brand
- Content
- Research
- Document
- KnowledgeBaseAsset
- Task
- TaskCategory
- MediaAsset
- TokenAllocation
- AccessPolicy
- And more...

**Exception**: The `User` model intentionally does NOT use `BelongsToTenant` to allow authentication across tenants.

#### Tenant Hierarchy

```
Parent Tenant (Agency)
├── Sub-tenant 1 (Client A)
├── Sub-tenant 2 (Client B)
└── Sub-tenant 3 (Client C)
```

**Model Structure**:
- `Tenant.parent_id` → Links to parent tenant
- `Tenant.subAccounts()` → HasMany sub-tenants
- `Tenant.parent()` → BelongsTo parent tenant

**Use Cases**:
- Agencies managing multiple client accounts
- Reseller/partner models
- Departmental separation

---

## Identity & Access Management (IAM)

### RBAC/ABAC Hybrid Model

Architect-AI combines Role-Based Access Control (RBAC) with Attribute-Based Access Control (ABAC) for flexible permissions.

### Role System

#### Core Roles
1. **Super Admin** - Platform-wide access (developer only)
2. **Agency Admin** - Full tenant management
3. **Content Creator** - Generate and edit content
4. **Researcher** - Access research tools
5. **Viewer** - Read-only access

#### Role Assignment with Scope

```php
// Assign role with optional scope (ABAC)
$authService->assignRole($user, $role, [
    'scope_type' => 'brand',
    'scope_id' => $brandId
]);
```

**Scope Types**:
- `null` - Tenant-wide access
- `brand` - Limited to specific brand
- `department` - Department-specific access

### Permission System

#### Permission Format
```
{resource}.{action}
```

**Examples**:
- `content.create` - Create content
- `content.update` - Update content
- `content.delete` - Delete content
- `brands.manage` - Manage brands
- `users.manage` - Manage users

#### Permission Inheritance
```
Agency Admin
├── All content permissions
├── All brand permissions
├── All user management permissions
└── All analytics permissions

Content Creator
├── content.create
├── content.update
├── content.delete
└── content.read
```

### Developer Access

**Developer Email**: Configured via `DEVELOPER_EMAIL` environment variable

**Capabilities**:
- Platform-wide access to all tenants
- User impersonation (with audit logging)
- View-only tenant administration
- System configuration access

**Impersonation Flow**:
1. Developer selects user to impersonate
2. System validates impersonation eligibility
3. Session stores original developer ID
4. All actions logged as impersonation
5. Can revert to original identity at any time

---

## Authentication & Security

### Multi-Factor Authentication (MFA)

#### Implementation
- **Algorithm**: TOTP (Time-based One-Time Password)
- **Package**: `pragmarx/google2fa-laravel`
- **Requirement**: Mandatory for Agency Admin and Super Admin roles

#### MFA Flow

**Setup**:
1. User navigates to `/auth/mfa/setup`
2. System generates TOTP secret
3. QR code displayed for authenticator app
4. User enters verification code
5. MFA enabled and secret stored encrypted

**Challenge**:
1. User logs in with email/password
2. Redirected to `/auth/mfa/challenge`
3. User enters 6-digit TOTP code
4. System validates against stored secret
5. Access granted if valid

**Rate Limiting**:
- 5 attempts per 15 minutes
- Automatic temporary lockout after failures

### Session Security

#### Session Configuration
```php
// config/session.php
'lifetime' => 120,        // 2 hours
'expire_on_close' => false,
'encrypt' => true,
```

#### Role-Based Timeouts
- **Super Admin**: 1 hour
- **Agency Admin**: 2 hours
- **Content Creator**: 4 hours
- **Viewer**: 8 hours

#### Concurrent Session Limits
- Super Admin: 1 concurrent session
- Agency Admin: 3 concurrent sessions
- Other roles: Unlimited

### Password Policy

#### NIST 800-63B Compliant
- **Minimum Length**: 12 characters
- **Complexity**: No mandatory special characters (NIST recommendation)
- **Blacklist**: Common passwords blocked
- **Password Hashing**: Laravel's default (bcrypt with automatic rehashing)

#### Password Reset Flow
1. User requests reset via `/auth/forgot-password`
2. Token generated and sent via email
3. Token expires after 1 hour
4. User sets new password via secure link
5. Old passwords invalidated

---

## Data Protection

### Encryption

#### In Transit
- **Protocol**: TLS 1.3 enforced
- **Implementation**: ngrok for development, HTTPS for production
- **Ciphers**: Strong cipher suite (TLS_AES_256_GCM_SHA384)

#### At Rest (Metadata)
Sensitive fields in `Tenant.metadata` are automatically encrypted:

```php
protected function metadata(): Attribute
{
    $sensitiveFields = [
        'facebook_access_token',
        'instagram_access_token',
        'linkedin_access_token',
        'api_secret',
        'custom_app_secret'
    ];

    // Auto-encrypt on save, auto-decrypt on retrieve
}
```

**Encryption Method**: Laravel's `Crypt::encryptString()`
**Algorithm**: AES-256-CBC

### CSRF Protection

- **Token**: Included in all forms
- **Verification**: Automatic on POST/PUT/DELETE requests
- **Exemption**: Only API endpoints with Sanctum authentication

### Secure Headers

#### Content Security Policy (CSP)
```
default-src 'self' cdnjs.cloudflare.com;
script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com;
style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com;
img-src 'self' data: https: res.cloudinary.com;
font-src 'self' cdnjs.cloudflare.com;
```

#### Other Headers
- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `Strict-Transport-Security: max-age=31536000`
- `Referrer-Policy: strict-origin-when-cross-origin`

---

## Auditing & Logging

### Audit Log Model

**Path**: `app/Models/AuditLog.php`

**Logged Fields**:
- `actor_type` - Who performed the action (User type)
- `actor_id` - Actor's ID
- `action` - Action performed (create, update, delete, impersonate, etc.)
- `resource_type` - Resource affected (Brand, Content, etc.)
- `resource_id` - Resource's ID
- `changes` - JSON diff of changes
- `ip_address` - Request IP address
- `user_agent` - Browser/user agent
- `created_at` - Timestamp

**Tracked Actions**:
- All CRUD operations on tenant data
- Role assignments and permission changes
- MFA enable/disable
- Impersonation start/stop
- Login attempts (success/failure)
- Token allocations and consumption

### Observers

Model observers automatically log changes:

```php
// app/Observers/BrandObserver.php
public function updated(Brand $brand)
{
    AuditLog::create([
        'actor_type' => get_class(auth()->user()),
        'actor_id' => auth()->id(),
        'action' => 'update',
        'resource_type' => get_class($brand),
        'resource_id' => $brand->id,
        'changes' => $brand->getChanges()
    ]);
}
```

### Developer Observability

Developers have full view access to all audit logs across tenants for:
- Security incident investigation
- Usage pattern analysis
- Compliance verification
- Debugging production issues

---

## Threat Mitigations

### Rate Limiting

#### Authentication Endpoints
- Login: 5 attempts per 15 minutes
- Agency Registration: 3 attempts per hour
- MFA Verification: 5 attempts per 15 minutes

#### API Endpoints
- Content Generation: 20 requests per minute per tenant
- Research: 5 requests per minute per tenant
- Global Search: 30 requests per minute per tenant

#### Enforcement
```php
// routes/api.php
Route::middleware('throttle:5,15')->post('login', ...);
Route::middleware('throttle:20,1')->post('/content/generate', ...);
```

### Anomaly Detection

Automatic suspension triggers:
- **Failed Login Attempts**: 10 consecutive failures → 1-hour lockout
- **Unauthorized API Requests**: 100+ per hour → Account review
- **Token Consumption**: Exceeds 200% of average usage → Alert

### SQL Injection Protection
- Laravel ORM parameter binding (no raw queries)
- Input validation on all controllers
- Type casting on model fields

### XSS Protection
- Laravel's automatic escaping in Blade templates
- Content Security Policy (CSP)
- Sanitization of user-generated content

---

## Compliance

### GDPR (General Data Protection Regulation)

#### Data Access Rights
- **Right to Access**: Users can request data export
- **Right to Rectification**: Users can update their data
- **Right to Erasure**: Complete tenant deletion protocol

#### Data Deletion Protocol
1. Tenant requests deletion via API
2. System validates requester has authorization
3. All associated data cascade deleted:
   - Users and their sessions
   - Brands and all brand data
   - Content, documents, research
   - Knowledge base assets
   - Audit logs (retained for 90 days for compliance)
4. Confirmation email sent

#### Data Portability
- Export all tenant data in JSON format
- Include all brands, content, and settings
- Exclude sensitive credentials (tokens, secrets)

### CCPA (California Consumer Privacy Act)

#### Consumer Rights
- Right to know what data is collected
- Right to delete personal information
- Right to opt-out of data sale (not applicable - no data sold)

#### Compliance
- Privacy policy accessible
- Clear data deletion process
- No sale of personal data

### SOC 2 Type II (Planned)

**Planned Controls**:
- Encryption at rest (database level)
- Access review process
- Change management logs
- Incident response procedures

---

## Security Best Practices

### Implemented ✓
- Multi-tenant data isolation
- Role-based access control
- Multi-factor authentication for admins
- Encrypted sensitive metadata
- TLS 1.3 enforced
- CSRF protection
- Rate limiting
- Audit logging
- Secure headers (CSP, HSTS)
- Developer impersonation with logging

### Planned 🔜
- Encryption at rest (database level)
- 2FA for all users (not just admins)
- API key rotation automation
- Security headers enhancement
- Penetration testing
- Bug bounty program
- SOC 2 Type II certification

---

## Developer Security Guidelines

### When Working with This Codebase

1. **Never** expose `tenant_id` in client-side responses
2. **Always** use `BelongsToTenant` trait for new tenant-scoped models
3. **Never** store secrets in plain text - use `Tenant.metadata` with encryption
4. **Always** validate and sanitize user input
5. **Always** use parameter binding - no raw SQL queries
6. **Always** log sensitive actions to audit trail
7. **Never** expose internal errors to users
8. **Always** use HTTPS in production
9. **Never** commit `.env` files or secrets to repository
10. **Always** review audit logs for suspicious activity

---

**Reference**: See `SECURITY.md` in project root for additional details.

---

**Documentation Generated**: January 17, 2026
