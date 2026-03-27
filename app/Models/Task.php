<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'type',
        'parent_id',
        'category_id',
        'alarm_enabled',
        'alarm_sound',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'alarm_enabled' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }
}
