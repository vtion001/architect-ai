<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use BelongsToTenant, HasUuids;

    protected $table = 'researches';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'query',
        'status',
        'result',
        'citations',
        'sources_count',
        'pages_count',
        'options',
    ];

    protected $casts = [
        'citations' => 'array',
        'options' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('result') && $model->result) {
                // Parse and strip AI Metadata JSON Block
                // Look for ```json { ... } ``` structure at the end of the content
                if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```\s*$/is', $model->result, $matches)) {
                    try {
                        $metadata = json_decode($matches[1], true);
                        if (is_array($metadata)) {
                            // Strip from content
                            $model->result = str_replace($matches[0], '', $model->result);
                            $model->result = trim($model->result);

                            // Merge into options for UI usage
                            $model->options = array_merge($model->options ?? [], $metadata);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Research: failed to parse AI metadata block', ['error' => $e->getMessage()]);
                    }
                }
            }
        });
    }
}
