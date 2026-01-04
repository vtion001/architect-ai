<?php

namespace App\Models\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->runningInConsole()) {
                return;
            }

            // Get tenant ID from session or service container
            $tenantId = session('current_tenant_id') ?? (app()->bound(Tenant::class) ? app(Tenant::class)->id : null);

            if ($tenantId) {
                // Safely check for developer mode without triggering user load
                if (auth()->hasUser() && auth()->user()->is_developer && session('developer_observability_mode')) {
                    return;
                }

                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });

        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $tenantId = session('current_tenant_id') ?? (app()->bound(Tenant::class) ? app(Tenant::class)->id : null);
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
