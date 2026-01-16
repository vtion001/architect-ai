<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Enums\ReportTemplate;

/**
 * Provides sample/preview content for each report template type.
 * 
 * Extracted from ReportService to follow SRP - this class has a single
 * responsibility: providing sample HTML content for template previews.
 */
class SampleContentProvider
{
    /**
     * Get sample content for a specific template type.
     */
    public function getContent(ReportTemplate $template, array $overrides = []): string
    {
        return match ($template) {
            ReportTemplate::EXECUTIVE_SUMMARY => $this->getExecutiveSummarySample(),
            ReportTemplate::TECHNICAL_AUDIT => $this->getTechnicalAuditSample(),
            ReportTemplate::FINANCIAL_ANALYSIS => $this->getFinancialAnalysisSample(),
            ReportTemplate::MARKET_ANALYSIS => $this->getMarketAnalysisSample(),
            ReportTemplate::FINANCIAL_OVERVIEW => $this->getFinancialOverviewSample(),
            ReportTemplate::COMPETITIVE_INTELLIGENCE => $this->getCompetitiveIntelligenceSample(),
            ReportTemplate::INFOGRAPHIC => $this->getInfographicSample(),
            ReportTemplate::TREND_ANALYSIS => $this->getTrendAnalysisSample(),
            ReportTemplate::PROPOSAL => $this->getProposalSample(),
            ReportTemplate::CONTRACT => $this->getContractSample($overrides),
            ReportTemplate::CV_RESUME => $this->getCvResumeSample(),
            ReportTemplate::COVER_LETTER => $this->getCoverLetterSample(),
            default => $this->getDefaultSample(),
        };
    }

    protected function getExecutiveSummarySample(): string
    {
        return <<<HTML
            <h2>Strategic Overview</h2>
            <p>This executive summary highlights the core achievements and strategic direction for the upcoming period. Data suggests a strong alignment between operational capacity and market demand.</p>
            <div class='callout'>
                <strong>Key Insight:</strong> Operational efficiency has increased by 14% since the implementation of the new RAG protocol.
            </div>
            <h3>Primary Objectives</h3>
            <ul>
                <li><strong>Growth:</strong> Scaling technical infrastructure to support 1M+ concurrent nodes.</li>
                <li><strong>Integration:</strong> Seamlessly mapping internal knowledge bases to external research data.</li>
            </ul>
            <h2>Executive Summary</h2>
            <p><strong>Date:</strong> {$this->getCurrentDate()}</p>
            <p>This report provides a high-level overview of the strategic analysis conducted on the requested topic. Our findings indicate a significant opportunity for growth by leveraging AI-driven architectural patterns.</p>
            <div class='callout'>
                <strong>Key Insight:</strong> Implementation of modular agentic workflows reduces development cycles by 40%.
            </div>
            <h3>Strategic Recommendations</h3>
            <ul>
                <li>Adopt a micro-services architecture for increased scalability.</li>
                <li>Integrate real-time RAG pipelines for dynamic content generation.</li>
                <li>Prioritize user-centric design principles in frontend implementation.</li>
            </ul>
        HTML;
    }

    protected function getTechnicalAuditSample(): string
    {
        return <<<'HTML'
            <h2>Technical Infrastructure Audit</h2>
            <p>A comprehensive review of the current system architecture reveals robust core stability but highlights areas for optimization in the data ingestion layer.</p>
            <h3>System Health Scorecard</h3>
            <table>
                <thead>
                    <tr><th>Component</th><th>Status</th><th>Latency</th><th>Uptime</th></tr>
                </thead>
                <tbody>
                    <tr><td>API Gateway</td><td><span style='color:green'>Healthy</span></td><td>45ms</td><td>99.99%</td></tr>
                    <tr><td>Vector DB</td><td><span style='color:orange'>Optimizing</span></td><td>120ms</td><td>99.95%</td></tr>
                    <tr><td>Worker Nodes</td><td><span style='color:green'>Healthy</span></td><td>N/A</td><td>99.90%</td></tr>
                </tbody>
            </table>
        HTML;
    }

