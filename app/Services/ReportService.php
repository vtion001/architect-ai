<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use Illuminate\Support\Facades\View;

class ReportService
{
    public function __construct(
        private readonly ResearchService $researchService
    ) {}

    public function generateReportHtml(ReportRequestData $data): string
    {
        $tenant = app(\App\Models\Tenant::class);
        $content = $this->generateContent($data);

        // Resolve Brand Logic
        $brandColor = $tenant->metadata['primary_color'] ?? '#00F2FF';
        $logoUrl = $tenant->metadata['logo_url'] ?? null;

        if ($data->brandId) {
            $brand = \App\Models\Brand::find($data->brandId);
            if ($brand) {
                $brandColor = $brand->colors['primary'] ?? $brandColor;
                $logoUrl = $brand->logo_url ?? $logoUrl;
            }
        }

        // Ensure profile photo URL is passed to the view
        // If it's a resume, we might want to ensure the photo block is part of the content if not using a separate variable in the view
        // But the view likely uses $profilePhotoUrl variable.
        
        return View::make($data->template->view(), [
            'content' => $content,
            'recipientName' => $data->recipientName ?? 'Recipient',
            'recipientTitle' => $data->recipientTitle,
            'variant' => $data->variant,
            'brandColor' => $brandColor,
            'logoUrl' => $logoUrl,
            'profilePhotoUrl' => $data->profilePhotoUrl, // Ensure this is populated in DTO
            'contactInfo' => [
                'email' => $data->email,
                'phone' => $data->phone,
                'location' => $data->location,
                'website' => $data->website,
            ],
            'personalInfo' => $data->personalInfo,
            // For Cover Letter, map recipient/sender fields appropriately
            'senderName' => $data->recipientName, // In Cover Letter context, user is sender
            'senderTitle' => $data->recipientTitle,
            'companyAddress' => $data->companyAddress,
        ])->render();
    }

    public function generatePreviewHtml(ReportTemplate $template, ?string $variant = null, ?string $brandId = null): string
    {
        $tenant = app(\App\Models\Tenant::class);
        $sampleContent = $this->getSampleContentForTemplate($template);

        // Resolve Brand Logic
        $brandColor = $tenant->metadata['primary_color'] ?? '#00F2FF';
        $logoUrl = $tenant->metadata['logo_url'] ?? null;

        if ($brandId) {
            $brand = \App\Models\Brand::find($brandId);
            if ($brand) {
                $brandColor = $brand->colors['primary'] ?? $brandColor;
                $logoUrl = $brand->logo_url ?? $logoUrl;
            }
        }

        return View::make($template->view(), [
            'content' => $sampleContent,
            'recipientName' => 'Sample Recipient',
            'recipientTitle' => 'Department Manager',
            'senderName' => 'Your Name',
            'senderTitle' => 'Professional Title',
            'companyAddress' => '123 Business Rd, Tech City',
            'variant' => $variant,
            'brandColor' => $brandColor,
            'logoUrl' => $logoUrl,
            'profilePhotoUrl' => null,
            'contactInfo' => [
                'email' => 'hello@example.com',
                'phone' => '+1 (555) 000-0000',
                'location' => 'City, Country',
                'website' => 'www.portfolio.com',
            ],
            'personalInfo' => []
        ])->render();
    }

