<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AiAgent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy for AiAgent model authorization.
 * Ensures users can only access AI agents within their tenant.
 */
class AiAgentPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_developer) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any AI agents.
     */
    public function viewAny(User $user): bool
    {
        return true; // Scoped via BelongsToTenant trait
    }

    /**
     * Determine whether the user can view the AI agent.
     */
    public function view(User $user, AiAgent $agent): bool
    {
        return $this->belongsToUserTenant($user, $agent);
    }

    /**
     * Determine whether the user can create AI agents.
     */
    public function create(User $user): bool
    {
        return true; // Tenant_id is auto-assigned via trait
    }

    /**
     * Determine whether the user can update the AI agent.
     */
    public function update(User $user, AiAgent $agent): bool
    {
        return $this->belongsToUserTenant($user, $agent);
    }

    /**
     * Determine whether the user can delete the AI agent.
     */
    public function delete(User $user, AiAgent $agent): bool
    {
        return $this->belongsToUserTenant($user, $agent);
    }

    /**
     * Determine whether the user can chat with the AI agent.
     */
    public function chat(User $user, AiAgent $agent): bool
    {
        return $this->belongsToUserTenant($user, $agent);
    }

    /**
     * Check if agent belongs to the user's tenant or a sub-account they manage.
     */
    protected function belongsToUserTenant(User $user, AiAgent $agent): bool
    {
        // Direct ownership
        if ($agent->tenant_id === $user->tenant_id) {
            return true;
        }

        // Agency managing sub-account
        if ($user->tenant->type === 'agency') {
            return $agent->tenant->parent_id === $user->tenant_id;
        }

        return false;
    }
}
