<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    use BelongsToTenant, HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'tagline',
        'description',
        'logo_url',
        'logo_public_id',          // Cloudinary public_id for management
        'favicon_url',
        'colors',
        'typography',
        'voice_profile',
        'contact_info',
        'social_handles',
        'industry',
        'is_default',
        'blueprints',
    ];

    protected $casts = [
        'colors' => 'array',
        'typography' => 'array',
        'voice_profile' => 'array',
        'contact_info' => 'array',
        'social_handles' => 'array',
        'is_default' => 'boolean',
        'blueprints' => 'array',
    ];

    /**
     * Get a specific blueprint for a template type.
     */
    public function getBlueprint(string $templateType): ?array
    {
        return $this->blueprints[$templateType] ?? null;
    }

    /**
     * Default color structure
     */
    public static function defaultColors(): array
    {
        return [
            'primary' => '#000000',
            'secondary' => '#ffffff',
            'accent' => '#3b82f6',
            'background' => '#ffffff',
            'text' => '#1f2937',
        ];
    }

    /**
     * Default typography structure
     */
    public static function defaultTypography(): array
    {
        return [
            'headings' => 'Inter',
            'body' => 'Inter',
            'accent' => 'Inter',
        ];
    }

    /**
     * Default voice profile structure
     */
    public static function defaultVoiceProfile(): array
    {
        return [
            'tone' => 'Professional',
            'personality' => '',
            'keywords' => '',
            'avoid_words' => '',
            'writing_style' => 'Balanced',
        ];
    }

    /**
     * Get merged colors with defaults
     */
    public function getColorsAttribute($value): array
    {
        $decoded = is_string($value) ? json_decode($value, true) : ($value ?? []);

        return array_merge(self::defaultColors(), $decoded ?? []);
    }

    /**
     * Get merged typography with defaults
     */
    public function getTypographyAttribute($value): array
    {
        $decoded = is_string($value) ? json_decode($value, true) : ($value ?? []);

        return array_merge(self::defaultTypography(), $decoded ?? []);
    }

    /**
     * Get merged voice profile with defaults
     */
    public function getVoiceProfileAttribute($value): array
    {
        $decoded = is_string($value) ? json_decode($value, true) : ($value ?? []);

        return array_merge(self::defaultVoiceProfile(), $decoded ?? []);
    }

    /**
     * Generate brand context for AI prompts
     */
    public function getAIContext(): string
    {
        $context = "Brand: {$this->name}";

        if ($this->tagline) {
            $context .= "\nTagline: {$this->tagline}";
        }

        if ($this->description) {
            $context .= "\nDescription: {$this->description}";
        }

        if ($this->industry) {
            $context .= "\nIndustry: {$this->industry}";
        }

        $voice = $this->voice_profile;
        if (! empty($voice['tone'])) {
            $context .= "\nTone: {$voice['tone']}";
        }
        if (! empty($voice['personality'])) {
            $context .= "\nPersonality: {$voice['personality']}";
        }
        if (! empty($voice['keywords'])) {
            $context .= "\nKey phrases to use: {$voice['keywords']}";
        }
        if (! empty($voice['avoid_words'])) {
            $context .= "\nWords/phrases to avoid: {$voice['avoid_words']}";
        }
        if (! empty($voice['writing_style'])) {
            $context .= "\nWriting style: {$voice['writing_style']}";
        }

        return $context;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
