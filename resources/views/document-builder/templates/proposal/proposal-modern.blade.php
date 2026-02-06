{{--
    Proposal Template: Modern Pitch
    
    Visually engaging proposal for creative services
    Startup/agency style with problem-solution framework
--}}

<div class="space-y-6">
    {{-- Pitch Type --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Pitch Focus
        </label>
        <select 
            x-model="analysisType"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
            <option value="Sales Pitch">Sales Pitch</option>
            <option value="Project Proposal">Creative Project</option>
            <option value="Partnership Offer">Strategic Partnership</option>
            <option value="Investment Pitch">Investment / Funding</option>
        </select>
    </div>

    {{-- Project/Pitch Name --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Pitch Title / Project Name
        </label>
        <input 
            type="text" 
            x-model="prompt"
            placeholder="e.g., Brand Identity Transformation"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Client/Audience Info --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Client / Prospect Name
            </label>
            <input 
                type="text" 
                x-model="recipientName"
                placeholder="Innovative Startup Inc."
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Key Decision Maker
            </label>
            <input 
                type="text" 
                x-model="recipientTitle"
                placeholder="Sarah Chen, Founder"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Investment Structure --}}
    <div class="bg-gradient-to-br from-orange-50 to-pink-50 rounded-2xl p-6 border border-orange-200 space-y-4">
        <h4 class="text-[10px] font-bold text-orange-900 uppercase tracking-wider flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Investment & Timeline
        </h4>
        
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-orange-900 mb-2">Total Investment</label>
                <input 
                    type="number" 
                    x-model="financials.totalInvestment"
                    placeholder="10000"
                    class="w-full px-4 py-2.5 rounded-xl border border-orange-200 text-sm font-bold"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-orange-900 mb-2">Currency</label>
                <select 
                    x-model="financials.currency"
                    class="w-full px-4 py-2.5 rounded-xl border border-orange-200 text-sm"
                >
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="PHP">PHP (₱)</option>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-orange-900 mb-2">Delivery Timeline</label>
                <input 
                    type="text" 
                    x-model="financials.timeline"
                    placeholder="6-8 weeks"
                    class="w-full px-4 py-2.5 rounded-xl border border-orange-200 text-sm"
                >
            </div>
        </div>

        {{-- Payment Structure --}}
        <div>
            <label class="block text-[9px] font-semibold text-orange-900 mb-3">Payment Structure</label>
            <template x-for="(milestone, index) in financials.paymentMilestones" :key="index">
                <div class="flex items-center gap-3 mb-2 bg-white rounded-lg p-2">
                    <input 
                        type="text" 
                        x-model="milestone.name"
                        placeholder="Phase name"
                        class="flex-1 px-4 py-2 rounded-lg border border-orange-100 text-sm"
                    >
                    <div class="flex items-center gap-2">
                        <input 
                            type="number" 
                            x-model="milestone.percentage"
                            min="0" max="100"
                            class="w-20 px-3 py-2 rounded-lg border border-orange-100 text-sm text-center font-bold"
                        >
                        <span class="text-sm text-orange-700 font-semibold">%</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Pitch Content (Problem-Solution-Impact) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Pitch Narrative (Problem → Solution → Impact)
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="Structure your pitch:&#10;&#10;THE CHALLENGE:&#10;[What problem does the client/market face?]&#10;&#10;OUR SOLUTION:&#10;[How does your proposal solve it?]&#10;&#10;WHAT WE'LL DELIVER:&#10;• [Key deliverable 1]&#10;• [Key deliverable 2]&#10;• [Key deliverable 3]&#10;&#10;THE IMPACT:&#10;[Measurable outcomes and business value]&#10;&#10;WHY US:&#10;[Your unique advantage and relevant experience]&#10;&#10;NEXT STEPS:&#10;[Clear call to action]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will create a visually compelling pitch deck format</p>
    </div>
</div>