    protected function getFinancialAnalysisSample(): string
    {
        return <<<'HTML'
            <h2>Q4 Financial Performance Analysis</h2>
            <p>Analyzing the fiscal data for the fourth quarter demonstrates a positive trend in revenue acquisition, driven primarily by the new subscription tier launch.</p>
            <h3>Revenue Breakdown</h3>
            <table>
                <thead>
                    <tr><th>Metric</th><th>Q3 Result</th><th>Q4 Result</th><th>Growth</th></tr>
                </thead>
                <tbody>
                    <tr><td>MRR</td><td>$125k</td><td>$145k</td><td><span style='color:green'>+16%</span></td></tr>
                    <tr><td>Burn Rate</td><td>$140k</td><td>$160k</td><td><span style='color:green'>-12.5%</span></td></tr>
                    <tr><td>CAC Payback</td><td>4.2 Months</td><td>6.0 Months</td><td><span style='color:green'>-1.8m</span></td></tr>
                </tbody>
            </table>
            <div class='callout'>
                <strong>Audit Result:</strong> All nodes reconciled. Treasury balance verified at 99.98% accuracy.
            </div>
        HTML;
    }

    protected function getMarketAnalysisSample(): string
    {
        return <<<'HTML'
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
        HTML;
    }

    protected function getFinancialOverviewSample(): string
    {
        return <<<'HTML'
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
        HTML;
    }

    protected function getCompetitiveIntelligenceSample(): string
    {
        return <<<'HTML'
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
        HTML;
    }

    protected function getInfographicSample(): string
    {
        return <<<'HTML'
            <h2>One-Pager Visual Summary</h2>
            <p>Transformation of complex research data into a high-impact visual representation. Optimized for quick executive review and strategic decision-making.</p>
            <div class='callout'>
                <strong>North Star Metric:</strong> 2.4k Intelligence Reports generated in Q4 via RAG-sync.
            </div>
        HTML;
    }

    protected function getTrendAnalysisSample(): string
    {
        return <<<'HTML'
            <h2>Emerging Trend Architecture</h2>
            <p>Identification of patterns across a 7-day network intensity sweep. Our model forecasts a surge in request complexity for specialized industry reports.</p>
            <div class='callout'>
                <strong>Forecast Alpha:</strong> Automation of 'Tier-2' analysis will be standard by 2026.
            </div>
        HTML;
    }

    protected function getProposalSample(): string
    {
        return <<<'HTML'
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
        HTML;
    }

