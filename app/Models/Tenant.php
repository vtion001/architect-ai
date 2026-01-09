<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasUuids;

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
            'custom_app_secret'
        ];

        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) use ($sensitiveFields) {
                if (!$value) return [];
                $data = json_decode($value, true);
                foreach ($sensitiveFields as $field) {
                    if (isset($data[$field]) && !empty($data[$field])) {
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
                if (!is_array($value)) return json_encode([]);
                foreach ($sensitiveFields as $field) {
                    if (isset($value[$field]) && !empty($value[$field])) {
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
}