    private function generateContent(ReportRequestData $data): string
    {
        $apiKey = config('services.openai.key');
        
        if (!$apiKey) {
            return $this->getDummyContent();
        }

        // 1. RAG: Fetch relevant internal knowledge base assets
        $kbContext = $this->getKnowledgeBaseContext($data->researchTopic ?? $data->prompt ?? '');

        // Perform Deep Research if a topic is provided
        $researchData = '';
        if ($data->researchTopic) {
            \Illuminate\Support\Facades\Log::info("Performing Gemini deep research for: " . $data->researchTopic);
            $researchData = $this->researchService->performResearch($data->researchTopic);
            \Illuminate\Support\Facades\Log::info("Research received. Length: " . strlen($researchData));
        }

        // Resolve Brand Blueprints
        $brandInstructions = "";
        $dataIntegrity = "";

        if ($data->template !== ReportTemplate::CV_RESUME && $data->template !== ReportTemplate::COVER_LETTER) {
            $dataIntegrity = "- **CRITICAL: DATA INTEGRITY.** You must RETAIN all quantitative data, metrics, and specific technical units (e.g., m2, kg, %, $, dates) from the source content. Do not approximate or omit these details.\n";
        }

        if ($data->brandId) {
            $brand = \App\Models\Brand::find($data->brandId);
            if ($brand && $blueprint = $brand->getBlueprint($data->template->value)) {
                $brandInstructions = "\n\n[STRICT BRAND PROTOCOL ACTIVE]\n";
                $brandInstructions .= "You are acting as a compliance agent for {$brand->name}.\n";
                $brandInstructions .= "You MUST follow this exact content structure:\n";
                
                if (!empty($blueprint['boilerplate_intro'])) {
                    $brandInstructions .= "- INTRODUCTION: Must start with: \"{$blueprint['boilerplate_intro']}\"\n";
                }
                
                if (!empty($blueprint['scope_of_work_template'])) {
                    $brandInstructions .= "- SCOPE SECTION: Use this exact text: \"{$blueprint['scope_of_work_template']}\" (Adapt variables only if explicitly asked).\n";
                }
                
                if (!empty($blueprint['legal_terms'])) {
                    $brandInstructions .= "- LEGAL/TERMS: Include verbatim: \"{$blueprint['legal_terms']}\"\n";
                }
                
                if (!empty($blueprint['structure_instruction'])) {
                    $brandInstructions .= "- LAYOUT RULE: {$blueprint['structure_instruction']}\n";
                }
                
                $brandInstructions .= "\n[END BRAND PROTOCOL]\n";
            }
        }

        try {
            $roleDescription = "expert business analyst and technical writer";
            $taskDescription = "HIGH-END HTML business report";
            $documentType = "report";

            if ($data->template === ReportTemplate::PROPOSAL) {
                $roleDescription = "expert proposal writer and sales strategist";
                $taskDescription = "HIGH-IMPACT business proposal";
                $documentType = "proposal";
            } elseif ($data->template === ReportTemplate::CONTRACT) {
                $roleDescription = "expert legal drafter, contract attorney, and international business law specialist";
                $taskDescription = "COMPREHENSIVE, LEGALLY SOUND, PROFESSIONALLY FORMATTED business contract";
                $documentType = "contract";
                
                $brandInstructions .= "\n\n[CONTRACT STRUCTURE MANDATE]\n";
                $brandInstructions .= "You must generate a comprehensive, professional legal contract by analyzing the pasted content.\n";
                $brandInstructions .= "CRITICAL: Extract all relevant details from the source content and organize into proper legal structure.\n\n";
                
                $brandInstructions .= "[REQUIRED CONTRACT SECTIONS - Generate ALL applicable sections]\n\n";
                
                $brandInstructions .= "1. PARTIES SECTION (REQUIRED):\n";
                $brandInstructions .= "   Generate using this exact HTML structure:\n";
                $brandInstructions .= "   <div class='parties-section'>\n";
                $brandInstructions .= "     <div class='party-block'>\n";
                $brandInstructions .= "       <h3>Service Provider (\"Provider\")</h3>\n";
                $brandInstructions .= "       <div class='party-field'><span class='party-label'>Name:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
                $brandInstructions .= "       <div class='party-field'><span class='party-label'>Business:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
                $brandInstructions .= "       <div class='party-field'><span class='party-label'>Address:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
                $brandInstructions .= "       <div class='party-field'><span class='party-label'>Email:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
                $brandInstructions .= "     </div>\n";
                $brandInstructions .= "     <div class='party-block'>\n";
                $brandInstructions .= "       <h3>Client (\"Client\")</h3>\n";
                $brandInstructions .= "       [Same structure for client details]\n";
                $brandInstructions .= "     </div>\n";
                $brandInstructions .= "   </div>\n\n";
                
                $brandInstructions .= "2. RECITALS/WHEREAS SECTION:\n";
                $brandInstructions .= "   <div class='recitals'>\n";
                $brandInstructions .= "     <p>WHEREAS, [context of the agreement];</p>\n";
                $brandInstructions .= "     <p>WHEREAS, [provider qualifications];</p>\n";
                $brandInstructions .= "     <p>WHEREAS, [purpose of engagement];</p>\n";
                $brandInstructions .= "     <p class='therefore-clause'>NOW, THEREFORE, in consideration of the mutual covenants...</p>\n";
                $brandInstructions .= "   </div>\n\n";
                
                $brandInstructions .= "3. ARTICLES - Use <h2> for main articles:\n";
                $brandInstructions .= "   Use <h2>ARTICLE I: SCOPE OF WORK AND DELIVERABLES</h2>\n";
                $brandInstructions .= "   Use <h3>1.1 Project Description</h3> for subsections\n";
                $brandInstructions .= "   Use <h4>A. Component Name</h4> for sub-subsections\n\n";
                
                $brandInstructions .= "4. STANDARD ARTICLES TO INCLUDE:\n";
                $brandInstructions .= "   - ARTICLE I: SCOPE OF WORK AND DELIVERABLES (extract from content)\n";
                $brandInstructions .= "   - ARTICLE II: PROJECT TIMELINE AND MILESTONES (if applicable)\n";
                $brandInstructions .= "   - ARTICLE III: COMPENSATION AND PAYMENT TERMS\n";
                $brandInstructions .= "   - ARTICLE IV: CLIENT RESPONSIBILITIES AND OBLIGATIONS\n";
                $brandInstructions .= "   - ARTICLE V: INTELLECTUAL PROPERTY RIGHTS\n";
                $brandInstructions .= "   - ARTICLE VI: WARRANTIES AND DISCLAIMERS\n";
                $brandInstructions .= "   - ARTICLE VII: DISPUTE RESOLUTION AND GOVERNING LAW\n";
                $brandInstructions .= "   - ARTICLE VIII: TERMINATION\n";
                $brandInstructions .= "   - ARTICLE IX: MISCELLANEOUS PROVISIONS\n\n";
                
                $brandInstructions .= "5. SPECIAL HTML CLASSES TO USE:\n";
                $brandInstructions .= "   - For critical/warning clauses: <div class='callout-critical'><strong>CRITICAL:</strong> text...</div>\n";
                $brandInstructions .= "   - For important notices: <div class='callout'><strong>Important:</strong> text...</div>\n";
                $brandInstructions .= "   - For informational notes: <div class='callout-info'>text...</div>\n";
                $brandInstructions .= "   - For milestone blocks: <div class='milestone-block'><h4>Milestone Name</h4><p>Details...</p></div>\n";
                $brandInstructions .= "   - For payment tables: <table class='payment-table'>...</table>\n\n";
                
                $brandInstructions .= "6. PAYMENT SCHEDULE TABLE FORMAT:\n";
                $brandInstructions .= "   <table class='payment-table'>\n";
                $brandInstructions .= "     <thead><tr><th>Payment</th><th>Description</th><th>Due Date</th><th>Amount</th></tr></thead>\n";
                $brandInstructions .= "     <tbody>\n";
                $brandInstructions .= "       <tr><td>Payment 1</td><td>Deposit</td><td>Upon signing</td><td>\$X,XXX.XX</td></tr>\n";
                $brandInstructions .= "       <tr class='total-row'><td colspan='3'>Total Contract Value</td><td>\$X,XXX.XX</td></tr>\n";
                $brandInstructions .= "     </tbody>\n";
                $brandInstructions .= "   </table>\n\n";
                
                $brandInstructions .= "7. LEGAL EMPHASIS:\n";
                $brandInstructions .= "   - Use <strong>BOLD CAPS</strong> for critical legal terms\n";
                $brandInstructions .= "   - Use <span class='legal-emphasis'>highlighted text</span> for key provisions\n";
                $brandInstructions .= "   - Use numbered lists for sequential requirements\n";
                $brandInstructions .= "   - Use bullet lists for non-sequential items\n\n";
                
                $brandInstructions .= "8. CONTENT EXTRACTION RULES:\n";
                $brandInstructions .= "   - Identify ALL services/deliverables from pasted content\n";
                $brandInstructions .= "   - Extract any pricing/payment terms mentioned\n";
                $brandInstructions .= "   - Identify timelines/milestones if mentioned\n";
                $brandInstructions .= "   - Extract party names and details if provided\n";
                $brandInstructions .= "   - Infer appropriate warranties and terms based on service type\n";
                $brandInstructions .= "   - Add industry-standard legal protections\n\n";
                
                $brandInstructions .= "9. DO NOT INCLUDE:\n";
                $brandInstructions .= "   - Signature blocks (already in template)\n";
                $brandInstructions .= "   - 'IN WITNESS WHEREOF' closing (already in template)\n";
                $brandInstructions .= "   - Contract header/title (already in template)\n\n";
                
                $brandInstructions .= "10. OUTPUT QUALITY:\n";
                $brandInstructions .= "    - Use formal, precise legal language\n";
                $brandInstructions .= "    - Be comprehensive but avoid unnecessary repetition\n";
                $brandInstructions .= "    - Include specific details from source content\n";
                $brandInstructions .= "    - Use blank lines ___________ where specific values should be filled in\n";
                $brandInstructions .= "    - Make the contract enforceable and professional\n";
            } elseif ($data->template === ReportTemplate::CV_RESUME) {
                $roleDescription = "expert career coach and resume writer";
                $taskDescription = "PROFESSIONAL ATS-FRIENDLY resume";
                $documentType = "resume";
                
                $brandInstructions .= "\n\n[RESUME STRUCTURE MANDATE]\n";
                $brandInstructions .= "You must strictly follow this structure for the resume HTML:\n";
                $brandInstructions .= "1. PROFESSIONAL SUMMARY: <h2>Professional Summary</h2><p>...</p>\n";
                $brandInstructions .= "2. EXPERIENCE: <h2>Work Experience</h2> (Use <h3>Job Title | Company</h3> and <div class='job-meta'>Date • Location</div> for each role. Use <ul> for bullets.)\n";
                $brandInstructions .= "3. EDUCATION: <h2>Education</h2> (Use <h3>University/School</h3> and <div class='job-meta'>Degree • Year</div>)\n";
                $brandInstructions .= "4. SKILLS: <h2>Skills</h2> (For 'Modern' variants, wrap individual skills in <span class='skill-tag'>Skill Name</span>. For 'Classic', use a simple comma-separated list or <p>.)\n";
                $brandInstructions .= "CRITICAL: Ensure you extract EDUCATION details from the source content if available. If missing, leave the section blank or omit it.\n";

                if ($data->targetRole) {
                    $brandInstructions .= "\n\n[RESUME TAILORING ACTIVE]\n";
                    $brandInstructions .= "TARGET ROLE: {$data->targetRole}\n";
                    
                    if ($data->jobDescription) {
                        $brandInstructions .= "JOB DESCRIPTION CONTEXT:\n{$data->jobDescription}\n";
                        $brandInstructions .= "INSTRUCTION: Analyze the Job Description. Identify top 5 keywords/skills. Rewrite the CV to explicitly match these keywords. Prove fit for this specific description.\n";
                    }

                    $brandInstructions .= "INSTRUCTION: \n";
                    $brandInstructions .= "1. Rewrite the professional summary to specifically align with the Target Role.\n";
                    $brandInstructions .= "2. Re-order or emphasize bullet points in Work Experience that demonstrate relevant skills.\n";
                    $brandInstructions .= "3. OUTPUT FORMAT: You must wrap your response in these specific tags:\n";
                    $brandInstructions .= "   <tailoring_report>\n";
                    $brandInstructions .= "      (Put the HTML for the AI Optimization Log here - the <ul> list of changes)\n";
                    $brandInstructions .= "   </tailoring_report>\n";
                    $brandInstructions .= "   <document_content>\n";
                    $brandInstructions .= "      (Put the main Resume HTML here - Summary, Experience, Education, Skills)\n";
                    $brandInstructions .= "   </document_content>\n";
                }

                // International CV Variant - Healthcare/MLS Format
                if ($data->variant === 'cv-international') {
                    $brandInstructions .= "\n\n[INTERNATIONAL CV FORMAT - HEALTHCARE/MLS STANDARD]\n";
                    $brandInstructions .= "You must strictly follow this structure for the international healthcare CV:\n\n";

                    $brandInstructions .= "1. PROFILE SECTION:\n";
                    $brandInstructions .= "   <h2 class='section-title'>PROFILE</h2>\n";
                    $brandInstructions .= "   <ul class='profile-summary'>\n";
                    $brandInstructions .= "      <li>Registered Medical Lab Scientist with # years of laboratory experience\n";
                    $brandInstructions .= "         <ul><li># of years of Current Lab Experience</li><li># of years of Previous Lab Experience</li></ul>\n";
                    $brandInstructions .= "      </li>\n";
                    $brandInstructions .= "   </ul>\n\n";

                    $brandInstructions .= "2. EDUCATION SECTION:\n";
                    $brandInstructions .= "   <h2 class='section-title'>EDUCATION</h2>\n";
                    $brandInstructions .= "   <div class='education-block'>\n";
                    $brandInstructions .= "      <div class='dates'>Dates Attended University</div>\n";
                    $brandInstructions .= "      <div class='institution'>University City, County</div>\n";
                    $brandInstructions .= "      <div class='degree'>Degree earned</div>\n";
                    $brandInstructions .= "   </div>\n\n";

                    $brandInstructions .= "3. WORK EXPERIENCE SECTION (for EACH facility, use this structure):\n";
                    $brandInstructions .= "   <h2 class='section-title'>WORK EXPERIENCE</h2>\n";
                    $brandInstructions .= "   <div class='facility-block'>\n";
                    $brandInstructions .= "      <div class='facility-name'>Current Facility Name</div>\n";
                    $brandInstructions .= "      <div class='facility-dates'>Joining Date (Month/Year) - Present</div>\n";
                    $brandInstructions .= "      <div class='facility-location'>Facility City, Country</div>\n";
                    $brandInstructions .= "      <div class='facility-website'>Facility website if any</div>\n";
                    $brandInstructions .= "      <div class='facility-description'>Brief hospital description: Describe number of beds in facility, type of patients served and list any accreditations</div>\n\n";
                    $brandInstructions .= "      <div class='job-details'>\n";
                    $brandInstructions .= "         <p><strong>Medical Laboratory Scientist:</strong> Rotating or Specific Benches</p>\n";
                    $brandInstructions .= "         <p><strong># Beds in Unit:</strong> # beds</p>\n";
                    $brandInstructions .= "         <p><strong>Patient Ratio:</strong> #:#</p>\n";
                    $brandInstructions .= "      </div>\n\n";
                    $brandInstructions .= "      <p><strong><u>Responsibilities:</u></strong></p>\n";
                    $brandInstructions .= "      <ul class='responsibility-list'><li>List daily clinical functions</li>...</ul>\n\n";
                    $brandInstructions .= "      <p><strong><u>Samples Handled:</u></strong></p>\n";
                    $brandInstructions .= "      <ul class='samples-list'><li>List the types and weekly volume</li>...</ul>\n\n";
                    $brandInstructions .= "      <p><strong>Equipment</strong></p>\n";
                    $brandInstructions .= "      <ul class='equipment-list'><li>List the types of lab equipment used</li>...</ul>\n";
                    $brandInstructions .= "   </div>\n\n";
                    $brandInstructions .= "   (Repeat facility-block for Previous Facilities)\n\n";

                    $brandInstructions .= "4. LICENSES & CERTIFICATIONS SECTION:\n";
                    $brandInstructions .= "   <h2 class='section-title'>LICENSES & CERTIFICATIONS</h2>\n";
                    $brandInstructions .= "   <ul class='certifications-block'>\n";
                    $brandInstructions .= "      <li>List All Licenses, RMT PRC etc.</li>\n";
                    $brandInstructions .= "      <li>List English Exams, IELTS Test Dates & Scores</li>\n";
                    $brandInstructions .= "      <li>List any Certifications, MLS(ASCPi), CGFNS, BLS, etc.</li>\n";
                    $brandInstructions .= "   </ul>\n\n";

                    $brandInstructions .= "IMPORTANT: Use underlined text formatting (<u>) for section headers like 'Responsibilities:', 'Samples Handled:'. Use the exact CSS classes provided above.\n";
                }
            } elseif ($data->template === ReportTemplate::COVER_LETTER) {
                $roleDescription = "expert career coach and persuasive writer";
                $taskDescription = "COMPELLING and PERSONALIZED cover letter";
                $documentType = "letter";

                $brandInstructions .= "\n\n[COVER LETTER STRATEGY]\n";
                $brandInstructions .= "You must write a highly persuasive cover letter body that strictly follows this 4-part structure:\n";
                $brandInstructions .= "DO NOT include the Header (Date, Address), Subject Line, or Sign-off (Sincerely, Name). These are automatically added by the template.\n";
                $brandInstructions .= "**IMPORTANT: DO NOT include section headers or labels like 'The Hook' or 'The Evidence'. Output ONLY the paragraphs.**\n";
                $brandInstructions .= "1. First Paragraph (The Hook): Start with a strong connection to the company (mission, recent project) and explicitly state the role applied for.\n";
                $brandInstructions .= "2. Middle Paragraphs (The Evidence): Select 2-3 'Hero Moments' from the source content (CV) that prove capability. Use numbers/metrics if available.\n";
                $brandInstructions .= "3. Later Paragraph (The Solution): Address a potential company pain point and explain how the candidate's skills solve it. Mention cultural fit.\n";
                $brandInstructions .= "4. Final Paragraph (Call to Action): Proactive closing requesting a discussion.\n";
                $brandInstructions .= "TONE: Narrative, conversational, enthusiastic, and persuasive.\n";
                
                if ($data->targetRole) {
                    $brandInstructions .= "TARGET ROLE: {$data->targetRole}\n";
                    if ($data->jobDescription) {
                        $brandInstructions .= "JOB DESCRIPTION CONTEXT:\n{$data->jobDescription}\n";
                        $brandInstructions .= "INSTRUCTION: Use the Job Description to identify specific pain points. In the 'Solution' paragraph, explicitly address how the candidate solves these specific problems.\n";
                    }
                    $brandInstructions .= "Ensure the tone matches the industry of the target role (e.g., Creative for Design, Formal for Law).\n";
                }
            }

            // Build template-specific user prompt
            $userPrompt = $this->buildUserPrompt($data, $documentType, $kbContext, $researchData);

            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->timeout(120)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an $roleDescription. 
                                         Your task is to take RAW research data, INTERNAL knowledge base data, and RAW source content and transform them into a $taskDescription.
                                         
                                         CORE DIRECTIVES:
                                         - **CRITICAL: OUTPUT MUST BE PURE HTML.** Do not use Markdown (no **bold**, no # headers, no -- separators). 
                                         - **CRITICAL: NO SYMBOLS.** Do not use # or * for formatting. Use <h2>, <h3>, <strong>, <ul>, <li> tags.
                                         - **CRITICAL: WRAP ALL TEXT.** Every paragraph must be in a <p> tag. Never return a block of text without tags.
                                         - **CRITICAL: RESTRUCTURE SOURCE CONTENT.** If provided, do not dump the 'Raw Source Content'. You must format it, break it into paragraphs, add headers, and lists.
                                         {$dataIntegrity}
                                         - THE 'RESEARCH DATA' AND 'INTERNAL KNOWLEDGE BASE' ARE YOUR PRIMARY SOURCES OF TRUTH. You must include the facts, figures, and insights from them. DO NOT GENERALIZE.
                                         - THE 'RESEARCH TOPIC' IS THE MANDATORY THEME. Every section must relate back to: {$data->researchTopic}.
                                         - GENERATE A DETAILED BUSINESS " . strtoupper($documentType) . ". Use a clean, single-column flow.
                                         - Use <h2> for section titles and <h3> for sub-sections.
                                         - Use <p>, <ul>, <li>, and <strong> for content.
                                         - ADVANCED LAYOUTS:
                                             * Use <table> for any data comparisons or metrics found in the research.
                                             * Use <div class='callout'>Content</div> for quotes or critical executive findings.
                                             * Use <div class='grid-2'><div>Part 1</div><div>Part 2</div></div> sparingly for small side-by-side data points.
                                         - Do not wrap in <html> or <body> tags.
                                         - Maintain a formal, authoritative, and analytical business tone.
                                         - YOUR PRIMARY JOB IS DESIGN AND STRUCTURE. Ensure the raw data looks like a premium produced $documentType.
                                         
                                         {$brandInstructions}"
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt
                        ],
                    ],
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                $rawResult = $response->json('choices.0.message.content');
                
                // Parse Split Response (Tailoring + Content)
                $tailoringReport = '';
                $cleanContent = $rawResult;

                if (preg_match('/<tailoring_report>(.*?)<\/tailoring_report>/s', $rawResult, $matches)) {
                    $tailoringReport = trim($matches[1]);
                }
                
                if (preg_match('/<document_content>(.*?)<\/document_content>/s', $rawResult, $matches)) {
                    $cleanContent = trim($matches[1]);
                } else {
                    // Fallback: If tags are missing but tailoring report exists, try to strip it
                    $cleanContent = preg_replace('/<tailoring_report>.*?<\/tailoring_report>/s', '', $cleanContent);
                }
                
                // Append separate marker for controller/view
                if ($tailoringReport) {
                    return $this->sanitizeOutput($cleanContent) . "<!-- TAILORING_REPORT_START -->" . $tailoringReport . "<!-- TAILORING_REPORT_END -->";
                }

                return $this->sanitizeOutput($cleanContent);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OpenAI Error: ' . $e->getMessage());
        }

