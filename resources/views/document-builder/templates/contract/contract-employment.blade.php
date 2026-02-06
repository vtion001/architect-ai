{{--
    Contract Template: Employment Contract
    
    Full-time employee agreement
    Includes compensation, benefits, and responsibilities
--}}

<div class="space-y-6">
    {{-- Position Details --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Position / Job Title
            </label>
            <input 
                type="text" 
                x-model="targetRole"
                placeholder="e.g., Senior Software Engineer"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Department
            </label>
            <input 
                type="text" 
                x-model="recipientTitle"
                placeholder="Engineering"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Employer Information --}}
    <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-200 space-y-4">
        <h4 class="text-[10px] font-bold text-indigo-900 uppercase tracking-wider">Employer Information</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-indigo-900 mb-2">Company Name</label>
                <input 
                    type="text" 
                    x-model="contractDetails.providerBusiness"
                    placeholder="Your Company Inc."
                    class="w-full px-4 py-2.5 rounded-xl border border-indigo-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-indigo-900 mb-2">Company Registration No.</label>
                <input 
                    type="text" 
                    x-model="contractDetails.providerTaxId"
                    placeholder="REG-123456"
                    class="w-full px-4 py-2.5 rounded-xl border border-indigo-200 text-sm"
                >
            </div>
        </div>
        <div>
            <label class="block text-[9px] font-semibold text-indigo-900 mb-2">Company Address</label>
            <input 
                type="text" 
                x-model="contractDetails.providerAddress"
                placeholder="123 Business Park, City, Country"
                class="w-full px-4 py-2.5 rounded-xl border border-indigo-200 text-sm"
            >
        </div>
    </div>

    {{-- Employee Information --}}
    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Employee Information</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Full Legal Name</label>
                <input 
                    type="text" 
                    x-model="recipientName"
                    placeholder="John David Smith"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Email Address</label>
                <input 
                    type="email" 
                    x-model="email"
                    placeholder="john.smith@email.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Residential Address</label>
                <input 
                    type="text" 
                    x-model="location"
                    placeholder="456 Employee St, City, State, ZIP"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">National ID / SSN</label>
                <input 
                    type="text" 
                    x-model="contractDetails.clientTaxId"
                    placeholder="XXX-XX-XXXX"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
    </div>

    {{-- Employment Terms --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Start Date
            </label>
            <input 
                type="date" 
                x-model="contractDetails.startDate"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Contract Type
            </label>
            <select 
                x-model="contractDetails.duration"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
                <option value="Permanent">Permanent (Indefinite)</option>
                <option value="Fixed-term 12 months">Fixed-term (12 months)</option>
                <option value="Fixed-term 24 months">Fixed-term (24 months)</option>
                <option value="Probationary 6 months">Probationary (6 months)</option>
            </select>
        </div>
    </div>

    {{-- Compensation & Terms --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Compensation, Responsibilities & Benefits
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="COMPENSATION:&#10;• Base Salary: $XX,XXX per annum&#10;• Payment Frequency: [Monthly/Bi-weekly]&#10;• Performance Bonus: [Details if applicable]&#10;&#10;JOB RESPONSIBILITIES:&#10;• [Primary responsibility 1]&#10;• [Primary responsibility 2]&#10;• [Additional duties as assigned]&#10;&#10;WORKING HOURS:&#10;• Standard Hours: 40 hours per week&#10;• Schedule: Monday-Friday, 9 AM - 5 PM&#10;• Remote Work: [Policy details]&#10;&#10;BENEFITS:&#10;• Health Insurance&#10;• Paid Time Off: [X days per year]&#10;• Retirement Plan: [401(k) match details]&#10;• Professional Development: [Training budget]&#10;&#10;TERMINATION:&#10;• Notice Period: [30/60/90 days]&#10;• Severance: [If applicable]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed font-mono"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will create a comprehensive employment contract with standard HR clauses</p>
    </div>
</div>
