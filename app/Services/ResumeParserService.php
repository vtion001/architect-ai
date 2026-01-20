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
     */
    public function extractData(string $text): array
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey) {
            return [];
        }

        try {
            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getExtractionPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => "Resume Text:\n" . substr($text, 0, 10000)
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return json_decode($content, true) ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Resume extraction failed: ' . $e->getMessage());
        }

        return [];
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

    /**
     * Get the AI extraction prompt.
     */
    protected function getExtractionPrompt(): string
    {
        return <<<'PROMPT'
You are an HR Data Extraction Specialist.
Extract the following candidate details from the resume text into a JSON object:
- `full_name`: Candidate's full name.
- `title`: Current or most recent job title.
- `email`: Email address.
- `phone`: Phone number.
- `location`: City/Country.
- `website`: Portfolio or LinkedIn URL.
- `personal_info`: object containing `age`, `dob`, `gender`, `civil_status`, `nationality`, `height`, `weight`, `place_of_birth`, `religion`, `languages`.

If a field is not found, leave it as null or empty string.
PROMPT;
    }
}
