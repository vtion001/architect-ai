---
trigger: always_on
---

# Laravel Project Rules (AI-Safe • MAXIMIZED Context ~12,000 chars)

This document is an **authoritative rulebook** for any Laravel project developed with AI assistance (Gemini Flash 3.0 in Antigravity IDE).

It is intentionally verbose. Repetition is deliberate. Context density improves AI compliance.

This is NOT a tutorial.
This is NOT optional.

Primary objectives:

* Enforce senior-level Laravel architecture
* Prevent AI hallucinations and anti-patterns
* Guarantee maintainability, scalability, and correctness

If a choice exists between **fast** and **correct** → ALWAYS choose **correct**.

---

## 1. Framework, Language, and Runtime Constraints

### 1.1 Laravel Version Policy

* Laravel **11.x or newer** is mandatory
* Laravel 12.x is allowed ONLY if no breaking changes apply
* Never generate Laravel 9/10 syntax unless explicitly requested

### 1.2 PHP Version Policy

* PHP **8.3+ only**
* Strict typing required in all files
* Union types, readonly properties, and enums are encouraged

### 1.3 Node & Tooling

* Node.js **18+**
* Vite is the ONLY supported asset bundler
* No legacy Mix configuration

---

## 2. Global Engineering Laws (Applies Everywhere)

These laws override all other instructions.

1. Business logic NEVER lives in controllers
2. Validation NEVER lives inline
3. Database writes ALWAYS use transactions
4. Arrays are forbidden for business data (use DTOs)
5. Magic strings are forbidden (use Enums)
6. Silent failures are forbidden
7. If context is missing → STOP and ASK

Breaking any of these is a critical failure.

---

## 3. Architectural Doctrine

### 3.1 Laravel MVC (Strict Interpretation)

Laravel provides MVC, but default MVC is insufficient for real systems.

**Controllers are transport layers only.**

Controllers MAY:

* Accept HTTP requests
* Resolve dependencies
* Call Services or Actions
* Return Responses

Controllers may NOT:

* Contain business rules
* Perform calculations
* Manage transactions
* Handle authorization logic directly
* Call external APIs

If a controller requires explanation → it is already wrong.

---

### 3.2 Single Responsibility Principle (SRP)

Every class must have **one reason to change**.

Indicators of SRP violation:

* Class name contains “And”, “Or”, or “Manager”
* File exceeds ~200 lines
* Method exceeds ~40 lines

When SRP is violated:

* Extract Actions
* Extract Services
* Extract DTOs

---

## 4. Canonical Directory Structure (Mandatory)

```
app/
├── Actions/          # Atomic reusable operations
├── Console/
├── DTOs/             # Typed data objects
├── Enums/            # Domain constants
├── Exceptions/       # Domain exceptions
├── Http/
│   ├── Controllers/  # HTTP orchestration only
│   ├── Middleware/
│   └── Requests/     # Validation & authorization
├── Jobs/             # Queued / async work
├── Models/           # Eloquent models
├── Policies/         # Authorization rules
├── Providers/
├── Services/         # Business logic core
└── Support/          # Helpers, traits, utilities
```

### 4.1 Forbidden Locations for Business Logic

Business logic must NEVER exist in:

* routes files
* controllers
* blade templates
* config files
* middleware

---

## 5. Models (Fat but Disciplined)

Models represent **state and relations**, not workflows.

### 5.1 Allowed in Models

* Relationships
* Query scopes
* Attribute casting
* Accessors & mutators

### 5.2 Forbidden in Models

* Multi-step workflows
* Business decisions
* External API calls
* Database transactions

### 5.3 Mandatory Model Rules

* `$fillable` MUST be defined (never `$guarded = []`)
* Relationships MUST be type-hinted
* Use modern `casts()` method

Models must remain predictable and side-effect free.

---

## 6. Controllers (Skinny by Law)

Controllers are **entry points only**.

### 6.1 Controller Rules

* No `new` keyword allowed
* Constructor dependency injection only
* Max ~30 lines per method
* Zero conditionals beyond basic branching

Controller flow MUST be:

1. Validate (FormRequest)
2. Execute (Service)
3. Respond

Anything else belongs elsewhere.

---

## 7. Validation (FormRequests Only)

### 7.1 Mandatory Usage

* Every write endpoint MUST use FormRequest
* Inline validation is forbidden

### 7.2 Responsibilities

* `authorize()` handles permissions ONLY
* `rules()` handles validation ONLY

FormRequests must NOT:

* Contain business logic
* Perform calculations
* Modify incoming data

---

## 8. DTOs (Data Transfer Objects)

DTOs replace associative arrays.

### 8.1 Why DTOs Are Mandatory

* Prevent hidden coupling
* Improve IDE autocomplete
* Enforce data contracts
* Reduce runtime errors

### 8.2 DTO Rules

* `readonly`
* Strict typing
* No setters
* No logic

DTOs are immutable by design.

---

## 9. Services (Business Logic Core)

Services are the **heart of the application**.

### 9.1 Service Responsibilities

* Enforce business rules
* Coordinate models and actions
* Control transactions

### 9.2 Service Rules

* Typed inputs and outputs
* All DB writes inside transactions
* Explicit error handling with try/catch

Services must NOT:

* Return HTTP responses
* Depend on Request objects

---

## 10. Actions (Atomic Operations)

Actions represent **one thing done well**.

Use Actions when:

* Logic is reused across services
* Logic is conceptually atomic

Actions should be:

* Small
* Deterministic
* Side-effect aware

---

## 11. Database Rules

### 11.1 Migrations

* Every migration MUST be reversible
* Proper indexing is mandatory
* Foreign keys must be enforced

### 11.2 Transactions

* All writes MUST be wrapped in transactions
* Partial state is forbidden

---

## 12. Eloquent & Performance Doctrine

### 12.1 Performance Rules

* Always prevent N+1 queries (`with()`)
* Use `chunk()` or `cursor()` for large datasets
* Never call `Model::all()` on large tables

### 12.2 Forbidden Practices

* Raw SQL queries
* Query Builder unless justified

---

## 13. Config & Environment Handling

* `env()` is allowed ONLY inside config files
* Application code must use `config()`

This is mandatory for config caching.

---

## 14. Error Handling Strategy

* Throw domain-specific exceptions
* Never swallow exceptions
* Never return null/false silently

Laravel’s exception handler renders responses.

---

## 15. API Development Rules

* APIs live in `routes/api.php`
* APIs MUST be versioned (`/api/v1`)
* API Resources required for responses

---

## 16. Authentication & Authorization

* Use Laravel Sanctum
* Never custom-roll authentication
* Authorization via Policies ONLY

---

## 17. Frontend (Laravel + Vite)

* Source files live in `resources/`
* Compiled assets live in `public/`
* Never edit compiled output

---

## 18. Testing Doctrine

### 18.1 Testing Tools

* Pest preferred
* PHPUnit allowed

### 18.2 Minimum Testing Requirements

* One happy path test
* One failure test

Untested code is incomplete code.

---

## 19. AI-Specific Hard Prohibitions

AI must NEVER:

* Invent database schemas
* Skip validation layers
* Place logic in controllers
* Use magic strings
* Use `env()` in application code
* Skip transactions

If information is missing → STOP AND ASK.

---

## 20. Final Law

> **Wrong code is worse than no code.**

This document is the single source of truth for Laravel development in this project.
