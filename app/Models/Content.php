<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use BelongsToTenant, HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'topic',
        'type',
        'context',
        'result',
        'image_url',
        'word_count',
        'status',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
