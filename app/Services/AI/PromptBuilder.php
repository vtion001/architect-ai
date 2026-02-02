<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * Prompt Builder Service
 * 
 * Centralizes prompt construction logic for AI features.
 * Provides reusable methods for building system prompts,
 * brand context, and formatting instructions.
 */
class PromptBuilder
{
    /**
     * Build a complete system prompt with all context.
     */
    public function build(array $components): string
    {
        return implode("\n\n", array_filter($components));
    }

    /**
     * Build brand context section.
     */
    public function brandContext(?\App\Models\Brand $brand): string
    {
        if (!$brand) {
            return '';
        }

        $context = "[STRICT BRAND IDENTITY ACTIVE]\n";
        $context .= "You are representing the brand: {$brand->name}\n";

        if ($brand->voice_profile) {
            $voice = $brand->voice_profile;
            $context .= "Tone: " . ($voice['tone'] ?? 'Standard') . "\n";
            $context .= "Style: " . ($voice['writing_style'] ?? 'Standard') . "\n";
            
            if (!empty($voice['keywords'])) {
                $context .= "Key Phrases: {$voice['keywords']}\n";
            }
            if (!empty($voice['avoid_words'])) {
                $context .= "Avoid Words: {$voice['avoid_words']}\n";
            }
        }

        if ($brand->description) {
            $context .= "Context: {$brand->description}\n";
        }

        $context .= "[END BRAND IDENTITY]";

        return $context;
    }

    /**
     * Build knowledge context section.
     */
    public function knowledgeContext(?string $content, string $label = 'PINNED KNOWLEDGE'): string
    {
        if (empty($content)) {
            return '';
        }

        return "--- {$label} ---\n{$content}";
    }

    /**
     * Get standard formatting instructions.
     */
    public function formattingInstructions(string $format = 'plain'): string
    {
        return match ($format) {
            'plain' => "CRITICAL: DO NOT use markdown symbols like '*' or '#' for formatting. Use plain text and clear spacing. For lists, use simple bullet points like '-' or '•'",
            'markdown' => "Format your response using markdown for better readability.",
            'html' => "Format your response using HTML tags for structured output.",
            default => '',
        };
    }

    /**
     * Build humanization instructions for content generation.
     */
    public function humanizeInstructions(string $tone = 'Professional'): string
    {
        return "STRICT HUMANIZATION GUIDELINES:
- Write like a real person sharing valuable insights, not an AI following a prompt.
- Use natural sentence variety (mix short and long sentences).
- Use contractions (e.g., 'don't', 'it's', 'we're') to sound conversational.
- Avoid AI 'tells' and clichés: Do NOT use words like 'delve', 'unlock', 'embark', 'comprehensive', 'in today's digital landscape', 'step into', 'step into the world', or 'tapestry'.
- Use active voice and focus on a direct connection with the reader.
- Inject a bit of personality and warmth while maintaining the '{$tone}' tone.";
    }

    /**
     * Sanitize AI response to remove unwanted formatting.
     */
    public function sanitize(string $text, string $format = 'plain'): string
    {
        if ($format === 'plain') {
            // Remove markdown symbols
            $text = str_replace(['**', '##', '#', '*'], '', $text);
        }

        return trim($text);
    }
}
