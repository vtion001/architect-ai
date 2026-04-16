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

        // Content Feed Filter (Recent Activity sidebar)
        contentFeedFilter: 'all',

        // Blog specific
        keywords: '',
        seoSuggestions: [],
        isLoadingSeoSuggestions: false,
        lastAutoSeoTopic: null,
        structure: 'Standard',
        isBatchMode: false,
        blogCount: 1,
        featuredImageType: 'ai',
        featuredImageUrl: '',
        showImagePreview: false,
        blogSuggestionKeyword: '',
        blogSuggestions: [],
        isLoadingBlogSuggestions: false,
        blogBody: '',
        isGeneratingBlogBody: false,

        // Image Creator Modal State
        showImageCreatorModal: false,
        imageFormat: 'realistic',
        imagePrompt: '',
        posterText: '',
        selectedAssetUrl: null,
        mediaAssets: [],
        isLoadingAssets: false,

        // Featured Image Upload State
        isUploadingFeaturedImage: false,
        featuredImageUploadError: '',
        isDraggingFeaturedImage: false,
        isGeneratingFeaturedImage: false,

        // Video specific - Core
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

        // Video specific - UGC Style
        ugcScenario: 'testimonial',
        environment: 'indoor-natural',
        cameraMovement: 'handheld',
        addImperfections: true,
        includeNoise: false,
        casualFraming: true,

        // Video specific - Cinematic Style
        cinematicMood: 'epic',
        lightingSetup: 'golden-hour',
        colorGrading: 'teal-orange',
        lensFlares: false,
        filmGrain: true,
        depthOfField: true,
        motionBlur: false,
        anamorphic: false,

        // Video specific - 3D Animation Style
        animationStyle: 'photorealistic',
        sceneEnvironment: 'infinite-white',
        cameraAnimation: 'orbit-360',
        material: 'metallic-chrome',
        rayTracing: true,
        globalIllumination: true,
        particleEffects: false,
        physicsSimulation: false,
        caustics: false,
        animationSpeed: 1.0,
        colorPalette: 'vibrant',

        // Video specific - Minimalist Style
        visualApproach: 'product-focused',
        backgroundStyle: 'pure-white',
        typographyFocus: 'headline-only',
        animationType: 'fade-in',
        colorScheme: 'black-white',
        centeredComposition: true,
        gridGuides: false,
        whiteSpace: true,
        sansSerifOnly: true,
        elementCount: 3,
        layout: 'centered',

        // Suggestions & AI
        suggestions: '',
        kbDiscovered: 0,
        isLoadingSuggestions: false,

        isRefining: false,

        isGenerating: false,
        showSuccessModal: false,
        createdContentId: null,
        batchChildren: [],
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
                // Auto-fetch SEO when switching to blog if topic exists
                if (value === 'blog' && this.topic && this.topic.length >= 3) {
                    this.fetchSeoSuggestions();
                }
            });

            // Auto-fetch SEO keywords when topic changes in blog mode
            let seoDebounceTimer = null;
            this.$watch('topic', (value) => {
                if (this.generator !== 'blog') return;
                clearTimeout(seoDebounceTimer);
                if (!value || value.length < 3) return;

                seoDebounceTimer = setTimeout(() => {
                    this.fetchSeoSuggestions();
                }, 800);
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
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Suggestions request failed: ${res.status} ${res.statusText}`);
                    }
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned HTML instead of JSON. Check console.');
                    }
                    return res.json();
                })
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

        fetchBlogSuggestions() {
            if (!this.blogSuggestionKeyword || this.isLoadingBlogSuggestions) return;
            this.isLoadingBlogSuggestions = true;
            this.blogSuggestions = [];

            fetch('/content-creator/suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ topic: this.blogSuggestionKeyword, type: 'blog_topics' })
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Blog suggestions request failed: ${res.status} ${res.statusText}`);
                    }
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned HTML instead of JSON. Check console.');
                    }
                    return res.json();
                })
                .then(data => {
                    // Parse suggestions - handle both array and string formats
                    let parsed = [];
                    if (Array.isArray(data.suggestions)) {
                        parsed = data.suggestions;
                    } else if (typeof data.suggestions === 'string' && data.suggestions.startsWith('[')) {
                        try {
                            parsed = JSON.parse(data.suggestions);
                        } catch (e) {
                            // Split by newlines or numbered patterns
                            const lines = data.suggestions.split('\n').filter(l => l.trim());
                            parsed = lines.map(line => {
                                const clean = line.replace(/^\d+[\.\)]\s*/, '').trim();
                                return { title: clean, description: 'AI-generated suggestion' };
                            });
                        }
                    } else if (typeof data.suggestions === 'string') {
                        // Parse numbered list format: "1. Title - Description"
                        const lines = data.suggestions.split('\n').filter(l => l.trim() && /^\d/.test(l.trim()));
                        parsed = lines.map(line => {
                            const match = line.match(/^\d+[\.\)]\s*(.+?)(?:\s*[-–—]\s*(.+))?$/);
                            if (match) {
                                return {
                                    title: match[1].trim(),
                                    description: match[2]?.trim() || 'AI-generated suggestion'
                                };
                            }
                            return { title: line.replace(/^\d+[\.\)]\s*/, '').trim(), description: 'AI-generated suggestion' };
                        });
                    }

                    // Ensure we have 3-5 suggestions
                    if (parsed.length === 0) {
                        parsed = [
                            { title: 'Top 10 ' + this.blogSuggestionKeyword + ' Tips for Beginners', description: 'Comprehensive guide covering essential strategies and best practices', category: 'How-to' },
                            { title: 'The Ultimate Guide to ' + this.blogSuggestionKeyword, description: 'In-depth resource for mastering this topic from basics to advanced', category: 'Guide' },
                            { title: this.blogSuggestionKeyword + ' Mistakes to Avoid in 2024', description: 'Common pitfalls and how to sidestep them effectively', category: 'Tips' },
                            { title: 'Why ' + this.blogSuggestionKeyword + ' Matters for Your Business', description: 'Explore the impact and benefits for professionals', category: 'Analysis' },
                            { title: this.blogSuggestionKeyword + ' vs Alternatives: Which is Right for You?', description: 'Compare options to make informed decisions', category: 'Comparison' }
                        ];
                    }

                    this.blogSuggestions = parsed.slice(0, 5);
                    this.isLoadingBlogSuggestions = false;
                })
                .catch(err => {
                    console.error(err);
                    this.blogSuggestions = [
                        { title: 'Error loading suggestions. Please try again.', description: '', category: '' }
                    ];
                    this.isLoadingBlogSuggestions = false;
                });
        },

        fetchSeoSuggestions() {
            if (!this.topic && !this.blogSuggestionKeyword) {
                return;
            }
            // Skip if already fetched for this exact topic
            if (this.topic && this.topic === this.lastAutoSeoTopic && this.keywords) {
                return;
            }
            if (this.isLoadingSeoSuggestions) return;
            this.isLoadingSeoSuggestions = true;
            this.seoSuggestions = [];

            const searchTerm = this.topic || this.blogSuggestionKeyword;
            const isAutoFetch = !this.blogSuggestionKeyword && this.topic === searchTerm;

            if (isAutoFetch) {
                this.lastAutoSeoTopic = this.topic;
            }

            fetch('/content-creator/suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ topic: searchTerm, type: 'seo_keywords' })
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`SEO suggestions request failed: ${res.status} ${res.statusText}`);
                    }
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned HTML instead of JSON. Check console.');
                    }
                    return res.json();
                })
                .then(data => {
                    let parsed = [];
                    if (Array.isArray(data.suggestions)) {
                        parsed = data.suggestions;
                    } else if (typeof data.suggestions === 'string') {
                        parsed = data.suggestions.split(/[,,\n]+/).map(s => s.trim()).filter(s => s.length > 2);
                    }
                    if (parsed.length === 0) {
                        parsed = [
                            searchTerm + ' guide',
                            searchTerm + ' tips',
                            'best ' + searchTerm,
                            searchTerm + ' strategies',
                            searchTerm + ' for beginners'
                        ];
                    }
                    const keywords = parsed.slice(0, 8);

                    if (isAutoFetch) {
                        this.keywords = keywords.join(', ');
                        this.seoSuggestions = [];
                    } else {
                        this.seoSuggestions = keywords;
                    }
                    this.isLoadingSeoSuggestions = false;
                })
                .catch(err => {
                    console.error(err);
                    this.seoSuggestions = [];
                    this.isLoadingSeoSuggestions = false;
                });
        },

        appendKeyword(kw) {
            if (this.keywords && !this.keywords.endsWith(',')) {
                this.keywords += ', ';
            }
            this.keywords += kw;
        },

        generateBlogBody() {
            if (!this.topic) {
                alert('Please enter a blog topic first.');
                return;
            }
            if (this.isGeneratingBlogBody) return;
            this.isGeneratingBlogBody = true;

            fetch('/content-creator/generate-blog-body', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    topic: this.topic,
                    keywords: this.keywords
                })
            })
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.success && data.body) {
                        this.blogBody = data.body;
                    } else {
                        alert(data.message || 'Failed to generate blog body.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Failed to generate blog body. Please try again.');
                    this.isGeneratingBlogBody = false;
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
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
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
            this.batchChildren = [];

            const payload = {
                topic: this.topic,
                generator: this.generator,
                type: this.type, // Use current type which is updated via watcher
                count: this.generator === 'blog' && this.isBatchMode ? this.blogCount : this.count,
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
                blog_body: this.blogBody,
                blog_structure: this.structure,
                is_batch_mode: this.isBatchMode,
                featured_image_type: this.featuredImageType,
                brand_id: this.selectedBrandId
            };

            const endpoint = (this.generator === 'blog' && this.isBatchMode)
                ? '/content-creator/blog/batch'
                : '/content-creator/generate';

            fetch(endpoint, {
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
                    console.log('[Content Creator] Response:', data);
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || data.error || 'Generation failed.');
                    }
                    return data;
                })
                .then(data => {
                    console.log('[Content Creator] Generator:', this.generator);
                    console.log('[Content Creator] Content Status:', data.content.status);
                    console.log('[Content Creator] Content ID:', data.content.id);
                    
                    if (this.generator === 'framework') {
                        if (data.content.status === 'generating') {
                            console.log('[Content Creator] Starting polling for framework ID:', data.content.id);
                            this.pollForFramework(data.content.id);
                        } else if (data.content.status === 'draft') {
                            // Already completed
                            console.log('[Content Creator] Framework already completed');
                            let result = data.content.result;
                            if (typeof result === 'string') {
                                try { result = JSON.parse(result); } catch(e) { console.error('Parse error', e); }
                            }
                            this.generatedCalendar = result;
                            this.frameworkId = data.content.id;
                            this.isGenerating = false;
                        } else {
                            console.warn('[Content Creator] Unexpected status:', data.content.status);
                            this.isGenerating = false;
                        }
                    } else if (this.generator === 'blog' && this.isBatchMode) {
                        this.createdContentId = data.content.id;
                        if (data.content.status === 'generating') {
                            console.log('[Content Creator] Batch generating, starting poll for ID:', data.content.id);
                            this.pollForBatch(data.content.id);
                        } else if (data.content.status === 'published') {
                            console.log('[Content Creator] Batch already completed');
                            this.fetchBatchChildren(data.content.id);
                            this.showSuccessModal = true;
                            this.isGenerating = false;
                        } else {
                            console.warn('[Content Creator] Unexpected batch status:', data.content.status);
                            this.isGenerating = false;
                        }
                    } else {
                        this.createdContentId = data.content.id;
                        this.showSuccessModal = true;
                        this.isGenerating = false;
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert(error.message);
                    this.isGenerating = false;
                });
        },

        pollForFramework(id) {
            console.log('[Content Creator] Poll started for ID:', id);
            let pollCount = 0;
            const maxPolls = 60; // 2 minutes max (60 * 2s)
            
            const interval = setInterval(() => {
                pollCount++;
                console.log(`[Content Creator] Polling attempt ${pollCount}/${maxPolls} for ID: ${id}`);
                
                if (pollCount > maxPolls) {
                    clearInterval(interval);
                    this.isGenerating = false;
                    console.error('[Content Creator] Polling timeout after 2 minutes');
                    alert('Generation is taking longer than expected. Please check your recent content or try again.');
                    return;
                }
                
                fetch(`/content-creator/${id}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(async res => {
                    if (!res.ok) throw new Error(`Poll failed: HTTP ${res.status}`);
                    const ct = res.headers.get('content-type');
                    if (!ct || !ct.includes('application/json')) throw new Error('Non-JSON response');
                    return res.json();
                })
                .then(data => {
                    const status = data.content.status;
                    console.log(`[Content Creator] Poll response - Status: ${status}`);
                    
                    if (status === 'draft') {
                        clearInterval(interval);
                        console.log('[Content Creator] Framework generation complete!');
                        
                        let result = data.content.result;
                        if (typeof result === 'string') {
                            try { 
                                result = JSON.parse(result); 
                                console.log('[Content Creator] Parsed result:', result);
                            } catch(e) { 
                                console.error('[Content Creator] JSON parse error:', e);
                            }
                        }
                        
                        this.generatedCalendar = result;
                        this.frameworkId = data.content.id;
                        this.isGenerating = false;
                        
                        // Count cards
                        const cardCount = Object.values(result || {}).reduce((sum, arr) => sum + (arr?.length || 0), 0);
                        console.log(`[Content Creator] Generated ${cardCount} cards`);
                    } else if (status === 'failed') {
                        clearInterval(interval);
                        this.isGenerating = false;
                        console.error('[Content Creator] Generation failed');
                        alert('Generation failed. Please try again. Check your token balance and queue status.');
                    }
                })
                .catch(e => {
                    console.error('[Content Creator] Polling error:', e);
                    clearInterval(interval);
                    this.isGenerating = false;
                    alert('Network error while checking generation status.');
                });
            }, 2000);
        },

        pollForBatch(id) {
            console.log('[Content Creator] Batch poll started for ID:', id);
            let pollCount = 0;
            const maxPolls = 60;

            const interval = setInterval(() => {
                pollCount++;
                console.log(`[Content Creator] Batch polling ${pollCount}/${maxPolls} for ID: ${id}`);

                if (pollCount > maxPolls) {
                    clearInterval(interval);
                    this.isGenerating = false;
                    console.error('[Content Creator] Batch polling timeout');
                    alert('Blog batch generation is taking longer than expected. Please check your recent content.');
                    return;
                }

                fetch(`/content-creator/${id}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(async res => {
                    if (!res.ok) throw new Error(`Batch poll failed: HTTP ${res.status}`);
                    const ct = res.headers.get('content-type');
                    if (!ct || !ct.includes('application/json')) throw new Error('Non-JSON response');
                    return res.json();
                })
                .then(data => {
                    const status = data.content.status;
                    console.log(`[Content Creator] Batch poll - Status: ${status}`);

                    if (status === 'published') {
                        clearInterval(interval);
                        console.log('[Content Creator] Batch generation complete!');
                        this.fetchBatchChildren(id);
                        this.showSuccessModal = true;
                        this.isGenerating = false;
                    } else if (status === 'failed') {
                        clearInterval(interval);
                        this.isGenerating = false;
                        console.error('[Content Creator] Batch generation failed');
                        alert('Blog batch generation failed. Please try again.');
                    }
                })
                .catch(e => {
                    console.error('[Content Creator] Batch polling error:', e);
                });
            }, 2000);
        },

        fetchBatchChildren(id) {
            fetch(`/content-creator/${id}/children?per_page=10`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(async res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const ct = res.headers.get('content-type');
                if (!ct || !ct.includes('application/json')) throw new Error('Non-JSON response');
                return res.json();
            })
            .then(data => {
                this.batchChildren = data.items || [];
                console.log(`[Content Creator] Loaded ${this.batchChildren.length} batch children`);
            })
            .catch(e => {
                console.error('[Content Creator] Failed to fetch batch children:', e);
            });
        },

        generateBulkImages() {
            if (!this.frameworkId) return;
            this.isBulkGeneratingImages = true;

            fetch('/content-creator/generate-bulk-images', {
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
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
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
            this.isBulkScheduling = true;

            fetch('/content-creator/bulk-schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    framework_id: this.frameworkId,
                    platforms: this.bulkPlatforms,
                    start_date: this.bulkStartDate
                })
            })
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
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
        },

        generateFeaturedImage() {
            // Use blog body to generate an AI image prompt, then generate the image
            if (this.isGenerating || this.isGeneratingFeaturedImage) return;

            const hasBlogBody = this.blogBody && this.blogBody.length > 50;
            const hasTopic = this.topic && this.topic.length >= 3;

            if (!hasTopic) {
                alert('Please enter a blog topic first.');
                return;
            }

            this.isGeneratingFeaturedImage = true;

            if (hasBlogBody) {
                // First generate an image prompt from the blog body using OpenAI
                this.isGenerating = true;
                fetch('/content-creator/generate-image-prompt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        topic: this.topic,
                        blog_body: this.blogBody
                    })
                })
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.success && data.prompt) {
                        this.imagePrompt = data.prompt;
                    } else {
                        // Fallback to topic-based prompt
                        this.imagePrompt = this.topic + ' - professional blog featured image, cinematic style';
                    }
                    this.featuredImageType = 'ai';
                    this.generateAdvancedImage();
                })
                .catch(err => {
                    console.error('Image prompt error:', err);
                    this.imagePrompt = this.topic + ' - professional blog featured image, cinematic style';
                    this.featuredImageType = 'ai';
                    this.generateAdvancedImage();
                });
            } else {
                // No blog body, use topic directly
                this.imagePrompt = this.topic + ' - professional blog featured image, cinematic style';
                this.featuredImageType = 'ai';
                this.generateAdvancedImage();
            }
        },

        openImageCreator() {
            this.imagePrompt = this.topic || '';
            this.showImageCreatorModal = true;
            this.loadMediaAssets();
        },

        loadMediaAssets() {
            if (this.isLoadingAssets) return;
            this.isLoadingAssets = true;
            fetch('/media-assets')
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => { this.mediaAssets = data.assets || []; })
                .catch(err => console.error('Failed to load assets:', err))
                .finally(() => { this.isLoadingAssets = false; });
        },

        generateAdvancedImage() {
            if (!this.imagePrompt.trim()) {
                alert('Please enter a prompt for the image.');
                return;
            }

            this.isGenerating = true;
            this.showImageCreatorModal = false;

            const payload = {
                prompt: this.imagePrompt,
                format: this.imageFormat,
                poster_text: this.imageFormat === 'poster' ? this.posterText : null,
                reference_asset_url: this.imageFormat === 'asset-reference' ? this.selectedAssetUrl : null,
                brand_id: this.imageFormat === 'poster' ? this.selectedBrandId : null
            };

            fetch('/content-creator/generate-media', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        this.featuredImageUrl = data.url;
                    } else {
                        alert(data.message || 'Generation failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Generation error. Please try again.');
                })
                .finally(() => {
                    this.isGenerating = false;
                    this.isGeneratingFeaturedImage = false;
                });
        },

        handleFeaturedImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploadFeaturedImage(file);
        },

        handleFeaturedImageDrop(event) {
            this.isDraggingFeaturedImage = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                this.featuredImageUploadError = 'Please upload an image file (PNG, JPG, WEBP)';
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                this.featuredImageUploadError = 'File size must be less than 10MB';
                return;
            }
            this.featuredImageUploadError = '';
            this.uploadFeaturedImage(file);
        },

        uploadFeaturedImage(file) {
            this.isUploadingFeaturedImage = true;
            this.featuredImageUploadError = '';

            const formData = new FormData();
            formData.append('image', file);
            formData.append('type', 'featured');

            fetch('/content-creator/upload-featured-image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(async res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        this.featuredImageUrl = data.url;
                    } else {
                        this.featuredImageUploadError = data.message || 'Upload failed';
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.featuredImageUploadError = 'Upload failed. Please try again.';
                })
                .finally(() => {
                    this.isUploadingFeaturedImage = false;
                });
        },


    }));
});