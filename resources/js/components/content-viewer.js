/**
 * Content Viewer Alpine.js Components
 * 
 * Extracted from content-viewer.blade.php for modularity.
 * Import this in your app.js and register the components.
 */

/**
 * Register all content viewer components with Alpine.
 * @param {object} config - Configuration object containing URLs and tokens
 */
export function registerContentViewerComponents(config) {
    document.addEventListener('alpine:init', () => {
        // Batch Manager Component
        Alpine.data('batchManager', () => ({
            showDeleteModal: false,
            isDeleting: false,
            showCopyAllToast: false,

            copyAllPosts() {
                const allPosts = window.__postsData.map(p => p.raw).join('\n\n---\n\n');
                navigator.clipboard.writeText(allPosts).then(() => {
                    this.showCopyAllToast = true;
                    setTimeout(() => { this.showCopyAllToast = false; }, 2500);
                }).catch(() => {
                    // Fallback for older browsers
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
                fetch(config.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = config.redirectUrl;
                        } else {
                            alert('Delete failed: ' + (data.message || 'Unknown error'));
                            this.isDeleting = false;
                            this.showDeleteModal = false;
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        alert('An error occurred during deletion.');
                        this.isDeleting = false;
                    });
            }
        }));

        // Post Card Component Factory
        Alpine.data('postCard', (postIndex) => {
            const postData = window.__postsData[postIndex] || {};

            return {
                // State
                index: postIndex,
                showMediaOptions: false,
                imageUrl: null,
                isUploading: false,
                isGenerating: false,
                isRegenerating: false,
                isPublishing: false,
                isEditing: false,
                isPublished: postData.published || false,
                publishResult: null,
                showPublishModal: false,
                selectedPlatforms: [],
                scheduleDate: new Date().toISOString().slice(0, 16),
                facebookPages: [],
                selectedFacebookPage: null,
                isFacebookConnected: config.isFacebookConnected,
                isFetchingPages: false,
                showPageModal: false,
                postNow: true,
                rawContent: postData.raw || '',
                htmlContent: postData.html || '',

                // Image Creator Modal State
                showImageCreatorModal: false,
                imageFormat: 'realistic',
                imagePrompt: '',
                posterText: '',
                selectedAssetUrl: null,
                selectedBrandId: null,
                mediaAssets: [],
                isLoadingAssets: false,
                brands: config.brands || [],
                showCopyToast: false,

                init() {
                    const visuals = config.visuals || {};
                    if (visuals && visuals[this.index]) {
                        this.imageUrl = visuals[this.index];
                    }

                    this.$watch('imageUrl', (val) => {
                        if (val) this.persistVisual(this.index);
                    });

                    // Load remembered Facebook Page
                    const savedPage = localStorage.getItem('arch_ai_fb_page');
                    if (savedPage) {
                        try {
                            this.selectedFacebookPage = JSON.parse(savedPage);
                        } catch (e) { console.error('Error loading saved page', e); }
                    }

                    if (this.isFacebookConnected) {
                        this.refreshPageData();
                    }
                },

                // ... Additional methods would be extracted here
                // For brevity, keeping as a reference structure

                isVideo(url) {
                    if (!url) return false;
                    return /\.(mp4|mov|avi|wmv|webm)$/i.test(url);
                },

                copyContent() {
                    navigator.clipboard.writeText(this.rawContent).then(() => {
                        this.showCopyToast = true;
                        setTimeout(() => { this.showCopyToast = false; }, 2000);
                    }).catch(() => {
                        const textarea = document.createElement('textarea');
                        textarea.value = this.rawContent;
                        textarea.style.position = 'fixed';
                        textarea.style.opacity = '0';
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        this.showCopyToast = true;
                        setTimeout(() => { this.showCopyToast = false; }, 2000);
                    });
                },

                formatForDisplay(text) {
                    let clean = text
                        .replace(/\*\*/g, '')
                        .replace(/\*/g, '')
                        .replace(/^#+\s+/gm, '')
                        .replace(/`/g, '');
                    return clean.replace(/\n/g, '<br>');
                },

                toggleEdit() {
                    this.isEditing = !this.isEditing;
                    if (!this.isEditing) {
                        this.htmlContent = this.formatForDisplay(this.rawContent);
                    }
                },

                persistVisual(idx) {
                    fetch(config.saveVisualUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken
                        },
                        body: JSON.stringify({ index: idx, image_url: this.imageUrl })
                    });
                },

                triggerUpload() {
                    this.$refs.fileInput.click();
                },

                refreshPageData() {
                    if (!this.selectedFacebookPage) return;

                    fetch('/social-planner/facebook-pages')
                        .then(res => res.ok ? res.json() : { pages: [] })
                        .then(data => {
                            const pages = data.pages || [];
                            if (pages.length > 0) {
                                const freshPage = pages.find(p => p.id === this.selectedFacebookPage.id);
                                if (freshPage) {
                                    this.selectedFacebookPage = freshPage;
                                    localStorage.setItem('arch_ai_fb_page', JSON.stringify(freshPage));
                                }
                            }
                        })
                        .catch(() => { });
                }
            };
        });
    });
}
