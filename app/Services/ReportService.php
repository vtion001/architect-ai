<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use App\Services\Report\SampleContentProvider;
use Illuminate\Support\Facades\View;

class ReportService
{
    public function __construct(
        private readonly ResearchService $researchService,
        private readonly KnowledgeBaseService $knowledgeBaseService,
        private readonly BrandResolverService $brandResolverService,
        private readonly SampleContentProvider $sampleContentProvider
    ) {}

    public function generateReportHtml(ReportRequestData $data): string
    {
        $content = $this->generateContent($data);

        // Resolve Brand Logic via centralized service
        $brandData = $this->brandResolverService->resolve($data->brandId);
        
        return View::make($data->template->view(), [
            'content' => $content,
            'recipientName' => $data->recipientName ?? 'Recipient',
            'recipientTitle' => $data->recipientTitle,
            'variant' => $data->variant,
            'brandColor' => $brandData['primary_color'],
            'logoUrl' => $brandData['logo_url'],
            'profilePhotoUrl' => $data->profilePhotoUrl,
            'contactInfo' => [
                'email' => $data->email,
                'phone' => $data->phone,
                'location' => $data->location,
                'website' => $data->website,
            ],
            'personalInfo' => $data->personalInfo,
            // For Cover Letter, map recipient/sender fields appropriately
            'senderName' => $data->recipientName,
            'senderTitle' => $data->recipientTitle,
            'companyAddress' => $data->companyAddress,
        ])->render();
    }

