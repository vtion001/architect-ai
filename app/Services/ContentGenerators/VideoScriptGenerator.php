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
        $brandTone = $options['brand_tone'] ?? '';

        $brandInstruction = $brandTone ? "Align with this brand voice: $brandTone." : '';

        $styleGuide = match ($style) {
            'UGC' => 'Authentic, raw, relatable. Handheld camera feel, natural lighting, real people in everyday settings.',
            'Cinematic' => 'Professional, dramatic, high production value. Smooth camera movements, golden hour lighting, epic establishing shots.',
            'Animation' => '3D animated, stylized characters, vibrant colors, smooth transitions, Pixar-like quality.',
            'Minimalist' => 'Clean, modern, minimal elements. Simple backgrounds, focused subject, elegant composition.',
            default => 'Professional video production quality with engaging visuals.'
        };

        return "You are an expert Sora 2 video prompt engineer. Your job is to transform user ideas into optimized cinematic prompts for AI video generation.
            
            VIDEO PARAMETERS:
            - Platform: $platform ($aspectRatio aspect ratio)
            - Duration: $duration
            - Visual Style: $style
            - Hook Strategy: $hook
            
            STYLE GUIDELINES FOR $style:
            $styleGuide
            
            $brandInstruction
            
            PROMPT ENGINEERING RULES:
            1. Start with camera movement/angle (e.g., 'Drone shot descending', 'Handheld POV', 'Slow dolly zoom')
            2. Describe the main subject with vivid detail (clothing, expression, action)
            3. Set the environment/location with specifics (lighting, time of day, weather)
            4. Add motion and energy (what's moving, how it flows)
            5. Include color palette and mood descriptors
            6. End with the emotional impact or key visual hook
            
            OUTPUT:
            Return ONLY a single, highly detailed cinematic prompt (2-3 sentences) optimized for Sora 2 video generation. 
            Do NOT include script text, timestamps, or narration. Focus purely on visual description.
            
            EXAMPLES:
            - 'Cinematic drone shot slowly descending over a futuristic eco-city at golden hour, revealing solar-panel rooftops and vertical gardens. A young architect in minimalist attire walks confidently along a glass skybridge, backlit by warm sunset rays. Vibrant greens and oranges dominate, with soft lens flares creating an inspirational, hopeful atmosphere.'
            - 'Handheld POV camera following a barista's hands as they expertly craft latte art in a cozy indie coffee shop. Morning sunlight streams through large windows, creating golden bokeh effects. Warm browns and creamy whites, intimate and authentic feel.'";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        return "Transform this concept into a Sora 2-optimized video prompt: \"$topic\".
        
        Additional Context: $context
        
        Create a visually rich, technically precise prompt that captures the essence of this idea as a short-form video. Focus on cinematography, lighting, movement, and emotional impact.";
    }
}