        // Fallback: Use template-specific sample content instead of generic dummy
        return $this->getSampleContentForTemplate($data->template);
    }

    /**
     * Remove lingering markdown artifacts and ensure clean HTML.
     */
    private function sanitizeOutput(string $content): string
    {
        // Remove markdown bold/italic markers
        $content = str_replace(['**', '___'], '', $content);
        
        // Remove header hashes if they slipped through
        $content = preg_replace('/^#+\s+/m', '', $content);
        
        // Remove bullet stars if they slipped through (only if followed by space)
        $content = preg_replace('/^\*\s+/m', '• ', $content);

        // Remove horizontal rule markers
        $content = preg_replace('/^-{3,}$/m', '<hr>', $content);
        
        // Final trim
        return trim($content);
    }

    /**
     * Build template-specific user prompts for AI generation.
     * This ensures each template type gets properly formatted output.
     */
    private function buildUserPrompt(ReportRequestData $data, string $documentType, ?string $kbContext, ?string $researchData): string
    {
        // Ensure null values become empty strings
        $kbContext = $kbContext ?? '';
        $researchData = $researchData ?? '';
        
        $baseContext = "
            INTERNAL KNOWLEDGE BASE (CONTEXT):
            ---
            {$kbContext}
            ---

            RESEARCH DATA (PRIMARY SOURCE):
            ---
            {$researchData}
            ---
            
            RAW SOURCE CONTENT (SUPPLEMENTARY):
            ---
            {$data->contentData}
            ---
        ";

        // Contract-specific prompt
        if ($data->template === ReportTemplate::CONTRACT) {
            return "Generate a FORMAL LEGAL CONTRACT document. DO NOT generate an analysis, summary, or report.
                
                CONTRACT TYPE: {$data->variant} ({$data->template->label()})
                
                PARTIES INVOLVED:
                - Service Provider/First Party: (Extract from source content or use placeholder)
                - Client/Second Party: {$data->recipientName} ({$data->recipientTitle})
                
                FOCUS/SUBJECT MATTER: {$data->researchTopic}
                ADDITIONAL CONTEXT: {$data->prompt}
                
                {$baseContext}
                
                CRITICAL INSTRUCTIONS:
                1. This MUST be a legal contract, NOT a business analysis or report.
                2. Use proper legal contract structure with ARTICLE I, ARTICLE II, etc.
                3. Include WHEREAS recitals at the beginning.
                4. Include proper legal sections: Scope of Work, Compensation, Payment Terms, Intellectual Property, Warranties, Termination, etc.
                5. Extract all relevant details from the RAW SOURCE CONTENT and organize into proper legal clauses.
                6. Use formal legal language throughout.
                7. Include payment tables using <table class='payment-table'> if financial terms are mentioned.
                8. Use <div class='callout-critical'> for critical legal provisions.
                9. DO NOT include 'Overview', 'Key Findings', 'Strategic Recommendations' or other report-style sections.
                10. The output must look like a real, enforceable legal contract.
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
        }

        // Resume/CV-specific prompt
        if ($data->template === ReportTemplate::CV_RESUME) {
            return "Generate a professional resume/CV.
                
                CANDIDATE DETAILS: (Extract from source content)
                TARGET ROLE: {$data->targetRole}
                STYLE VARIANT: {$data->variant}
                
                {$baseContext}
                
                STRUCTURE:
                1. Professional Summary (tailored to target role if provided)
                2. Work Experience (reverse chronological)
                3. Education
                4. Skills
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
        }

        // Cover Letter-specific prompt
        if ($data->template === ReportTemplate::COVER_LETTER) {
            return "Generate a professional cover letter.
                
                FROM: {$data->recipientName}
                TO: Hiring Manager
                FOR POSITION: {$data->targetRole}
                
                {$baseContext}
                
                STRUCTURE:
                1. Opening hook explaining interest in the role
                2. 2-3 paragraphs showcasing relevant experience
                3. Closing with call to action
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
        }

        // Proposal-specific prompt
        if ($data->template === ReportTemplate::PROPOSAL) {
            return "Generate a professional business proposal.
                
                PROJECT/SERVICE: {$data->researchTopic}
                CLIENT: {$data->recipientName} ({$data->recipientTitle})
                OBJECTIVE: {$data->analysisType}
                FOCUS: {$data->prompt}
                STYLE VARIANT: {$data->variant}
                
                {$baseContext}
                
                STRUCTURE:
                1. Executive Summary
                2. Problem Statement / Client Needs
                3. Proposed Solution
                4. Scope of Work & Deliverables
                5. Timeline & Milestones
                6. Pricing / Investment
                7. Terms & Conditions
                8. Call to Action
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
        }

        // Default prompt for reports and other templates
        return "Generate a highly detailed business {$data->template->label()} $documentType. 
                
                MANDATORY RESEARCH TOPIC: {$data->researchTopic}
                
                Analysis Case / Objective: {$data->analysisType}.
                Focus / Strategic Mandate: {$data->prompt}.
                Style Variant: {$data->variant}. 
                Recipient: {$data->recipientName} ({$data->recipientTitle}). 
                
                {$baseContext}
                
                Instruction: Create a comprehensive $documentType specifically about '{$data->researchTopic}'. Use the RESEARCH DATA and INTERNAL KNOWLEDGE BASE provided as your factual base. Build a detailed narrative using the business layout tools (tables, callouts, grids) provided in your system instructions. Do not omit data. Expand the raw research into professional technical analysis. **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
    }

    private function getDummyContent(): string
    {
        return "
            <h2>Overview</h2>
            <p>This report provides a comprehensive analysis based on the latest market data and strategic indicators. Our findings suggest a strong upward trajectory in key performance areas, driven by robust demand and operational efficiencies.</p>
            
            <h3>Key Findings</h3>
            <ul>
                <li><strong>Substantial Growth:</strong> Revenue has increased by 15% quarter-over-quarter, exceeding initial projections.</li>
                <li><strong>Market Penetration:</strong> New product lines have successfully captured 8% additional market share in the target demographic.</li>
                <li><strong>Operational Cost Reduction:</strong> Implementation of automated workflows has reduced overhead by 12%.</li>
            </ul>

            <h2>Detailed Analysis</h2>
            <p>The current fiscal period has been marked by significant volatility in the broader market. However, our strategic positioning allowed us to capitalize on emerging trends. Specifically, the shift towards sustainable solutions has aligned perfectly with our recent product roadmap updates.</p>
            <p>Competitor analysis reveals that while main rivals are consolidating, there is a clear opportunity to expand into underserved niche segments. Our recommendation is to accelerate the R&amp;D timeline for Project Alpha to seize this window of opportunity.</p>

            <h3>Strategic Recommendations</h3>
            <p>Based on these insights, we recommend the following immediate actions:</p>
            <ol>
                <li>Increase marketing spend in Q3 to support the new launch.</li>
                <li>Hire 3 additional senior engineers to support the AI initiative.</li>
                <li>Diversify supply chain partners to mitigate geopolitical risks.</li>
            </ol>

            <h2>Conclusion</h2>
            <p>In conclusion, the organization is well-positioned for sustained growth. By focusing on core competencies and remaining agile in the face of market changes, we anticipate continuing to outperform industry benchmarks.</p>
        ";
    }

    private function getSampleContentForTemplate(ReportTemplate $template): string
    {
        return match ($template) {
            ReportTemplate::EXECUTIVE_SUMMARY => "
                <h2>Strategic Overview</h2>
                <p>This executive summary highlights the core achievements and strategic direction for the upcoming period. Data suggests a strong alignment between operational capacity and market demand.</p>
                <div class='callout'>
                    <strong>Key Insight:</strong> Operational efficiency has increased by 14% since the implementation of the new RAG protocol.
                </div>
                <h3>Primary Objectives</h3>
                <ul>
                    <li><strong>Growth:</strong> Scaling technical infrastructure to support 1M+ concurrent nodes.</li>
                    <li><strong>Integration:</strong> Seamlessly mapping internal knowledge bases to external research data.</li>
                    <li><strong>Security:</strong> Enforcing multi-layered identity access gateways across all sub-accounts.</li>
                </ul>
            ",
            ReportTemplate::MARKET_ANALYSIS => "
                <h2>Market Landscape Analysis</h2>
                <p>The current market environment is characterized by rapid technological shifts and evolving consumer expectations. Our analysis identifies three key pillars for sustained competitiveness.</p>
                <div class='grid-2'>
                    <div>
                        <h3>Market Drivers</h3>
                        <ul>
                            <li>Increasing demand for AI-driven automation</li>
                            <li>Shift towards decentralized intelligence</li>
                            <li>Emphasis on data privacy and sovereignty</li>
                        </ul>
                    </div>
                    <div>
                        <h3>Competitive Threats</h3>
                        <ul>
                            <li>Rapid commoditization of basic LLM services</li>
                            <li>Emerging regulatory frameworks in key sectors</li>
                            <li>Talent scarcity in specialized engineering roles</li>
                        </ul>
                    </div>
                </div>
                <h3>Market Sentiment Grid</h3>
                <table>
                    <thead>
                        <tr><th>Sector</th><th>Sentiment</th><th>Confidence</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Enterprise SaaS</td><td>Bullish</td><td>88%</td></tr>
                        <tr><td>Infrastructure</td><td>Stable</td><td>72%</td></tr>
                        <tr><td>Consulting</td><td>Disruptive</td><td>91%</td></tr>
                    </tbody>
                </table>
            ",
            ReportTemplate::FINANCIAL_OVERVIEW => "
                <h2>Fiscal Intelligence Summary</h2>
                <p>Financial performance indicators remain strong with significant improvements in capital efficiency. High-level metrics indicate a resilient revenue model capable of withstanding market volatility.</p>
                <h3>Consolidated Metrics</h3>
                <table>
                    <thead>
                        <tr><th>KPI</th><th>Actual</th><th>Target</th><th>Variance</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Gross Margin</td><td>68%</td><td>65%</td><td><span style='color:green'>+3%</span></td></tr>
                        <tr><td>Burn Rate</td><td>$140k</td><td>$160k</td><td><span style='color:green'>-12.5%</span></td></tr>
                        <tr><td>CAC Payback</td><td>4.2 Months</td><td>6.0 Months</td><td><span style='color:green'>-1.8m</span></td></tr>
                    </tbody>
                </table>
                <div class='callout'>
                    <strong>Audit Result:</strong> All nodes reconciled. Treasury balance verified at 99.98% accuracy.
                </div>
            ",
            ReportTemplate::COMPETITIVE_INTELLIGENCE => "
                <h2>Competitive Intel Brief</h2>
                <p>Real-time monitoring of competitor protocol deployments reveals a shift toward agentic frameworks. We maintain a technical lead in RAG-integrated report architecture.</p>
                <h3>Battlecard Comparison</h3>
                <table>
                    <thead>
                        <tr><th>Feature</th><th>ArchitectAI</th><th>Competitor X</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Deep Research Hub</td><td>Native Integration</td><td>API Only</td></tr>
                        <tr><td>Multi-Tenant IAM</td><td>Hardened Isolation</td><td>Basic Groups</td></tr>
                        <tr><td>Custom Templates</td><td>100% Flexible</td><td>Fixed Overlays</td></tr>
                    </tbody>
                </table>
            ",
            ReportTemplate::INFOGRAPHIC => "
                <h2>One-Pager Visual Summary</h2>
                <p>Transformation of complex research data into a high-impact visual representation. Optimized for quick executive review and strategic decision-making.</p>
                <div class='callout'>
                    <strong>North Star Metric:</strong> 2.4k Intelligence Reports generated in Q4 via RAG-sync.
                </div>
            ",
            ReportTemplate::TREND_ANALYSIS => "
                <h2>Emerging Trend Architecture</h2>
                <p>Identification of patterns across a 7-day network intensity sweep. Our model forecasts a surge in request complexity for specialized industry reports.</p>
                <div class='callout'>
                    <strong>Forecast Alpha:</strong> Automation of 'Tier-2' analysis will be standard by 2026.
                </div>
            ",
            ReportTemplate::PROPOSAL => "
                <h2>Executive Proposal</h2>
                <p>We are pleased to present this proposal for your review. Our team has analyzed your requirements and developed a strategy that aligns with your business goals.</p>
                <div class='callout'>
                    <strong>Value Proposition:</strong> Our solution is designed to increase operational efficiency by 25% within the first 6 months.
                </div>
                <h3>Project Scope</h3>
                <ul>
                    <li>Phase 1: Discovery & Strategy</li>
                    <li>Phase 2: Implementation & Development</li>
                    <li>Phase 3: Testing & Deployment</li>
                </ul>
                <h3>Investment Summary</h3>
                <table>
                    <thead><tr><th>Item</th><th>Description</th><th>Cost</th></tr></thead>
                    <tbody>
                        <tr><td>Strategy</td><td>Initial consulting and roadmap</td><td>$5,000</td></tr>
                        <tr><td>Development</td><td>Core system build</td><td>$15,000</td></tr>
                        <tr><td>Total</td><td><strong>Project Total</strong></td><td><strong>$20,000</strong></td></tr>
                    </tbody>
                </table>
            ",
            ReportTemplate::CONTRACT => "
                <div class='parties-section'>
                    <div class='party-block'>
                        <h3>Service Provider (\"Provider\")</h3>
                        <div class='party-field'><span class='party-label'>Name:</span><span class='party-value'>John Smith</span></div>
                        <div class='party-field'><span class='party-label'>Business:</span><span class='party-value'>TechFlow Solutions LLC</span></div>
                        <div class='party-field'><span class='party-label'>Address:</span><span class='party-value'>123 Innovation Drive, San Francisco, CA 94102</span></div>
                        <div class='party-field'><span class='party-label'>Email:</span><span class='party-value'>john@techflow.io</span></div>
                    </div>
                    <div class='party-block'>
                        <h3>Client (\"Client\")</h3>
                        <div class='party-field'><span class='party-label'>Name:</span><span class='party-value'>Acme Corporation</span></div>
                        <div class='party-field'><span class='party-label'>Business:</span><span class='party-value'>Acme Corporation Inc.</span></div>
                        <div class='party-field'><span class='party-label'>Address:</span><span class='party-value'>456 Enterprise Blvd, New York, NY 10001</span></div>
                        <div class='party-field'><span class='party-label'>Email:</span><span class='party-value'>contracts@acme.com</span></div>
                    </div>
                </div>

                <div class='recitals'>
                    <p>WHEREAS, the Client desires to engage the Provider to deliver professional software development services;</p>
                    <p>WHEREAS, the Provider possesses the necessary skills, expertise, and resources to provide such services;</p>
                    <p>WHEREAS, both parties wish to establish clear terms and conditions governing this business relationship;</p>
                    <p class='therefore-clause'>NOW, THEREFORE, in consideration of the mutual covenants and agreements contained herein, and for other good and valuable consideration, the receipt and sufficiency of which are hereby acknowledged, the parties agree as follows:</p>
                </div>

                <h2>ARTICLE I: SCOPE OF WORK AND DELIVERABLES</h2>
                <h3>1.1 Project Description</h3>
                <p>The Provider agrees to design, develop, and deploy a custom web application for the Client, specifically:</p>
                <ul>
                    <li>Complete website development using modern web technologies</li>
                    <li>Responsive design compatible with desktop, tablet, and mobile devices</li>
                    <li>Custom admin panel and content management functionality</li>
                    <li>API integration with third-party services</li>
                    <li>Security implementation and performance optimization</li>
                </ul>

                <h3>1.2 Excluded from Scope</h3>
                <p>The following items are explicitly excluded unless separately contracted: ongoing hosting fees, domain registration, third-party service subscriptions, content writing beyond basic optimization, and ongoing maintenance beyond the support period.</p>

                <h2>ARTICLE II: COMPENSATION AND PAYMENT TERMS</h2>
                <h3>2.1 Total Contract Value</h3>
                <p>The total compensation for all services described in this Agreement is <strong>TWENTY THOUSAND DOLLARS ($20,000.00 USD)</strong>.</p>

                <table class='payment-table'>
                    <thead><tr><th>Payment</th><th>Description</th><th>Due Date</th><th>Amount</th></tr></thead>
                    <tbody>
                        <tr><td>Payment 1</td><td>Initial Deposit (50%)</td><td>Upon contract execution</td><td>$10,000.00</td></tr>
                        <tr><td>Payment 2</td><td>Final Payment (50%)</td><td>Upon project completion</td><td>$10,000.00</td></tr>
                        <tr class='total-row'><td colspan='3'>Total Contract Value</td><td>$20,000.00</td></tr>
                    </tbody>
                </table>

                <div class='callout-critical'>
                    <strong>CRITICAL PAYMENT PROVISION:</strong>
                    The Provider shall not be obligated to commence any work or proceed to the next milestone until the applicable milestone payment has been received and cleared. This is a material term of this Agreement.
                </div>

                <h2>ARTICLE III: INTELLECTUAL PROPERTY RIGHTS</h2>
                <p>Upon receipt of final payment and full satisfaction of all financial obligations, Provider hereby assigns, transfers, and conveys to Client all custom code, design elements, and documentation created specifically for this project.</p>

                <h2>ARTICLE IV: WARRANTIES AND DISCLAIMERS</h2>
                <p>Provider warrants that services will be performed in a professional and workmanlike manner. Work will conform to industry standards for web development and be reasonably free from defects at time of delivery.</p>

                <div class='callout'>
                    <strong>Confidentiality:</strong> Both parties agree to maintain the confidentiality of all proprietary information disclosed during the term of this agreement and for three (3) years thereafter.
                </div>

                <h2>ARTICLE V: TERMINATION</h2>
                <p>Either party may terminate this Agreement with thirty (30) days written notice. In the event of termination, the Client shall pay for all services rendered up to the termination date. All milestone payments already made are non-refundable once work has commenced.</p>
            ",
            ReportTemplate::CV_RESUME => "
                <h2>Professional Summary</h2>
                <p>Strategic and results-driven Senior Architect with 8+ years of experience in enterprise system design. Expert in cloud-native infrastructure and AI-driven automation. Proven track record of scaling high-performance teams and delivering multi-tenant SaaS solutions.</p>
                
                <h2>Work Experience</h2>
                <h3>Senior Lead Engineer | TechFlow Corp</h3>
                <div class='job-meta'>Jan 2022 - Present • San Francisco, CA</div>
                <ul>
                    <li>Spearheaded the migration of legacy monoliths to microservices, reducing deployment time by 60%.</li>
                    <li>Managed a team of 12 full-stack engineers, conducting code reviews and architectural planning.</li>
                    <li>Implemented a global RAG protocol for internal knowledge sharing.</li>
                </ul>

                <h3>Software Consultant | Freelance</h3>
                <div class='job-meta'>Jun 2018 - Dec 2021 • Remote</div>
                <ul>
                    <li>Delivered custom ERP solutions for Fortune 500 clients.</li>
                    <li>Optimized database performance, resulting in a 40% reduction in query latency.</li>
                </ul>

                <h2>Key Skills</h2>
                <p>Laravel, Vue.js, AWS, Docker, Kubernetes, System Design, Team Leadership</p>
            ",
            ReportTemplate::COVER_LETTER => "
                <p><strong>[Your Name]</strong><br>[Your Address]<br>[City, State, Zip Code]<br>[Your Email]<br>[Your Phone Number]</p>
                <p>Date: [Month Day, Year]</p>
                <p><strong>[Hiring Manager Name]</strong><br>[Title]<br>[Company Name]<br>[Company Address]</p>
                <p>Dear [Hiring Manager Name],</p>
                <p>I have been following [Company Name]'s recent work in [Industry/Project], and I am thrilled to apply for the <strong>[Job Title]</strong> position. Your commitment to [Mission/Value] resonates deeply with my professional philosophy, and I see a perfect alignment between my skills and your current goals.</p>
                <p>Throughout my career, I have consistently delivered results. As a [Previous Role] at [Previous Company], I [Action Verb] [Key Responsibility], resulting in a [Quantifiable Achievement, e.g., 20% increase in efficiency]. In another instance, I led a project that [Another Achievement], demonstrating my ability to [Skill relevant to role].</p>
                <p>I understand that [Company Name] is currently facing challenges with [Potential Pain Point]. My background in [Skill/Area] allows me to step in and provide immediate solutions. I pride myself on being [Cultural Fit Attribute, e.g., detail-oriented and collaborative], ensuring I can integrate seamlessly into your high-performing team.</p>
                <p>I would welcome the opportunity to discuss how my background can help [Company Name] achieve its objectives. Thank you for your time and consideration.</p>
                <p>Sincerely,</p>
                <p><strong>[Your Name]</strong></p>
            ",
            default => $this->getSampleContent(),
        };
    }

    /**
     * RAG: Retrieve relevant context from the tenant's knowledge base.
     */
    protected function getKnowledgeBaseContext(string $query): ?string
    {
        $tenant = app(\App\Models\Tenant::class);
        if (!$tenant || empty($query)) return null;

        $assets = \App\Models\KnowledgeBaseAsset::where('tenant_id', $tenant->id)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%" . $query . "%")
                  ->orWhere('content', 'like', "%" . $query . "%");
            })
            ->limit(3)
            ->get();

        if ($assets->isEmpty()) return null;

        return $assets->map(fn($a) => "--- SOURCE: {$a->title} ---
{$a->content}")->implode("\n\n");
    }
}
