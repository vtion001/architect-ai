{{--
    Contract Template: Freelance Agreement
    
    Independent contractor / project-based engagement
    Flexible terms for freelance work
--}}

<div class="space-y-6">
    {{-- Project Details --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Project / Engagement Title
        </label>
        <input 
            type="text" 
            x-model="prompt"
            placeholder="e.g., Website Development Project"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
    </div>

    {{-- Client (Hiring Party) --}}
    <div class="bg-purple-50 rounded-2xl p-6 border border-purple-200 space-y-4">
        <h4 class="text-[10px] font-bold text-purple-900 uppercase tracking-wider">Client / Hiring Party</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-purple-900 mb-2">Company / Individual Name</label>
                <input 
                    type="text" 
                    x-model="recipientName"
                    placeholder="Client Company Inc."
                    class="w-full px-4 py-2.5 rounded-xl border border-purple-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-purple-900 mb-2">Contact Person</label>
                <input 
                    type="text" 
                    x-model="recipientTitle"
                    placeholder="Jane Doe, Project Manager"
                    class="w-full px-4 py-2.5 rounded-xl border border-purple-200 text-sm"
                >
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-purple-900 mb-2">Email</label>
                <input 
                    type="email" 
                    x-model="contractDetails.clientEmail"
                    placeholder="client@company.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-purple-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-purple-900 mb-2">Business Address</label>
                <input 
                    type="text" 
                    x-model="contractDetails.clientAddress"
                    placeholder="123 Client St, City, State"
                    class="w-full px-4 py-2.5 rounded-xl border border-purple-200 text-sm"
                >
            </div>
        </div>
    </div>

    {{-- Freelancer (Contractor) --}}
    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Freelancer / Contractor</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Your Name / Business Name</label>
                <input 
                    type="text" 
                    x-model="senderName"
                    placeholder="Your Name or LLC"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Professional Title</label>
                <input 
                    type="text" 
                    x-model="senderTitle"
                    placeholder="Freelance Developer"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Email</label>
                <input 
                    type="email" 
                    x-model="email"
                    placeholder="freelancer@email.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Tax ID / EIN (if applicable)</label>
                <input 
                    type="text" 
                    x-model="contractDetails.providerTaxId"
                    placeholder="XX-XXXXXXX"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
    </div>

    {{-- Project Terms --}}
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Project Start Date
            </label>
            <input 
                type="date" 
                x-model="contractDetails.startDate"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Expected Duration
            </label>
            <input 
                type="text" 
                x-model="contractDetails.duration"
                placeholder="3 months"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Rate Structure
            </label>
            <select 
                x-model="analysisType"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
                <option value="Fixed Price">Fixed Price Project</option>
                <option value="Hourly Rate">Hourly Rate</option>
                <option value="Monthly Retainer">Monthly Retainer</option>
                <option value="Milestone-Based">Milestone-Based</option>
            </select>
        </div>
    </div>

    {{-- Scope & Payment Terms --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Scope of Work, Deliverables & Payment Terms
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="SCOPE OF WORK:&#10;• [Detailed description of work to be performed]&#10;• [Specific tasks and responsibilities]&#10;&#10;DELIVERABLES:&#10;• [Deliverable 1 with deadline]&#10;• [Deliverable 2 with deadline]&#10;• [Final deliverables and acceptance criteria]&#10;&#10;COMPENSATION:&#10;• Total Project Fee: $X,XXX&#10;• Payment Schedule:&#10;  - 30% upfront ($X,XXX) upon contract signing&#10;  - 40% ($X,XXX) upon [milestone]&#10;  - 30% ($X,XXX) upon project completion&#10;• Payment Method: [Wire transfer/PayPal/etc.]&#10;• Invoice Terms: Due within 7 days&#10;&#10;IP & OWNERSHIP:&#10;• Work-for-hire: All deliverables owned by client upon final payment&#10;• Contractor retains right to portfolio use&#10;&#10;TERMINATION:&#10;• Either party may terminate with [7/14] days notice&#10;• Payment due for all work completed to date"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed font-mono"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will generate a professional freelance contract with IP protection and payment terms</p>
    </div>
</div>
