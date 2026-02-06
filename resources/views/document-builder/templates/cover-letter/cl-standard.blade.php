{{--
    Cover Letter Template: Standard Professional
    
    Traditional formal letter format
    Best for corporate and traditional industries
--}}

<div class="space-y-6">
    {{-- Target Role --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Target Position
        </label>
        <input 
            type="text" 
            x-model="targetRole"
            placeholder="e.g., Senior Marketing Manager"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Job Description (for AI matching) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Job Description / Requirements
        </label>
        <textarea 
            x-model="jobDescription"
            rows="4"
            placeholder="Paste the job description here for AI to tailor your cover letter..."
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will match your experience to the job requirements</p>
    </div>

    {{-- Company Information --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Company Name
            </label>
            <input 
                type="text" 
                x-model="recipientName"
                placeholder="ABC Corporation"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Hiring Manager Name
            </label>
            <input 
                type="text" 
                x-model="recipientTitle"
                placeholder="Jane Smith (optional)"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Company Address
        </label>
        <input 
            type="text" 
            x-model="companyAddress"
            placeholder="123 Business St, City, State ZIP"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Import Resume --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            Import Your Resume/CV
            <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full">For AI Drafting</span>
        </label>
        <div class="relative">
            <input 
                type="file" 
                @change="parseResume"
                accept=".pdf,.txt,.md,.docx,.doc"
                class="w-full px-5 py-3.5 rounded-2xl border border-dashed border-slate-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-100 file:text-teal-700 hover:file:bg-teal-200 transition-all"
            >
            <div x-show="isParsing" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-teal-600">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Parsing resume...
                </div>
            </div>
        </div>
        <p class="text-[10px] text-slate-500 mt-2">AI will extract your qualifications and achievements</p>
    </div>

    {{-- AI Draft Button --}}
    <div x-show="sourceContent && targetRole" class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-2xl p-6 border border-teal-200">
        <button 
            @click="draftCoverLetter"
            :disabled="isParsing"
            class="w-full px-6 py-4 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span x-show="!isParsing">AI Draft Cover Letter</span>
            <span x-show="isParsing">Drafting...</span>
        </button>
        <p class="text-[10px] text-center text-teal-700 mt-3">AI will write a tailored cover letter matching the job requirements</p>
    </div>

    {{-- Cover Letter Content --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Cover Letter Content
        </label>
        <textarea 
            x-model="sourceContent"
            rows="12"
            placeholder="Write your cover letter here or let AI draft it for you...&#10;&#10;Dear Hiring Manager,&#10;&#10;I am writing to express my interest in the [Position] role at [Company]. With [X years] of experience in [field], I am confident that my skills align perfectly with your requirements...&#10;&#10;In my current role at [Company], I have successfully [achievement]...&#10;&#10;I am particularly drawn to [Company] because...&#10;&#10;Thank you for considering my application. I look forward to discussing how I can contribute to your team.&#10;&#10;Sincerely,&#10;[Your Name]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">Traditional business letter format will be applied automatically</p>
    </div>
</div>
