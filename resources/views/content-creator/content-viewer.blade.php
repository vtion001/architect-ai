{{--
    Content Viewer - Main Layout
    
    Modularized from 1,346 lines to ~200 lines.
    Post card components and modals extracted to /partials/post-card directory.
    Alpine.js logic available in /resources/js/components/content-viewer/
--}}
@extends('layouts.app')

@php
    // Pre-process posts data for JavaScript
    $rawResult = trim($content->result ?? '');
    
    // Blog posts should not be split - they are single cohesive pieces of content
    $blogMetaDescription = '';
    $blogTitle = '';
    $blogBodyHtml = '';

    if ($content->type === 'blog') {
        // Extract meta description: "Meta Description: <text>" at the start
        if (preg_match('/^Meta Description:\s*(.+?)(?=\n)/is', $rawResult, $metaMatch)) {
            $blogMetaDescription = trim($metaMatch[1]);
            $rawResult = preg_replace('/^Meta Description:\s*.+?\n/is', '', $rawResult, 1);
        }

        // Extract H1 title: "# Title"
        if (preg_match('/^#\s+(.+?)\n/im', $rawResult, $titleMatch)) {
            $blogTitle = trim($titleMatch[1]);
        }

        // Convert cleaned markdown to HTML for body
        $blogBodyHtml = \Illuminate\Support\Str::markdown(trim($rawResult));

        $rawSegments = [trim($rawResult)];
    } else {
        // Improved regex to handle various newline formats and common separators (---, ***, ___)
        // Using 'm' flag to treat as multiple lines, making start/end line anchors reliable
        $rawSegments = preg_split('/^\s*[-*_]{3,}\s*$/m', $rawResult);
        
        // Fallback 1: Numbered list split (e.g. "1. ", "2. ")
        if (count($rawSegments) < ($content->options['count'] ?? 1)) {
            // Only split by numbers that are at the start of a line
            $numberedSplit = preg_split('/^\s*\d+\.\s+/m', $rawResult);
            $numberedSplit = array_values(array_filter(array_map('trim', $numberedSplit)));
            if (count($numberedSplit) >= ($content->options['count'] ?? 1)) {
                $rawSegments = $numberedSplit;
            }
        }
        
        // Fallback 2: Double newline split
        if (count($rawSegments) < ($content->options['count'] ?? 1)) {
            $newlineSplit = preg_split('/\R{2,}/', $rawResult);
            if (count($newlineSplit) >= ($content->options['count'] ?? 1)) {
                $rawSegments = $newlineSplit;
            }
        }
    }

    $postsData = [];
    $globalHashtags = '';

    if (!empty($rawSegments)) {
        $rawSegments = array_values(array_filter(array_map('trim', $rawSegments)));
        
        // Handle global hashtags if they appear as a separate segment at the end
        $lastSegment = end($rawSegments);
        if (count($rawSegments) > 1 && str_starts_with($lastSegment, '#') && strlen($lastSegment) < 300 && !str_contains($lastSegment, "\n")) {
            array_pop($rawSegments);
            $globalHashtags = $lastSegment;
        }
    } else {
        $rawSegments = [$content->result ?? 'No content generated.'];
    }

    foreach ($rawSegments as $idx => $post) {
        $finalPostContent = trim($post);
        // Clean up remaining leading number if split didn't catch it
        $finalPostContent = preg_replace('/^\d+\.\s*/', '', $finalPostContent);
        
        if ($globalHashtags && !str_contains($finalPostContent, $globalHashtags)) {
            $finalPostContent .= "\n\n" . $globalHashtags;
        }
        
        if ($content->type === 'blog') {
            $cleanHtml = \Illuminate\Support\Str::markdown($finalPostContent);
        } else {
            $cleanText = preg_replace('/^#+\s+/m', '', $finalPostContent);
            $cleanText = str_replace(['*', '`'], '', $cleanText);
            $cleanHtml = nl2br(e($cleanText));
        }
        
        $postsData[] = [
            'index' => $idx,
            'raw' => $finalPostContent,
            'html' => $cleanHtml,
            'published' => in_array($idx, $publishedIndexes ?? [])
        ];
    }
@endphp

@section('content')
@if($content->status === 'generating')
    @include('content-creator.partials.loading-state')
@elseif($content->status === 'failed')
    @include('content-creator.partials.failed-state')
