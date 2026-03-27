<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cover Letter Draft Service
 *
 * Generates AI-powered cover letter drafts based on CV and target role.
 * Extracted from DocumentBuilderController for better separation of concerns.
 */
class CoverLetterDraftService
{
    /**
     * Draft a cover letter using AI.
     */
    public function draft(string $targetRole, string $sourceContent): array
    {
        $apiKey = config('services.minimax.key');
        $baseUrl = config('services.minimax.base_url', 'https://api.minimaxi.com/v1');
        $model = config('services.minimax.model', 'M2.7');

        if (! $apiKey) {
            return [
                'success' => false,
                'message' => 'MiniMax AI not configured',
            ];
        }

        try {
            $response = Http::withToken($apiKey)->post($baseUrl.'/text/chatcompletion_v2', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getDraftingPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => "TARGET ROLE:\n{$targetRole}\n\nCANDIDATE CV:\n{$sourceContent}",
                    ],
                ],
                'max_completion_tokens' => 2000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'draft' => $response->json('choices.0.message.content'),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Cover letter drafting failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Drafting failed',
        ];
    }

    /**
     * Get the AI drafting prompt.
     */
    protected function getDraftingPrompt(): string
    {
        return <<<'PROMPT'
You are an Expert Career Strategist. 
Your job is to draft a NARRATIVE and PERSUASIVE cover letter story based on a candidate's CV and a Target Role.

RULES:
1. THE HOOK: Explain why this specific role at this specific company matters. (Narrative story).
2. THE EVIDENCE: Pick 2-3 'Hero Moments' from the CV. Use quantifiable results (numbers).
3. THE SOLUTION: Address a likely company pain point and explain how the candidate solves it.
4. CALL TO ACTION: Proactive closing.

STYLE: Narrative, conversational, and enthusiastic. 
FORMAT: Return raw text paragraphs. No HTML, no symbols.
PROMPT;
    }
}
