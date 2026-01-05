<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    use HasUuids;

    protected $fillable = [
        'email',
        'name',
        'agency_name',
        'status',
        'provisioned_at',
        'rejected_at',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'provisioned_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
}
