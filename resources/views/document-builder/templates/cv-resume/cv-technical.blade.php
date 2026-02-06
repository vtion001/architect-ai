{{--
    CV/Resume Template: Technical Expert
    
    Focused on tech stack, programming languages, and project history
    Optimized for software engineering and technical roles
--}}

<div class="space-y-6">
    {{-- Target Technical Role & Job Description (ENHANCED) --}}
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
        <label class="block text-[10px] font-bold text-blue-900 uppercase tracking-wider mb-3 flex items-center gap-2">
            ⚡ Target Technical Position & Job Requirements
            <span class="text-[9px] bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full font-normal">AI-Powered Matching</span>
        </label>
        <input 
            type="text" 
            x-model="targetRole"
            placeholder="e.g., Full-Stack Engineer, DevOps Architect, SRE, Backend Developer"
            class="w-full px-5 py-3.5 rounded-2xl border border-blue-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition-all font-mono mb-4"
        >
        <label class="block text-[10px] font-bold text-blue-900 uppercase tracking-wider mb-3">
            Job Description (Optional but Recommended)
        </label>
        <textarea 
            x-model="jobDescription"
            rows="6"
            placeholder="Paste the technical job description here for AI to match required languages, frameworks, tools, and technical requirements..."
            class="w-full px-5 py-4 rounded-2xl border border-blue-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition-all font-mono"
        ></textarea>
        <p class="text-[10px] text-blue-700 mt-3 leading-relaxed">🎯 AI will analyze tech stack requirements, programming languages, and system design needs to perfectly tailor your resume while preserving all your technical achievements.</p>
    </div>

    {{-- Profile Photo (Optional for Technical) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Profile Photo (Optional)
        </label>
        <input 
            type="file" 
            @change="uploadPhoto"
            accept="image/*"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all"
        >
    </div>

    {{-- Technical Contact Info --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Email
            </label>
            <input 
                type="email" 
                x-model="email"
                placeholder="dev@example.com"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono"
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
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono"
            >
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Location
            </label>
            <input 
                type="text" 
                x-model="location"
                placeholder="Remote / City"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                GitHub Profile
            </label>
            <input 
                type="url" 
                x-model="website"
                placeholder="github.com/username"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                LinkedIn
            </label>
            <input 
                type="url" 
                x-model="personalInfo.linkedin"
                placeholder="linkedin.com/in/username"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono"
            >
        </div>
    </div>

    {{-- Resume Import --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            📄 Import Technical Resume
            <span class="text-[9px] bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-normal">Complete Extraction</span>
        </label>
        <div class="relative">
            <input 
                type="file" 
                @change="parseResume"
                accept=".pdf,.txt,.md,.docx,.doc,.rtf"
                class="w-full px-5 py-3.5 rounded-2xl border border-dashed border-slate-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 transition-all"
            >
            <div x-show="isParsing" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-blue-600">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Extracting complete technical resume data...
                </div>
            </div>
        </div>
        <div class="mt-3 bg-violet-50 rounded-xl p-4 border border-violet-200">
            <p class="text-[9px] text-violet-800 font-semibold mb-2">✓ AI extracts 100% of your technical resume:</p>
            <ul class="text-[9px] text-violet-700 space-y-1 ml-4">
                <li>• Professional summary & technical expertise</li>
                <li>• Complete work history with project details & impact metrics</li>
                <li>• Education, degrees & technical certifications</li>
                <li>• Programming languages, frameworks & tools</li>
                <li>• Open source contributions, GitHub projects & achievements</li>
                <li>• All quantifiable technical accomplishments</li>
            </ul>
        </div>
    </div>

    {{-- Complete Technical Resume Content (All Details Preserved) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Complete Technical Resume Content
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="📋 PASTE OR REVIEW YOUR COMPLETE TECHNICAL RESUME:&#10;&#10;TECHNICAL SUMMARY:&#10;[Your technical expertise - will be optimized for target role]&#10;&#10;CORE TECHNICAL COMPETENCIES:&#10;• Languages: Python, JavaScript, Go | Frameworks: React, Django, Kubernetes (AI adds relevant skills)&#10;&#10;TECHNICAL EXPERIENCE & KEY PROJECTS:&#10;Senior Engineer | Company | Dates&#10;• Built scalable system handling 10M requests/day (99.99% uptime)&#10;• Reduced deployment time by 70% with CI/CD pipeline&#10;&#10;TECH STACK & TOOLS:&#10;Cloud: AWS, GCP, Azure | DevOps: Docker, K8s, Terraform | Databases: PostgreSQL, Redis&#10;&#10;CERTIFICATIONS & OPEN SOURCE:&#10;[AWS certifications, GitHub contributions, technical publications]&#10;&#10;⚠️ IMPORTANT: AI preserves ALL technical details while tailoring for the role!"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono leading-relaxed"
        ></textarea>
        <div class="mt-3 bg-emerald-50 rounded-xl p-4 border border-emerald-200">
            <p class="text-[9px] text-emerald-800 font-semibold mb-2">🛠️ AI Technical Resume Optimization (Zero Data Loss):</p>
            <ul class="text-[9px] text-emerald-700 space-y-1 ml-4">
                <li>✓ <strong>ADDS</strong> Core Technical Competencies organized by category on page 1</li>
                <li>✓ <strong>PRIORITIZES</strong> relevant tech stack & programming languages at top</li>
                <li>✓ <strong>TAILORS</strong> content to match job's technical requirements</li>
                <li>✓ <strong>PRESERVES</strong> all your project metrics & system scale achievements</li>
                <li>✓ <strong>ENHANCES</strong> descriptions with technical impact & performance metrics</li>
                <li>✓ <strong>FORMATS</strong> with clean structure optimized for technical recruiters</li>
            </ul>
        </div>
    </div>
</div>
