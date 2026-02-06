{{--
    CV/Resume Template: Classic Professional
    
    Optimized for ATS (Applicant Tracking Systems)
    Clean, text-focused layout
    
    ENHANCED: Complete PDF extraction + AI tailoring
    - Extracts ALL resume content (no data loss)
    - Adds Core Competencies section
    - Prioritizes skills & summary on page 1
    - Tailors content based on job description
--}}

<div class="space-y-6">
    {{-- Target Role & Job Description --}}
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200 space-y-4">
        <h4 class="text-[10px] font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Target Position & Job Description
        </h4>
        
        <div>
            <label class="block text-[9px] font-semibold text-blue-900 mb-2">Target Position *</label>
            <input 
                type="text" 
                x-model="targetRole"
                placeholder="e.g., Senior Software Engineer"
                class="w-full px-4 py-2.5 rounded-xl border border-blue-200 text-sm focus:ring-2 focus:ring-blue-400/20 focus:border-blue-400 transition-all"
            >
            <p class="text-[9px] text-blue-700 mt-1.5">⚡ AI will optimize your resume for this specific role</p>
        </div>

        <div>
            <label class="block text-[9px] font-semibold text-blue-900 mb-2">Job Description (Paste full job posting)</label>
            <textarea 
                x-model="jobDescription"
                rows="6"
                placeholder="Paste the complete job description here...&#10;&#10;The AI will:&#10;✓ Match keywords from the job description&#10;✓ Emphasize relevant skills & experiences&#10;✓ Highlight accomplishments that prove your fit&#10;✓ Ensure ATS compatibility"
                class="w-full px-4 py-3 rounded-xl border border-blue-200 text-sm focus:ring-2 focus:ring-blue-400/20 focus:border-blue-400 transition-all leading-relaxed"
            ></textarea>
            <p class="text-[9px] text-blue-700 mt-1.5">🎯 AI will identify top 5 keywords and tailor your resume to match</p>
        </div>
    </div>

    {{-- Profile Photo Upload --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Professional Photo (Optional)
        </label>
        <div class="relative">
            <input 
                type="file" 
                @change="uploadPhoto"
                accept="image/*"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all"
            >
            <div x-show="isUploadingPhoto" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-primary">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Uploading...
                </div>
            </div>
        </div>
        <div x-show="profilePhotoUrl" class="mt-4 flex items-center gap-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50">
            <img :src="profilePhotoUrl" class="w-12 h-12 rounded-full object-cover">
            <span class="text-xs text-emerald-700">Photo uploaded successfully</span>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Email Address
            </label>
            <input 
                type="email" 
                x-model="email"
                placeholder="your.email@example.com"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Phone Number
            </label>
            <input 
                type="tel" 
                x-model="phone"
                placeholder="+1 (555) 123-4567"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Location
            </label>
            <input 
                type="text" 
                x-model="location"
                placeholder="City, Country"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Website / Portfolio
            </label>
            <input 
                type="url" 
                x-model="website"
                placeholder="https://yourportfolio.com"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Resume Import (ENHANCED: Complete Extraction) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            📄 Import Existing Resume
            <span class="text-[9px] bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-normal">Complete Extraction</span>
        </label>
        <div class="relative">
            <input 
                type="file" 
                @change="parseResume"
                accept=".pdf,.txt,.md,.docx,.doc,.rtf"
                class="w-full px-5 py-3.5 rounded-2xl border border-dashed border-slate-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-200 transition-all"
            >
            <div x-show="isParsing" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-violet-600">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Extracting complete resume data...
                </div>
            </div>
        </div>
        <div class="mt-3 bg-violet-50 rounded-xl p-4 border border-violet-200">
            <p class="text-[9px] text-violet-800 font-semibold mb-2">✓ AI extracts 100% of your resume content:</p>
            <ul class="text-[9px] text-violet-700 space-y-1 ml-4">
                <li>• Professional summary & career objectives</li>
                <li>• Complete work history with dates & achievements</li>
                <li>• Education, degrees & certifications</li>
                <li>• Technical & soft skills</li>
                <li>• Projects, awards & publications</li>
                <li>• All quantifiable metrics & accomplishments</li>
            </ul>
        </div>
    </div>

    {{-- Complete Resume Content (All Details Preserved) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Complete Resume Content
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="📋 PASTE OR REVIEW YOUR COMPLETE RESUME CONTENT:&#10;&#10;PROFESSIONAL SUMMARY:&#10;[Your career summary - will be optimized for target role]&#10;&#10;CORE COMPETENCIES:&#10;• Skill 1 | Skill 2 | Skill 3 (AI will add relevant competencies)&#10;&#10;WORK EXPERIENCE:&#10;Job Title | Company Name | Dates&#10;• Achievement 1 (with metrics if possible)&#10;• Achievement 2&#10;&#10;EDUCATION:&#10;Degree, Institution, Year&#10;&#10;CERTIFICATIONS & SKILLS:&#10;[All technical and soft skills]&#10;&#10;⚠️ IMPORTANT: AI will preserve ALL your content while tailoring it for the job!"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
        <div class="mt-3 bg-emerald-50 rounded-xl p-4 border border-emerald-200">
            <p class="text-[9px] text-emerald-800 font-semibold mb-2">🎯 AI Resume Optimization (Zero Data Loss):</p>
            <ul class="text-[9px] text-emerald-700 space-y-1 ml-4">
                <li>✓ <strong>ADDS</strong> Core Competencies section on page 1</li>
                <li>✓ <strong>PRIORITIZES</strong> skills & professional summary at top</li>
                <li>✓ <strong>TAILORS</strong> content to match job description keywords</li>
                <li>✓ <strong>PRESERVES</strong> all your original achievements & details</li>
                <li>✓ <strong>ENHANCES</strong> descriptions with action verbs & impact</li>
                <li>✓ <strong>FORMATS</strong> for ATS compatibility & readability</li>
            </ul>
        </div>
    </div>
</div>
