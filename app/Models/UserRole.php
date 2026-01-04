<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserRole extends Pivot
{
    use HasUuids;

    protected $table = 'user_roles';

    public $incrementing = false;

    protected $keyType = 'string';
}