@else
<script>
    // Post data prepared server-side to avoid HTML attribute escaping issues
    window.__postsData = @json($postsData);
    window.__contentViewerConfig = {
        deleteUrl: @js(route("content-creator.destroy", $content->id)),
        redirectUrl: @js(route("content-creator.index")),
        saveVisualUrl: @js(url("/content-creator/{$content->id}/save-visual")),
        csrfToken: @js(csrf_token()),
        contentId: @js($content->id),
        contentType: @js($content->type),
        isFacebookConnected: @js($isFacebookConnected),
        brands: @js($brands ?? []),
        visuals: @js($content->options['visuals'] ?? []),
        blog: {
            title: @js($blogTitle),
            metaDescription: @js($blogMetaDescription),
            bodyHtml: @js($blogBodyHtml),
        }
    };
    
    document.addEventListener('alpine:init', () => {
        // Batch Manager component
        Alpine.data('batchManager', () => ({
            showDeleteModal: false,
            isDeleting: false,
            showCopyAllToast: false,
            batchChildren: [],
            batchPage: 1,
            batchLastPage: 1,
            isLoadingChildren: false,
            
            init() {
                if (window.__contentViewerConfig.contentType === 'blog_batch') {
                    this.loadBatchChildren();
                }
            },
            
            loadBatchChildren() {
                this.isLoadingChildren = true;
                const nextPage = this.batchPage + 1;
                fetch(`/content-creator/${window.__contentViewerConfig.contentId}/children?page=${nextPage}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.__contentViewerConfig.csrfToken }
                })
                .then(res => res.json())
                .then(data => {
                    this.batchChildren = [...this.batchChildren, ...data.items];
                    this.batchPage = data.current_page;
                    this.batchLastPage = data.last_page;
                })
                .catch(e => console.error(e))
                .finally(() => { this.isLoadingChildren = false; });
            },
            
            copyAllPosts() {
                const allPosts = window.__postsData.map(p => p.raw).join('\n\n---\n\n');
                navigator.clipboard.writeText(allPosts).then(() => {
                    this.showCopyAllToast = true;
                    setTimeout(() => { this.showCopyAllToast = false; }, 2500);
                }).catch(err => {
                    const textarea = document.createElement('textarea');
                    textarea.value = allPosts;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    this.showCopyAllToast = true;
                    setTimeout(() => { this.showCopyAllToast = false; }, 2500);
                });
            },
            
            deleteBatch() {
                this.isDeleting = true;
                fetch(window.__contentViewerConfig.deleteUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.__contentViewerConfig.csrfToken, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) { window.location.href = window.__contentViewerConfig.redirectUrl; }
                    else { alert('Delete failed: ' + (data.message || 'Unknown error')); this.isDeleting = false; this.showDeleteModal = false; }
                })
                .catch(e => { console.error(e); alert('An error occurred during deletion.'); this.isDeleting = false });
            }
        }));

        // Post Card component
        Alpine.data('postCard', (postIndex) => {
            const cfg = window.__contentViewerConfig;
            const postData = window.__postsData[postIndex] || {};
            return {
                index: postIndex,
                showMediaOptions: false, imageUrl: null, isUploading: false, isGenerating: false, isRegenerating: false, isPublishing: false, isEditing: false,
                isPublished: postData.published || false, publishResult: null, showPublishModal: false, selectedPlatforms: [], scheduleDate: new Date().toISOString().slice(0, 16),
                facebookPages: [], selectedFacebookPage: null, isFacebookConnected: cfg.isFacebookConnected, isFetchingPages: false, showPageModal: false, postNow: true,
                rawContent: postData.raw || '', htmlContent: postData.html || '',
                showImageCreatorModal: false, imageFormat: 'realistic', imagePrompt: '', posterText: '', selectedAssetUrl: null, selectedBrandId: null, mediaAssets: [], isLoadingAssets: false, brands: cfg.brands, showCopyToast: false,
            
                init() {
                    if (cfg.visuals && cfg.visuals[this.index]) this.imageUrl = cfg.visuals[this.index];
                    this.$watch('imageUrl', (val) => { if (val) this.persistVisual(this.index); });
                    const savedPage = localStorage.getItem('arch_ai_fb_page');
                    if (savedPage) { try { this.selectedFacebookPage = JSON.parse(savedPage); } catch (e) {} }
                    if (this.isFacebookConnected) this.refreshPageData();
                },
                refreshPageData() { if (!this.selectedFacebookPage) return; fetch('/social-planner/facebook-pages').then(res => res.ok ? res.json() : { pages: [] }).then(data => { const pages = data.pages || []; if (pages.length > 0) { const fresh = pages.find(p => p.id === this.selectedFacebookPage.id); if (fresh) { this.selectedFacebookPage = fresh; localStorage.setItem('arch_ai_fb_page', JSON.stringify(fresh)); } } }).catch(() => {}); },
                toggleEdit() { this.isEditing = !this.isEditing; if (!this.isEditing) this.htmlContent = this.formatForDisplay(this.rawContent); },
                persistVisual(idx) { fetch(cfg.saveVisualUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ index: idx, image_url: this.imageUrl }) }); },
                fetchFacebookPages() { this.isFetchingPages = true; this.showPageModal = true; fetch('/social-planner/facebook-pages').then(res => res.json()).then(data => { this.facebookPages = data.pages || []; this.isFacebookConnected = this.facebookPages.length > 0; if (this.facebookPages.length === 0) { if (!this.isFacebookConnected) { alert('No connection. Link Facebook in Social Planner.'); this.showPageModal = false; } else alert('No pages found.'); } }).catch(err => { console.error(err); alert('Error with Social Engine.'); this.showPageModal = false; }).finally(() => { this.isFetchingPages = false; }); },
                fetchInstagramPages() { this.fetchFacebookPages(); }, fetchLinkedinPages() { alert('LinkedIn coming soon.'); }, fetchTwitterPages() { alert('Twitter coming soon.'); },
                selectPage(page) { this.selectedFacebookPage = page; localStorage.setItem('arch_ai_fb_page', JSON.stringify(page)); this.showPageModal = false; if (!this.selectedPlatforms.includes('facebook')) this.selectedPlatforms.push('facebook'); },
                isVideo(url) { if (!url) return false; return /\.(mp4|mov|avi|wmv|webm)$/i.test(url); },
                copyContent() { navigator.clipboard.writeText(this.rawContent).then(() => { this.showCopyToast = true; setTimeout(() => { this.showCopyToast = false; }, 2000); }).catch(() => { const t = document.createElement('textarea'); t.value = this.rawContent; t.style.position = 'fixed'; t.style.opacity = '0'; document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t); this.showCopyToast = true; setTimeout(() => { this.showCopyToast = false; }, 2000); }); },
                triggerUpload() { this.$refs.fileInput.click(); },
                handleUpload(event) { const file = event.target.files[0]; if (!file) return; this.isUploading = true; if (file.type.startsWith('image/')) { this.compressImage(file, (blob) => { this.performUpload(blob, file.name); }); } else { this.performUpload(file, file.name); } },
                performUpload(blob, name) { const fd = new FormData(); fd.append('file', blob, name); fetch('/content-creator/upload-media', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' } }).then(async res => { if (!res.ok) { if (res.status === 413) throw new Error('File too large.'); return res.json(); } return res.json(); }).then(data => { if (data.success) { this.imageUrl = data.url; this.showMediaOptions = false; } else alert(data.message || 'Upload failed'); }).catch(err => { console.error(err); alert(err.message || 'Upload error'); }).finally(() => { this.isUploading = false; }); },
                compressImage(file, cb) { const r = new FileReader(); r.readAsDataURL(file); r.onload = (e) => { const img = new Image(); img.src = e.target.result; img.onload = () => { const c = document.createElement('canvas'); const MAX = 1920; let w = img.width, h = img.height; if (w > h) { if (w > MAX) { h *= MAX / w; w = MAX; } } else { if (h > MAX) { w *= MAX / h; h = MAX; } } c.width = w; c.height = h; c.getContext('2d').drawImage(img, 0, 0, w, h); c.toBlob((blob) => { cb(blob); }, 'image/jpeg', 0.8); }; }; },
                openImageCreator() {
                    // Extract a meaningful image prompt from blog content
                    let content = this.rawContent;
                    let imagePrompt = '';
                    let posterText = '';
                    
                    // Get first line as potential title/topic
                    const lines = content.split('\n').filter(l => l.trim());
                    let title = lines[0] || '';
                    // Clean title - remove markdown headers and numbering
                    title = title.replace(/^#+\s*/, '').replace(/^\d+\.\s*/, '').trim();
                    
                    // Extract key themes by removing numbered list items and getting main concepts
                    let mainContent = content
                        .replace(/#\w+/g, '') // Remove hashtags
                        .replace(/\n\d+\.\s*[^\n]*/g, '') // Remove numbered list items
                        .replace(/\n+/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                    
                    // Build a descriptive image prompt
                    if (title) {
                        imagePrompt = title;
                        if (mainContent.length > 0) {
                            imagePrompt += ' - ' + mainContent.substring(0, 200);
                        }
                    } else {
                        imagePrompt = mainContent.substring(0, 300);
                    }
                    
                    // Limit lengths
                    imagePrompt = imagePrompt.substring(0, 500);
                    posterText = title ? title.substring(0, 80) : imagePrompt.substring(0, 80);
                    
                    this.imagePrompt = imagePrompt;
                    this.posterText = posterText;
                    this.showImageCreatorModal = true;
                    this.showMediaOptions = false;
                    if (this.mediaAssets.length === 0) this.loadMediaAssets();
                },
                loadMediaAssets() { this.isLoadingAssets = true; fetch('/media-assets?limit=20', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken } }).then(res => res.json()).then(data => { this.mediaAssets = data.assets || []; }).catch(err => console.error(err)).finally(() => { this.isLoadingAssets = false; }); },
                generateAdvancedImage() { if (!this.imagePrompt.trim()) { alert('Please enter a prompt.'); return; } this.isGenerating = true; this.showImageCreatorModal = false; fetch('/content-creator/generate-media', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ prompt: this.imagePrompt, format: this.imageFormat, poster_text: this.imageFormat === 'poster' ? this.posterText : null, reference_asset_url: this.imageFormat === 'asset-reference' ? this.selectedAssetUrl : null, brand_id: this.imageFormat === 'poster' ? this.selectedBrandId : null }) }).then(res => res.json()).then(data => { if (data.success) { this.imageUrl = data.url; this.showMediaOptions = false; } else alert(data.message || 'Generation failed'); }).catch(err => { console.error(err); alert('Generation error.'); }).finally(() => { this.isGenerating = false; }); },
                generateImage() { this.openImageCreator(); },
                regenerateText() { this.isRegenerating = true; fetch('/content-creator/regenerate', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ content_id: cfg.contentId, current_text: this.rawContent }) }).then(res => res.json())                .then(data => { if (data.success) { this.rawContent = data.new_text; this.htmlContent = data.new_html ?? this.formatForDisplay(data.new_text); } else alert('Failed to redo'); }).catch(e => console.error(e)).finally(() => { this.isRegenerating = false; }); },
                formatForDisplay(text) { return text.replace(/\*\*/g, '').replace(/\*/g, '').replace(/^#+\s+/gm, '').replace(/`/g, '').replace(/\n/g, '<br>'); },
                openPublishModal() { this.showPublishModal = true; },
                confirmPublish() { if (this.selectedPlatforms.length === 0) { alert('Select at least one platform.'); return; } if (this.selectedPlatforms.includes('instagram') && !this.imageUrl) { alert('Instagram requires an image.'); return; } let fbId = null, fbToken = null, igId = null; if (this.selectedPlatforms.includes('facebook') || this.selectedPlatforms.includes('instagram')) { if (this.selectedFacebookPage) { fbId = this.selectedFacebookPage.id; fbToken = this.selectedFacebookPage.access_token; if (this.selectedFacebookPage.instagram_business_account) igId = this.selectedFacebookPage.instagram_business_account.id; } else { if (!confirm('No Meta Page selected. Continue?')) return; } } if (this.selectedPlatforms.includes('instagram') && !igId) { if (!confirm('No linked IG account. Continue?')) return; } this.isPublishing = true; fetch('/content-creator/publish', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ content_id: cfg.contentId, segment_index: this.index, final_text: this.rawContent, image_url: this.imageUrl, platforms: this.selectedPlatforms, scheduled_at: this.postNow ? 'now' : this.scheduleDate, facebook_page_id: fbId, facebook_page_token: fbToken, instagram_account_id: igId }) }).then(res => res.json()).then(data => { if (data.success) { this.isPublished = true; this.publishResult = data.message; if (data.results?.facebook && !data.results.facebook.success) this.publishResult += "\n\nNote: FB failed: " + data.results.facebook.error; if (data.results?.instagram && !data.results.instagram.success) this.publishResult += "\n\nNote: IG failed: " + data.results.instagram.error; } else alert('Publishing failed: ' + (data.message || 'Unknown error')); }).finally(() => { this.isPublishing = false; this.showPublishModal = false; }); }
            };
        });
    });
