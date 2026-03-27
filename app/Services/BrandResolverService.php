<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use App\Models\Tenant;

/**
 * Centralized Brand Resolution Service.
 *
 * Consolidates brand color/logo resolution logic that was duplicated across
 * ReportService, DocumentBuilderController, and ContentCreatorController.
 */
class BrandResolverService
{
    /**
     * Resolve brand styling (colors, logo) with tenant fallback.
     *
     * @return array{primary_color: string, logo_url: string|null, brand: Brand|null}
     */
    public function resolve(?string $brandId = null): array
    {
        $tenant = app(Tenant::class);

        // Default to tenant styling
        $primaryColor = $tenant->metadata['primary_color'] ?? '#00F2FF';
        $logoUrl = $tenant->metadata['logo_url'] ?? null;
        $brand = null;

        // Override with brand-specific styling if provided
        if ($brandId) {
            $brand = Brand::find($brandId);
            if ($brand) {
                $primaryColor = $brand->colors['primary'] ?? $primaryColor;
                $logoUrl = $brand->logo_url ?? $logoUrl;
            }
        }

        return [
            'primary_color' => $primaryColor,
            'logo_url' => $logoUrl,
            'brand' => $brand,
        ];
    }

    /**
     * Get brand blueprint for a specific template type.
     *
     * @return array|null The blueprint configuration or null if not found
     */
    public function getBlueprint(?string $brandId, string $templateType): ?array
    {
        if (! $brandId) {
            return null;
        }

        $brand = Brand::find($brandId);
        if (! $brand) {
            return null;
        }

        return $brand->getBlueprint($templateType);
    }

    /**
     * Build brand instruction prompt for AI generation.
     */
    public function buildBrandInstructions(?string $brandId, string $templateType): string
    {
        $blueprint = $this->getBlueprint($brandId, $templateType);

        if (! $blueprint) {
            return '';
        }

        $brand = Brand::find($brandId);
        $instructions = "\n\n[STRICT BRAND PROTOCOL ACTIVE]\n";
        $instructions .= "You are acting as a compliance agent for {$brand->name}.\n";
        $instructions .= "You MUST follow this exact content structure:\n";

        if (! empty($blueprint['boilerplate_intro'])) {
            $instructions .= "- INTRODUCTION: Must start with: \"{$blueprint['boilerplate_intro']}\"\n";
        }

        if (! empty($blueprint['scope_of_work_template'])) {
            $instructions .= "- SCOPE SECTION: Use this exact text: \"{$blueprint['scope_of_work_template']}\" (Adapt variables only if explicitly asked).\n";
        }

        if (! empty($blueprint['legal_terms'])) {
            $instructions .= "- LEGAL/TERMS: Include verbatim: \"{$blueprint['legal_terms']}\"\n";
        }

        if (! empty($blueprint['structure_instruction'])) {
            $instructions .= "- LAYOUT RULE: {$blueprint['structure_instruction']}\n";
        }

        $instructions .= "\n[END BRAND PROTOCOL]\n";

        return $instructions;
    }

    /**
     * Build brand context for content generation (social media, blogs, etc).
     */
    public function buildBrandContext(?string $brandId): ?string
    {
        if (! $brandId) {
            return null;
        }

        $brand = Brand::find($brandId);
        if (! $brand) {
            return null;
        }

        $context = "\n\n[SYSTEM: STRICT BRAND GUIDELINES ENFORCED]\n";
        $context .= "Identity: {$brand->name}\n";

        if (! empty($brand->voice_profile['tone'])) {
            $context .= "Tone of Voice: {$brand->voice_profile['tone']}\n";
        }

        if (! empty($brand->voice_profile['keywords'])) {
            $context .= "Mandatory Keywords: {$brand->voice_profile['keywords']}\n";
        }

        if (! empty($brand->contact_info['website'])) {
            $context .= "Website Context: {$brand->contact_info['website']}\n";
        }

        return $context;
    }
}
