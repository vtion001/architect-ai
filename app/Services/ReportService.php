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
        $content = $this->generateContent($data);

        return View::make($data->template->view(), [
            'content' => $content,
            'recipientName' => $data->recipientName ?? 'Recipient',
            'variant' => $data->variant
        ])->render();
    }

    public function generatePreviewHtml(ReportTemplate $template, ?string $variant = null): string
    {
        $sampleContent = $this->getSampleContent();

        return View::make($template->view(), [
            'content' => $sampleContent,
            'recipientName' => 'Sample Recipient',
            'variant' => $variant
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

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->timeout(120)
                ->post('https://api.openai.com/v1_1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an expert business analyst and technical writer. 
                                         Your task is to take RAW research data, INTERNAL knowledge base data, and RAW source content and transform them into a HIGH-END HTML business report.
                                         
                                         CORE DIRECTIVES:
                                         - THE 'RESEARCH DATA' AND 'INTERNAL KNOWLEDGE BASE' ARE YOUR PRIMARY SOURCES OF TRUTH. You must include the facts, figures, and insights from them. DO NOT GENERALIZE.
                                         - THE 'RESEARCH TOPIC' IS THE MANDATORY THEME. Every section must relate back to: {$data->researchTopic}.
                                         - GENERATE A DETAILED BUSINESS REPORT. Use a clean, single-column flow.
                                         - Use <h2> for section titles and <h3> for sub-sections.
                                         - Use <p>, <ul>, <li>, and <strong> for content.
                                         - ADVANCED LAYOUTS:
                                             * Use <table> for any data comparisons or metrics found in the research.
                                             * Use <div class='callout'>Content</div> for quotes or critical executive findings.
                                             * Use <div class='grid-2'><div>Part 1</div><div>Part 2</div></div> sparingly for small side-by-side data points.
                                         - Do not wrap in <html> or <body> tags.
                                         - Maintain a formal, authoritative, and analytical business tone.
                                         - If 'Source Content' is provided, restructure and professionalize it into the report narrative.
                                         - YOUR PRIMARY JOB IS DESIGN AND STRUCTURE. Ensure the raw data looks like a premium produced report."
                        ],
                        [
                            'role' => 'user',
                            'content' => "Generate a highly detailed business {$data->template->label()} report. 
                                         
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
                                         
                                         Instruction: Create a comprehensive report specifically about '{$data->researchTopic}'. Use the RESEARCH DATA and INTERNAL KNOWLEDGE BASE provided as your factual base. Build a detailed narrative using the business layout tools (tables, callouts, grids) provided in your system instructions. Do not omit data. Expand the raw research into professional technical analysis."
                        ],
                    ],
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OpenAI Error: ' . $e->getMessage());
        }

        return $this->getDummyContent();
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
                $q->where('title', 'like', "%" + $query + "%")
                  ->orWhere('content', 'like', "%" + $query + "%");
            })
            ->limit(3)
            ->get();

        if ($assets->isEmpty()) return null;

        return $assets->map(fn($a) => "--- SOURCE: {$a->title} ---\n{$a->content}")->implode("\n\n");
    }
}
