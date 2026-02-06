<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;

/**
 * Contract Generator
 * 
 * Specialized generator for comprehensive, legally sound business contracts with:
 * - Formal legal structure (Parties, Recitals, Articles, Signatures)
 * - International business law compliance
 * - Comprehensive article coverage (Scope, Timeline, Payment, IP, Termination, Dispute Resolution)
 * - Professional HTML formatting with legal-specific CSS classes
 * - Content extraction and organization from source materials
 * - Party details, payment schedules, and milestone tables
 * 
 * This generator creates enforceable legal contracts with proper
 * structure, language, and protections for both parties.
 */
class ContractGenerator extends BaseGenerator
{
    /**
     * Get document type identifier.
     */
    public function getDocumentType(): string
    {
        return 'contract';
    }

    /**
     * Get AI role description.
     */
    public function getRoleDescription(): string
    {
        return 'expert legal drafter, contract attorney, and international business law specialist';
    }

    /**
     * Get task description.
     */
    public function getTaskDescription(): string
    {
        return 'COMPREHENSIVE, LEGALLY SOUND, PROFESSIONALLY FORMATTED business contract';
    }

    /**
     * Build system prompt with contract structure mandate.
     */
    public function buildSystemPrompt(ReportRequestData $data): string
    {
        $roleDescription = $this->getRoleDescription();
        $taskDescription = $this->getTaskDescription();
        $documentType = $this->getDocumentType();
        
        $dataIntegrity = $this->buildDataIntegrityInstruction();
        $coreDirectives = $this->buildCoreDirectives($dataIntegrity);
        
        // Contracts typically don't use brand instructions (legal documents are standardized)
        $contractMandate = $this->buildContractStructureMandate($data);
        
        return "You are an $roleDescription. 
                Your task is to take RAW source content and transform it into a $taskDescription.
                
                $coreDirectives
                
                - THE RAW SOURCE CONTENT contains details about the services, parties, and terms.
                - YOUR PRIMARY JOB IS LEGAL STRUCTURE AND ORGANIZATION. Transform raw information into proper legal contract format.
                - Use formal, precise legal language throughout.
                - Be comprehensive but avoid unnecessary repetition.
                - Make the contract enforceable and professional.
                
                $contractMandate";
    }

