{{--
    CV/Resume Template: International Standard
    
    Healthcare/MLS format with comprehensive personal details
    Includes facility information, equipment, samples, and certifications
--}}

<div class="space-y-6">
    {{-- Target Role & Job Description (ENHANCED) --}}
    <div class="bg-teal-50 rounded-2xl p-6 border border-teal-200">
        <label class="block text-[10px] font-bold text-teal-900 uppercase tracking-wider mb-3 flex items-center gap-2">
            ⚡ Target Position & Job Requirements
            <span class="text-[9px] bg-teal-200 text-teal-800 px-2 py-0.5 rounded-full font-normal">AI-Powered Matching</span>
        </label>
        <input 
            type="text" 
            x-model="targetRole"
            placeholder="e.g., Medical Laboratory Scientist, Clinical Researcher, Healthcare Specialist"
            class="w-full px-5 py-3.5 rounded-2xl border border-teal-200 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-400 transition-all mb-4"
        >
        <label class="block text-[10px] font-bold text-teal-900 uppercase tracking-wider mb-3">
            Job Description (Optional but Recommended)
        </label>
        <textarea 
            x-model="jobDescription"
            rows="6"
            placeholder="Paste the job description here for AI to match required certifications, equipment experience, and clinical requirements..."
            class="w-full px-5 py-4 rounded-2xl border border-teal-200 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-400 transition-all"
        ></textarea>
        <p class="text-[10px] text-teal-700 mt-3 leading-relaxed">🎯 AI will analyze clinical requirements, certifications, and facility needs to perfectly tailor your CV while preserving all your professional qualifications.</p>
    </div>

    {{-- Profile Photo (Required for International) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            Professional Photograph
            <span class="text-[9px] bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Required</span>
        </label>
        <input 
            type="file" 
            @change="uploadPhoto"
            accept="image/*"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all"
        >
        <div x-show="profilePhotoUrl" class="mt-4 flex items-center gap-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50">
            <img :src="profilePhotoUrl" class="w-20 h-24 rounded-lg object-cover shadow-sm">
            <span class="text-xs text-emerald-700">Passport-style photo uploaded</span>
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
                placeholder="professional@email.com"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Contact Number
            </label>
            <input 
                type="tel" 
                x-model="phone"
                placeholder="+63 912 345 6789"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Complete Address
            </label>
            <input 
                type="text" 
                x-model="location"
                placeholder="Street, Barangay, City, Province, Country"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Professional Website / LinkedIn
            </label>
            <input 
                type="url" 
                x-model="website"
                placeholder="https://linkedin.com/in/yourprofile"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Personal Information (International Standard) --}}
    <div class="bg-slate-50 rounded-2xl p-6 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-4">Personal Details</h4>
        
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Age</label>
                <input 
                    type="number" 
                    x-model="personalInfo.age"
                    placeholder="28"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Date of Birth</label>
                <input 
                    type="date" 
                    x-model="personalInfo.dob"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Gender</label>
                <select 
                    x-model="personalInfo.gender"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Civil Status</label>
                <select 
                    x-model="personalInfo.civil_status"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
                    <option value="">Select</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Nationality</label>
                <input 
                    type="text" 
                    x-model="personalInfo.nationality"
                    placeholder="Filipino"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Religion</label>
                <input 
                    type="text" 
                    x-model="personalInfo.religion"
                    placeholder="Optional"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Place of Birth</label>
                <input 
                    type="text" 
                    x-model="personalInfo.place_of_birth"
                    placeholder="City, Country"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Languages Spoken</label>
                <input 
                    type="text" 
                    x-model="personalInfo.languages"
                    placeholder="English, Filipino, etc."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
    </div>

    {{-- Resume Import (ENHANCED: Complete Extraction) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            📄 Import Existing CV
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
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Extracting complete CV data...
                </div>
            </div>
        </div>
        <div class="mt-3 bg-violet-50 rounded-xl p-4 border border-violet-200">
            <p class="text-[9px] text-violet-800 font-semibold mb-2">✓ AI extracts 100% of your CV content:</p>
            <ul class="text-[9px] text-violet-700 space-y-1 ml-4">
                <li>• Professional summary & career objectives</li>
                <li>• Complete work history with facility details & responsibilities</li>
                <li>• Education, degrees & professional certifications/licenses</li>
                <li>• Laboratory equipment & clinical skills</li>
                <li>• Research experience, publications & professional affiliations</li>
                <li>• All personal details & quantifiable achievements</li>
            </ul>
        </div>
    </div>

    {{-- Professional Experience & Qualifications (All Details Preserved) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Complete Professional Experience & Qualifications
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="📋 PASTE OR REVIEW YOUR COMPLETE CV CONTENT:&#10;&#10;PROFESSIONAL SUMMARY:&#10;[Your healthcare expertise - will be optimized for target role]&#10;&#10;CORE COMPETENCIES:&#10;• Clinical Skills | Lab Equipment | Certifications (AI adds relevant competencies)&#10;&#10;PROFESSIONAL EXPERIENCE:&#10;Position | Healthcare Facility/Department | Dates&#10;• Key responsibilities & patient care achievements&#10;• Equipment operated & procedures performed&#10;&#10;EDUCATION & CERTIFICATIONS:&#10;Degree, Institution, Year | Professional Licenses | Certifications&#10;&#10;RESEARCH & AFFILIATIONS:&#10;[Publications, conferences, professional memberships]&#10;&#10;⚠️ IMPORTANT: AI preserves ALL qualifications while tailoring for the role!"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
        <div class="mt-3 bg-emerald-50 rounded-xl p-4 border border-emerald-200">
            <p class="text-[9px] text-emerald-800 font-semibold mb-2">🏭 AI Healthcare CV Optimization (Zero Data Loss):</p>
            <ul class="text-[9px] text-emerald-700 space-y-1 ml-4">
                <li>✓ <strong>ADDS</strong> Core Clinical Competencies section on page 1</li>
                <li>✓ <strong>PRIORITIZES</strong> certifications & key skills at top</li>
                <li>✓ <strong>TAILORS</strong> content to match job's clinical requirements</li>
                <li>✓ <strong>PRESERVES</strong> all your licenses, certifications & experience details</li>
                <li>✓ <strong>ENHANCES</strong> descriptions with clinical impact & patient outcomes</li>
                <li>✓ <strong>FORMATS</strong> according to international CV standards</li>
            </ul>
        </div>
    </div>
</div>
