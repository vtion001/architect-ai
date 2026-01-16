/**
 * Content Creator Alpine.js Component
 * 
 * Extracted from content-creator.blade.php for modularity.
 * This file contains the main component logic for the content creator page.
 */

/**
 * Factory function to create the content creator component configuration
 * @param {Object} config - Configuration object containing routes and data
 * @returns {Object} - Alpine.js component data object
 */
export function createContentCreatorComponent(config) {
    return {
        // Generator State
        generator: 'post',
        topic: '',
        type: 'blog-post',
        count: 2,
        tone: 'Default Tone',
        length: 'Default Length',
        context: '',
        cta: '',

        // CTA Snippets
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

        // Brand Selection
        brands: config.brands || [],
        selectedBrandId: '',

        // Post Settings
        addLineBreaks: true,
        includeHashtags: false,

        // Blog Settings
        keywords: '',
        structure: 'Standard',
        isBatchMode: false,
        featuredImageType: 'ai',

        // Video Settings
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

        // Suggestions State
        suggestions: '',
        kbDiscovered: 0,
        isLoadingSuggestions: false,

        // Generation State
        isRefining: false,
        isGenerating: false,
        showSuccessModal: false,
        createdContentId: null,
        generatedCalendar: null,

        /**
         * Initialize component - load saved snippets from localStorage
         */
        init() {
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
        },

        /**
         * Add a new CTA snippet
         */
        addSnippet() {
            if (this.newSnippet.trim()) {
                this.cta_snippets.push(this.newSnippet.trim());
                this.newSnippet = '';
            }
        },

        /**
         * Remove a CTA snippet by index
         * @param {number} index 
         */
        removeSnippet(index) {
            this.cta_snippets.splice(index, 1);
        },

        /**
         * Fetch AI-powered topic suggestions
         */
        fetchSuggestions() {
            if (!this.topic || this.isLoadingSuggestions) return;

            this.isLoadingSuggestions = true;
            this.suggestions = '';
            this.kbDiscovered = 0;

            fetch(config.routes.suggestions, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
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

        /**
         * Refine context using AI
         */
        refineContext() {
            if (!this.context || this.isRefining) return;

            this.isRefining = true;

            fetch(config.routes.refine, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
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

        /**
         * Generate content based on current settings
         */
        generateContent() {
            if (!this.topic) {
                alert('Please enter a topic.');
                return;
            }

            this.isGenerating = true;
            this.generatedCalendar = null;

            const payload = {
                topic: this.topic,
                generator: this.generator,
                type: this.generator === 'post' ? this.type : this.generator,
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

            fetch(config.routes.generate, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
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
        }
    };
}

// Auto-register with Alpine if available globally
if (typeof window !== 'undefined' && window.Alpine) {
    document.addEventListener('alpine:init', () => {
        // Component will be registered by inline script with config
    });
}
