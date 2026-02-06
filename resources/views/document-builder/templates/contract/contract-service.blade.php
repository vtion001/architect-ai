{{--
    Contract Template: Service Agreement
    
    Comprehensive international services contract
    Includes payment terms, deliverables, and legal clauses
--}}

<div class="space-y-6">
    {{-- Contract Type --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Agreement Type
        </label>
        <input 
            type="text" 
            value="Service Agreement"
            disabled
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm"
        >
    </div>

    {{-- Service Provider (Your Company) --}}
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200 space-y-4">
        <h4 class="text-[10px] font-bold text-blue-900 uppercase tracking-wider">Service Provider Details</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-blue-900 mb-2">Business Name</label>
                <input 
                    type="text" 
                    x-model="contractDetails.providerBusiness"
                    placeholder="Your Company LLC"
                    class="w-full px-4 py-2.5 rounded-xl border border-blue-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-blue-900 mb-2">Tax ID / Registration Number</label>
                <input 
                    type="text" 
                    x-model="contractDetails.providerTaxId"
                    placeholder="12-3456789"
                    class="w-full px-4 py-2.5 rounded-xl border border-blue-200 text-sm"
                >
            </div>
        </div>
        <div>
            <label class="block text-[9px] font-semibold text-blue-900 mb-2">Business Address</label>
            <input 
                type="text" 
                x-model="contractDetails.providerAddress"
                placeholder="123 Provider St, City, Country"
                class="w-full px-4 py-2.5 rounded-xl border border-blue-200 text-sm"
            >
        </div>
    </div>

    {{-- Client Information --}}
    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Client Details</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Client Name / Company</label>
                <input 
                    type="text" 
                    x-model="recipientName"
                    placeholder="ABC Corporation"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Client Email</label>
                <input 
                    type="email" 
                    x-model="contractDetails.clientEmail"
                    placeholder="client@company.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Client Address</label>
                <input 
                    type="text" 
                    x-model="contractDetails.clientAddress"
                    placeholder="456 Client Ave"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">City</label>
                <input 
                    type="text" 
                    x-model="contractDetails.clientCity"
                    placeholder="New York"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Country</label>
                <input 
                    type="text" 
                    x-model="contractDetails.clientCountry"
                    placeholder="United States"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
        <div>
            <label class="block text-[9px] font-semibold text-slate-600 mb-2">Tax ID (Optional)</label>
            <input 
                type="text" 
                x-model="contractDetails.clientTaxId"
                placeholder="98-7654321"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
            >
        </div>
    </div>

    {{-- Contract Terms --}}
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
                Contract Duration
            </label>
            <input 
                type="text" 
                x-model="contractDetails.duration"
                placeholder="12 months"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Services & Terms --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Services, Deliverables & Terms
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="SERVICES TO BE PROVIDED:&#10;• [Service 1]&#10;• [Service 2]&#10;• [Service 3]&#10;&#10;DELIVERABLES:&#10;• [Deliverable 1]&#10;• [Deliverable 2]&#10;&#10;PAYMENT TERMS:&#10;• Total Contract Value: $X&#10;• Payment Schedule: [Monthly/Milestone-based]&#10;• Invoice Terms: Net 30&#10;&#10;RESPONSIBILITIES:&#10;Provider: [Your responsibilities]&#10;Client: [Client responsibilities]&#10;&#10;TERMINATION CLAUSE:&#10;[Notice period and conditions]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed font-mono"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will structure this into formal legal contract language</p>
    </div>
</div>
