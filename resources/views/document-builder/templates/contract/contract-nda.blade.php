{{--
    Contract Template: Non-Disclosure Agreement (NDA)
    
    Confidentiality and non-disclosure agreement
    Protects proprietary information
--}}

<div class="space-y-6">
    {{-- Agreement Type --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Agreement Type
        </label>
        <select 
            x-model="analysisType"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
        >
            <option value="Mutual NDA">Mutual NDA (Both Parties)</option>
            <option value="Unilateral NDA">Unilateral NDA (One-Way)</option>
            <option value="Multilateral NDA">Multilateral NDA (Multiple Parties)</option>
        </select>
    </div>

    {{-- Disclosing Party --}}
    <div class="bg-red-50 rounded-2xl p-6 border border-red-200 space-y-4">
        <h4 class="text-[10px] font-bold text-red-900 uppercase tracking-wider">Disclosing Party (Your Company)</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-red-900 mb-2">Company / Individual Name</label>
                <input 
                    type="text" 
                    x-model="senderName"
                    placeholder="Your Company Inc."
                    class="w-full px-4 py-2.5 rounded-xl border border-red-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-red-900 mb-2">Representative Title</label>
                <input 
                    type="text" 
                    x-model="senderTitle"
                    placeholder="CEO, Authorized Signatory"
                    class="w-full px-4 py-2.5 rounded-xl border border-red-200 text-sm"
                >
            </div>
        </div>
        <div>
            <label class="block text-[9px] font-semibold text-red-900 mb-2">Business Address</label>
            <input 
                type="text" 
                x-model="contractDetails.providerAddress"
                placeholder="789 Discloser St, City, State, Country"
                class="w-full px-4 py-2.5 rounded-xl border border-red-200 text-sm"
            >
        </div>
    </div>

    {{-- Receiving Party --}}
    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
        <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Receiving Party</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Party Name</label>
                <input 
                    type="text" 
                    x-model="recipientName"
                    placeholder="Recipient Company LLC"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-slate-600 mb-2">Representative</label>
                <input 
                    type="text" 
                    x-model="recipientTitle"
                    placeholder="Name, Title"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >
            </div>
        </div>
        <div>
            <label class="block text-[9px] font-semibold text-slate-600 mb-2">Address</label>
            <input 
                type="text" 
                x-model="contractDetails.clientAddress"
                placeholder="123 Receiver Blvd, City, State, Country"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
            >
        </div>
    </div>

    {{-- Agreement Terms --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Effective Date
            </label>
            <input 
                type="date" 
                x-model="contractDetails.startDate"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                Confidentiality Period
            </label>
            <input 
                type="text" 
                x-model="contractDetails.duration"
                placeholder="3 years"
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            >
        </div>
    </div>

    {{-- Confidential Information & Terms --}}
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Confidential Information & Terms
        </label>
        <textarea 
            x-model="sourceContent"
            rows="14"
            placeholder="PURPOSE OF DISCLOSURE:&#10;[Why confidential information is being shared]&#10;&#10;CONFIDENTIAL INFORMATION INCLUDES:&#10;• Trade secrets and proprietary technology&#10;• Business plans and strategies&#10;• Financial information&#10;• Customer and supplier lists&#10;• Product roadmaps and R&D&#10;• [Additional specific items]&#10;&#10;EXCLUSIONS (Information NOT covered):&#10;• Publicly available information&#10;• Information already known to receiving party&#10;• Information independently developed&#10;&#10;PERMITTED USE:&#10;[Specific purposes for which information can be used]&#10;&#10;RETURN/DESTRUCTION:&#10;[Terms for returning or destroying confidential materials upon termination]"
            class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed font-mono"
        ></textarea>
        <p class="text-[10px] text-slate-500 mt-2">AI will generate legally sound NDA language with standard clauses</p>
    </div>
</div>
