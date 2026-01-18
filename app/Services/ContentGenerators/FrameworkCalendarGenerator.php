<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

/**
 * Framework Calendar Generator Strategy.
 * 
 * Generates a complete weekly content plan based on the 4-Pillar Framework:
 * - Educational (3x)
 * - Showcase (2x)
 * - Conversational (1-2x)
 * - Promotional (1x)
 */
class FrameworkCalendarGenerator extends BaseContentGenerator
{
    public function getType(): string
    {
        return 'framework_calendar';
    }

    public function getSystemPrompt(array $options = []): string
    {
        $tone = $options['tone'] ?? 'Professional';
        $brandTone = $options['brand_tone'] ?? '';
        $humanize = $this->getHumanizeInstruction($tone);
        
        $brandInstruction = $brandTone ? "Adopt this brand voice: $brandTone." : "";

        return "You are a Strategic Content Manager. Your goal is to plan a high-impact weekly content calendar.
            
            $brandInstruction

            FRAMEWORK PILLARS:
            1. Educational (3 posts): How-to guides, insights, tutorials. Goal: Build Authority.
            2. Showcase (2 posts): Case studies, before/after, portfolio. Goal: Demonstrate Expertise.
            3. Conversational (2 posts): Polls, questions, discussions. Goal: Build Community.
            4. Promotional (1 post): Offers, services. Goal: Drive Conversions.

            $humanize

            OUTPUT FORMAT:
            You must return a strictly valid JSON object. 
            Do NOT include markdown formatting (like ```json or ```). 
            Do NOT include any text before or after the JSON.
            
            Structure:
            {
                \"educational\": [
                    { \"hook\": \"Attention-grabbing headline\", \"caption\": \"Full caption text...\", \"visual_idea\": \"Description for a poster/image\" },
                    ... (3 items)
                ],
                \"showcase\": [
                    { \"hook\": \"...\", \"caption\": \"...\", \"visual_idea\": \"...\" },
                    ... (2 items)
                ],
                \"conversational\": [
                    { \"hook\": \"...\", \"caption\": \"...\", \"visual_idea\": \"...\" },
                    ... (2 items)
                ],
                \"promotional\": [
                    { \"hook\": \"...\", \"caption\": \"...\", \"visual_idea\": \"...\" }
                    ... (1 item)
                ]
            }";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        return "Generate a 1-week content calendar for the topic: \"$topic\".
        Context/Mandates: $context.
        Ensure the 'visual_idea' for each post describes a clean, modern poster or image concept that matches the caption.
        Focus on value and engagement.";
    }
}