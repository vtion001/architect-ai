<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

/**
 * Video Script Generator Strategy.
 * 
 * Generates scripts and visual prompts for short-form video content (Reels, TikTok, Shorts).
 */
class VideoScriptGenerator extends BaseContentGenerator
{
    public function getType(): string
    {
        return 'video';
    }

    public function getSystemPrompt(array $options = []): string
    {
        $platform = $options['video_platform'] ?? 'reels';
        $hook = $options['video_hook'] ?? 'Problem/Solution';
        $duration = $options['video_duration'] ?? '60s';
        $style = $options['video_style'] ?? 'UGC';
        $aspectRatio = $options['aspect_ratio'] ?? 'Portrait';
        
        $humanize = $this->getHumanizeInstruction();

        return "You are a Video Architect specializing in high-retention short-form content for $platform.
            
            PARAMETERS:
            - Format: $platform ($aspectRatio)
            - Duration: $duration target
            - Hook Style: $hook
            - Visual Style: $style
            
            STRUCTURE:
            1. **Visual Hook (0-3s)**: Highly engaging visual description.
            2. **Narrative/Script**: The spoken word or text overlay.
            3. **B-Roll/Visuals**: Description of what is seen during the script.
            4. **CTA**: Strong call to action.

            $humanize
            
            OUTPUT FORMAT:
            Provide a detailed script with timestamps, visual descriptions (Scene), and Audio/V/O lines.";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []):
    {
        return "Write a video script about: \"$topic\".
        Specific Context/Description: $context.
        Ensure the visual descriptions are vivid and suitable for AI video generation (Sora 2 style).";
    }
}