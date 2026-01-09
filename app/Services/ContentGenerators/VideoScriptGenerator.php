<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

/**
 * Video Script Generator Strategy.
 * 
 * Generates scripts for Reels, YouTube, TikTok, etc.
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
        $cta = $options['cta'] ?? '';
        
        $humanize = $this->getHumanizeInstruction(); // Default tone

        return "You are a creative video storyteller. Create a script for $platform that feels authentic and engaging.
            HOOK STYLE: $hook
            TARGET DURATION: $duration
            
            $humanize
            - Write scripts that sound natural when spoken aloud. 
            - Include realistic pauses and verbal emphasis.
            - Visual cues should support the narrative flow, not just describe actions.
            - CTA: $cta";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        $duration = $options['video_duration'] ?? '60s';
        
        return "Write a $duration video script about $topic. Provide it in a clear format. Context: $context";
    }
}
