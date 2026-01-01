

<!-- Template Category Selection Grid -->
<div class="grid grid-cols-2 gap-3" x-show="!showVariantModal">
    @foreach($templateCategories as $category)
    <button
        @click="
            selectedCategory = @js($category);
            template = '{{ $category['id'] }}';
            showVariantModal = true;
        "
        class="flex flex-col items-start p-3 rounded-xl border text-left transition-all hover:shadow-md"
        :class="template === '{{ $category['id'] }}' 
            ? 'border-primary bg-primary/5 ring-2 ring-primary/20' 
            : 'border-border bg-card hover:border-primary/50'"
    >
        <div class="w-full flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 transition-transform"
                 style="background-color: {{ $category['color'] }}15; color: {{ $category['color'] }}"
                 :class="template === '{{ $category['id'] }}' ? 'scale-110 shadow-lg' : ''">
                <i data-lucide="{{ $category['icon'] }}" class="w-5 h-5"></i>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 transition-colors"
               :class="template === '{{ $category['id'] }}' ? 'text-primary' : 'text-muted-foreground/50'"></i>
        </div>
        <span class="text-[13px] font-bold leading-tight"
              :class="template === '{{ $category['id'] }}' ? 'text-primary' : 'text-muted-foreground'">
            {{ $category['name'] }}
        </span>
    </button>
    @endforeach
</div>