    public function generatePreviewHtml(ReportTemplate $template, ?string $variant = null, ?string $brandId = null, array $overrides = []): string
    {
        // Use centralized sample content provider
        $sampleContent = $this->sampleContentProvider->getContent($template, $overrides);

        // Resolve Brand Logic via centralized service
        $brandData = $this->brandResolverService->resolve($brandId);

        return View::make($template->view(), [
            'content' => $sampleContent,
            'recipientName' => 'Sample Recipient',
            'recipientTitle' => 'Department Manager',
            'senderName' => 'Your Name',
            'senderTitle' => 'Professional Title',
            'companyAddress' => '123 Business Rd, Tech City',
            'variant' => $variant,
            'brandColor' => $brandData['primary_color'],
            'logoUrl' => $brandData['logo_url'],
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
        // 1. RAG: Fetch relevant internal knowledge base assets using centralized service
        $kbContext = $this->knowledgeBaseService->getContext($data->researchTopic ?? $data->prompt ?? '');

        // Perform Deep Research if a topic is provided
        $researchData = '';
        if ($data->researchTopic) {
            \Illuminate\Support\Facades\Log::info("Performing Gemini deep research for: " . $data->researchTopic);
            $researchData = $this->researchService->performResearch($data->researchTopic);
            \Illuminate\Support\Facades\Log::info("Research received. Length: " . strlen($researchData));
        }

        // Build data integrity instructions for non-resume templates
        $dataIntegrity = "";
        if ($data->template !== ReportTemplate::CV_RESUME && $data->template !== ReportTemplate::COVER_LETTER) {
            $dataIntegrity = "- **CRITICAL: DATA INTEGRITY.** You must RETAIN all quantitative data, metrics, and specific technical units (e.g., m2, kg, %, $, dates) from the source content. Do not approximate or omit these details.\n";
        }

        // Resolve Brand Blueprints via centralized service
        $brandInstructions = $this->brandResolverService->buildBrandInstructions(
            $data->brandId, 
            $data->template->value
        );

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

        // Fallback: Use template-specific sample content via centralized provider
        $overrides = $data->contractDetails ?? [];
        $overrides['recipientName'] = $data->recipientName;
        $overrides['recipientTitle'] = $data->recipientTitle;
        return $this->sampleContentProvider->getContent($data->template, $overrides);
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
            $cDetails = $data->contractDetails;
            
            $providerDetails = "
                - Name: " . ($cDetails['providerName'] ?? 'Service Provider') . "
                - Business: " . ($cDetails['providerBusiness'] ?? '') . "
                - Address: " . ($cDetails['providerAddress'] ?? '') . "
                - Email: " . ($cDetails['providerEmail'] ?? '') . "
                - Tax ID: " . ($cDetails['providerTaxId'] ?? '');

            $clientDetails = "
                - Name: {$data->recipientName}
                - Title: {$data->recipientTitle}
                - Business: {$data->companyAddress} 
                - Address: " . ($cDetails['clientAddress'] ?? '') . "
                - City/State: " . ($cDetails['clientCity'] ?? '') . "
                - Country: " . ($cDetails['clientCountry'] ?? '') . "
                - Email: " . ($cDetails['clientEmail'] ?? '') . "
                - Tax ID: " . ($cDetails['clientTaxId'] ?? '');

            $financialDetails = "
                - Contract Value: " . ($cDetails['contractValue'] ?? 'To be agreed') . "
                - Start Date: " . ($cDetails['startDate'] ?? 'Upon signing') . "
                - Duration: " . ($cDetails['duration'] ?? 'Until completion');

            return "Generate a COMPREHENSIVE, LEGALLY SOUND, PROFESSIONALLY FORMATTED business contract.
                
                CONTRACT TYPE: {$data->variant} ({$data->template->label()})
                
                PARTIES INVOLVED:
                - Service Provider/First Party: $providerDetails
                - Client/Second Party: $clientDetails
                
                CONTRACT SPECIFICS: $financialDetails
                
                FOCUS/SUBJECT MATTER: {$data->researchTopic}
                ADDITIONAL CONTEXT: {$data->prompt}
                
                {$baseContext}
                
                MANDATORY CONTRACT STRUCTURE (Include ALL of the following articles):

                1. **PARTIES SECTION** at the top with fields for BOTH parties:
                   - Use <div class='parties-section'> with two <div class='party-block'>
                   - Include fill-in fields: Name, Business Name, Address, City/State/Postal, Country, Email, Phone, Tax ID
                   - Use <span class='fill-field'>_________</span> for blanks to fill in

                2. **RECITALS** with WHEREAS clauses:
                   - Use <div class='recitals'> 
                   - 3-4 WHEREAS statements explaining purpose
                   - NOW, THEREFORE clause with <p class='therefore-clause'>

                3. **ARTICLE I: SCOPE OF WORK AND DELIVERABLES**
                   - Section 1.1 Project Description with bullet list
                   - Section 1.2 Excluded from Scope

                4. **ARTICLE II: PROJECT TIMELINE AND MILESTONES**
                   - Section 2.1 Project Duration with fill-in: <span class='fill-field'>____ weeks/months</span>
                   - Section 2.2 Project Commencement Date (with conditions)
                   - Section 2.3 Milestone Schedule using <table class='milestone-table'>

                5. **ARTICLE III: COMPENSATION AND PAYMENT TERMS**
                   - Section 3.1 Total Contract Value with fill-in amount
                   - Section 3.2 Payment Structure using <table class='payment-table'> with 3 payment milestones
                   - Section 3.3 Critical Payment Provisions in <div class='callout-critical'>
                   - Section 3.4 Late Payment Penalties

                6. **ARTICLE IV: CLIENT RESPONSIBILITIES AND OBLIGATIONS**
                   - Section 4.1 Timely Cooperation requirements
                   - Section 4.2 Consequences of Non-Compliance

                7. **ARTICLE V: INTELLECTUAL PROPERTY RIGHTS**
                   - Section 5.1 Ownership Upon Final Payment
                   - Section 5.2 Provider-Retained Rights  
                   - Section 5.3 Confidentiality

                8. **ARTICLE VI: WARRANTIES AND DISCLAIMERS**
                   - Section 6.1 Provider Warranties
                   - Section 6.2 Disclaimers (NO WARRANTY OF RESULTS)
                   - Section 6.3 Limitation of Liability

                9. **ARTICLE VII: DISPUTE RESOLUTION AND GOVERNING LAW**
                   - Section 7.1 Governing Law with fill-in jurisdiction
                   - Section 7.2 Dispute Resolution Procedure (Negotiation -> Mediation -> Arbitration)

                10. **ARTICLE VIII: TERMINATION**
                    - Section 8.1 Termination for Convenience
                    - Section 8.2 Termination for Cause

                11. **ARTICLE IX: MISCELLANEOUS PROVISIONS**
                    - Entire Agreement, Amendments, Independent Contractor clauses

                12. **SIGNATURES** section at the end

                CRITICAL FORMATTING RULES:
                - Use <h2> for ARTICLE titles
                - Use <h3> for section numbers (1.1, 1.2, etc.)
                - Use <ul> and <li> for bullet points
                - Use <ol> for numbered lists
                - Use <table class='payment-table'> for payment schedules
                - Use <table class='milestone-table'> for milestone schedules
                - Use <div class='callout-critical'> for critical provisions (ALL CAPS warnings)
                - Use <div class='callout'> for important notes
                - Use <span class='fill-field'>_________</span> for blank fields to fill in
                - Use formal, legal language throughout
                - DO NOT use 'Overview', 'Key Findings', or other report-style sections
                - This must look like a REAL, ENFORCEABLE legal contract

                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS (#, *, **, etc.).**";
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
            $brandData = $this->brandResolverService->resolve($data->brandId);
            $brandName = $brandData['brand']?->name ?? 'Service Provider';

            return "Generate a professional business proposal.
                
                PROPOSAL FROM (The Brand): {$brandName}
                PROPOSAL FOR (The Client / Identity Destination): {$data->recipientName}
                CLIENT ROLE: {$data->recipientTitle}
                CLIENT ADDRESS: {$data->companyAddress}
                
                PROJECT/SERVICE: {$data->researchTopic}
                OBJECTIVE: {$data->analysisType}
                FOCUS: {$data->prompt}
                STYLE VARIANT: {$data->variant}
                
                {$baseContext}
                
                CORE MANDATE: 
                - Address the content to the Client ({$data->recipientName}).
                - The document is written FROM the perspective of {$brandName}.
                - Ensure the 'Problem Statement' and 'Proposed Solution' are centered on the Client's needs.
                
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

    /**
     * Get generic dummy content for fallback.
     * 
     * @deprecated Use SampleContentProvider::getContent() with a template type instead.
     */
    private function getDummyContent(): string
    {
        return $this->sampleContentProvider->getContent(ReportTemplate::EXECUTIVE_SUMMARY);
    }

    /**
     * Get sample content for a specific template.
     * 
     * @deprecated Use SampleContentProvider::getContent() directly.
     */
    private function getSampleContentForTemplate(ReportTemplate $template, array $overrides = []): string
    {
        return $this->sampleContentProvider->getContent($template, $overrides);
    }

    /**
     * Get sample content - delegate to SampleContentProvider.
     * 
     * @deprecated Use SampleContentProvider directly.
     */
    private function getSampleContent(): string  
    {
        return $this->sampleContentProvider->getContent(ReportTemplate::EXECUTIVE_SUMMARY);
    }

    /**
     * RAG: Retrieve relevant context from the tenant's knowledge base.
     * 
     * @deprecated Use KnowledgeBaseService::getContext() directly.
     */
    protected function getKnowledgeBaseContext(string $query): ?string
    {
        return $this->knowledgeBaseService->getContext($query);
    }
}

