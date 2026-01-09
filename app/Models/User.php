<?php

namespace App\Models;

// Note: User intentionally does not use BelongsToTenant global scope
// because User IS the authentication model and scoping would prevent login.
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasUuids, HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'tenant_id',
        'email',
        'password',
        'status',
        'mfa_enabled',
        'mfa_secret',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected $casts = [
        'password' => 'hashed',
        'mfa_enabled' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->using(UserRole::class)
            ->withPivot(['id', 'scope_type', 'scope_id', 'expires_at'])
            ->withTimestamps();
    }

    public function getIsDeveloperAttribute(): bool
    {
        return $this->email === config('iam.developer_email');
    }
}