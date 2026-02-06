{{--
    Shared Report Templates Form
    
    Used for:
    - Executive Summary
    - Market Analysis
    - Financial Overview
    - Competitive Intelligence
    - Trend Analysis
    - Infographic/One-Pager
--}}

<div class="space-y-6">
    {{-- Report Type (Already selected via template) --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Report Type
        </label>
        <input 
            type="text" 
            :value="selectedCategoryData?.name"
            disabled
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-semibold"
        >
    </div>

    {{-- Analysis Focus --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Analysis Focus
        </label>
        <select 
            x-model="analysisType"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
            <template x-for="objective in availableObjectives" :key="objective">
                <option :value="objective" x-text="objective"></option>
            </template>
        </select>
        <p class="text-[10px] text-slate-500 mt-2">Analysis approach will adapt based on your focus area</p>
    </div>

    {{-- Report Title/Topic --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Report Title / Topic
        </label>
        <input 
            type="text" 
            x-model="researchTopic"
            placeholder="e.g., Q4 2024 Market Performance Analysis"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Company/Subject Information --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Company / Organization
            </label>
            <input 
                type="text" 
                x-model="recipientName"
                placeholder="Company Name (if applicable)"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Industry / Sector
            </label>
            <input 
                type="text" 
                x-model="recipientTitle"
                placeholder="e.g., Technology, Healthcare"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Report Period (for time-based reports) --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Reporting Period From
            </label>
            <input 
                type="date" 
                x-model="contractDetails.startDate"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Period Duration
            </label>
            <input 
                type="text" 
                x-model="contractDetails.duration"
                placeholder="Q4 2024, Annual, etc."
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Analysis Prompt/Instructions --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Analysis Instructions & Key Questions
        </label>
        <textarea 
            x-model="prompt"
            rows="4"
            placeholder="What specific insights are you looking for? What questions should this report answer?&#10;&#10;Example:&#10;- Analyze market share trends vs competitors&#10;- Identify growth opportunities in emerging markets&#10;- Assess financial performance against targets"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
        ></textarea>
    </div>

    {{-- Data Source Content --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Source Data & Context
        </label>
        <textarea 
            x-model="sourceContent"
            rows="12"
            placeholder="Paste relevant data, metrics, and context here:&#10;&#10;FINANCIAL DATA:&#10;• Revenue: $X million (YoY: +X%)&#10;• Profit Margin: X%&#10;• Market Cap: $X billion&#10;&#10;MARKET DATA:&#10;• Market Size: $X billion&#10;• Growth Rate: X% CAGR&#10;• Key Players: Company A, B, C&#10;&#10;KEY METRICS:&#10;• Customer Acquisition Cost: $X&#10;• Lifetime Value: $X&#10;• Churn Rate: X%&#10;&#10;QUALITATIVE INSIGHTS:&#10;[Industry trends, competitive moves, regulatory changes, etc.]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed font-mono"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will analyze this data and generate insights, charts, and recommendations</p>
    </div>

    {{-- Report Recipients (Optional) --}}
    <div class="bg-slate-50 rounded-2xl p-6 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Report Prepared For (Optional)</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Recipient Name</label>
                <input 
                    type="text" 
                    x-model="senderName"
                    placeholder="Board of Directors, Executive Team, etc."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Prepared By</label>
                <input 
                    type="text" 
                    x-model="senderTitle"
                    placeholder="Your Name, Your Title"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
    </div>
</div>
