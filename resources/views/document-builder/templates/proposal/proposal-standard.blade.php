{{--
    Proposal Template: Standard Business Proposal
    
    Clear offer with deliverables, timeline, and pricing
    Professional format for service proposals
--}}

<div class="space-y-6">
    {{-- Proposal Type --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Proposal Type
        </label>
        <select 
            x-model="analysisType"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
            <option value="Project Proposal">Project Proposal</option>
            <option value="Sales Pitch">Sales Pitch</option>
            <option value="Grant Application">Grant Application</option>
            <option value="Partnership Offer">Partnership Offer</option>
        </select>
    </div>

    {{-- Project Title --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Project / Proposal Title
        </label>
        <input 
            type="text" 
            x-model="prompt"
            placeholder="e.g., Website Redesign Project"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Client Information --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Client / Organization Name
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
                Contact Person & Title
            </label>
            <input 
                type="text" 
                x-model="recipientTitle"
                placeholder="John Smith, CEO"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Financial Details --}}
    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-200 space-y-4">
        <h4 class="text-[10px] font-bold text-amber-900 uppercase tracking-wider flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Investment & Timeline
        </h4>
        
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-amber-900 mb-2">Total Investment</label>
                <input 
                    type="number" 
                    x-model="financials.totalInvestment"
                    placeholder="5000"
                    class="w-full px-4 py-2.5 rounded-xl border border-amber-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-amber-900 mb-2">Currency</label>
                <select 
                    x-model="financials.currency"
                    class="w-full px-4 py-2.5 rounded-xl border border-amber-200 text-sm"
                >
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="PHP">PHP (₱)</option>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-amber-900 mb-2">Timeline</label>
                <input 
                    type="text" 
                    x-model="financials.timeline"
                    placeholder="4-6 weeks"
                    class="w-full px-4 py-2.5 rounded-xl border border-amber-200 text-sm"
                >
            </div>
        </div>

        {{-- Payment Milestones --}}
        <div>
            <label class="block text-[9px] font-semibold text-amber-900 mb-3">Payment Milestones</label>
            <template x-for="(milestone, index) in financials.paymentMilestones" :key="index">
                <div class="flex items-center gap-3 mb-2">
                    <input 
                        type="text" 
                        x-model="milestone.name"
                        placeholder="Milestone name"
                        class="flex-1 px-4 py-2 rounded-lg border border-amber-200 text-sm"
                    >
                    <div class="flex items-center gap-2">
                        <input 
                            type="number" 
                            x-model="milestone.percentage"
                            min="0" max="100"
                            class="w-20 px-3 py-2 rounded-lg border border-amber-200 text-sm text-center"
                        >
                        <span class="text-sm text-amber-700">%</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Proposal Scope & Details --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Scope, Deliverables & Methodology
        </label>
        <textarea 
            x-model="sourceContent"
            rows="12"
            placeholder="Describe the proposal in detail:&#10;&#10;PROJECT OVERVIEW:&#10;[Brief description of what you're proposing]&#10;&#10;DELIVERABLES:&#10;• [Deliverable 1]&#10;• [Deliverable 2]&#10;• [Deliverable 3]&#10;&#10;METHODOLOGY:&#10;Phase 1: [Description]&#10;Phase 2: [Description]&#10;Phase 3: [Description]&#10;&#10;VALUE PROPOSITION:&#10;[Why this proposal benefits the client]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed font-mono"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will structure this into a professional proposal format</p>
    </div>
</div>
