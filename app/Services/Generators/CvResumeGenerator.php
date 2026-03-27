<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;

/**
 * CV/Resume Generator
 *
 * Specialized generator for professional resumes and CVs with:
 * - ATS-friendly formatting
 * - Job description tailoring (keyword matching)
 * - Core competencies structure
 * - Zero data loss policy
 * - Multiple style variants (classic, modern, technical, international)
 *
 * This generator implements advanced tailoring that enhances resumes
 * for specific job descriptions while preserving 100% of original content.
 */
class CvResumeGenerator extends BaseGenerator
{
    /**
     * Get document type identifier.
     */
    public function getDocumentType(): string
    {
        return 'resume';
    }

    /**
     * Get AI role description.
     */
    public function getRoleDescription(): string
    {
        return 'expert career coach and resume writer';
    }

    /**
     * Get task description.
     */
    public function getTaskDescription(): string
    {
        return 'PROFESSIONAL ATS-FRIENDLY resume';
    }

    /**
     * Build system prompt with resume-specific instructions.
     */
    public function buildSystemPrompt(ReportRequestData $data): string
    {
        $roleDescription = $this->getRoleDescription();
        $taskDescription = $this->getTaskDescription();
        $documentType = $this->getDocumentType();

        $coreDirectives = $this->buildCoreDirectives();
        $brandInstructions = $this->buildBrandInstructions($data->brandId, $data->template->value);

        // Build resume structure mandate
        $structureMandate = $this->buildResumeStructureMandate($data);

        // Build tailoring instructions if target role is provided
        $tailoringInstructions = $this->buildTailoringInstructions($data);

        // Build variant-specific instructions
        $variantInstructions = $this->buildVariantInstructions($data->variant);

        return "You are an $roleDescription. 
                Your task is to take RAW resume content and transform it into a $taskDescription.
                
                $coreDirectives
                
                - THE RAW SOURCE CONTENT is your primary data source. You must include ALL information provided.
                - YOUR PRIMARY JOB IS DESIGN AND STRUCTURE. Ensure the raw data looks like a premium produced $documentType.
                
                $brandInstructions
                
                $structureMandate
                
                $tailoringInstructions
                
                $variantInstructions";
    }

    /**
     * Build resume structure mandate with sections and formatting.
     */
    protected function buildResumeStructureMandate(ReportRequestData $data): string
    {
        $mandate = "\n[RESUME STRUCTURE MANDATE - ZERO DATA LOSS]\n";
        $mandate .= "You must strictly follow this structure for the resume HTML:\n\n";

        $mandate .= "PAGE 1 PRIORITY SECTIONS (Must appear on first page):\n";
        $mandate .= "1. PROFESSIONAL SUMMARY: <h2>Professional Summary</h2><p>3-4 impactful sentences tailored to target role</p>\n\n";

        $mandate .= "2. CORE COMPETENCIES: <h2>Core Competencies</h2>\n";
        $mandate .= "   <div class='competencies-grid' style='display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin:1rem 0;'>\n";
        $mandate .= "      <div class='competency-item' style='padding:0.5rem;background:#f8fafc;border-left:3px solid #3b82f6;'>Skill 1</div>\n";
        $mandate .= "      (Include 9-12 key competencies matching job requirements)\n";
        $mandate .= "   </div>\n\n";

        $mandate .= "3. KEY SKILLS HIGHLIGHTS: <h2>Key Skills</h2>\n";
        $mandate .= "   - For 'Modern/Technical' variants: Use <span class='skill-tag' style='display:inline-block;padding:0.25rem 0.75rem;margin:0.25rem;background:#e0f2fe;color:#0369a1;border-radius:9999px;font-size:0.875rem;'>Skill Name</span>\n";
        $mandate .= "   - For 'Classic' variant: Use organized list with categories\n";
        $mandate .= "   - Prioritize skills from job description\n\n";

        $mandate .= "SUBSEQUENT PAGE SECTIONS:\n";
        $mandate .= "4. WORK EXPERIENCE: <h2>Work Experience</h2>\n";
        $mandate .= "   - Use <h3>Job Title | Company</h3> and <div class='job-meta'>Date • Location</div> for each role\n";
        $mandate .= "   - Use <ul> with <li> for ALL achievement bullets\n";
        $mandate .= "   - PRESERVE every metric, percentage, and quantifiable achievement\n";
        $mandate .= "   - Start bullets with strong action verbs (Led, Architected, Implemented, Reduced, Increased)\n";
        $mandate .= "   - Include ALL projects and responsibilities from source content\n\n";

        $mandate .= "5. EDUCATION: <h2>Education</h2>\n";
        $mandate .= "   - Use <h3>Degree | University/School</h3> and <div class='job-meta'>Year • GPA (if >3.5)</div>\n";
        $mandate .= "   - Include honors, relevant coursework, thesis if mentioned\n";
        $mandate .= "   - PRESERVE all education details from source\n\n";

        $mandate .= "6. CERTIFICATIONS & LICENSES: <h2>Certifications</h2>\n";
        $mandate .= "   - List ALL certifications with issuing body and date\n";
        $mandate .= "   - Include credential IDs if provided\n\n";

        $mandate .= "7. ADDITIONAL SECTIONS (if present in source):\n";
        $mandate .= "   - Projects, Awards, Publications, Volunteer Experience\n";
        $mandate .= "   - Only include if present in source content\n\n";

        return $mandate;
    }

