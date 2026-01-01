<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use Illuminate\Support\Facades\View;

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

        // Perform Deep Research if a topic is provided
        $researchData = '';
        if ($data->researchTopic) {
            $researchData = $this->researchService->performResearch($data->researchTopic);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an expert business analyst and technical writer. 
                                         Your task is to generate a professional report in HTML format. 
                                         - Use <h2> and <h3> for headers. Use <p>, <ul>, <li> for content. 
                                         - Do not wrap in <html> or <body> tags.
                                         - THE LAYOUT MUST BE HIGHLY PROFESSIONAL AND DETAILED.
                                         - If 'Source Content' is provided, restructure and professionalize it into the report sections.
                                         - If 'Research Data' is provided, incorporate those facts and figures into the analysis.
                                         - Maintain a formal, analytical tone throughout."
                        ],
                        [
                            'role' => 'user',
                            'content' => "Generate a {$data->template->label()} report. 
                                         Optionally focusing on: {$data->prompt}.
                                         Variant style: {$data->variant}. 
                                         Recipient: {$data->recipientName} ({$data->recipientTitle}). 
                                         
                                         REAL-WORLD RESEARCH DATA:
                                         ---
                                         {$researchData}
                                         ---
                                         
                                         SOURCE CONTENT TO BE TRANSFORMED:
                                         ---
                                         {$data->contentData}
                                         ---
                                         
                                         Instruction: Take the research and source content above and expand it into a full, detailed report suitable for the '{$data->template->label()}' template. If no content is provided, use the research data to build a high-quality analysis."
                        ],
                    ],
                    'temperature' => 0.7,
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
            
            <h3>Key Metrics</h3>
            <ul>
                <li><strong>Revenue Growth:</strong> +24.5% YoY</li>
                <li><strong>Market Share:</strong> 18.3% (up from 15.7%)</li>
                <li><strong>Customer Satisfaction:</strong> 4.8/5.0 rating</li>
                <li><strong>Operational Efficiency:</strong> 12% cost reduction</li>
            </ul>

            <h2>Market Analysis</h2>
            <p>The current market landscape presents significant opportunities for expansion. Key trends indicate a shift towards digital-first solutions, with growing demand in the enterprise segment.</p>
            
            <h3>Competitive Positioning</h3>
            <p>Our analysis reveals a strong competitive position with differentiated offerings in:</p>
            <ol>
                <li>AI-powered analytics capabilities</li>
                <li>Seamless integration ecosystem</li>
                <li>Enterprise-grade security features</li>
            </ol>

            <h2>Strategic Recommendations</h2>
            <p>Based on comprehensive data analysis, we recommend prioritizing the following initiatives:</p>
            <ul>
                <li>Expand product portfolio in high-growth segments</li>
                <li>Invest in R&D for next-generation platform</li>
                <li>Strengthen partnerships in key markets</li>
            </ul>

            <h2>Financial Outlook</h2>
            <p>Projections indicate continued growth trajectory with expected revenue targets of $50M by Q4. Cost optimization initiatives are on track to deliver an additional 8% margin improvement.</p>
        ";
    }
}
