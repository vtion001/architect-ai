{{-- Document Builder - Template Selection --}}
<div class="space-y-4">
    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 mb-3 block">
        Select Report Template Style
    </label>
    @include('components.template-selector')
    
    <!-- Selected Template Indicator -->
    <div class="mt-3 p-3 bg-muted/40 rounded-lg flex items-center justify-between" x-show="templateVariant">
        <div class="text-sm">
            <span class="text-muted-foreground">Selected Style:</span>
            <span class="font-semibold ml-1" x-text="selectedVariantData?.name"></span>
        </div>
        <button @click="showVariantModal = true" class="text-xs text-primary hover:underline">Change</button>
    </div>
</div>