    /**
     * Build tailoring instructions for job description matching.
     */
    protected function buildTailoringInstructions(ReportRequestData $data): string
    {
        if (! $data->targetRole) {
            return '';
        }

        $instructions = "\n[RESUME TAILORING ACTIVE - ENHANCEMENT ONLY]\n";
        $instructions .= "TARGET ROLE: {$data->targetRole}\n";

        if ($data->jobDescription) {
            $instructions .= "\nJOB DESCRIPTION CONTEXT:\n{$data->jobDescription}\n\n";
            $instructions .= "KEYWORD MATCHING STRATEGY:\n";
            $instructions .= "1. Extract top 10-15 keywords/skills from job description\n";
            $instructions .= "2. Identify which keywords candidate already has in their experience\n";
            $instructions .= "3. Add relevant keywords to Core Competencies section\n";
            $instructions .= "4. Naturally weave job keywords into achievement bullets WHERE ACCURATE\n";
            $instructions .= "5. Prioritize matching skills in Key Skills section\n\n";
        }

        $instructions .= "CRITICAL TAILORING RULES:\n";
        $instructions .= "✓ DO: Rewrite professional summary for target role\n";
        $instructions .= "✓ DO: Re-order experience bullets to highlight relevant achievements first\n";
        $instructions .= "✓ DO: Add relevant keywords from job description to competencies\n";
        $instructions .= "✓ DO: Enhance action verbs and quantify impact where possible\n";
        $instructions .= "✓ DO: Add context that demonstrates fit for the role\n\n";

        $instructions .= "✗ DON'T: Remove ANY dates, companies, or achievements from source\n";
        $instructions .= "✗ DON'T: Delete ANY work experience, education, or certifications\n";
        $instructions .= "✗ DON'T: Reduce the number of achievement bullets\n";
        $instructions .= "✗ DON'T: Summarize or condense technical details\n";
        $instructions .= "✗ DON'T: Fabricate experience or skills not in source content\n\n";

        $instructions .= "OUTPUT FORMAT:\n";
        $instructions .= "   <tailoring_report>\n";
        $instructions .= "      <ul style='list-style:none;padding:0;'>\n";
        $instructions .= "         <li>✓ Added X keywords from job description to Core Competencies</li>\n";
        $instructions .= "         <li>✓ Tailored Professional Summary to emphasize [specific skills]</li>\n";
        $instructions .= "         <li>✓ Re-ordered experience bullets to highlight relevant projects</li>\n";
        $instructions .= "         <li>✓ Enhanced action verbs and quantified Y achievements</li>\n";
        $instructions .= "         <li>✓ Preserved 100% of original content (zero data loss)</li>\n";
        $instructions .= "      </ul>\n";
        $instructions .= "   </tailoring_report>\n";
        $instructions .= "   <document_content>\n";
        $instructions .= "      (Complete Resume HTML with all sections)\n";
        $instructions .= "   </document_content>\n";

        return $instructions;
    }

