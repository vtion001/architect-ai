<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;

/**
 * Cover Letter Generator
 * 
 * Specialized generator for persuasive, personalized cover letters with:
 * - 4-part story structure (Hook, Evidence, Solution, Call to Action)
 * - Job description integration
 * - Company research alignment
 * - Narrative, conversational tone
 * - Evidence-based achievement highlighting
 * 
 * This generator creates compelling cover letters that connect
 * candidate experience to company needs and role requirements.
 */
class CoverLetterGenerator extends BaseGenerator
{
    /**
     * Get document type identifier.
     */
    public function getDocumentType(): string
    {
        return 'letter';
    }

    /**
     * Get AI role description.
     */
    public function getRoleDescription(): string
    {
        return 'expert career coach and persuasive writer';
    }

    /**
     * Get task description.
     */
    public function getTaskDescription(): string
    {
        return 'COMPELLING and PERSONALIZED cover letter';
    }

    /**
     * Build system prompt with cover letter strategy.
     */
    public function buildSystemPrompt(ReportRequestData $data): string
    {
        $roleDescription = $this->getRoleDescription();
        $taskDescription = $this->getTaskDescription();
        $documentType = $this->getDocumentType();
        
        $coreDirectives = $this->buildCoreDirectives();
        $brandInstructions = $this->buildBrandInstructions($data->brandId, $data->template->value);
        
        // Build cover letter strategy
        $strategy = $this->buildCoverLetterStrategy($data);
        
        return "You are an $roleDescription. 
                Your task is to take candidate experience data and transform it into a $taskDescription.
                
                $coreDirectives
                
                - THE RAW SOURCE CONTENT contains the candidate's background (from CV/resume).
                - YOUR PRIMARY JOB IS STORYTELLING. Create a narrative that connects the candidate to the opportunity.
                - Use a conversational, enthusiastic, and persuasive tone.
                
                $brandInstructions
                
                $strategy";
    }

    /**
     * Build cover letter strategy with 4-part structure.
     */
    protected function buildCoverLetterStrategy(ReportRequestData $data): string
    {
        $strategy = "\n[COVER LETTER STRATEGY]\n";
        $strategy .= "You must write a highly persuasive cover letter body that strictly follows this 4-part structure:\n";
        $strategy .= "DO NOT include the Header (Date, Address), Subject Line, or Sign-off (Sincerely, Name). These are automatically added by the template.\n";
        $strategy .= "**IMPORTANT: DO NOT include section headers or labels like 'The Hook' or 'The Evidence'. Output ONLY the paragraphs.**\n\n";
        
        $strategy .= "1. First Paragraph (The Hook):\n";
        $strategy .= "   - Start with a strong connection to the company (mission, recent project, values)\n";
        $strategy .= "   - Explicitly state the role applied for\n";
        $strategy .= "   - Show genuine enthusiasm and research\n\n";
        
        $strategy .= "2. Middle Paragraphs (The Evidence):\n";
        $strategy .= "   - Select 2-3 'Hero Moments' from the source content (CV) that prove capability\n";
        $strategy .= "   - Use numbers/metrics if available (quantified achievements)\n";
        $strategy .= "   - Tell brief stories that demonstrate relevant skills\n";
        $strategy .= "   - Connect each achievement to the target role requirements\n\n";
        
        $strategy .= "3. Later Paragraph (The Solution):\n";
        $strategy .= "   - Address a potential company pain point (from job description or research)\n";
        $strategy .= "   - Explain how the candidate's skills solve this specific problem\n";
        $strategy .= "   - Mention cultural fit and alignment with company values\n";
        $strategy .= "   - Show understanding of industry challenges\n\n";
        
        $strategy .= "4. Final Paragraph (Call to Action):\n";
        $strategy .= "   - Proactive closing requesting a discussion\n";
        $strategy .= "   - Express excitement about the opportunity\n";
        $strategy .= "   - Suggest next steps (interview, conversation)\n";
        $strategy .= "   - Confident but not presumptuous tone\n\n";
        
        $strategy .= "TONE GUIDELINES:\n";
        $strategy .= "   - Narrative and conversational (not robotic or formulaic)\n";
        $strategy .= "   - Enthusiastic but professional\n";
        $strategy .= "   - Persuasive without being pushy\n";
        $strategy .= "   - Industry-appropriate (Creative for Design, Formal for Law, etc.)\n\n";
        
        if ($data->targetRole) {
            $strategy .= "TARGET ROLE: {$data->targetRole}\n";
            
            if ($data->jobDescription) {
                $strategy .= "\nJOB DESCRIPTION CONTEXT:\n{$data->jobDescription}\n\n";
                $strategy .= "INSTRUCTION: Use the Job Description to identify specific pain points and requirements.\n";
                $strategy .= "In the 'Solution' paragraph, explicitly address how the candidate solves these specific problems.\n";
                $strategy .= "Match the tone and language style used in the job description.\n\n";
            }
            
            $strategy .= "Ensure the tone matches the industry of the target role:\n";
            $strategy .= "   - Tech/Startup: Enthusiastic, innovative, collaborative\n";
            $strategy .= "   - Finance/Legal: Professional, detail-oriented, authoritative\n";
            $strategy .= "   - Creative/Design: Expressive, passionate, portfolio-focused\n";
            $strategy .= "   - Healthcare: Compassionate, evidence-based, patient-focused\n";
            $strategy .= "   - Education: Dedicated, student-centered, growth-minded\n\n";
        }
        
        $strategy .= "FORMATTING RULES:\n";
        $strategy .= "   - Each paragraph should be wrapped in <p> tags\n";
        $strategy .= "   - Use <strong> for emphasis on key achievements or skills\n";
        $strategy .= "   - No bullet points or lists (narrative format only)\n";
        $strategy .= "   - 3-5 paragraphs total (excluding header/footer)\n";
        $strategy .= "   - Each paragraph should be 3-5 sentences\n\n";
        
        return $strategy;
    }

    /**
     * Format user prompt for cover letter generation.
     */
    protected function formatUserPrompt(ReportRequestData $data, string $baseContext): string
    {
        return "Generate a professional cover letter.
                
                FROM: {$data->recipientName}
                TO: Hiring Manager / Recruiting Team
                FOR POSITION: {$data->targetRole}
                COMPANY: {$data->companyAddress}
                
                {$baseContext}
                
                INSTRUCTIONS:
                1. Review the candidate's background in the RAW SOURCE CONTENT
                2. Identify the 2-3 most impressive and relevant achievements
                3. Connect these achievements to the target role requirements
                4. Write a compelling narrative following the 4-part structure
                5. Output ONLY the cover letter body paragraphs (no header, no closing)
                
                OUTPUT FORMAT:
                <p>First paragraph (The Hook)...</p>
                <p>Second paragraph (Evidence - Hero Moment 1)...</p>
                <p>Third paragraph (Evidence - Hero Moment 2)...</p>
                <p>Fourth paragraph (The Solution)...</p>
                <p>Final paragraph (Call to Action)...</p>
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
    }

    /**
     * Cover letters don't require deep research (rely on source content).
     */
    public function requiresResearch(): bool
    {
        return false;
    }
}
