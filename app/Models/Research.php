<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use BelongsToTenant, HasUuids;

    protected $table = 'researches';

    protected $guarded = [];

    protected $casts = [
        'citations' => 'array',
        'options' => 'array',
    ];
}