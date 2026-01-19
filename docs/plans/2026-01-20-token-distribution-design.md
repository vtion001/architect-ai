# Design Document: Token Distribution & User Quotas

**Date:** 2026-01-20
**Topic:** Token Distribution, User Quotas, and Freemium Model
**Status:** Approved

## Overview
Implement a managed token economy within the Architect AI platform. The system uses a "Tenant Wallet" model where a shared pool of tokens is owned by the Tenant (Organization), but individual users within that tenant are subject to "User Quotas" (Limits) to ensure fair distribution and prevent over-consumption.

## Architecture & Data Schema

### 1. Storage
- **Tenant Wallet (Existing):** `TokenAllocation` rows track the total available balance for the organization.
- **User Limits (New):** A `TokenLimit` model/table to track individual consumption.
    - `id` (UUID)
    - `user_id` (FK to `users`)
    - `tenant_id` (FK to `tenants`)
    - `type` (Enum: `monthly`, `lifetime`, `unlimited`)
    - `amount` (Integer: The limit value)
    - `used` (Integer: Current consumption in the period)
    - `reset_at` (Timestamp: When the next reset should occur)

### 2. Service Layer (`TokenService`)
- **`consume(User $user, int $amount)`**:
    - **Step 1:** Check if the user has a `TokenLimit`. If `used + amount > amount`, throw `UserTokenLimitExceededException`.
    - **Step 2:** Check if the Tenant has sufficient balance via `TokenAllocation`. If not, throw `InsufficientTenantBalanceException`.
    - **Step 3:** Perform atomic update: Decrement `TokenAllocation.balance` and Increment `TokenLimit.used` within a DB transaction.
- **`grant(Tenant $tenant, int $amount)`**: Used for the signup bonus and subsequent purchases.

## Logic & Workflows

### 1. Signup Flow ("Freemium")
Upon user registration and tenant creation:
- Automatically call `TokenService::grant($tenant, 5000, 'Welcome Bonus')`.
- (Optional) Set a default `TokenLimit` for the owner/creator.

### 2. Purchase Flow
- When a payment is confirmed (Stripe/External), the system calls `TokenService::grant` to top up the Tenant Wallet.
- This creates a new `TokenAllocation` record, making tokens immediately available to all users (subject to their individual limits).

### 3. Reset Mechanism
A scheduled Laravel command (`tokens:reset-limits`) runs monthly (or based on `reset_at`) to zero out the `used` column for users with recurring limits.

## UI/UX & Reporting

### 1. Reporting (Dashboard)
- **User View:** A progress bar showing "Personal Usage" against their limit.
- **Admin View:** A "Token Governance" dashboard listing all users, their current usage, and an "Edit Limit" action.

### 2. Error Handling
- **402 Payment Required:** Returned when the Tenant Wallet is empty.
- **403 Forbidden:** Returned when a User has hit their personal limit (with a specific error code for the frontend to differentiate).

## Success Criteria
- New users start with a non-zero token balance.
- Users cannot exceed their assigned personal quota even if the tenant has tokens.
- Admins can purchase more tokens for the tenant, which resolves "Empty Wallet" errors for all users.
