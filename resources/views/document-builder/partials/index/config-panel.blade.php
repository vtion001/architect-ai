{{-- Document Builder Index - Configuration Panel --}}
<div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Report Configuration</h3>
        <p class="text-sm text-muted-foreground">Select template, upload data, and compose your analysis request</p>
    </div>
    <div class="p-6 pt-0 space-y-6">
        {{-- Template Selection --}}
        @include('document-builder.partials.index.config-template')
        
        {{-- Analysis Type --}}
        @include('document-builder.partials.index.config-analysis')
        
        {{-- Strategic Mandate --}}
        @include('document-builder.partials.index.config-mandate')
        
        {{-- Source Content --}}
        @include('document-builder.partials.index.config-source')
        
        {{-- Research Topic --}}
        @include('document-builder.partials.index.config-research')
        
        {{-- Recipient Info --}}
        @include('document-builder.partials.index.config-recipient')
        
        {{-- File Upload --}}
        @include('document-builder.partials.index.config-upload')
        
        {{-- Generate Button --}}
        @include('document-builder.partials.index.config-actions')
    </div>
</div>
