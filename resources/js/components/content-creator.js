document.addEventListener('alpine:init', () => {
    Alpine.data('contentCreator', () => ({
        generator: 'post',
        topic: '',
        type: 'social-media', // Default type for post generator
        count: 2,
        tone: 'Default Tone',
        length: 'Default Length',
        context: '',
        cta: '',
        cta_snippets: [
            'Join the waitlist at arch-ai.io/beta',
            'Book a free consultation today!',
            'Like and Follow for more updates!',
            'Send us a message for inquiries.',
            'Visit our shop for exclusive deals!',
            'Check out the link in our bio!',
        ],
        showCtaSnippets: false,
        newSnippet: '',
        isManagingSnippets: false,

        brands: [],
        selectedBrandId: '',

        addLineBreaks: true,
        includeHashtags: false,

        // Blog specific
        keywords: '',
        structure: 'Standard',
        isBatchMode: false,
        featuredImageType: 'ai',

        // Video specific
        videoStyle: 'UGC',
        videoDescription: '',
        sourceImage: '',
        aiModel: 'Sora 2 BEST',
        resolution: '1080p',
        aspectRatio: 'Portrait',
        videoDuration: '10 seconds (7 tokens)',
        platform: 'reels',
        hookStyle: 'Problem/Solution',
        duration: '60s',

        // Suggestions & AI
        suggestions: '',
        kbDiscovered: 0,
        isLoadingSuggestions: false,

        isRefining: false,

        isGenerating: false,
        showSuccessModal: false,
        createdContentId: null,
        generatedCalendar: null,
        frameworkId: null, // Track framework ID for bulk operations
        isBulkGeneratingImages: false,
        isBulkScheduling: false,

        // Bulk Schedule Modal State
        showBulkScheduleModal: false,
        bulkStartDate: new Date().toISOString().slice(0, 10),
        bulkPlatforms: ['facebook'], // Default

        init() {
            // Initialize brands from global variable if available
            if (window.__contentCreatorBrands) {
                this.brands = window.__contentCreatorBrands;
            }

            const savedSnippets = localStorage.getItem('arch_ai_cta_snippets');
            if (savedSnippets) {
                try {
                    this.cta_snippets = JSON.parse(savedSnippets);
                } catch (e) {
                    console.error('Failed to parse saved snippets');
                }
            }

            this.$watch('cta_snippets', (value) => {
                localStorage.setItem('arch_ai_cta_snippets', JSON.stringify(value));
            });

            // Watch generator changes to set appropriate default types
            this.$watch('generator', (value) => {
                if (value === 'post') this.type = 'social-media';
                if (value === 'blog') this.type = 'blog-post';
                if (value === 'video') this.type = 'video';
                if (value === 'framework') this.type = 'framework_calendar';
            });
        },

        addSnippet() {
            if (this.newSnippet.trim()) {
                this.cta_snippets.push(this.newSnippet.trim());
                this.newSnippet = '';
            }
        },

        removeSnippet(index) {
            this.cta_snippets.splice(index, 1);
        },

        fetchSuggestions() {
            if (!this.topic || this.isLoadingSuggestions) return;
            this.isLoadingSuggestions = true;
            this.suggestions = '';
            this.kbDiscovered = 0;

            fetch('/content-creator/suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ topic: this.topic })
            })
                .then(res => res.json())
                .then(data => {
                    this.suggestions = data.suggestions;
                    this.kbDiscovered = data.kb_count || 0;
                    this.isLoadingSuggestions = false;
                })
                .catch(err => {
                    console.error(err);
                    this.suggestions = 'Error fetching suggestions.';
                    this.isLoadingSuggestions = false;
                });
        },

        refineContext() {
            if (!this.context || this.isRefining) return;
            this.isRefining = true;

            fetch('/content-creator/refine', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ context: this.context })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.context) {
                        this.context = data.context;
                    }
                    this.isRefining = false;
                })
                .catch(err => {
                    console.error(err);
                    this.isRefining = false;
                });
        },

        generateContent() {
            if (!this.topic) {
                alert('Please enter a topic.');
                return;
            }
            this.isGenerating = true;
            this.generatedCalendar = null;
            this.frameworkId = null;

            const payload = {
                topic: this.topic,
                generator: this.generator,
                type: this.type, // Use current type which is updated via watcher
                count: this.count,
                tone: this.tone,
                length: this.length,
                context: this.context,
                cta: this.cta,
                addLineBreaks: this.addLineBreaks,
                includeHashtags: this.includeHashtags,
                video_platform: this.platform,
                video_hook: this.hookStyle,
                video_duration: this.duration,
                video_style: this.videoStyle,
                video_description: this.topic || this.videoDescription,
                source_image: this.sourceImage,
                ai_model: this.aiModel,
                resolution: this.resolution,
                aspect_ratio: this.aspectRatio,
                generation_duration: this.videoDuration,
                blog_keywords: this.keywords,
                blog_structure: this.structure,
                is_batch_mode: this.isBatchMode,
                featured_image_type: this.featuredImageType,
                brand_id: this.selectedBrandId
            };

            fetch('/content-creator/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Server Error (HTML):', text);
                        throw new Error('Server returned HTML instead of JSON. Check console.');
                    }
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Generation failed.');
                    }
                    return data;
                })
                .then(data => {
                    if (this.generator === 'framework') {
                        try {
                            const rawContent = data.content.content;
                            this.generatedCalendar = typeof rawContent === 'string' ? JSON.parse(rawContent) : rawContent;
                            this.frameworkId = data.content.id; // Store framework ID for bulk actions
                        } catch (e) {
                            console.error('JSON Parse Error', e);
                            alert('Calendar generated but format was invalid.');
                        }
                    } else {
                        this.createdContentId = data.content.id;
                        this.showSuccessModal = true;
                    }
                    this.isGenerating = false;
                })
                .catch(error => {
                    console.error(error);
                    alert(error.message);
                    this.isGenerating = false;
                });
        },

        generateBulkImages() {
            if (!this.frameworkId) return;
            this.isBulkGeneratingImages = true;

            fetch('/api/content/generate-bulk-images', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    framework_id: this.frameworkId,
                    style: 'poster' // Default to poster style for calendar content
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Image generation initiated for ' + data.count + ' posts. Check the media registry shortly.');
                    } else {
                        alert('Failed: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error initiating bulk generation.');
                })
                .finally(() => {
                    this.isBulkGeneratingImages = false;
                });
        },

        openBulkScheduleModal() {
            if (!this.frameworkId) return;
            this.showBulkScheduleModal = true;
        },

        confirmBulkSchedule() {
            if (!this.frameworkId) return;
            if (this.bulkPlatforms.length === 0) {
                alert('Please select at least one platform.');
                return;
            }

            this.isBulkScheduling = true;

            fetch('/api/content/bulk-schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    framework_id: this.frameworkId,
                    start_date: this.bulkStartDate,
                    platforms: this.bulkPlatforms
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        this.showBulkScheduleModal = false;
                    } else {
                        alert('Failed: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error initiating bulk scheduling.');
                })
                .finally(() => {
                    this.isBulkScheduling = false;
                });
        }
    }));
});