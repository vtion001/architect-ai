<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy for Brand model authorization.
 * Ensures users can only access brands within their tenant.
 */
class BrandPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Developers bypass all checks in observability mode
        if ($user->is_developer) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any brands.
     */
    public function viewAny(User $user): bool
    {
        return true; // Scoped via BelongsToTenant trait
    }

    /**
     * Determine whether the user can view the brand.
     */
    public function view(User $user, Brand $brand): bool
    {
        return $this->belongsToUserTenant($user, $brand);
    }

    /**
     * Determine whether the user can create brands.
     */
    public function create(User $user): bool
    {
        return true; // Tenant_id is auto-assigned via trait
    }

    /**
     * Determine whether the user can update the brand.
     */
    public function update(User $user, Brand $brand): bool
    {
        return $this->belongsToUserTenant($user, $brand);
    }

    /**
     * Determine whether the user can delete the brand.
     */
    public function delete(User $user, Brand $brand): bool
    {
        return $this->belongsToUserTenant($user, $brand);
    }

    /**
     * Determine whether the user can set brand as default.
     */
    public function setDefault(User $user, Brand $brand): bool
    {
        return $this->belongsToUserTenant($user, $brand);
    }

    /**
     * Check if brand belongs to the user's tenant or a sub-account they manage.
     */
    protected function belongsToUserTenant(User $user, Brand $brand): bool
    {
        // Direct ownership
        if ($brand->tenant_id === $user->tenant_id) {
            return true;
        }

        // Agency managing sub-account
        if ($user->tenant->type === 'agency') {
            return $brand->tenant->parent_id === $user->tenant_id;
        }

        return false;
    }
}
