{{--
    Template Form Router
    
    Dynamically loads the appropriate form partial based on:
    - Selected template (template)
    - Selected variant (templateVariant)
    
    This allows each template to have its own custom form fields
    without affecting the main document-builder file.
--}}

<div>
    {{-- CV/Resume Forms --}}
    <template x-if="template === 'cv-resume'">
        <div>
            <template x-if="templateVariant === 'cv-classic'">
                <div>@include('document-builder.templates.cv-resume.cv-classic')</div>
            </template>
            <template x-if="templateVariant === 'cv-modern'">
                <div>@include('document-builder.templates.cv-resume.cv-modern')</div>
            </template>
            <template x-if="templateVariant === 'cv-technical'">
                <div>@include('document-builder.templates.cv-resume.cv-technical')</div>
            </template>
            <template x-if="templateVariant === 'cv-international'">
                <div>@include('document-builder.templates.cv-resume.cv-international')</div>
            </template>
        </div>
    </template>

    {{-- Cover Letter Forms --}}
    <template x-if="template === 'cover-letter'">
        <div>
            <template x-if="templateVariant === 'cl-standard'">
                <div>@include('document-builder.templates.cover-letter.cl-standard')</div>
            </template>
            <template x-if="templateVariant === 'cl-creative'">
                <div>@include('document-builder.templates.cover-letter.cl-creative')</div>
            </template>
        </div>
    </template>

    {{-- Proposal Forms --}}
    <template x-if="template === 'proposal'">
        <div>
            <template x-if="templateVariant === 'proposal-standard'">
                <div>@include('document-builder.templates.proposal.proposal-standard')</div>
            </template>
            <template x-if="templateVariant === 'proposal-modern'">
                <div>@include('document-builder.templates.proposal.proposal-modern')</div>
            </template>
        </div>
    </template>

    {{-- Contract Forms --}}
    <template x-if="template === 'contract'">
        <div>
            <template x-if="templateVariant === 'contract-service'">
                <div>@include('document-builder.templates.contract.contract-service')</div>
            </template>
            <template x-if="templateVariant === 'contract-nda'">
                <div>@include('document-builder.templates.contract.contract-nda')</div>
            </template>
            <template x-if="templateVariant === 'contract-employment'">
                <div>@include('document-builder.templates.contract.contract-employment')</div>
            </template>
            <template x-if="templateVariant === 'contract-freelance'">
                <div>@include('document-builder.templates.contract.contract-freelance')</div>
            </template>
        </div>
    </template>

    {{-- Report Templates (Shared Form) --}}
    <template x-if="['executive-summary', 'market-analysis', 'financial-overview', 'competitive-intelligence', 'trend-analysis', 'infographic'].includes(template)">
        <div>@include('document-builder.templates.reports.shared-form')</div>
    </template>

    {{-- Custom Template (Fallback) --}}
    <template x-if="template === 'custom'">
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                    Custom Document Title
                </label>
                <input 
                    type="text" 
                    x-model="prompt"
                    placeholder="Enter document title"
                    class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
                    Document Content
                </label>
                <textarea 
                    x-model="sourceContent"
                    rows="12"
                    placeholder="Enter your custom document content..."
                    class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                ></textarea>
            </div>
        </div>
    </template>
</div>
