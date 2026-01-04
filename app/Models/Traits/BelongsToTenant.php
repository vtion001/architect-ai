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
            // 1. If not authenticated, default to scoping (or handle public access if applicable)
            if (!auth()->check()) {
                return;
            }

            // 2. If developer has enabled "Observability Mode", bypass isolation
            if (auth()->user()->is_developer && session('developer_observability_mode')) {
                return;
            }

            // 3. Otherwise, enforce strict tenant isolation
            $builder->where('tenant_id', auth()->user()->tenant_id);
        });

        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
