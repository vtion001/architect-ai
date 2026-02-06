{{--
    CV/Resume Template: Modern Creative
    
    Two-column layout with skills bars and visual accents
    Best for creative and design roles
--}}

<div class="space-y-6">
    {{-- Target Role & Job Description (ENHANCED) --}}
    <div class="bg-purple-50 rounded-2xl p-6 border border-purple-200">
        <label class="block text-[10px] font-bold text-purple-900 uppercase tracking-wider mb-3 flex items-center gap-2">
            ⚡ Target Creative Role & Job Description
            <span class="text-[9px] bg-purple-200 text-purple-800 px-2 py-0.5 rounded-full font-normal">AI-Powered Matching</span>
        </label>
        <input 
            type="text" 
            x-model="targetRole"
            placeholder="e.g., UX Designer, Creative Director, Brand Manager"
            class="w-full px-5 py-3.5 rounded-2xl border border-purple-200 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 transition-all mb-4"
        >
        <label class="block text-[10px] font-bold text-purple-900 uppercase tracking-wider mb-3">
            Job Description (Optional but Recommended)
        </label>
        <textarea 
            x-model="jobDescription"
            rows="6"
            placeholder="Paste the job description here for AI to match keywords, design skills, and creative requirements..."
            class="w-full px-5 py-4 rounded-2xl border border-purple-200 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 transition-all"
        ></textarea>
        <p class="text-[10px] text-purple-700 mt-3 leading-relaxed">🎯 AI will analyze design requirements, creative tools, and portfolio needs to perfectly tailor your resume while preserving all your achievements.</p>
    </div>

    {{-- Profile Photo Upload (Required for Modern) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            Professional Photo
            <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full">Recommended</span>
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
            <img :src="profilePhotoUrl" class="w-16 h-16 rounded-xl object-cover shadow-sm">
            <div>
                <span class="text-xs font-semibold text-emerald-700 block">Photo uploaded</span>
                <span class="text-[10px] text-emerald-600">Will appear in sidebar</span>
            </div>
        </div>
    </div>

    {{-- Contact & Links --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Email
            </label>
            <input 
                type="email" 
                x-model="email"
                placeholder="creative@example.com"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Phone
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
                placeholder="City, State"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Portfolio URL
            </label>
            <input 
                type="url" 
                x-model="website"
                placeholder="https://behance.net/yourwork"
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
                    Extracting complete creative portfolio data...
                </div>
            </div>
        </div>
        <div class="mt-3 bg-violet-50 rounded-xl p-4 border border-violet-200">
            <p class="text-[9px] text-violet-800 font-semibold mb-2">✓ AI extracts 100% of your resume content:</p>
            <ul class="text-[9px] text-violet-700 space-y-1 ml-4">
                <li>• Professional summary & creative philosophy</li>
                <li>• Complete work history with design projects</li>
                <li>• Education, degrees & design certifications</li>
                <li>• Creative tools & technical skills</li>
                <li>• Portfolio projects, awards & exhibitions</li>
                <li>• All quantifiable results & client impact</li>
            </ul>
        </div>
    </div>

    {{-- Complete Resume Content (All Creative Details Preserved) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Complete Resume Content
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="📋 PASTE OR REVIEW YOUR COMPLETE CREATIVE RESUME:&#10;&#10;CREATIVE PHILOSOPHY/SUMMARY:&#10;[Your design approach - will be optimized for target role]&#10;&#10;CORE DESIGN COMPETENCIES:&#10;• UI/UX Design | Brand Identity | Visual Systems (AI adds relevant skills)&#10;&#10;WORK EXPERIENCE & KEY PROJECTS:&#10;Creative Role | Company/Agency | Dates&#10;• Project 1 with measurable impact (e.g., increased engagement 40%)&#10;• Project 2 with creative innovation&#10;&#10;DESIGN TOOLS & SKILLS:&#10;Figma, Adobe Creative Suite, Sketch, etc.&#10;&#10;PORTFOLIO HIGHLIGHTS & AWARDS:&#10;[All exhibitions, recognitions, publications]&#10;&#10;⚠️ IMPORTANT: AI preserves ALL your work while tailoring for the creative role!"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
        <div class="mt-3 bg-emerald-50 rounded-xl p-4 border border-emerald-200">
            <p class="text-[9px] text-emerald-800 font-semibold mb-2">🎨 AI Creative Resume Optimization (Zero Data Loss):</p>
            <ul class="text-[9px] text-emerald-700 space-y-1 ml-4">
                <li>✓ <strong>ADDS</strong> Core Design Competencies with visual skill bars on page 1</li>
                <li>✓ <strong>PRIORITIZES</strong> design tools & creative philosophy at top</li>
                <li>✓ <strong>TAILORS</strong> content to match creative job requirements</li>
                <li>✓ <strong>PRESERVES</strong> all your portfolio projects & achievements</li>
                <li>✓ <strong>ENHANCES</strong> project descriptions with creative impact metrics</li>
                <li>✓ <strong>FORMATS</strong> with modern two-column layout & visual accents</li>
            </ul>
        </div>
    </div>
</div>
