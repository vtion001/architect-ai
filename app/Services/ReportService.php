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

        return View::make($data->template->view(), [
            'content' => $content,
            'recipientName' => $data->recipientName ?? 'Recipient',
            'variant' => $data->variant,
            'brandColor' => $brandColor,
            'logoUrl' => $logoUrl
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
            'variant' => $variant,
            'brandColor' => $brandColor,
            'logoUrl' => $logoUrl
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
                $roleDescription = "expert legal drafter and contract specialist";
                $taskDescription = "LEGALLY SOUND business contract";
                $documentType = "contract";
            }

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
                            'content' => "Generate a highly detailed business {$data->template->label()} $documentType. 
                                         
                                         MANDATORY RESEARCH TOPIC: {$data->researchTopic}
                                         
                                         Analysis Case / Objective: {$data->analysisType}.
                                         Focus / Strategic Mandate: {$data->prompt}.
                                         Style Variant: {$data->variant}. 
                                         Recipient: {$data->recipientName} ({$data->recipientTitle}). 
                                         
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
                                         
                                         Instruction: Create a comprehensive $documentType specifically about '{$data->researchTopic}'. Use the RESEARCH DATA and INTERNAL KNOWLEDGE BASE provided as your factual base. Build a detailed narrative using the business layout tools (tables, callouts, grids) provided in your system instructions. Do not omit data. Expand the raw research into professional technical analysis. **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**"
                        ],
                    ],
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                $rawResult = $response->json('choices.0.message.content');
                return $this->sanitizeOutput($rawResult);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OpenAI Error: ' . $e->getMessage());
        }

        return $this->getDummyContent();
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
                <h2>1. Services Provided</h2>
                <p>The Provider agrees to deliver the services as outlined in the attached Statement of Work (SOW). All services will be performed in a professional manner.</p>
                <h3>2. Payment Terms</h3>
                <p>Client agrees to pay the Provider the total sum of $20,000. Payment shall be made in two installments: 50% upfront and 50% upon completion.</p>
                <div class='callout'>
                    <strong>Confidentiality:</strong> Both parties agree to maintain the confidentiality of all proprietary information disclosed during the term of this agreement.
                </div>
                <h3>3. Termination</h3>
                <p>Either party may terminate this agreement with 30 days written notice. In the event of termination, the Client shall pay for all services rendered up to the termination date.</p>
            ",
            default => $this->getSampleContent(),
        };
    }

    private function getSampleContent(): string
    {
        return "
            <h2>Executive Overview</h2>
            <p>This is a sample report demonstrating the template layout and styling. The actual content will be generated based on your inputs and data sources.</p>
            
            <div class='callout'>
                <strong>Strategic Note:</strong> This template now supports multi-page dynamic flow. The AI can automatically expand your content based on research depth and provide data-driven tables.
            </div>

            <h3>Key Metrics Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Metric Category</th>
                        <th>Current Value</th>
                        <th>YoY Growth</th>
                        <th>Target (Q4)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Revenue Growth</td>
                        <td>+24.5%</td>
                        <td>4.2%</td>
                        <td>+30%</td>
                    </tr>
                    <tr>
                        <td>Market Share</td>
                        <td>18.3%</td>
                        <td>2.6%</td>
                        <td>20%</td>
                    </tr>
                    <tr>
                        <td>Customer NPS</td>
                        <td>4.8/5.0</td>
                        <td>0.3</td>
                        <td>4.9</td>
                    </tr>
                </tbody>
            </table>

            <div class='page-break'></div>

            <h2>Market Analysis</h2>
            <p>The current market landscape presents significant opportunities for expansion. Key trends indicate a shift towards digital-first solutions, with growing demand in the enterprise segment.</p>
            
            <div class='grid-2'>
                <div>
                    <h3>Competitive Edge</h3>
                    <ul>
                        <li>AI-powered analytics</li>
                        <li>Seamless integration</li>
                        <li>Enterprise security</li>
                    </ul>
                </div>
                <div>
                    <h3>Growth Areas</h3>
                    <ul>
                        <li>Cloud Infrastructure</li>
                        <li>Data Governance</li>
                        <li>Automated Reporting</li>
                    </ul>
                </div>
            </div>

            <h2>Strategic Recommendations</h2>
            <p>Based on comprehensive data analysis, we recommend prioritizing the following initiatives:</p>
            <ul>
                <li>Expand product portfolio in high-growth segments</li>
                <li>Invest in R&D for next-generation platform</li>
                <li>Strengthen partnerships in key markets</li>
            </ul>
        ";
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

        return $assets->map(fn($a) => "--- SOURCE: {$a->title} ---\n{$a->content}")->implode("\n\n");
    }
}
