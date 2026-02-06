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
You are an HR Data Extraction Specialist with expertise in comprehensive resume parsing.

CRITICAL INSTRUCTION: Extract 100% of resume content with ZERO DATA LOSS. Every detail matters for job matching.

Extract the following information from the resume text into a structured JSON object:

**BASIC INFORMATION:**
- `full_name`: Candidate's full name
- `title`: Current or most recent job title
- `email`: Email address
- `phone`: Phone number
- `location`: City/State/Country
- `website`: Portfolio, LinkedIn, or GitHub URL
- `professional_summary`: Complete professional summary/objective (verbatim)

**PERSONAL DETAILS (if present):**
- `personal_info`: object containing `age`, `dob`, `gender`, `civil_status`, `nationality`, `height`, `weight`, `place_of_birth`, `religion`, `languages`, `city`, `alternate_phone`

**WORK EXPERIENCE (COMPLETE & DETAILED):**
- `work_experience`: array of objects, each with:
  - `company`: Company/organization name
  - `title`: Job title/position
  - `dates`: Employment period (e.g., "Jan 2020 - Present")
  - `location`: Company location (if mentioned)
  - `achievements`: array of ALL bullet points/achievements with metrics
  - `technologies`: array of technologies/tools used (if mentioned)

**EDUCATION:**
- `education`: array of objects with:
  - `degree`: Degree/certification name
  - `institution`: School/university name
  - `year`: Graduation year or period
  - `gpa`: GPA if mentioned
  - `honors`: Any honors/achievements

**SKILLS & COMPETENCIES:**
- `technical_skills`: array of all technical skills (programming languages, tools, frameworks)
- `soft_skills`: array of soft skills (leadership, communication, etc.)
- `languages_spoken`: array of languages with proficiency levels

**CERTIFICATIONS & LICENSES:**
- `certifications`: array of objects with:
  - `name`: Certification name
  - `issuer`: Issuing organization
  - `date`: Date obtained or expiry
  - `credential_id`: ID if provided

**PROJECTS & ACHIEVEMENTS:**
- `projects`: array of personal/professional projects with:
  - `name`: Project name
  - `description`: What it does/achieved
  - `technologies`: Tech stack used
  - `impact`: Metrics/outcomes
- `awards`: array of awards, publications, conferences, recognitions

**ADDITIONAL SECTIONS (if present):**
- `volunteer_experience`: Volunteer work
- `professional_affiliations`: Memberships in professional organizations
- `publications`: Research papers, articles, books
- `patents`: Patent information

IMPORTANT RULES:
1. Extract EVERY detail, no matter how small
2. Preserve ALL metrics, numbers, and quantifiable achievements
3. Maintain exact phrasing for accomplishments (don't summarize)
4. Include ALL technologies, tools, and skills mentioned
5. If a section doesn't exist, return empty array/null, don't omit the field
6. Maintain chronological order for work experience and education

Return only valid JSON with NO additional commentary.
PROMPT;
    }
}
