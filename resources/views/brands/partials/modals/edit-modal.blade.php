{{-- Edit Brand Modal --}}
{{-- 
    Expects parent x-data context with:
    - showEditModal, selectedBrand, isSaving, isAnalyzing
    - editLogoPreview
    - updateBrand(), handleEditLogoSelect(), analyzeBlueprint()
--}}
<div x-show="showEditModal && selectedBrand" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
    <template x-if="selectedBrand">
        <div @click.away="showEditModal = false" class="bg-card w-full max-w-3xl max-h-[90vh] rounded-[32px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200 my-auto">
            
            {{-- Modal Header --}}
            <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-tighter">Edit Brand Kit</h2>
                    <p class="text-xs text-muted-foreground mt-1" x-text="selectedBrand.name"></p>
                </div>
                <button @click="showEditModal = false"><i data-lucide="x" class="w-6 h-6 text-muted-foreground"></i></button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar space-y-8">
                
                {{-- Basic Info Section --}}
                @include('brands.partials.modals.form-sections.basic-info', [
                    'modelPrefix' => 'selectedBrand',
                    'showScrapeButton' => false
                ])

                {{-- Logo Upload Section --}}
                @include('brands.partials.modals.form-sections.logo-upload', [
                    'mode' => 'edit'
                ])

                {{-- Colors Section --}}
                @include('brands.partials.modals.form-sections.color-palette', [
                    'modelPrefix' => 'selectedBrand'
                ])

                {{-- Voice Profile Section (Simplified for edit) --}}
                @include('brands.partials.modals.form-sections.voice-profile', [
                    'modelPrefix' => 'selectedBrand',
                    'showFullFields' => false
                ])

                {{-- Document Blueprints Section --}}
                @include('brands.partials.modals.form-sections.blueprints', [
                    'modelPrefix' => 'selectedBrand',
                    'tabVariable' => 'editBlueprintTab',
                    'inputIdPrefix' => 'edit',
                    'isEdit' => true
                ])

            </div>
            
            {{-- Modal Footer --}}
            <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                <button @click="showEditModal = false" class="px-6 py-3 rounded-xl border border-border font-bold text-xs uppercase">Cancel</button>
                <button @click="updateBrand" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black text-xs uppercase tracking-widest shadow-lg disabled:opacity-50 flex items-center gap-2">
                    <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                    <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </template>
</div>
