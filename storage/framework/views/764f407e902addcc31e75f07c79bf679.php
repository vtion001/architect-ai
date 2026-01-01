<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    categories: <?php echo \Illuminate\Support\Js::from($templateCategories)->toHtml() ?>,
    template: 'executive-summary',
    templateVariant: 'exec-corporate',
    recipientName: '',
    recipientTitle: '',
    analysisType: 'Comparative Analysis',
    prompt: '',
    sourceContent: '',
    researchTopic: '',
    isGenerating: false,
    isLoadingPreview: false,
    activeTab: 'preview',
    htmlPreview: '',
    zoomLevel: 0.5,
    showVariantModal: false, 
    selectedCategory: null,
    get selectedCategoryData() { return this.categories.find(c => c.id === this.template); },
    get selectedVariantData() { 
        if (!this.selectedCategoryData) return null;
        return this.selectedCategoryData.variants.find(v => v.id === this.templateVariant);
    },
    fetchPreview() {
        this.isLoadingPreview = true;
        const params = new URLSearchParams({
            template: this.template,
            variant: this.templateVariant
        });
        fetch('<?php echo e(route('report-builder.preview')); ?>?' + params.toString())
            .then(response => response.json())
            .then(data => {
                this.htmlPreview = data.html;
                this.isLoadingPreview = false;
            })
            .catch(error => {
                console.error('Preview error:', error);
                this.isLoadingPreview = false;
            });
    },
    generateReport() {
        this.isGenerating = true;
        fetch('<?php echo e(route('report-builder.generate')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                template: this.template,
                variant: this.templateVariant,
                recipientName: this.recipientName,
                recipientTitle: this.recipientTitle,
                analysisType: this.analysisType,
                prompt: this.prompt,
                contentData: this.sourceContent,
                researchTopic: this.researchTopic
            })
        })
        .then(response => response.json())
        .then(data => {
            this.htmlPreview = data.html;
            this.isGenerating = false;
            this.activeTab = 'preview';
        })
        .catch(error => {
            console.error('Generation Error:', error);
            this.isGenerating = false;
        });
    },
    handleFullView() {
        if (!this.htmlPreview) return;
        const newWin = window.open('', '_blank');
        newWin.document.write(this.htmlPreview);
        newWin.document.close();
    },
    downloadPdf() {
        if (!this.htmlPreview) return;
        const newWin = window.open('', '_blank');
        newWin.document.write(this.htmlPreview);
        newWin.document.close();
        
        // Add a small timeout to ensure styles are parsed before print dialog
        setTimeout(() => {
            newWin.print();
        }, 500);
    },
    init() {
        // Fetch preview on page load
        this.fetchPreview();
        this.$nextTick(() => {
            if (window.lucide) window.lucide.createIcons();
        });

        // Watch for template or variant changes
        this.$watch('template', () => {
            if (this.selectedCategoryData && this.selectedCategoryData.variants.length > 0) {
                this.templateVariant = this.selectedCategoryData.variants[0].id;
            }
            this.fetchPreview();
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
        this.$watch('templateVariant', () => {
            this.fetchPreview();
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
        this.$watch('showVariantModal', (value) => {
            if (value) this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
    }
}">
    <div class="mb-6">

        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-foreground">Report Builder</h1>
                <p class="text-sm text-muted-foreground">Create custom reports with AI-powered analysis</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Configuration Panel -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Report Configuration</h3>
                <p class="text-sm text-muted-foreground">Select template, upload data, and compose your analysis request</p>
            </div>
            <div class="p-6 pt-0 space-y-6">
                <!-- Template Selection Grid -->
                <!-- x-data augmentation to support modal state -->
                <div class="space-y-4">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 mb-3 block">
                        Select Report Template Style
                    </label>
                    <?php echo $__env->make('components.template-selector', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    
                    <!-- Selected Template Indicator -->
                    <div class="mt-3 p-3 bg-muted/40 rounded-lg flex items-center justify-between" x-show="templateVariant">
                        <div class="text-sm">
                            <span class="text-muted-foreground">Selected Style:</span>
                            <span class="font-semibold ml-1" x-text="selectedVariantData?.name"></span>
                        </div>
                        <button @click="showVariantModal = true" class="text-xs text-primary hover:underline">Change</button>
                    </div>
                </div>

                <!-- Analysis Type -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Analysis Type
                    </label>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <span x-text="analysisType">Comparative Analysis</span>
                            <i data-lucide="chevron-down" class="h-4 w-4 opacity-50"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-50 mt-1 w-full rounded-md border bg-popover p-1 text-popover-foreground shadow-md outline-none" style="display: none;">
                            <template x-for="type in ['Comparative Analysis', 'Growth Strategy', 'Financial Audit', 'SWOT Analysis']">
                                <button @click="analysisType = type; open = false" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50" x-text="type"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Strategic Mandate -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Strategic Mandate / Instructions</label>
                    <p class="text-[11px] text-muted-foreground mb-1">Describe the specific goal or additional instructions for the AI analyst.</p>
                    <textarea 
                        x-model="prompt"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="e.g. Focus on ROI for the next 5 years and include a risk assessment..."></textarea>
                </div>

                <!-- Source Content -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Source Content / Context</label>
                    <p class="text-[11px] text-muted-foreground mb-1">Paste raw notes or data here. AI will restructure it professionally into the template.</p>
                    <textarea 
                        x-model="sourceContent"
                        class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="e.g. Our company grew 20% this year. We launched 3 new products. John led the sales team well..."></textarea>
                </div>

                <!-- Research Topic -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none flex items-center gap-2">
                        Deep Research Topic
                        <span class="inline-flex items-center rounded-full border px-1.5 py-0.5 text-[10px] font-semibold bg-primary/10 text-primary border-transparent">Research Engine</span>
                    </label>
                    <div class="relative">
                        <input 
                            x-model="researchTopic"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-9 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                            placeholder="e.g. Market trends for semiconductor industry in Q3 2024..." />
                        <i data-lucide="brain" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary opacity-70"></i>
                    </div>
                </div>

                <!-- Recipient Info -->
                <div class="space-y-4 border-t pt-4">
                    <label class="text-sm font-semibold text-primary">Recipient Information</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Full Name</label>
                            <input x-model="recipientName" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="e.g. John Doe" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Title / Role</label>
                            <input x-model="recipientTitle" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="e.g. CEO" />
                        </div>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="space-y-3">
                    <label class="text-sm font-medium leading-none">Upload Document (PDF or Image)</label>
                    <div class="border-2 border-dashed border-border rounded-xl p-4 transition-colors hover:border-primary/50 relative">
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                        <div class="flex flex-col items-center justify-center py-4 text-center pointer-events-none">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                <i data-lucide="upload" class="w-6 h-6 text-primary"></i>
                            </div>
                            <span class="text-sm font-medium">Click to upload or drag and drop</span>
                            <span class="text-xs text-muted-foreground mt-1">PDF, JPEG, PNG, WEBP (max 10MB)</span>
                        </div>
                    </div>
                </div>

                 <!-- Generate Button -->
                 <div class="flex gap-3">
                    <button
                        @click="generateReport"
                        :disabled="isGenerating"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 flex-1"
                    >
                        <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="isGenerating"></i>
                        <span x-show="isGenerating">Generating...</span>
                        <i data-lucide="sparkles" class="w-4 h-4 mr-2" x-show="!isGenerating"></i>
                        <span x-show="!isGenerating">Generate Report</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between p-6">
                <div class="space-y-1.5">
                    <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                        Report Preview
                    </h3>
                    <p class="text-sm text-muted-foreground">Live preview of your generated report</p>
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        @click="downloadPdf"
                        :disabled="!htmlPreview"
                        class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3 disabled:opacity-50"
                    >
                        <i data-lucide="download" class="w-3.5 h-3.5 mr-2"></i>
                        PDF
                    </button>
                    <button 
                        @click="handleFullView"
                        :disabled="!htmlPreview"
                        class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3 disabled:opacity-50"
                    >
                        <i data-lucide="maximize-2" class="w-3.5 h-3.5 mr-2"></i>
                        Full View
                    </button>
                </div>
            </div>
            <div class="p-6 pt-0">
                <!-- Preview/Code Panel -->
                <div class="w-full flex flex-col">
                    <!-- Tabs and Zoom Controls -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="grid grid-cols-2 bg-muted p-1 rounded-md w-48">
                            <button @click="activeTab = 'preview'" 
                                :class="activeTab === 'preview' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:bg-background/50'"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium transition-all">
                                Preview
                            </button>
                            <button @click="activeTab = 'html'"
                                :class="activeTab === 'html' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:bg-background/50'"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium transition-all">
                                HTML Code
                            </button>
                        </div>
                        <div class="flex items-center gap-2 ml-auto">
                            <button @click="zoomLevel = Math.max(0.3, zoomLevel - 0.1)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-muted text-muted-foreground transition-colors" title="Zoom Out">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                            <span class="text-xs text-muted-foreground font-medium w-12 text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                            <button @click="zoomLevel = Math.min(1.5, zoomLevel + 0.1)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-muted text-muted-foreground transition-colors" title="Zoom In">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'preview'" class="border rounded-lg min-h-[700px] bg-muted/30 relative flex flex-col items-center justify-start p-6 overflow-y-auto overflow-x-hidden">
                        <!-- Loading Overlay -->
                        <div x-show="isGenerating || isLoadingPreview" class="absolute inset-0 bg-background/60 backdrop-blur-[2px] z-30 flex items-center justify-center rounded-xl" style="display: none;">
                            <div class="flex flex-col items-center gap-4 bg-white p-8 rounded-2xl shadow-2xl border ring-1 ring-slate-900/5">
                                <div class="relative">
                                    <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="sparkles" class="lucide lucide-sparkles w-6 h-6 text-primary absolute inset-0 m-auto animate-pulse"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>
                                </div>
                                <div class="text-center">
                                    <span class="text-lg font-bold block text-slate-900" x-text="isGenerating ? 'Generating Report' : 'Loading Preview'"></span>
                                    <span class="text-sm text-slate-500" x-text="isGenerating ? 'AI is crafting your document...' : 'Fetching template preview...'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Template Preview via iframe -->
                        <div x-show="!isLoadingPreview && htmlPreview" 
                              class="shadow-2xl bg-white ring-1 ring-slate-900/5 overflow-hidden origin-top transition-transform duration-200" 
                              :style="`width: 210mm; min-height: 297mm; transform: scale(${zoomLevel})`">
                            <iframe :srcdoc="htmlPreview" class="w-full border-none" style="height: 297mm;" sandbox="allow-same-origin allow-scripts"></iframe>
                        </div>

                        <!-- Placeholder when no preview is available -->
                        <div x-show="!isLoadingPreview && !htmlPreview" 
                             class="flex-shrink-0 shadow-lg bg-white rounded-sm relative origin-top transition-transform duration-200 flex flex-col items-center justify-center"
                             :style="`width: 210mm; height: 297mm; transform: scale(${zoomLevel})`">
                            <i data-lucide="file-text" class="w-16 h-16 text-muted-foreground/30 mb-4"></i>
                            <h3 class="text-lg font-medium text-muted-foreground">No Preview Available</h3>
                            <p class="text-sm text-muted-foreground/70">Select a template to see the preview</p>
                        </div>
                    </div>

                    <div x-show="activeTab === 'html'" class="border rounded-lg p-4 min-h-[400px] bg-muted/30 overflow-auto font-mono text-xs">
                         <div x-show="!htmlPreview" class="flex items-center justify-center h-[400px] text-muted-foreground">
                            <p>No HTML code available yet.</p>
                        </div>
                        <div x-show="htmlPreview">
                            <pre class="bg-slate-100 p-2 rounded text-[10px] whitespace-pre-wrap"><code x-text="htmlPreview"></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/report-builder/index.blade.php ENDPATH**/ ?>