    /**
     * Build variant-specific instructions.
     */
    protected function buildVariantInstructions(?string $variant): string
    {
        if ($variant !== 'cv-international') {
            return '';
        }

        $instructions = "\n[INTERNATIONAL CV FORMAT - HEALTHCARE/MLS STANDARD]\n";
        $instructions .= "You must strictly follow this structure for the international healthcare CV:\n\n";
        $instructions .= "CRITICAL: ZERO DATA LOSS. You must include 100% of the extracted resume details.\n";
        $instructions .= "Do NOT omit any roles, dates, employers, projects, certifications, skills, education, or achievements.\n";
        $instructions .= "Tailor and enhance wording for the job description, but NEVER remove or condense details.\n\n";

        $instructions .= "0. PROFESSIONAL SUMMARY:\n";
        $instructions .= "   <h2 class='section-title'>PROFESSIONAL SUMMARY</h2>\n";
        $instructions .= "   <p>3-4 impactful sentences tailored to the target role</p>\n\n";

        $instructions .= "1. CORE COMPETENCIES:\n";
        $instructions .= "   <h2 class='section-title'>CORE COMPETENCIES</h2>\n";
        $instructions .= "   <div class='competencies-grid' style='display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin:1rem 0;'>\n";
        $instructions .= "      <div class='competency-item' style='padding:0.5rem;background:#f8fafc;border-left:3px solid #3b82f6;'>Skill 1</div>\n";
        $instructions .= "      (Include 9-12 key competencies matching job requirements)\n";
        $instructions .= "   </div>\n\n";

        $instructions .= "2. PROFILE SECTION:\n";
        $instructions .= "   <h2 class='section-title'>PROFILE</h2>\n";
        $instructions .= "   <ul class='profile-summary'>\n";
        $instructions .= "      <li>Registered Medical Lab Scientist with # years of laboratory experience\n";
        $instructions .= "         <ul><li># of years of Current Lab Experience</li><li># of years of Previous Lab Experience</li></ul>\n";
        $instructions .= "      </li>\n";
        $instructions .= "   </ul>\n\n";

        $instructions .= "3. EDUCATION SECTION:\n";
        $instructions .= "   <h2 class='section-title'>EDUCATION</h2>\n";
        $instructions .= "   <div class='education-block'>\n";
        $instructions .= "      <div class='dates'>Dates Attended University</div>\n";
        $instructions .= "      <div class='institution'>University City, County</div>\n";
        $instructions .= "      <div class='degree'>Degree earned</div>\n";
        $instructions .= "   </div>\n\n";

        $instructions .= "4. WORK EXPERIENCE SECTION (for EACH facility, use this structure):\n";
        $instructions .= "   <h2 class='section-title'>WORK EXPERIENCE</h2>\n";
        $instructions .= "   <div class='facility-block'>\n";
        $instructions .= "      <div class='facility-name'>Current Facility Name</div>\n";
        $instructions .= "      <div class='facility-dates'>Joining Date (Month/Year) - Present</div>\n";
        $instructions .= "      <div class='facility-location'>Facility City, Country</div>\n";
        $instructions .= "      <div class='facility-website'>Facility website if any</div>\n";
        $instructions .= "      <div class='facility-description'>Brief hospital description: Describe number of beds in facility, type of patients served and list any accreditations</div>\n\n";
        $instructions .= "      <div class='job-details'>\n";
        $instructions .= "         <p><strong>Medical Laboratory Scientist:</strong> Rotating or Specific Benches</p>\n";
        $instructions .= "         <p><strong># Beds in Unit:</strong> # beds</p>\n";
        $instructions .= "         <p><strong>Patient Ratio:</strong> #:#</p>\n";
        $instructions .= "      </div>\n\n";
        $instructions .= "      <p><strong><u>Responsibilities:</u></strong></p>\n";
        $instructions .= "      <ul class='responsibility-list'><li>List daily clinical functions</li>...</ul>\n\n";
        $instructions .= "      <p><strong><u>Samples Handled:</u></strong></p>\n";
        $instructions .= "      <ul class='samples-list'><li>List the types and weekly volume</li>...</ul>\n\n";
        $instructions .= "      <p><strong>Equipment</strong></p>\n";
        $instructions .= "      <ul class='equipment-list'><li>List the types of lab equipment used</li>...</ul>\n";
        $instructions .= "   </div>\n\n";
        $instructions .= "   (Repeat facility-block for Previous Facilities)\n\n";

        $instructions .= "5. LICENSES & CERTIFICATIONS SECTION:\n";
        $instructions .= "   <h2 class='section-title'>LICENSES & CERTIFICATIONS</h2>\n";
        $instructions .= "   <ul class='certifications-block'>\n";
        $instructions .= "      <li>List All Licenses, RMT PRC etc.</li>\n";
        $instructions .= "      <li>List English Exams, IELTS Test Dates & Scores</li>\n";
        $instructions .= "      <li>List any Certifications, MLS(ASCPi), CGFNS, BLS, etc.</li>\n";
        $instructions .= "   </ul>\n\n";

        $instructions .= "IMPORTANT: Use underlined text formatting (<u>) for section headers like 'Responsibilities:', 'Samples Handled:'. Use the exact CSS classes provided above.\n";

        return $instructions;
    }

