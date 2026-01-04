<?php

namespace App\Services;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\AccessPolicy;
use Illuminate\Support\Facades\Log;

class AuthorizationService
{
    /**
     * Determine if the user can perform the action on the resource.
     */
    public function can(User $user, string $permission, $resource = null, array $context = []): bool
    {
        // 1. Developer Bypass (Observability Mode)
        if ($user->is_developer) {
            return true;
        }

        // 2. Tenant Isolation (Hard Boundary)
        if ($resource && isset($resource->tenant_id) && $user->tenant_id !== $resource->tenant_id) {
            // Check if it's a sub-account the user has access to
            if (!$this->hasCrossTenantAccess($user, $resource->tenant_id)) {
                return false;
            }
        }

        // 3. Evaluate Dynamic Policies (ABAC)
        $policyResult = $this->evaluatePolicies($user, $permission, $resource, $context);
        if ($policyResult !== null) {
            return $policyResult;
        }

        // 4. Check Roles/Permissions (RBAC fallback)
        return $this->hasPermission($user, $permission);
    }

    protected function evaluatePolicies(User $user, string $permission, $resource, array $context): ?bool
    {
        // Get policies for the tenant, ordered by priority
        $policies = AccessPolicy::where('tenant_id', $user->tenant_id)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($policies as $policy) {
            if ($this->conditionsMatch($policy->conditions, $user, $permission, $resource, $context)) {
                return $policy->effect === 'allow';
            }
        }

        return null; // No policy matched
    }

    /**
     * Evaluate complex JSON conditions against the current context.
     * Supports nested 'all' (AND) and 'any' (OR) logic.
     */
    protected function conditionsMatch(array $conditions, User $user, string $permission, $resource, array $context): bool
    {
        if (isset($conditions['all'])) {
            foreach ($conditions['all'] as $subCondition) {
                if (!$this->evaluateCondition($subCondition, $user, $permission, $resource, $context)) {
                    return false;
                }
            }
            return true;
        }

        if (isset($conditions['any'])) {
            foreach ($conditions['any'] as $subCondition) {
                if ($this->evaluateCondition($subCondition, $user, $permission, $resource, $context)) {
                    return true;
                }
            }
            return false;
        }

        // Fallback to single condition evaluation if not nested
        return $this->evaluateCondition($conditions, $user, $permission, $resource, $context);
    }

    protected function evaluateCondition(array $condition, User $user, string $permission, $resource, array $context): bool
    {
        $attribute = $condition['attribute'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? null;

        if (!$attribute) return true;

        $actualValue = $this->getAttributeValue($attribute, $user, $permission, $resource, $context);

        // Replace placeholder in value (e.g., {{user.id}})
        if (is_string($value) && str_contains($value, '{{')) {
            $value = $this->resolvePlaceholders($value, $user, $resource);
        }

        return match ($operator) {
            'equals' => $actualValue == $value,
            'not_equals' => $actualValue != $value,
            'in' => is_array($value) && in_array($actualValue, $value),
            'not_in' => is_array($value) && !in_array($actualValue, $value),
            'contains' => str_contains($actualValue, $value),
            default => false,
        };
    }

    protected function getAttributeValue(string $attribute, User $user, string $permission, $resource, array $context)
    {
        return match ($attribute) {
            'user.id' => $user->id,
            'user.email' => $user->email,
            'user.role' => $user->roles()->first()?->name,
            'action' => $permission,
            'resource.owner_id' => $resource?->user_id ?? $resource?->owner_id,
            'resource.status' => $resource?->status,
            'resource.type' => $resource ? get_class($resource) : null,
            default => $context[$attribute] ?? null,
        };
    }

    protected function resolvePlaceholders(string $value, User $user, $resource): string
    {
        return str_replace(
            ['{{user.id}}', '{{user.tenant_id}}'],
            [$user->id, $user->tenant_id],
            $value
        );
    }

    protected function hasPermission(User $user, string $permission): bool
    {
        [$resource, $action] = explode('.', $permission);
        
        return $user->roles()->whereHas('permissions', function ($query) use ($resource, $action) {
            $query->where('resource', $resource)
                  ->where('action', $action);
        })->exists();
    }

    protected function hasCrossTenantAccess(User $user, string $targetTenantId): bool
    {
        // Check if user has a role scoped to this sub-account
        return $user->roles()
            ->where('scope_type', 'sub_account')
            ->where('scope_id', $targetTenantId)
            ->exists();
    }

    /**
     * Log an action to the audit log.
     */
    public function audit(User $user, string $action, $resource = null, string $result = 'success', string $justification = null)
    {
        AuditLog::create([
            'actor_id' => $user->id,
            'actor_type' => $user->is_developer ? 'developer' : 'user',
            'tenant_id' => $user->tenant_id,
            'action' => $action,
            'resource_type' => $resource ? (is_string($resource) ? $resource : get_class($resource)) : null,
            'resource_id' => $resource ? (is_object($resource) ? $resource->id : null) : null,
            'result' => $result,
            'ip_address' => request()->ip(),
            'justification' => $justification,
        ]);
    }
}