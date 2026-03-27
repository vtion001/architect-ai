<?php

namespace App\Models;

use App\Enums\FeatureType;
use App\Enums\PlanType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'plan',
        'plan_status',
        'parent_id',
        'name',
        'slug',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Encrypt sensitive fields within metadata automatically.
     */
    protected function metadata(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        $sensitiveFields = [
            'facebook_access_token',
            'instagram_access_token',
            'linkedin_access_token',
            'api_secret',
            'custom_app_secret',
        ];

        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) use ($sensitiveFields) {
                if (! $value) {
                    return [];
                }
                $data = json_decode($value, true);
                foreach ($sensitiveFields as $field) {
                    if (isset($data[$field]) && ! empty($data[$field])) {
                        try {
                            $data[$field] = \Illuminate\Support\Facades\Crypt::decryptString($data[$field]);
                        } catch (\Exception $e) {
                            // If decryption fails, it might be in plain text (transition phase) or corrupted
                        }
                    }
                }

                return $data;
            },
            set: function ($value) use ($sensitiveFields) {
                if (! is_array($value)) {
                    return json_encode([]);
                }
                foreach ($sensitiveFields as $field) {
                    if (isset($value[$field]) && ! empty($value[$field])) {
                        $value[$field] = \Illuminate\Support\Facades\Crypt::encryptString($value[$field]);
                    }
                }

                return json_encode($value);
            }
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_id');
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Plan Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the plan type as an enum.
     */
    public function getPlanType(): PlanType
    {
        return PlanType::tryFrom($this->plan) ?? PlanType::STARTER;
    }

    /**
     * Check if tenant is on Starter plan.
     */
    public function isStarterPlan(): bool
    {
        return $this->getPlanType() === PlanType::STARTER;
    }

    /**
     * Check if tenant is on Pro plan.
     */
    public function isProPlan(): bool
    {
        return $this->getPlanType() === PlanType::PRO;
    }

    /**
     * Check if tenant is on Agency plan.
     */
    public function isAgencyPlan(): bool
    {
        return $this->getPlanType() === PlanType::AGENCY;
    }

    /**
     * Check if tenant has Pro-level features (Pro or Agency).
     */
    public function hasProFeatures(): bool
    {
        return $this->getPlanType()->hasProFeatures();
    }

    /**
     * Check if tenant has unlimited feature credits.
     */
    public function hasUnlimitedCredits(): bool
    {
        return $this->getPlanType()->hasUnlimitedCredits();
    }

    /**
     * Check if tenant can create sub-accounts.
     */
    public function canCreateSubAccounts(): bool
    {
        return $this->getPlanType()->canCreateSubAccounts();
    }

    /**
     * Check if tenant can access a specific feature.
     */
    public function canAccessFeature(FeatureType $feature): bool
    {
        return (bool) config("features.plans.{$this->plan}.access.{$feature->value}", false);
    }

    /**
     * Get the credit limit for a specific feature.
     */
    public function getFeatureCreditLimit(FeatureType $feature): int
    {
        return (int) config("features.plans.{$this->plan}.credits.{$feature->value}", 0);
    }

    /**
     * Get the maximum number of sub-accounts allowed.
     */
    public function getMaxSubAccounts(): int
    {
        return (int) config("features.plans.{$this->plan}.max_sub_accounts", 0);
    }
}