    /**
     * Format user prompt for resume generation.
     */
    protected function formatUserPrompt(ReportRequestData $data, string $baseContext): string
    {
        return "Generate a professional resume/CV.
                
                CANDIDATE DETAILS: (Extract from source content)
                TARGET ROLE: {$data->targetRole}
                STYLE VARIANT: {$data->variant}
                
                {$baseContext}
                
                STRUCTURE:
                1. Professional Summary (tailored to target role if provided)
                2. Core Competencies (matching job requirements)
                3. Key Skills (prioritized for target role)
                4. Work Experience (reverse chronological, all details preserved)
                5. Education (complete details)
                6. Certifications (all certifications listed)
                7. Additional Sections (if present in source)
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
    }

    /**
     * Sanitize output with tailoring report extraction.
     */
    public function sanitizeOutput(string $rawOutput): string
    {
        // Parse Split Response (Tailoring + Content)
        $tailoringReport = '';
        $cleanContent = $rawOutput;

        if (preg_match('/<tailoring_report>(.*?)<\/tailoring_report>/s', $rawOutput, $matches)) {
            $tailoringReport = trim($matches[1]);
        }

        if (preg_match('/<document_content>(.*?)<\/document_content>/s', $rawOutput, $matches)) {
            $cleanContent = trim($matches[1]);
        } else {
            // Fallback: If tags are missing but tailoring report exists, try to strip it
            $cleanContent = preg_replace('/<tailoring_report>.*?<\/tailoring_report>/s', '', $cleanContent);
        }

        // Apply base sanitization
        $cleanContent = parent::sanitizeOutput($cleanContent);

        // Append tailoring report marker if exists
        if ($tailoringReport) {
            return $cleanContent.'<!-- TAILORING_REPORT_START -->'.$tailoringReport.'<!-- TAILORING_REPORT_END -->';
        }

        return $cleanContent;
    }

    /**
     * CV/Resume doesn't typically require deep research.
     */
    public function requiresResearch(): bool
    {
        return false;
    }
}
