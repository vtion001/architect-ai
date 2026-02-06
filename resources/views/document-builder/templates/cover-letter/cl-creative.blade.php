{{--
    Cover Letter Template: Modern Creative
    
    Contemporary layout with personal branding elements
    Best for creative, design, and startup roles
--}}

<div class="space-y-6">
    {{-- Target Role --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Target Creative Role
        </label>
        <input 
            type="text" 
            x-model="targetRole"
            placeholder="e.g., Brand Designer, Creative Strategist"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Job Description --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Job Posting / Requirements
        </label>
        <textarea 
            x-model="jobDescription"
            rows="4"
            placeholder="Paste the job description for AI to analyze..."
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        ></textarea>
    </div>

    {{-- Company & Contact --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Company / Organization
            </label>
            <input 
                type="text" 
                x-model="recipientName"
                placeholder="Creative Studio Inc."
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Contact Person (Optional)
            </label>
            <input 
                type="text" 
                x-model="recipientTitle"
                placeholder="Alex Johnson, Creative Director"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Your Contact Info --}}
    <div class="bg-slate-50 rounded-2xl p-6 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Your Contact Information</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Your Name</label>
                <input 
                    type="text" 
                    x-model="senderName"
                    placeholder="Your Full Name"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Your Title</label>
                <input 
                    type="text" 
                    x-model="senderTitle"
                    placeholder="Your Professional Title"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Email</label>
                <input 
                    type="email" 
                    x-model="email"
                    placeholder="you@email.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Portfolio URL</label>
                <input 
                    type="url" 
                    x-model="website"
                    placeholder="https://yourportfolio.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
    </div>

    {{-- Resume Import --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
            Import Your Portfolio/Resume
            <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full">For AI Drafting</span>
        </label>
        <div class="relative">
            <input 
                type="file" 
                @change="parseResume"
                accept=".pdf,.txt,.md,.docx"
                class="w-full px-5 py-3.5 rounded-2xl border border-dashed border-slate-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200 transition-all"
            >
            <div x-show="isParsing" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-purple-600">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Analyzing...
                </div>
            </div>
        </div>
    </div>

    {{-- AI Draft Button --}}
    <div x-show="sourceContent && targetRole" class="bg-gradient-to-r from-purple-50 via-pink-50 to-cyan-50 rounded-2xl p-6 border border-purple-200">
        <button 
            @click="draftCoverLetter"
            :disabled="isParsing"
            class="w-full px-6 py-4 bg-gradient-to-r from-purple-500 via-pink-500 to-cyan-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <span x-show="!isParsing">AI Draft Creative Letter</span>
            <span x-show="isParsing">Crafting...</span>
        </button>
        <p class="text-[10px] text-center text-purple-700 mt-3">AI will craft a compelling, personality-driven letter</p>
    </div>

    {{-- Letter Content --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Your Story & Pitch
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="Write your creative cover letter or let AI draft it...&#10;&#10;Hi [Hiring Manager],&#10;&#10;When I saw the [Position] opening at [Company], I knew I had to reach out. Your work on [project] genuinely inspired my own approach to [skill/field].&#10;&#10;I've spent the last [X years] [achievement]. What excites me most is [passion point]...&#10;&#10;Here's what I can bring to your team:&#10;• [Key skill/achievement]&#10;• [Another strength]&#10;• [Unique value proposition]&#10;&#10;I'd love to chat more about how my experience with [relevant project] aligns with your goals for [company initiative].&#10;&#10;Looking forward to connecting,&#10;[Your Name]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">Modern, conversational tone with visual hierarchy will be applied</p>
    </div>
</div>
