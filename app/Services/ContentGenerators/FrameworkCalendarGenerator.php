<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

/**
 * Framework Calendar Generator Strategy.
 *
 * Generates a complete weekly content plan based on the 4-Pillar Framework:
 * - Educational (3x)
 * - Showcase (2x)
 * - Conversational (2x)
 * - Promotional (1x)
 */
class FrameworkCalendarGenerator extends BaseContentGenerator
{
    /**
     * Override generate to force JSON mode and stricter temperature.
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string
    {
        $options['response_format'] = ['type' => 'json_object'];
        $options['temperature'] = 0.4; // Lower temperature for stricter adherence
        $options['max_tokens'] = 4000; // Ensure enough space for 8 full posts
        $options['model'] = 'gpt-4o'; // Force higher intelligence model for complex JSON structure

        return parent::generate($topic, $context, $options);
    }

    public function getType(): string
    {
        return 'framework_calendar';
    }

    public function getSystemPrompt(array $options = []): string
    {
        $tone = $options['tone'] ?? 'Professional';
        $brandTone = $options['brand_tone'] ?? '';
        $humanize = $this->getHumanizeInstruction($tone);

        $brandInstruction = $brandTone ? "Adopt this brand voice: $brandTone." : '';

        return "You are a Strategic Content Manager. Your goal is to plan a high-impact weekly content calendar.
            
            $brandInstruction

            FRAMEWORK PILLARS (STRICT COUNT ENFORCEMENT):
            1. Educational: EXACTLY 3 posts. NO MORE, NO LESS.
            2. Showcase: EXACTLY 2 posts. NO MORE, NO LESS.
            3. Conversational: EXACTLY 2 posts. NO MORE, NO LESS.
            4. Promotional: EXACTLY 1 post. NO MORE, NO LESS.
            
            TOTAL POSTS: 8

            $humanize

            OUTPUT FORMAT:
            You must return a strictly valid JSON object. 
            Do NOT include markdown formatting. 
            Do NOT include any text before or after the JSON.
            
            Structure (MUST FILL ALL OBJECTS):
            {
                \"educational\": [
                    { \"hook\": \"Educational Hook 1...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" },
                    { \"hook\": \"Educational Hook 2...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" },
                    { \"hook\": \"Educational Hook 3...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" }
                ],
                \"showcase\": [
                    { \"hook\": \"Showcase Hook 1...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" },
                    { \"hook\": \"Showcase Hook 2...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" }
                ],
                \"conversational\": [
                    { \"hook\": \"Conversational Hook 1...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" },
                    { \"hook\": \"Conversational Hook 2...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" }
                ],
                \"promotional\": [
                    { \"hook\": \"Promotional Hook 1...\", \"caption\": \"Full caption...\", \"visual_idea\": \"...\" }
                ]
            }
            
            CRITICAL INSTRUCTION: You MUST generate exactly 8 distinct items in total across these categories. Do not group them. Do not summarize. Fill every single slot in the JSON structure above. Each pillar must have EXACTLY the specified number of items.";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        return "Generate a 1-week content calendar for the topic: \"$topic\".
        Context/Mandates: $context.
        Ensure the 'visual_idea' for each post describes a clean, modern poster or image concept that matches the caption.
        Focus on value and engagement.";
    }
}
