<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use BelongsToTenant, HasUuids;

    protected $table = 'researches';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

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
                        // usage of Log facade requires import, but we want to be minimal.
                        // Just ignore parse error and leave content or strip?
                        // User request: "make sure it doesn't appear".
                        // So we strip it anyway if regex matches?
                        // But if json is invalid, better keep it text?
                        // We strictly decode first.
                    }
                }
            }
        });
    }
}