</script>

<div class="p-8 max-w-5xl mx-auto" x-data="batchManager()">
    {{-- Batch Header --}}
    @include('content-creator.partials.batch-header')

    {{-- Stats Grid --}}
    @include('content-creator.partials.stats-grid')

    @if($content->type === 'blog')
        {{-- Blog Article Layout - Single cohesive long-form layout --}}
        @if(!empty($postsData))
            @include('content-creator.partials.post-card.article-layout')
        @endif
    @elseif($content->type === 'blog_batch')
        {{-- Blog Batch: Compact card grid with lazy loading --}}
        <div class="grid grid-cols-1 gap-6 pb-20">
            <template x-for="(child, idx) in batchChildren" :key="child.id">
                <div class="bg-card border border-border rounded-xl shadow-md p-6 animate-in fade-in slide-in-from-bottom-4 duration-500 relative"
                     :style="`animation-delay: ${idx * 150}ms`">
                    <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center text-sm font-black shadow-lg border-4 border-background z-20"
                         x-text="child.batch_index + 1"></div>
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-black text-foreground leading-tight mb-1" x-text="child.title"></h3>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black uppercase tracking-wider text-purple-600"
                                      x-text="child.angle"></span>
                                <span class="text-[10px] text-muted-foreground"
                                      x-text="child.word_count + ' words'"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a :href="`/content-creator/${child.id}`"
                               class="w-8 h-8 rounded-lg bg-muted/50 hover:bg-primary/10 flex items-center justify-center transition-colors"
                               title="View full article">
                                <i data-lucide="external-link" class="w-4 h-4 text-muted-foreground"></i>
                            </a>
                            <button @click="navigator.clipboard.writeText(child.title)"
                                    class="w-8 h-8 rounded-lg bg-muted/50 hover:bg-primary/10 flex items-center justify-center transition-colors"
                                    title="Copy title">
                                <i data-lucide="copy" class="w-4 h-4 text-muted-foreground"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-muted-foreground leading-relaxed line-clamp-3 mb-4" x-text="child.excerpt"></p>
                    <div class="flex items-center gap-3 pt-3 border-t border-border/50">
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-purple-600/10 text-purple-600 border border-purple-600/20"
                              x-text="child.focus_keyword"></span>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-600/10 text-green-600 border border-green-600/20"
                              :class="child.status === 'published' ? 'bg-green-600/10 text-green-600' : 'bg-yellow-600/10 text-yellow-600'"
                              x-text="child.status"></span>
                    </div>
                </div>
            </template>
        </div>
        
        {{-- Load More (only if more pages exist) --}}
        <div x-show="batchChildren.length > 0 && batchPage < batchLastPage" class="text-center pb-20">
            <button @click="loadBatchChildren()"
                    :disabled="isLoadingChildren"
                    class="px-8 py-3 bg-muted/50 hover:bg-muted border border-border rounded-xl text-xs font-black uppercase tracking-wider transition-all disabled:opacity-50">
                <span x-show="!isLoadingChildren">Load More</span>
                <span x-show="isLoadingChildren" class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    Loading...
                </span>
            </button>
        </div>
        
        {{-- Empty / Loading State for blog batch --}}
        <div x-show="batchChildren.length === 0 && !isLoadingChildren" class="text-center py-20 text-muted-foreground">
            <i data-lucide="layers" class="w-12 h-12 mx-auto mb-4 opacity-30"></i>
            <p class="text-sm font-bold">Blog batch is still generating...</p>
            <p class="text-xs mt-1">Each blog is being crafted with a unique angle. This may take a moment.</p>
        </div>
    @else
        {{-- Social Media Card Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-20">
            @foreach($postsData as $postInfo)
            <div x-data="postCard({{ $postInfo['index'] }})"
                 class="w-full bg-card border border-border rounded-xl shadow-md overflow-visible animate-in fade-in slide-in-from-bottom-4 duration-500 relative"
                 style="animation-delay: {{ $postInfo['index'] * 150 }}ms;">

                {{-- Index Badge --}}
                <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-black shadow-lg border-4 border-background z-20">
                    {{ $postInfo['index'] + 1 }}
                </div>

                {{-- Success Overlay --}}
                @include('content-creator.partials.post-card.success-overlay')

                {{-- Hidden File Input --}}
                <input type="file" x-ref="fileInput" class="hidden" accept="image/*,video/*" @change="handleUpload">

                {{-- Post Header --}}
                @include('content-creator.partials.post-card.card-header')

                {{-- Content Body --}}
                @include('content-creator.partials.post-card.content-body')

                {{-- Media Area --}}
                @include('content-creator.partials.post-card.media-area')

                {{-- Actions Footer --}}
                @include('content-creator.partials.post-card.actions-footer')

                {{-- Publish Modal --}}
                @include('content-creator.partials.post-card.modals.publish-modal')

                {{-- Image Creator Modal --}}
                @include('content-creator.partials.post-card.modals.image-creator-modal')
            </div>
            @endforeach
        </div>
    @endif

    {{-- Delete Batch Confirmation Modal --}}
    @include('content-creator.partials.delete-modal')
</div>
@endif
@endsection
