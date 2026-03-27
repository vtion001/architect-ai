<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Resume Parser Service
 * 
 * Handles resume parsing and AI-powered data extraction.
 * Extracted from DocumentBuilderController for better separation of concerns.
 */
class ResumeParserService
{
    public function __construct(
        protected PdfToTextService $pdfToTextService
    ) {}

    /**
     * Extract text from resume file.
     */
    public function extractText(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'pdf') {
            return $this->pdfToTextService->extract($file->getPathname());
        }
        
        if ($extension === 'docx') {
            return $this->extractDocxText($file->getPathname());
        }
        
        // Fallback for text-based files
        return file_get_contents($file->getPathname());
    }

    /**
     * Extract text from DOCX file.
     */
    protected function extractDocxText(string $path): string
    {
        $content = '';
        $zip = new \ZipArchive;

        if ($zip->open($path) === true) {
            // Check for document.xml
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $xmlData = $zip->getFromIndex($index);
                $dom = new \DOMDocument;
                $dom->loadXML($xmlData, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $content = strip_tags($dom->saveXML());
            }
            $zip->close();
        }

        return $content;
    }

    /**
     * Extract structured data from resume text using AI.
     *
     * Includes retry logic and JSON validation to handle cases where
     * the model doesn't return valid JSON on the first attempt.
     */
    public function extractData(string $text): array
    {
        $apiKey = config('services.minimax.key');
        $baseUrl = config('services.minimax.base_url', 'https://api.minimax.io/v1');
        $model = config('services.minimax.model', 'minimax-m2.7');

        if (!$apiKey) {
            return [];
        }

        $maxRetries = 2;
        $truncatedText = substr($text, 0, 10000);

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withToken($apiKey)->post($baseUrl . '/text/chatcompletion_v2', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getExtractionPrompt($attempt > 0)
                        ],
                        [
                            'role' => 'user',
                            'content' => "Resume Text:\n" . $truncatedText
                        ]
                    ],
                    'max_completion_tokens' => 4000,
                    'temperature' => 0.3
                ]);

                if (!$response->successful()) {
                    Log::warning('Resume extraction attempt failed', [
                        'attempt' => $attempt + 1,
                        'status' => $response->status()
                    ]);
                    continue;
                }

                $content = $response->json('choices.0.message.content');

                // Validate JSON
                if (empty($content)) {
                    Log::warning('Resume extraction returned empty content', [
                        'attempt' => $attempt + 1
                    ]);
                    continue;
                }

                // Try to parse as JSON directly
                $data = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    return $this->validateAndCleanData($data);
                }

                // If not valid JSON, try to extract JSON from response
                $extractedData = $this->extractJsonFromResponse($content);

                if ($extractedData !== null) {
                    return $this->validateAndCleanData($extractedData);
                }

                Log::warning('Resume extraction invalid JSON', [
                    'attempt' => $attempt + 1,
                    'content_preview' => substr($content, 0, 200)
                ]);

            } catch (\Exception $e) {
                Log::error('Resume extraction exception', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // All attempts failed, return empty array
        Log::error('Resume extraction failed after all retries');
        return [];
    }

    /**
     * Extract JSON from response that may contain extra text.
     */
    protected function extractJsonFromResponse(string $content): ?array
    {
        // Strategy 1: Try direct parse first
        $data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        // Strategy 2: Try to find and extract JSON object
        // Find the first { and last } to get potential JSON
        $firstBrace = strpos($content, '{');
        $lastBrace = strrpos($content, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $potentialJson = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
            $data = json_decode($potentialJson, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $data;
            }
        }

        // Strategy 3: Remove markdown code blocks
        $cleaned = preg_replace('/```json\s*/', '', $content);
        $cleaned = preg_replace('/```\s*/', '', $cleaned);
        $cleaned = trim($cleaned);

        // Try parsing cleaned version
        $data = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        // Strategy 4: Extract from markdown code block
        if (preg_match('/```[\s\S]*?```/', $content, $matches)) {
            $block = trim($matches[0], '`');
            $block = preg_replace('/^json\s*/', '', $block);
            $block = trim($block);

            // Try to find JSON in the block
            $firstBrace = strpos($block, '{');
            $lastBrace = strrpos($block, '}');
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $block = substr($block, $firstBrace, $lastBrace - $firstBrace + 1);
            }

            $data = json_decode($block, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Validate and clean extracted data.
     */
    protected function validateAndCleanData(array $data): array
    {
        // Ensure all expected top-level keys exist
        $expectedKeys = [
            'full_name', 'title', 'email', 'phone', 'location', 'website',
            'professional_summary', 'contact_info', 'personal_info',
            'work_experience', 'education', 'technical_skills', 'soft_skills',
            'languages_spoken', 'certifications', 'projects', 'awards',
            'volunteer_experience', 'professional_affiliations', 'publications', 'patents'
        ];

        $cleaned = [];
        foreach ($expectedKeys as $key) {
            $cleaned[$key] = $data[$key] ?? null;
        }

        // Ensure arrays are actually arrays
        $arrayKeys = [
            'work_experience', 'education', 'technical_skills', 'soft_skills',
            'languages_spoken', 'certifications', 'projects', 'awards',
            'volunteer_experience', 'professional_affiliations', 'publications', 'patents'
        ];

        foreach ($arrayKeys as $key) {
            if (isset($cleaned[$key]) && !is_array($cleaned[$key])) {
                $cleaned[$key] = [];
            }
        }

        return $cleaned;
    }

    /**
     * Get the AI extraction prompt with optional retry instruction.
     */
    protected function getExtractionPrompt(bool $isRetry = false): string
    {
        $basePrompt = 'You are an HR Data Extraction Specialist with expertise in comprehensive resume parsing.

CRITICAL INSTRUCTION: Extract 100% of resume content with ZERO DATA LOSS. Every detail matters for job matching.

IMPORTANT: You MUST return ONLY valid JSON. No markdown, no code blocks, no explanation. Just pure JSON.

Extract the following information into a structured JSON object:

{
  "full_name": "Candidate full name",
  "title": "Current or most recent job title",
  "email": "Email address",
  "phone": "Phone number",
  "location": "City/State/Country",
  "website": "Portfolio, LinkedIn, or GitHub URL",
  "professional_summary": "Complete professional summary (verbatim)",
  "contact_info": {"email": "", "phone": "", "location": "", "address": "", "website": "", "linkedin": ""},
  "personal_info": {"age": null, "dob": null, "gender": null, "civil_status": null, "nationality": null, "languages": []},
  "work_experience": [{"company": "", "title": "", "dates": "", "location": "", "achievements": [], "technologies": []}],
  "education": [{"degree": "", "institution": "", "year": "", "gpa": null, "honors": ""}],
  "technical_skills": [],
  "soft_skills": [],
  "languages_spoken": [],
  "certifications": [{"name": "", "issuer": "", "date": "", "credential_id": ""}],
  "projects": [{"name": "", "description": "", "technologies": [], "impact": ""}],
  "awards": [],
  "volunteer_experience": [],
  "professional_affiliations": [],
  "publications": [],
  "patents": []
}

CRITICAL RULES:
1. Return ONLY the JSON object - nothing else
2. If a field is not found, use null or empty array []
3. Preserve ALL numbers, metrics, and dates exactly as stated
4. Maintain chronological order for work and education';

        if ($isRetry) {
            $basePrompt .= '

RETRY INSTRUCTION: Your previous response was not valid JSON. Please return ONLY a valid JSON object with no markdown formatting, no code blocks, and no additional text.';
        }

        return $basePrompt;
    }

    /**
     * Parse resume and extract all data.
     */
    public function parse(UploadedFile $file): array
    {
        $text = $this->extractText($file);
        
        if (empty(trim($text))) {
            return [
                'success' => false,
                'message' => 'Could not extract text from the document.'
            ];
        }

        return [
            'success' => true,
            'text' => $text,
            'extracted_data' => $this->extractData($text)
        ];
    }
}