    /**
     * Build comprehensive contract structure mandate.
     */
    protected function buildContractStructureMandate(ReportRequestData $data): string
    {
        $mandate = "\n[CONTRACT STRUCTURE MANDATE]\n";
        $mandate .= "You must generate a comprehensive, professional legal contract by analyzing the pasted content.\n";
        $mandate .= "CRITICAL: Extract all relevant details from the source content and organize into proper legal structure.\n\n";
        
        $mandate .= "[REQUIRED CONTRACT SECTIONS - Generate ALL applicable sections]\n\n";
        
        // 1. PARTIES SECTION
        $mandate .= "1. PARTIES SECTION (REQUIRED):\n";
        $mandate .= "   Generate using this exact HTML structure:\n";
        $mandate .= "   <div class='parties-section'>\n";
        $mandate .= "     <div class='party-block'>\n";
        $mandate .= "       <h3>Service Provider (\"Provider\")</h3>\n";
        $mandate .= "       <div class='party-field'><span class='party-label'>Name:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
        $mandate .= "       <div class='party-field'><span class='party-label'>Business:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
        $mandate .= "       <div class='party-field'><span class='party-label'>Address:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
        $mandate .= "       <div class='party-field'><span class='party-label'>Email:</span><span class='party-value'>[Extract or use ___________]</span></div>\n";
        $mandate .= "     </div>\n";
        $mandate .= "     <div class='party-block'>\n";
        $mandate .= "       <h3>Client (\"Client\")</h3>\n";
        $mandate .= "       [Same structure for client details]\n";
        $mandate .= "     </div>\n";
        $mandate .= "   </div>\n\n";
        
        // 2. RECITALS/WHEREAS
        $mandate .= "2. RECITALS/WHEREAS SECTION:\n";
        $mandate .= "   <div class='recitals'>\n";
        $mandate .= "     <p>WHEREAS, [context of the agreement];</p>\n";
        $mandate .= "     <p>WHEREAS, [provider qualifications];</p>\n";
        $mandate .= "     <p>WHEREAS, [purpose of engagement];</p>\n";
        $mandate .= "     <p class='therefore-clause'>NOW, THEREFORE, in consideration of the mutual covenants...</p>\n";
        $mandate .= "   </div>\n\n";
        
        // 3. ARTICLES
        $mandate .= "3. ARTICLES - Use <h2> for main articles:\n";
        $mandate .= "   Use <h2>ARTICLE I: SCOPE OF WORK AND DELIVERABLES</h2>\n";
        $mandate .= "   Use <h3>1.1 Project Description</h3> for subsections\n";
        $mandate .= "   Use <h4>A. Component Name</h4> for sub-subsections\n\n";
        
        // 4. STANDARD ARTICLES
        $mandate .= "4. STANDARD ARTICLES TO INCLUDE:\n";
        $mandate .= "   - ARTICLE I: SCOPE OF WORK AND DELIVERABLES (extract from content)\n";
        $mandate .= "   - ARTICLE II: PROJECT TIMELINE AND MILESTONES (if applicable)\n";
        $mandate .= "   - ARTICLE III: COMPENSATION AND PAYMENT TERMS\n";
        $mandate .= "   - ARTICLE IV: CLIENT RESPONSIBILITIES AND OBLIGATIONS\n";
        $mandate .= "   - ARTICLE V: INTELLECTUAL PROPERTY RIGHTS\n";
        $mandate .= "   - ARTICLE VI: WARRANTIES AND DISCLAIMERS\n";
        $mandate .= "   - ARTICLE VII: DISPUTE RESOLUTION AND GOVERNING LAW\n";
        $mandate .= "   - ARTICLE VIII: TERMINATION\n";
        $mandate .= "   - ARTICLE IX: MISCELLANEOUS PROVISIONS\n\n";
        
        // 5. SPECIAL HTML CLASSES
        $mandate .= "5. SPECIAL HTML CLASSES TO USE:\n";
        $mandate .= "   - For critical/warning clauses: <div class='callout-critical'><strong>CRITICAL:</strong> text...</div>\n";
        $mandate .= "   - For important notices: <div class='callout'><strong>Important:</strong> text...</div>\n";
        $mandate .= "   - For informational notes: <div class='callout-info'>text...</div>\n";
        $mandate .= "   - For milestone blocks: <div class='milestone-block'><h4>Milestone Name</h4><p>Details...</p></div>\n";
        $mandate .= "   - For payment tables: <table class='payment-table'>...</table>\n\n";
        
        // 6. PAYMENT SCHEDULE TABLE
        $mandate .= "6. PAYMENT SCHEDULE TABLE FORMAT:\n";
        $mandate .= "   <table class='payment-table'>\n";
        $mandate .= "     <thead><tr><th>Payment</th><th>Description</th><th>Due Date</th><th>Amount</th></tr></thead>\n";
        $mandate .= "     <tbody>\n";
        $mandate .= "       <tr><td>Payment 1</td><td>Deposit</td><td>Upon signing</td><td>\$X,XXX.XX</td></tr>\n";
        $mandate .= "       <tr class='total-row'><td colspan='3'>Total Contract Value</td><td>\$X,XXX.XX</td></tr>\n";
        $mandate .= "     </tbody>\n";
        $mandate .= "   </table>\n\n";
        
        // 7. LEGAL EMPHASIS
        $mandate .= "7. LEGAL EMPHASIS:\n";
        $mandate .= "   - Use <strong>BOLD CAPS</strong> for critical legal terms\n";
        $mandate .= "   - Use <span class='legal-emphasis'>highlighted text</span> for key provisions\n";
        $mandate .= "   - Use numbered lists for sequential requirements\n";
        $mandate .= "   - Use bullet lists for non-sequential items\n\n";
        
        // 8. CONTENT EXTRACTION RULES
        $mandate .= "8. CONTENT EXTRACTION RULES:\n";
        $mandate .= "   - Identify ALL services/deliverables from pasted content\n";
        $mandate .= "   - Extract any pricing/payment terms mentioned\n";
        $mandate .= "   - Identify timelines/milestones if mentioned\n";
        $mandate .= "   - Extract party names and details if provided\n";
        $mandate .= "   - Infer appropriate warranties and terms based on service type\n";
        $mandate .= "   - Add industry-standard legal protections\n\n";
        
        // 9. DO NOT INCLUDE
        $mandate .= "9. DO NOT INCLUDE:\n";
        $mandate .= "   - Signature blocks (already in template)\n";
        $mandate .= "   - 'IN WITNESS WHEREOF' closing (already in template)\n";
        $mandate .= "   - Contract header/title (already in template)\n\n";
        
        // 10. OUTPUT QUALITY
        $mandate .= "10. OUTPUT QUALITY:\n";
        $mandate .= "    - Use formal, precise legal language\n";
        $mandate .= "    - Be comprehensive but avoid unnecessary repetition\n";
        $mandate .= "    - Include specific details from source content\n";
        $mandate .= "    - Use blank lines ___________ where specific values should be filled in\n";
        $mandate .= "    - Make the contract enforceable and professional\n\n";
        
        return $mandate;
    }

    /**
     * Format user prompt for contract generation.
     */
    protected function formatUserPrompt(ReportRequestData $data, string $baseContext): string
    {
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

            1. **PARTIES SECTION** at the top using the card-based layout:
               - Use <div class='parties-section'> containing two <div class='party-block'>
               - Each party-block should have:
                 * <div class='party-header'> with <h3> and <span class='party-designation'>
                 * <div class='party-fields'> containing multiple <div class='party-row'>
               - Each party-row should have:
                 * <label class='party-label'>Field Name</label>
                 * <div class='party-value'>Dynamic Value</div>
               - Include these fields: Full Legal Name, Business Name, Business Address, City/State/Postal, Email, Tax ID
               - Use actual data from the parties information above
               - Leave empty party-value divs for fields without data (NO underscores or placeholders)

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

    /**
     * Contracts typically don't need research (based on parties' agreement terms).
     */
    public function requiresResearch(): bool
    {
        return false;
    }

    /**
     * Contracts are standardized legal documents - brand voice not typically applied.
     */
    public function supportsBrandInstructions(): bool
    {
        return false;
    }
}