<!-- Variant Selection Modal (Overlay) -->
<div x-show="showVariantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
    <div class="bg-background rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden border" @click.away="showVariantModal = false">
        
        <!-- Header -->
        <div class="p-6 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" 
                     :style="{ backgroundColor: selectedCategory?.color + '15', color: selectedCategory?.color }">
                    <i :data-lucide="selectedCategory?.icon" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold" x-text="selectedCategory?.name + ' Templates'"></h3>
                    <p class="text-sm text-muted-foreground">Choose a template style for your report</p>
                </div>
            </div>
            <button @click="showVariantModal = false" class="p-2 hover:bg-muted rounded-full">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Variants Grid -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="variant in selectedCategory?.variants" :key="variant.id">
                    <div 
                        @click="
                            templateVariant = variant.id; 
                            showVariantModal = false;
                        "
                        class="group relative rounded-xl border bg-card p-4 hover:shadow-lg transition-all hover:border-primary/50 cursor-pointer"
                    >
                        <!-- Template Preview -->
                        <div class="mb-3 h-24 w-full rounded-lg overflow-hidden relative bg-white border border-gray-100">
                            <!-- Corporate -->
                            <template x-if="variant.previewImage === 'corporate'">
                                <div class="w-full h-full bg-white flex flex-col">
                                    <div class="h-3 w-full" :style="{ backgroundColor: selectedCategory.color }"></div>
                                    <div class="p-2 space-y-2">
                                        <div class="flex gap-2">
                                            <div class="w-1/3 h-8 bg-gray-100 rounded border border-gray-200"></div>
                                            <div class="w-1/3 h-8 bg-gray-100 rounded border border-gray-200"></div>
                                            <div class="w-1/3 h-8 bg-gray-100 rounded border border-gray-200 relative">
                                                <div class="absolute bottom-0 left-1 w-1 h-3 opacity-30" :style="{ backgroundColor: selectedCategory.color }"></div>
                                                <div class="absolute bottom-0 left-3 w-1 h-5 opacity-60" :style="{ backgroundColor: selectedCategory.color }"></div>
                                            </div>
                                        </div>
                                        <div class="h-6 w-full bg-gray-50 rounded border border-gray-200 flex items-center px-2 gap-2">
                                            <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                                            <div class="h-1 w-1/2 bg-gray-200 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Minimal -->
                            <template x-if="variant.previewImage === 'minimal'">
                                <div class="w-full h-full bg-white p-3 flex flex-col">
                                    <div class="w-10 h-1 rounded mb-2" :style="{ backgroundColor: selectedCategory.color }"></div>
                                    <div class="w-full h-2 bg-gray-800 rounded mb-1"></div>
                                    <div class="w-2/3 h-2 bg-gray-300 rounded mb-3"></div>
                                    <div class="space-y-1">
                                        <div class="w-full h-1 bg-gray-100 rounded"></div>
                                        <div class="w-full h-1 bg-gray-100 rounded"></div>
                                    </div>
                                </div>
                            </template>

                            <!-- Detailed -->
                            <template x-if="variant.previewImage === 'detailed'">
                                <div class="w-full h-full bg-white">
                                    <div class="h-4 w-full flex items-center px-2 gap-1 mb-1" :style="{ backgroundColor: selectedCategory.color }">
                                        <div class="w-2 h-2 rounded-full bg-white/50"></div>
                                    </div>
                                    <div class="p-2 grid grid-cols-2 gap-2">
                                        <div class="h-8 bg-gray-50 rounded p-1 flex items-end gap-1">
                                            <div class="w-1.5 h-3 rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                            <div class="w-1.5 h-5 rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                            <div class="w-1.5 h-4 rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        </div>
                                        <div class="h-8 bg-gray-50 rounded flex items-center justify-center">
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-200" :style="{ borderColor: selectedCategory.color }"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Market Overview -->
                            <template x-if="variant.previewImage === 'market'">
                                <div class="w-full h-full bg-white p-2">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        <div class="w-12 h-1 bg-gray-800 rounded"></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 h-12">
                                        <div class="border border-gray-100 rounded p-1 flex items-end justify-between">
                                            <div class="w-2 h-full bg-gray-100 rounded-t opacity-30" :style="{ backgroundColor: selectedCategory.color }"></div>
                                            <div class="w-2 h-3/4 bg-gray-100 rounded-t opacity-60" :style="{ backgroundColor: selectedCategory.color }"></div>
                                            <div class="w-2 h-1/2 bg-gray-100 rounded-t opacity-90" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        </div>
                                        <div class="border border-gray-100 rounded flex items-center justify-center">
                                             <div class="w-8 h-8 rounded-full border-4 border-transparent border-t-current" :style="{ color: selectedCategory.color }"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                             <!-- Competitive -->
                             <template x-if="variant.previewImage === 'competitive'">
                                <div class="w-full h-full bg-white p-2">
                                    <div class="w-8 h-1 rounded mb-2" :style="{ backgroundColor: selectedCategory.color }"></div>
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2 p-1 bg-gray-50 rounded">
                                            <div class="w-2 h-2 rounded-full border" :style="{ borderColor: selectedCategory.color }"></div>
                                            <div class="flex-1 h-1 bg-gray-200 rounded"></div>
                                            <div class="w-4 h-1.5 rounded" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        </div>
                                         <div class="flex items-center gap-2 p-1 bg-gray-50 rounded">
                                            <div class="w-2 h-2 rounded-full border" :style="{ borderColor: selectedCategory.color }"></div>
                                            <div class="flex-1 h-1 bg-gray-200 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                             <!-- Segment -->
                             <template x-if="variant.previewImage === 'segment'">
                                <div class="w-full h-full bg-white p-2">
                                    <div class="flex gap-1 mb-2">
                                        <div class="flex-1 text-center">
                                            <div class="h-0.5 w-full rounded mb-1" :style="{ backgroundColor: selectedCategory.color, opacity: 0.4 }"></div>
                                        </div>
                                        <div class="flex-1 text-center">
                                            <div class="h-0.5 w-full rounded mb-1" :style="{ backgroundColor: selectedCategory.color, opacity: 0.7 }"></div>
                                        </div>
                                        <div class="flex-1 text-center">
                                            <div class="h-0.5 w-full rounded mb-1" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1">
                                        <div class="h-3 bg-gray-100 rounded"></div>
                                        <div class="h-3 bg-gray-100 rounded"></div>
                                        <div class="h-3 bg-gray-100 rounded"></div>
                                        <div class="h-3 bg-gray-100 rounded"></div>
                                        <div class="h-3 bg-gray-100 rounded"></div>
                                        <div class="h-3 bg-gray-100 rounded"></div>
                                    </div>
                                </div>
                            </template>


                            <!-- Financial Dashboard -->
                            <template x-if="variant.previewImage === 'dashboard'">
                                <div class="w-full h-full bg-gray-900 p-2 flex flex-col">
                                    <div class="flex gap-2 mb-2">
                                        <div class="flex-1 h-6 bg-gray-800 rounded"></div>
                                        <div class="flex-1 h-6 bg-gray-800 rounded"></div>
                                        <div class="flex-1 h-6 bg-gray-800 rounded"></div>
                                    </div>
                                    <div class="flex-1 bg-gray-800 rounded flex items-end gap-1 p-1">
                                        <div class="flex-1 h-1/3 rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        <div class="flex-1 h-2/3 rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        <div class="flex-1 h-1/2 rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                        <div class="flex-1 h-full rounded-t" :style="{ backgroundColor: selectedCategory.color }"></div>
                                    </div>
                                </div>
                            </template>

                            <!-- Battlecard -->
                            <template x-if="variant.previewImage === 'battlecard'">
                                <div class="w-full h-full p-2" :style="{ backgroundColor: selectedCategory.color }">
                                    <div class="w-full h-1 bg-white/50 rounded mb-2"></div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="h-10 bg-white/20 rounded p-1">
                                            <div class="w-full h-0.5 bg-white/40 mb-1"></div>
                                            <div class="w-2/3 h-0.5 bg-white/40"></div>
                                        </div>
                                        <div class="h-10 bg-white/20 rounded p-1">
                                            <div class="w-full h-0.5 bg-white/40 mb-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                             
                             <!-- Timeline/Deep Dive Fallbacks -->
                             <template x-if="['timeline', 'industry', 'flexible', 'casestudy', 'deepdive', 'forecast', 'quarterly', 'startup'].includes(variant.previewImage)">
                                 <div class="w-full h-full bg-white p-2 flex flex-col items-center justify-center opacity-50">
                                     <i :data-lucide="selectedCategory.icon" class="w-8 h-8 opacity-20" :style="{ color: selectedCategory.color }"></i>
                                 </div>
                             </template>

                        </div>

                        <div class="space-y-2">
                            <h3 class="font-semibold text-sm group-hover:text-primary transition-colors" x-text="variant.name"></h3>
                            <p class="text-xs text-muted-foreground line-clamp-2" x-text="variant.description"></p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="tag in variant.tags" :key="tag">
                                    <span class="inline-flex items-center rounded-full border px-1.5 py-0.5 text-[10px] font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80" x-text="tag"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t flex justify-end">
            <button @click="showVariantModal = false" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                Cancel
            </button>
        </div>
    </div>
</div>