    protected function getContractSample(array $overrides = []): string
    {
        $providerName = $overrides['providerName'] ?? '_________________________________________';
        $providerBusiness = $overrides['providerBusiness'] ?? '_________________________________________';
        $providerAddress = $overrides['providerAddress'] ?? '_________________________________________';
        $providerEmail = $overrides['providerEmail'] ?? '_________________________________________';
        $providerTaxId = $overrides['providerTaxId'] ?? '_________________________________________';
        
        $recipientName = $overrides['recipientName'] ?? 'Acme Corporation';
        $recipientTitle = $overrides['recipientTitle'] ?? 'Manager';
        $clientAddress = $overrides['clientAddress'] ?? '_________________________________________';
        $clientCity = $overrides['clientCity'] ?? '_________________________________________';
        $clientCountry = $overrides['clientCountry'] ?? 'United States';
        $clientEmail = $overrides['clientEmail'] ?? '_________________________________________';
        $clientTaxId = $overrides['clientTaxId'] ?? '_________________________________________';

        return <<<HTML
            <!-- PARTIES SECTION -->
            <div class='parties-section'>
                <div class='party-block'>
                    <h3>SERVICE PROVIDER ("Provider")</h3>
                    <div class='party-field'><span class='party-label'>Name:</span><span class='party-value fill-field'>{$providerName}</span></div>
                    <div class='party-field'><span class='party-label'>Business Name:</span><span class='party-value fill-field'>{$providerBusiness}</span></div>
                    <div class='party-field'><span class='party-label'>Address:</span><span class='party-value fill-field'>{$providerAddress}</span></div>
                    <div class='party-field'><span class='party-label'>City, State, Zip:</span><span class='party-value fill-field'>_________________________________________</span></div>
                    <div class='party-field'><span class='party-label'>Email:</span><span class='party-value fill-field'>{$providerEmail}</span></div>
                    <div class='party-field'><span class='party-label'>Tax ID:</span><span class='party-value fill-field'>{$providerTaxId}</span></div>
                </div>
                <div class='party-block'>
                    <h3>CLIENT ("Client")</h3>
                    <div class='party-field'><span class='party-label'>Name:</span><span class='party-value'>{$recipientName}</span></div>
                    <div class='party-field'><span class='party-label'>Title:</span><span class='party-value'>{$recipientTitle}</span></div>
                    <div class='party-field'><span class='party-label'>Address:</span><span class='party-value fill-field'>{$clientAddress}</span></div>
                    <div class='party-field'><span class='party-label'>City, State, Zip:</span><span class='party-value fill-field'>{$clientCity}</span></div>
                    <div class='party-field'><span class='party-label'>Country:</span><span class='party-value'>{$clientCountry}</span></div>
                    <div class='party-field'><span class='party-label'>Email:</span><span class='party-value fill-field'>{$clientEmail}</span></div>
                    <div class='party-field'><span class='party-label'>Tax ID:</span><span class='party-value fill-field'>{$clientTaxId}</span></div>
                </div>
            </div>

            <!-- RECITALS -->
            <div class='recitals'>
                <p>WHEREAS, the Client desires to engage the Provider to deliver professional services as described herein;</p>
                <p>WHEREAS, the Provider possesses the necessary skills, expertise, and resources to provide such services;</p>
                <p>WHEREAS, both parties wish to establish clear terms and conditions governing this business relationship;</p>
                <p class='therefore-clause'>NOW, THEREFORE, in consideration of the mutual covenants and agreements contained herein, and for other good and valuable consideration, the receipt and sufficiency of which are hereby acknowledged, the parties agree as follows:</p>
            </div>

            <!-- ARTICLE I: SCOPE OF WORK -->
            <h2>ARTICLE I: SCOPE OF WORK AND DELIVERABLES</h2>
            
            <h3>1.1 Project Description</h3>
            <p>The Provider agrees to deliver the following services and deliverables:</p>
            <ul>
                <li>Complete project development as specified in attached specifications</li>
                <li>Professional quality work meeting industry standards</li>
                <li>Documentation and training materials as applicable</li>
                <li>Post-delivery support period as outlined in this Agreement</li>
            </ul>

            <h3>1.2 Excluded from Scope</h3>
            <p>The following items are explicitly excluded unless separately contracted:</p>
            <ul>
                <li>Ongoing maintenance beyond the specified support period</li>
                <li>Third-party service fees and subscriptions</li>
                <li>Additional features not specified in Section 1.1</li>
                <li>Content creation beyond basic optimization</li>
            </ul>

            <!-- ARTICLE II: PROJECT TIMELINE -->
            <h2>ARTICLE II: PROJECT TIMELINE AND MILESTONES</h2>
            
            <h3>2.1 Project Duration</h3>
            <p>The estimated project duration is <strong><span class='fill-field'>______ weeks/months</span></strong> from the Project Commencement Date, subject to timely Client cooperation and milestone payment compliance.</p>

            <h3>2.2 Project Commencement Date</h3>
            <div class='callout-critical'>
                <strong>CRITICAL PROVISION:</strong> The Project Commencement Date shall be defined as the date when ALL of the following conditions have been met:
                <ol>
                    <li>This Contract has been executed by both parties</li>
                    <li>Initial deposit payment has been received and cleared</li>
                    <li>Client has provided all required materials and access credentials</li>
                </ol>
            </div>

            <h3>2.3 Milestone Schedule</h3>
            <table class='milestone-table'>
                <thead><tr><th>Milestone</th><th>Description</th><th>Duration</th><th>Payment Due</th></tr></thead>
                <tbody>
                    <tr><td>Phase 1</td><td>Discovery & Planning</td><td>Week 1</td><td>Payment 1 (Before Start)</td></tr>
                    <tr><td>Phase 2</td><td>Development</td><td>Weeks 2-3</td><td>Payment 2 (Upon Completion)</td></tr>
                    <tr><td>Phase 3</td><td>Testing & QA</td><td>Week 4</td><td>N/A</td></tr>
                    <tr><td>Phase 4</td><td>Launch & Handover</td><td>Week 5</td><td>Final Payment (Upon Launch)</td></tr>
                </tbody>
            </table>

            <!-- ARTICLE III: COMPENSATION -->
            <h2>ARTICLE III: COMPENSATION AND PAYMENT TERMS</h2>
            
            <h3>3.1 Total Contract Value</h3>
            <p>The total compensation for all services described in this Agreement is:</p>
            <p class='contract-amount'><strong><span class='fill-field'>_________________ DOLLARS (\$_______.00 USD)</span></strong></p>

            <h3>3.2 Payment Structure</h3>
            <table class='payment-table'>
                <thead><tr><th>Payment</th><th>Description</th><th>Due Date</th><th>Amount</th></tr></thead>
                <tbody>
                    <tr><td>Payment 1</td><td>Initial Deposit (50%)</td><td>Upon contract execution</td><td class='fill-field'>\$________</td></tr>
                    <tr><td>Payment 2</td><td>Progress Payment (30%)</td><td>Upon Phase 2 completion</td><td class='fill-field'>\$________</td></tr>
                    <tr><td>Payment 3</td><td>Final Payment (20%)</td><td>Upon project completion</td><td class='fill-field'>\$________</td></tr>
                    <tr class='total-row'><td colspan='3'>Total Contract Value</td><td class='fill-field'>\$________</td></tr>
                </tbody>
            </table>

            <h3>3.3 Critical Payment Provisions</h3>
            <div class='callout-critical'>
                <strong>NO WORK WITHOUT PAYMENT:</strong>
                <p>THE PROVIDER SHALL NOT BE OBLIGATED TO COMMENCE ANY WORK OR PROCEED TO THE NEXT MILESTONE UNTIL THE APPLICABLE MILESTONE PAYMENT HAS BEEN RECEIVED AND CLEARED IN THE PROVIDER'S ACCOUNT. THIS IS A MATERIAL TERM OF THIS AGREEMENT.</p>
            </div>

            <h3>3.4 Late Payment Penalties</h3>
            <ul>
                <li>Days 1-7 overdue: Grace period with friendly reminder</li>
                <li>Days 8-14 overdue: 5% late fee assessed on outstanding amount</li>
                <li>Days 15-30 overdue: Additional 10% late fee (15% total)</li>
                <li>Over 30 days overdue: Additional 10% late fee (25% total) + right to suspend work</li>
            </ul>

            <!-- ARTICLE IV: CLIENT RESPONSIBILITIES -->
            <h2>ARTICLE IV: CLIENT RESPONSIBILITIES AND OBLIGATIONS</h2>
            
            <h3>4.1 Timely Cooperation</h3>
            <p>Client agrees to provide timely cooperation including:</p>
            <ul>
                <li><strong>Information and Materials:</strong> within 5 business days of request</li>
                <li><strong>Access Credentials:</strong> within 3 business days of request</li>
                <li><strong>Approvals and Feedback:</strong> within 48-72 hours as specified</li>
                <li><strong>Availability:</strong> for scheduled meetings and training sessions</li>
            </ul>

            <h3>4.2 Consequences of Non-Compliance</h3>
            <p>If Client fails to meet obligations:</p>
            <ul>
                <li>Project timeline automatically extends by the duration of delay plus 50%</li>
                <li>Provider is not responsible for missing deadlines due to Client delays</li>
                <li>Extended project management fees may apply for delays exceeding 30 days</li>
            </ul>

            <!-- ARTICLE V: INTELLECTUAL PROPERTY -->
            <h2>ARTICLE V: INTELLECTUAL PROPERTY RIGHTS</h2>
            
            <h3>5.1 Ownership Upon Final Payment</h3>
            <p>Upon receipt of final payment and full satisfaction of all financial obligations, Provider hereby assigns, transfers, and conveys to Client all custom work created specifically for this project.</p>
            
            <div class='callout-critical'>
                <strong>CONDITION PRECEDENT:</strong> THE TRANSFER OF INTELLECTUAL PROPERTY RIGHTS IS EXPRESSLY CONDITIONED UPON FULL PAYMENT OF ALL AMOUNTS DUE UNDER THIS AGREEMENT. UNTIL FINAL PAYMENT IS RECEIVED, PROVIDER RETAINS ALL RIGHTS, TITLE, AND INTEREST IN THE WORK PRODUCT.
            </div>

            <h3>5.2 Provider-Retained Rights</h3>
            <p>Provider retains ownership of pre-existing materials, reusable components, general knowledge, and portfolio rights.</p>

            <h3>5.3 Confidentiality</h3>
            <p>Both parties agree to maintain the confidentiality of all proprietary information disclosed during the term of this agreement and for three (3) years thereafter.</p>

            <!-- ARTICLE VI: WARRANTIES -->
            <h2>ARTICLE VI: WARRANTIES AND DISCLAIMERS</h2>
            
            <h3>6.1 Provider Warranties</h3>
            <ul>
                <li>Services will be performed in a professional and workmanlike manner</li>
                <li>Work will conform to industry standards</li>
                <li>Work will be reasonably free from defects at time of delivery</li>
                <li>30-day warranty period for bug fixes after launch</li>
            </ul>

            <h3>6.2 Disclaimers</h3>
            <div class='callout'>
                <strong>NO WARRANTY OF RESULTS:</strong> Provider makes no warranty regarding business results, including website traffic, search rankings, conversion rates, or revenue increases. Success depends on factors outside Provider's control.
            </div>

            <h3>6.3 Limitation of Liability</h3>
            <p><strong>IN NO EVENT SHALL PROVIDER'S TOTAL LIABILITY EXCEED THE TOTAL AMOUNT PAID BY CLIENT UNDER THIS AGREEMENT.</strong></p>

            <!-- ARTICLE VII: DISPUTE RESOLUTION -->
            <h2>ARTICLE VII: DISPUTE RESOLUTION AND GOVERNING LAW</h2>
            
            <h3>7.1 Governing Law</h3>
            <p>This Agreement shall be governed by the laws of <span class='fill-field'>_________________________</span> without regard to conflict of law provisions.</p>

            <h3>7.2 Dispute Resolution Procedure</h3>
            <ol>
                <li><strong>Informal Negotiation:</strong> 30 calendar days to resolve through direct discussions</li>
                <li><strong>Mediation:</strong> If negotiation fails, submit to non-binding mediation</li>
                <li><strong>Binding Arbitration:</strong> Final resolution through binding arbitration</li>
            </ol>

            <!-- ARTICLE VIII: TERMINATION -->
            <h2>ARTICLE VIII: TERMINATION</h2>
            
            <h3>8.1 Termination for Convenience</h3>
            <p>Client may terminate with 30 days written notice. All work completed shall be paid for and milestone payments made are non-refundable.</p>

            <h3>8.2 Termination for Cause</h3>
            <p>Either party may terminate immediately if the other party materially breaches and fails to cure within 15 calendar days of written notice.</p>

            <!-- ARTICLE IX: MISCELLANEOUS -->
            <h2>ARTICLE IX: MISCELLANEOUS PROVISIONS</h2>
            
            <h3>9.1 Entire Agreement</h3>
            <p>This Agreement constitutes the entire understanding between the parties and supersedes all prior agreements.</p>

            <h3>9.2 Amendments</h3>
            <p>No modification shall be effective unless in writing and signed by both parties.</p>

            <h3>9.3 Independent Contractor</h3>
            <p>Provider is an independent contractor. Nothing in this Agreement creates an employment, partnership, or agency relationship.</p>
        HTML;
    }

    protected function getCvResumeSample(): string
    {
        return <<<'HTML'
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
        HTML;
    }

    protected function getCoverLetterSample(): string
    {
        return <<<'HTML'
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
        HTML;
    }

    protected function getDefaultSample(): string
    {
        return <<<HTML
            <h2>Intelligence Report</h2>
            <p><strong>Date:</strong> {$this->getCurrentDate()}</p>
            <p>This document provides a synthesized view of the analysis performed. The executive brief outlines key takeaways and strategic recommendations based on aggregated data inputs.</p>
            <div class='callout'>
                <strong>Summary:</strong> Our agentic architecture successfully processed the user's query. This sample represents the output structure.
            </div>
        HTML;
    }

    protected function getCurrentDate(): string
    {
        return now()->format('F d, Y');
    }
}
